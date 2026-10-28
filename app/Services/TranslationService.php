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
            // Pastikan cache bukan pesan error yang sempat tersimpan sebelumnya
            if (!str_contains($cached, 'QUERY LENGTH LIMIT') && !str_contains($cached, 'MYMEMORY')) {
                return $cached;
            }
            Cache::forget($cacheKey);
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

        // 4. Fallback Provider 3: MyMemory API (dengan Chunking jika teks > 450 karakter)
        try {
            $translatedMyMemory = self::translateViaMyMemory($trimmed, $target, $source);
            if (!empty($translatedMyMemory)) {
                Cache::put($cacheKey, $translatedMyMemory, 86400 * 30);
                return $translatedMyMemory;
            }
        } catch (\Throwable $e) {
            Log::warning('TranslationService all providers failed: ' . $e->getMessage(), [
                'source_text' => $trimmed,
            ]);
        }

        // Safe fallback: return original text without caching failure so subsequent calls can retry
        return $text;
    }

    /**
     * Terjemahkan via MyMemory API dengan chunking cerdas untuk teks > 450 karakter
     * (MyMemory membatasi query maksimal 500 karakter).
     */
    private static function translateViaMyMemory(string $text, string $target, string $source): ?string
    {
        $chunks = self::chunkText($text, 450);
        $translatedChunks = [];

        foreach ($chunks as $chunk) {
            $chunkTrimmed = trim($chunk);
            if ($chunkTrimmed === '') {
                $translatedChunks[] = '';
                continue;
            }

            $response = Http::timeout(6)
                ->get('https://api.mymemory.translated.net/get', [
                    'q' => $chunkTrimmed,
                    'langpair' => $source . '|' . $target,
                ]);

            if (!$response->successful()) {
                return null;
            }

            // MyMemory mengembalikan HTTP 200 walau responseStatus = 403 saat limit terlampaui
            $responseStatus = (string) $response->json('responseStatus');
            if ($responseStatus !== '200') {
                return null;
            }

            $rawText = $response->json('responseData.translatedText');
            if (
                empty($rawText) ||
                str_starts_with($rawText, 'MYMEMORY WARNING') ||
                str_contains($rawText, 'QUERY LENGTH LIMIT EXCEEDED') ||
                str_contains($rawText, 'MYMEMORY')
            ) {
                return null;
            }

            $decoded = html_entity_decode(trim($rawText), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $translatedChunks[] = $decoded;
        }

        $result = implode("\n", $translatedChunks);
        return trim($result) !== '' ? trim($result) : null;
    }

    /**
     * Pecah teks panjang menjadi potongan-potongan <= maxLength karakter agar tidak melebihi limit API.
     */
    private static function chunkText(string $text, int $maxLength = 450): array
    {
        if (mb_strlen($text) <= $maxLength) {
            return [$text];
        }

        $lines = explode("\n", $text);
        $chunks = [];
        $current = '';

        foreach ($lines as $line) {
            if (mb_strlen($current . "\n" . $line) > $maxLength && $current !== '') {
                $chunks[] = trim($current);
                $current = $line;
            } else {
                $current = $current === '' ? $line : $current . "\n" . $line;
            }

            // Jika satu baris sendiri sudah melebihi maxLength, pecah berdasarkan kalimat atau spasi
            while (mb_strlen($current) > $maxLength) {
                $cutPos = mb_strrpos(mb_substr($current, 0, $maxLength), '. ');
                if ($cutPos === false) {
                    $cutPos = mb_strrpos(mb_substr($current, 0, $maxLength), ' ');
                }
                if ($cutPos === false) {
                    $cutPos = $maxLength;
                } else {
                    $cutPos += 1;
                }

                $chunks[] = trim(mb_substr($current, 0, $cutPos));
                $current = trim(mb_substr($current, $cutPos));
            }
        }

        if ($current !== '') {
            $chunks[] = trim($current);
        }

        return array_values(array_filter($chunks));
    }
}
