<?php

namespace App\Services;

use App\Models\AssessmentRecord;
use App\Models\AssetRequest;

/**
 * Deterministic, explainable rule engine. It never calls an LLM, so it is
 * always stored as is_ai_generated = false.
 */
class AIService
{
    public const METHOD_VERSION = 'v1.1-rules';

    public function assess(AssetRequest $request): AssessmentRecord
    {
        $request->loadMissing(['category', 'worker.workerProfile']);
        $profile  = $request->worker?->workerProfile;
        $category = $request->category;

        $score = 50.0;
        $flags = [];

        // 1. Price vs. catalog benchmark (max +25 / min -15)
        $amount    = (float) $request->requested_amount_bdt;
        $benchmark = $category ? (float) $category->estimated_cost_bdt : 0.0;

        if ($benchmark > 0) {
            $ratio = $amount / $benchmark;
            $pct   = (int) round(($ratio - 1) * 100);
            $cost  = sprintf('Requested BDT %s vs. benchmark BDT %s (%+d%%).',
                number_format($amount), number_format($benchmark), $pct);

            if ($ratio >= 0.85 && $ratio <= 1.15) {
                $score += 25;
            } elseif ($ratio <= 1.5) {
                $score += 10;
                $flags[] = 'price_above_benchmark';
            } else {
                $score -= 15;
                $flags[] = 'price_anomaly';
            }
            if ($ratio < 0.5) {
                $flags[] = 'price_far_below_benchmark';
            }
        } else {
            $cost = 'No benchmark price available for this category.';
        }

        // 2. Experience (max +15)
        $years = (int) ($profile->experience_years ?? 0);
        if ($years >= 5)      { $score += 15; }
        elseif ($years >= 2)  { $score += 8; }
        else                  { $flags[] = 'limited_experience'; }
        $experience = "{$years} year(s) of stated experience as "
            . ($profile->occupation ?? 'unspecified occupation') . '.';

        // 3. Justification quality (max +5)
        $just = trim((string) $request->livelihood_justification);
        if (mb_strlen($just) >= 80) { $score += 5; }
        else                        { $flags[] = 'thin_justification'; }

        // 4. Location stated (max +5)
        $location = trim((string) ($profile->location ?? ''));
        if ($location !== '') { $score += 5; }

        $score = max(0.0, min(100.0, $score));
        $label = $score >= 75 ? 'HIGH' : ($score >= 50 ? 'MEDIUM' : 'LOW');

        $flagText = [
            'price_above_benchmark'     => 'Price is above the benchmark; compare vendor quotes carefully.',
            'price_anomaly'             => 'Price is far above the benchmark (over 50%); verifier should check closely.',
            'price_far_below_benchmark' => 'Price is far below the benchmark; confirm the equipment type or condition.',
            'limited_experience'        => 'Less than 2 years of stated experience.',
            'thin_justification'        => 'Justification is very short.',
        ];

        $explanation = [
            'experience' => $experience,
            'location'   => $location !== '' ? "Stated location: {$location}." : 'Location not stated.',
            'cost'       => $cost,
            'viability'  => "Rule-based score {$score}/100 ({$label}).",
        ];
        if ($matchedFlags = array_values(array_intersect($flags, array_keys($flagText)))) {
            $explanation['flags'] = implode(' ', array_map(fn ($f) => $flagText[$f], $matchedFlags));
        }

        return AssessmentRecord::create([
            'request_id'           => $request->id,
            'suitability_category' => $label,
            'numerical_score'      => $score,
            'explanation_json'     => $explanation,
            'method_version'       => self::METHOD_VERSION,
            'is_ai_generated'      => false,
            'provider_name'        => 'rule-engine',
        ]);
    }
}
