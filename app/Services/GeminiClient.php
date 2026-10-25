<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GeminiClient
{
    public function enabled(): bool
    {
        return config('services.ai.provider') === 'gemini'
            && filled(config('services.ai.key'));
    }

    /** Returns decoded JSON array, or null on any failure. */
    public function generateJson(string $system, string $user, array $schema): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        $model = config('services.ai.model', 'gemini-2.5-flash');
        $url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $res = Http::timeout(config('services.ai.timeout', 8))
                ->retry(1, 300, throw: false)
                ->withHeaders(['x-goog-api-key' => config('services.ai.key')])
                ->post($url, [
                    'systemInstruction' => ['parts' => [['text' => $system]]],
                    'contents'          => [['role' => 'user', 'parts' => [['text' => $user]]]],
                    'generationConfig'  => [
                        'temperature'      => 0.2,
                        'maxOutputTokens'  => 1024,
                        'responseMimeType' => 'application/json',
                        'responseSchema'   => $schema,
                    ],
                ]);

            if (! $res->successful()) {
                Log::warning('Gemini non-200', [
                    'status' => $res->status(),
                    'msg'    => Str::limit((string) data_get($res->json(), 'error.message'), 200),
                ]);
                return null;
            }

            $text = data_get($res->json(), 'candidates.0.content.parts.0.text');
            if (! is_string($text) || trim($text) === '') {
                Log::warning('Gemini empty', ['finish' => data_get($res->json(), 'candidates.0.finishReason')]);
                return null;
            }

            $decoded = json_decode($text, true);

            return is_array($decoded) ? $decoded : null;
        } catch (Throwable $e) {
            Log::warning('Gemini call failed', ['error' => class_basename($e)]);
            return null;
        }
    }
}
