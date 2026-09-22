<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Test;
use Illuminate\Database\Eloquent\Collection;

class TestSequenceService
{
    /**
     * Ambil semua test untuk suatu lowongan, diurutkan berdasarkan
     * kapan test tersebut di-assign ke lowongan (job_test.created_at asc).
     * Test yang paling awal di-assign = urutan pertama (Test 1).
     */
    public static function getOrderedTests(Job $job): Collection
    {
        return $job->tests()
            ->orderByPivot('created_at', 'asc')
            ->with(['category'])
            ->get();
    }

    /**
     * Cek apakah seorang pelamar diizinkan mengakses test tertentu.
     *
     * Aturan sequential:
     * - Test pertama (index 0) selalu bisa diakses (jika status lamaran sudah allowed).
     * - Test ke-N hanya bisa diakses jika attempt test ke-(N-1) sudah
     *   berstatus 'completed', 'passed', atau 'failed'.
     *
     * @return bool
     */
    public static function canAccessTest(JobApplication $application, Test $test): bool
    {
        $orderedTests = self::getOrderedTests($application->job);

        if ($orderedTests->isEmpty()) {
            return false;
        }

        // Cari posisi test ini dalam urutan
        $index = $orderedTests->search(fn($t) => $t->id === $test->id);

        // Test tidak ditemukan dalam lowongan ini
        if ($index === false) {
            return false;
        }

        // Test pertama selalu boleh diakses (gerbang status lamaran di-handle di controller)
        if ($index === 0) {
            return true;
        }

        // Test ke-N: cek apakah test ke-(N-1) sudah selesai
        $previousTest = $orderedTests[$index - 1];

        return $application->testAttempts()
            ->where('test_id', $previousTest->id)
            ->whereIn('status', ['completed', 'passed', 'failed'])
            ->exists();
    }

    /**
     * Dapatkan test berikutnya yang belum dikerjakan setelah test yang diberikan.
     * Mengembalikan null jika tidak ada test berikutnya.
     */
    public static function getNextTest(JobApplication $application, Test $currentTest): ?Test
    {
        $orderedTests = self::getOrderedTests($application->job);
        $index = $orderedTests->search(fn($t) => $t->id === $currentTest->id);

        if ($index === false || $index >= $orderedTests->count() - 1) {
            return null;
        }

        $nextTest = $orderedTests[$index + 1];

        // Pastikan test berikutnya belum pernah diselesaikan
        $alreadyCompleted = $application->testAttempts()
            ->where('test_id', $nextTest->id)
            ->whereIn('status', ['completed', 'passed', 'failed'])
            ->exists();

        return $alreadyCompleted ? null : $nextTest;
    }

    /**
     * Bangun data progress test lengkap untuk ditampilkan sebagai stepper di view.
     * Mengembalikan array dengan struktur:
     * [
     *   [
     *     'test'      => Test,
     *     'index'     => int,       // urutan (0-based)
     *     'status'    => string,    // 'completed'|'in_progress'|'unlocked'|'locked'
     *     'attempt'   => TestAttempt|null,
     *     'canAccess' => bool,
     *   ],
     *   ...
     * ]
     */
    public static function buildTestProgress(JobApplication $application): array
    {
        $orderedTests = self::getOrderedTests($application->job);
        $attempts = $application->testAttempts ?? collect();
        $progress = [];

        foreach ($orderedTests as $index => $test) {
            $attempt = $attempts
                ->where('test_id', $test->id)
                ->sortByDesc('id')
                ->first();

            $canAccess = self::canAccessTest($application, $test);

            if ($attempt && in_array($attempt->status, ['completed', 'passed', 'failed'])) {
                $stepStatus = 'completed';
            } elseif ($attempt && $attempt->status === 'in_progress') {
                $stepStatus = 'in_progress';
            } elseif ($canAccess) {
                $stepStatus = 'unlocked'; // Bisa diakses, belum mulai
            } else {
                $stepStatus = 'locked'; // Terkunci
            }

            $progress[] = [
                'test'      => $test,
                'index'     => $index,
                'status'    => $stepStatus,
                'attempt'   => $attempt,
                'canAccess' => $canAccess,
            ];
        }

        return $progress;
    }
}
