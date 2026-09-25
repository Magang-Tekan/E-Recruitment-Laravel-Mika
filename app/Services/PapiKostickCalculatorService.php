<?php

namespace App\Services;

use App\Models\PapiNorm;
use App\Models\PapiTestResult;
use App\Models\TestAttempt;

class PapiKostickCalculatorService
{
    /**
     * Calculate PAPI Kostick test attempt scores, role/need totals, validity, and fetch interpretations.
     */
    public function calculate(TestAttempt $attempt): ?PapiTestResult
    {
        // 1. Ambil semua jawaban pada attempt ini beserta opsi pertanyaannya
        $answers = $attempt->answers()->with(['option', 'question.options'])->get();

        $papiAnswers = $answers->filter(function ($ans) {
            return ($ans->question && $ans->question->question_type === 'papi_kostick')
                || !empty($ans->option?->attribute_tag);
        });

        if ($papiAnswers->isEmpty()) {
            return null;
        }

        // 2. Inisialisasi 20 Faktor Skor dengan nilai default 0
        $factorCodes = ['N', 'G', 'A', 'L', 'P', 'I', 'T', 'V', 'X', 'S', 'B', 'O', 'R', 'D', 'C', 'Z', 'E', 'K', 'F', 'W'];
        $scores = array_fill_keys($factorCodes, 0);
        $rawAnswers = [];

        foreach ($answers as $ans) {
            $tag = strtoupper($ans->option?->attribute_tag ?? '');
            if (isset($scores[$tag])) {
                $scores[$tag]++;
            }

            if ($ans->question) {
                $qNumber = $ans->question->metadata['number'] ?? $ans->question_id;
                $choice = null;
                if ($ans->question->options) {
                    $sortedOpts = $ans->question->options->sortBy('id')->values();
                    if (isset($sortedOpts[0]) && $sortedOpts[0]->id === $ans->option_id) {
                        $choice = 'a';
                    } elseif (isset($sortedOpts[1]) && $sortedOpts[1]->id === $ans->option_id) {
                        $choice = 'b';
                    }
                }

                $rawAnswers[$qNumber] = [
                    'option_id' => $ans->option_id,
                    'tag' => $tag,
                    'choice' => $choice,
                    'text' => $ans->option?->option_text ?? '',
                ];
            }
        }

        // 3. Hitung Total Atas (Peran / Role) & Total Bawah (Kebutuhan / Need)
        // Total Atas (Role): G, L, I, T, V, S, R, D, C, E
        $roleFactors = ['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E'];
        $roleScore = 0;
        foreach ($roleFactors as $rf) {
            $roleScore += $scores[$rf] ?? 0;
        }

        // Total Bawah (Need): N, A, P, X, B, O, Z, K, F, W
        $needFactors = ['N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W'];
        $needScore = 0;
        foreach ($needFactors as $nf) {
            $needScore += $scores[$nf] ?? 0;
        }

        $isValid = ($roleScore === 45 && $needScore === 45);

        // 4. Ambil Interpretasi Norma dari Database untuk setiap skor
        $interpretations = [];
        $allNorms = PapiNorm::all()->groupBy('factor_code');

        foreach ($factorCodes as $code) {
            $val = $scores[$code] ?? 0;
            $factorNorms = $allNorms->get($code, collect());

            // 1. Cari norma dengan kecocokan rentang persis
            $matchedNorm = $factorNorms->first(function ($norm) use ($val) {
                return $val >= $norm->min_score && $val <= $norm->max_score;
            });

            // 2. Fallback jika nilai berada di luar rentang master (nilai > max atau < min)
            if (!$matchedNorm && $factorNorms->isNotEmpty()) {
                $matchedNorm = $val > $factorNorms->max('max_score')
                    ? $factorNorms->sortByDesc('max_score')->first()
                    : $factorNorms->sortBy('min_score')->first();
            }

            $interpretations[$code] = [
                'score' => $val,
                'interpretation' => $matchedNorm?->interpretation ?? 'Normal',
                'description' => $matchedNorm?->description ?? '',
            ];
        }

        // 5. Simpan / Perbarui Hasil ke tabel papi_test_results
        return PapiTestResult::updateOrCreate(
            ['test_attempt_id' => $attempt->id],
            [
                'scores' => $scores,
                'role_score' => $roleScore,
                'need_score' => $needScore,
                'is_valid' => $isValid,
                'interpretations' => $interpretations,
                'raw_answers' => $rawAnswers,
            ]
        );
    }
}
