<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Setting;
use App\Models\Test;
use App\Models\QuestionBank;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Perbarui default global batas pengerjaan tes online (hari kerja Senin-Jumat).
     */
    public function updateDefaultDeadline(Request $request)
    {
        $validated = $request->validate([
            'default_test_deadline_days' => 'required|integer|min:1|max:30',
        ], [
            'default_test_deadline_days.required' => 'Jumlah hari kerja default wajib diisi.',
            'default_test_deadline_days.integer'  => 'Jumlah hari kerja harus berupa angka bulat.',
            'default_test_deadline_days.min'      => 'Minimal batas waktu tes adalah 1 hari kerja.',
            'default_test_deadline_days.max'      => 'Maksimal batas waktu tes default adalah 30 hari kerja.',
        ]);

        $days = (int) $validated['default_test_deadline_days'];
        Setting::set(
            'default_test_deadline_days',
            (string) $days,
            'Batas waktu default pengerjaan tes online pelamar (hari kerja Senin-Jumat)'
        );

        return redirect()->back()->with(
            'update',
            "Default batas waktu tes global berhasil diatur menjadi {$days} hari kerja (berlaku untuk lowongan tanpa pengaturan khusus)."
        );
    }
    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'nullable|exists:jobs,id',
            'job_ids' => 'nullable|array',
            'job_ids.*' => 'exists:jobs,id',
            'category_id' => 'required|exists:test_categories,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
            'total_questions' => 'required|integer|min:1',
            'is_random' => 'nullable|boolean',
            'selected_questions' => 'nullable|array',
            'selected_questions.*' => 'exists:question_banks,id',
        ]);

        try {
            DB::beginTransaction();

            $selectedQuestions = $request->input('selected_questions', []);
            $totalQuestions = $request->filled('total_questions') && (int) $request->total_questions > 0
                ? (int) $request->total_questions
                : count($selectedQuestions);

            // Handle job_ids (multi-select) or legacy single job_id
            $jobIds = $request->input('job_ids', []);
            if (empty($jobIds) && $request->filled('job_id')) {
                $jobIds = [$request->job_id];
            }
            $jobIds = array_values(array_filter($jobIds));
            $primaryJobId = !empty($jobIds) ? $jobIds[0] : null;

            $test = Test::create([
                'test_type' => 'recruitment',
                'job_id' => $primaryJobId,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'duration_minutes' => $request->duration_minutes,
                'passing_score' => $request->passing_score,
                'total_questions' => $totalQuestions,
                'is_random' => $request->boolean('is_random'),
            ]);

            // Sync many-to-many jobs
            $test->jobs()->sync($jobIds);
            $this->assignDeadlineForReviewedApplications($jobIds);

            if (!empty($selectedQuestions)) {
                $orderedQuestions = QuestionBank::whereIn('id', $selectedQuestions)->get()->sortBy(function ($q) {
                    return $q->metadata['number'] ?? $q->id;
                })->values();

                $syncData = [];
                foreach ($orderedQuestions as $index => $q) {
                    $syncData[$q->id] = ['order_number' => $index + 1];
                }
                $test->questions()->sync($syncData);
            }

            DB::commit();

            return redirect()->route('admin.test')
                ->with('create', 'Paket Ujian berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.test')
                ->with('error', 'Gagal membuat paket ujian: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $test = Test::findOrFail($id);

        $request->validate([
            'job_id' => 'nullable|exists:jobs,id',
            'job_ids' => 'nullable|array',
            'job_ids.*' => 'exists:jobs,id',
            'category_id' => 'required|exists:test_categories,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
            'total_questions' => 'required|integer|min:1',
            'is_random' => 'nullable|boolean',
            'selected_questions' => 'nullable|array',
            'selected_questions.*' => 'exists:question_banks,id',
        ]);

        try {
            DB::beginTransaction();

            $selectedQuestions = $request->input('selected_questions', []);
            $totalQuestions = $request->filled('total_questions') && (int) $request->total_questions > 0
                ? (int) $request->total_questions
                : count($selectedQuestions);

            // Handle job_ids (multi-select) or legacy single job_id
            $jobIds = $request->input('job_ids', []);
            if ($request->has('job_ids')) {
                $jobIds = array_values(array_filter($jobIds));
            } elseif ($request->filled('job_id')) {
                $jobIds = [$request->job_id];
            } else {
                $jobIds = [];
            }
            $primaryJobId = !empty($jobIds) ? $jobIds[0] : null;

            $test->update([
                'job_id' => $primaryJobId,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'duration_minutes' => $request->duration_minutes,
                'passing_score' => $request->passing_score,
                'total_questions' => $totalQuestions,
                'is_random' => $request->boolean('is_random'),
            ]);

            // Sync many-to-many jobs
            $test->jobs()->sync($jobIds);
            $this->assignDeadlineForReviewedApplications($jobIds);

            if (!empty($selectedQuestions)) {
                $orderedQuestions = QuestionBank::whereIn('id', $selectedQuestions)->get()->sortBy(function ($q) {
                    return $q->metadata['number'] ?? $q->id;
                })->values();

                $syncData = [];
                foreach ($orderedQuestions as $index => $q) {
                    $syncData[$q->id] = ['order_number' => $index + 1];
                }
                $test->questions()->sync($syncData);
            } else {
                $test->questions()->detach();
            }

            DB::commit();

            return redirect()->route('admin.test')
                ->with('update', 'Paket Ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.test')
                ->with('error', 'Gagal memperbarui paket ujian: ' . $e->getMessage());
        }
    }

    /**
     * Isi batas tes bagi lamaran berstatus Reviewed yang sebelumnya belum punya paket tes.
     */
    private function assignDeadlineForReviewedApplications(array $jobIds): void
    {
        if (empty($jobIds)) {
            return;
        }

        $groupedJobIds = Job::whereIn('id', $jobIds)
            ->get()
            ->groupBy(fn (Job $job) => $job->effectiveTestDeadlineDays());

        foreach ($groupedJobIds as $days => $jobs) {
            $deadline = JobApplication::calculateTestDeadline(workingDays: (int) $days);

            JobApplication::whereIn('job_id', $jobs->pluck('id')->all())
                ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) = 'reviewed'")
                ->whereNull('test_deadline_at')
                ->update(['test_deadline_at' => $deadline]);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            $test = Test::findOrFail($id);

            // Proteksi: Cegah hapus jika sudah ada peserta yang mengerjakan/memiliki riwayat evaluasi kecuali dikonfirmasi paksa
            $attemptCount = $test->attempts()->count();
            if ($attemptCount > 0 && !$request->boolean('force_delete')) {
                return redirect()->route('admin.test')
                    ->with('error', "Paket Ujian tidak dapat dihapus karena memiliki {$attemptCount} riwayat pengerjaan/evaluasi peserta. Centang konfirmasi hapus paksa jika ingin tetap menghapus.");
            }

            DB::beginTransaction();

            $test->jobs()->detach();
            $test->questions()->detach();
            $test->delete();

            DB::commit();

            $successMsg = $attemptCount > 0
                ? "Paket Ujian beserta {$attemptCount} riwayat pengerjaan peserta berhasil dihapus."
                : "Paket Ujian berhasil dihapus.";

            return redirect()->route('admin.test')
                ->with('delete', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.test')
                ->with('error', 'Paket Ujian gagal dihapus: ' . $e->getMessage());
        }
    }
}
