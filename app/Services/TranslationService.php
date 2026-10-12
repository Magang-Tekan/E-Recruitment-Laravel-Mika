<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Translate text automatically from source language to target language.
     * Default: Indonesian (id) -> English (en).
     *
     * @param string|null $text
     * @param string $target
     * @param string $source
     * @return string|null
     */
    public static function translate(?string $text, string $target = 'en', string $source = 'id'): ?string
    {
        if ($text === null || trim($text) === '') {
            return $text;
        }

        $trimmed = trim($text);

        // Check cache first to optimize speed and avoid repeated network calls
        $cacheKey = 'auto_trans_' . md5($trimmed . '_' . $source . '_' . $target);
        
        return Cache::remember($cacheKey, 86400 * 30, function () use ($trimmed, $target, $source, $text) {
            try {
                $response = Http::timeout(6)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get('https://translate.googleapis.com/translate_a/single', [
                        'client' => 'gtx',
                        'sl' => $source,
                        'tl' => $target,
                        'dt' => 't',
                        'q' => $trimmed,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $translated = '';
                    if (isset($data[0]) && is_array($data[0])) {
                        foreach ($data[0] as $part) {
                            $translated .= $part[0] ?? '';
                        }
                    }

                    if (!empty(trim($translated))) {
                        return trim($translated);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('TranslationService auto-translate error: ' . $e->getMessage(), [
                    'source_text' => $trimmed,
                ]);
            }

            // Safe fallback: return original text if service is unreachable
            return $text;
        });
    }
}
