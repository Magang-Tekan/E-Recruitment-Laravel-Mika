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
        
        // 1. Check cache first
        $cached = Cache::get($cacheKey);
        if ($cached !== null && $cached !== '') {
            return $cached;
        }

        // 2. Primary Provider: Google Translate with dict-chrome-ex (official Chrome client, avoids 429 block)
        try {
            $response = Http::asForm()
                ->timeout(8)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                ])
                ->post('https://translate.googleapis.com/translate_a/single?client=dict-chrome-ex&sl=' . urlencode($source) . '&tl=' . urlencode($target) . '&dt=t', [
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

                $cleanTrans = trim($translated);
                if (!empty($cleanTrans)) {
                    Cache::put($cacheKey, $cleanTrans, 86400 * 30);
                    return $cleanTrans;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('TranslationService dict-chrome-ex notice: ' . $e->getMessage());
        }

        // 3. Fallback Provider 2: Google Translate via GET (gtx client)
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

                $cleanTrans = trim($translated);
                if (!empty($cleanTrans)) {
                    Cache::put($cacheKey, $cleanTrans, 86400 * 30);
                    return $cleanTrans;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('TranslationService gtx notice: ' . $e->getMessage());
        }

        // 4. Fallback Provider 3: MyMemory API (if Google is unreachable from VPS)
        try {
            $response = Http::timeout(6)
                ->get('https://api.mymemory.translated.net/get', [
                    'q' => $trimmed,
                    'langpair' => $source . '|' . $target,
                ]);

            if ($response->successful()) {
                $rawText = $response->json('responseData.translatedText');
                if (!empty($rawText) && !str_starts_with($rawText, 'MYMEMORY WARNING')) {
                    $decoded = html_entity_decode(trim($rawText), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (!empty($decoded)) {
                        Cache::put($cacheKey, $decoded, 86400 * 30);
                        return $decoded;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TranslationService all providers failed: ' . $e->getMessage(), [
                'source_text' => $trimmed,
            ]);
        }

        // Safe fallback: return original text without caching failure so subsequent calls can retry
        return $text;
    }
}
