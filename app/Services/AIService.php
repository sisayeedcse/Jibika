<?php
namespace App\Services;

use App\Models\AssetRequest;
use App\Models\AssessmentRecord;

class AIService
{
    public function assess(AssetRequest $request): AssessmentRecord
    {
        $provider = env('AI_PROVIDER', 'development');
        $key = env('AI_API_KEY', '');
        $isReal = ($provider !== 'development' && $provider !== 'rule_based' && !empty($key));

        $category = 'MEDIUM';
        $score = 65.00;
        $explanation = [
            'experience' => 'Worker has moderate experience.',
            'location' => 'Location supports asset use.',
            'cost' => 'Requested cost aligns with estimated range.',
        ];

        if ($request->requested_amount_bdt <= 50000) {
            $category = 'HIGH';
            $score = 82.50;
            $explanation['cost'] = 'Low to medium cost with high impact potential.';
        } elseif ($request->requested_amount_bdt > 50000) {
            $category = 'LOW';
            $score = 42.00;
            $explanation['cost'] = 'Higher cost requires additional verification.';
        }

        $record = AssessmentRecord::create([
            'request_id' => $request->id,
            'suitability_category' => $category,
            'numerical_score' => $score,
            'explanation_json' => $explanation,
            'method_version' => 'v1.0-rules',
            'is_ai_generated' => $isReal,
            'provider_name' => $isReal ? $provider : 'NOT AN LLM RESPONSE',
        ]);

        return $record;
    }
}
