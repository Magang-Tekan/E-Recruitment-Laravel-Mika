<?php

namespace App\Services;

use App\Models\QuestionBank;
use App\Models\TestAttempt;
use Illuminate\Support\Collection;

/**
 * Menghitung "Nilai per Aspek" dari jawaban objektif sebuah attempt.
 *
 * Aspek diambil dari prefix nama sub-kategori sebelum tanda pisah,
 * sehingga tiap butir soal pada bank soal Tes Kemampuan Kerja
 * dikelompokkan ke aspek induknya (Ketelitian & Pengecekan Data, dst).
 */
class AspectEvaluationService
{
    /** Ambang batas aspek umum: di bawah ini hanya digali saat wawancara. */
    public const ASPECT_THRESHOLD = 50;

    /** Ambang batas Etika & Integritas: di bawah ini menggugurkan kelulusan. */
    public const ETHICS_THRESHOLD = 70;

    /**
     * @param Collection|null $questions Opsional; daftar QuestionBank yang diujikan pada attempt.
     *                                   Jika null, soal diambil dari attempt->question_ids.
     */
    public static function calculateAspects(TestAttempt $attempt, ?Collection $questions = null): array
    {
        $answers = $attempt->relationLoaded('answers')
            ? $attempt->answers
            : $attempt->answers()->with('question.subCategory')->get();

        $questions = ($questions ?? self::resolveQuestions($attempt, $answers))
            ->filter(fn ($question) => self::questionType($question) === 'multiple_choice')
            ->values();

        $groups = [];

        foreach ($questions as $question) {
            $name = self::aspectName($question);

            if (!isset($groups[$name])) {
                $groups[$name] = ['name' => $name, 'earned' => 0.0, 'max' => 0.0, 'question_count' => 0];
            }

            $earned = $answers
                ->where('question_id', $question->id)
                ->sum(fn ($answer) => (float) ($answer->score ?? 0));

            $groups[$name]['earned'] += $earned;
            $groups[$name]['max'] += self::maxPoints($question);
            $groups[$name]['question_count']++;
        }

        $aspects = [];
        $flaggedForInterview = [];
        $failedEthics = false;

        foreach ($groups as $group) {
            $score = $group['max'] > 0
                ? (int) round(($group['earned'] / $group['max']) * 100)
                : 0;

            $isEthics = self::isEthicsAspect($group['name']);

            if ($isEthics) {
                if ($score < self::ETHICS_THRESHOLD) {
                    $status = 'Di bawah batas etika (' . self::ETHICS_THRESHOLD . ')';
                    $statusType = 'danger';
                    $failedEthics = true;
                } else {
                    $status = 'Memadai';
                    $statusType = 'success';
                }
            } else {
                if ($score < self::ASPECT_THRESHOLD) {
                    $status = 'Perlu digali saat wawancara';
                    $statusType = 'warning';
                    $flaggedForInterview[] = $group['name'];
                } else {
                    $status = 'Memadai';
                    $statusType = 'success';
                }
            }

            $aspects[] = [
                'name' => $group['name'],
                'poin_str' => self::formatPoints($group['earned']) . '/' . self::formatPoints($group['max']),
                'earned_points' => $group['earned'],
                'max_points' => $group['max'],
                'question_count' => $group['question_count'],
                'score' => $score,
                'status' => $status,
                'status_type' => $statusType,
                'is_ethics' => $isEthics,
            ];
        }

        return [
            'aspects' => $aspects,
            'flagged_for_interview' => $flaggedForInterview,
            'failed_ethics' => $failedEthics,
            'summary' => empty($flaggedForInterview)
                ? null
                : 'Aspek yang perlu didalami saat wawancara: ' . implode(', ', $flaggedForInterview) . '.',
        ];
    }

    /**
     * Lampirkan hasil kalkulasi ke sekumpulan attempt (mis. baris tabel evaluasi)
     * dengan satu query soal untuk seluruh halaman agar tidak N+1.
     */
    public static function attachToAttempts(Collection $attempts): void
    {
        if ($attempts->isEmpty()) {
            return;
        }

        $questionIds = collect();

        foreach ($attempts as $attempt) {
            $questionIds = $questionIds->merge($attempt->question_ids ?? []);
            if ($attempt->relationLoaded('answers')) {
                $questionIds = $questionIds->merge($attempt->answers->pluck('question_id'));
            }
        }

        $questionIds = $questionIds->filter()->unique()->values();

        $questions = $questionIds->isEmpty()
            ? collect()
            : QuestionBank::with('subCategory')->whereIn('id', $questionIds)->get()->keyBy('id');

        foreach ($attempts as $attempt) {
            $attemptQuestionIds = collect($attempt->question_ids ?? [])->filter()->values();

            if ($attemptQuestionIds->isEmpty() && $attempt->relationLoaded('answers')) {
                $attemptQuestionIds = $attempt->answers->pluck('question_id')->filter()->unique()->values();
            }

            $attemptQuestions = $attemptQuestionIds
                ->map(fn ($id) => $questions->get($id))
                ->filter()
                ->values();

            $attempt->aspect_evaluation = self::calculateAspects($attempt, $attemptQuestions);
        }
    }

    /**
     * Nama aspek induk: prefix sub-kategori sebelum tanda pisah "–", "—", atau "-".
     */
    protected static function aspectName($question): string
    {
        $name = $question->subCategory?->name;

        if (!$name) {
            $metadata = $question->metadata ?? [];
            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true) ?: [];
            }
            $name = $metadata['sub_category'] ?? ($metadata['topic'] ?? null);
        }

        $name = trim((string) $name);

        if ($name === '') {
            return 'Umum';
        }

        $parts = preg_split('/[\x{2013}\x{2014}-]/u', $name, 2);

        return trim($parts[0] ?? $name) ?: 'Umum';
    }

    protected static function questionType($question): string
    {
        return (string) ($question->question_type ?? 'multiple_choice');
    }

    /**
     * Bobot maksimal butir soal. Soal bergradasi memakai opsi berbobot tertinggi
     * (metadata.option_weights) bila tersedia.
     */
    protected static function maxPoints($question): float
    {
        $points = (float) ($question->points ?? 0);

        $metadata = $question->metadata;
        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true) ?: [];
        }
        $weights = is_array($metadata) ? ($metadata['option_weights'] ?? null) : null;
        if (is_string($weights)) {
            $weights = json_decode($weights, true);
        }

        if (is_array($weights) && !empty($weights)) {
            $numeric = array_filter($weights, 'is_numeric');
            if (!empty($numeric)) {
                $points = max($points, (float) max($numeric));
            }
        }

        return $points > 0 ? $points : 1.0;
    }

    protected static function isEthicsAspect(string $name): bool
    {
        return str_contains(strtolower($name), 'etika');
    }

    protected static function formatPoints(float $value): string
    {
        if (floor($value) == $value) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    protected static function resolveQuestions(TestAttempt $attempt, Collection $answers): Collection
    {
        $ids = collect($attempt->question_ids ?? [])->filter()->values();

        if ($ids->isEmpty()) {
            return $answers
                ->map(fn ($answer) => $answer->question)
                ->filter()
                ->unique('id')
                ->values();
        }

        $loaded = QuestionBank::with('subCategory')->whereIn('id', $ids)->get()->keyBy('id');

        return $ids->map(fn ($id) => $loaded->get($id))->filter()->values();
    }
}
