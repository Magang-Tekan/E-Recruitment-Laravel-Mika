<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\QuestionBank;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
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
            $totalQuestions = count($selectedQuestions) > 0 
                ? count($selectedQuestions) 
                : (int) $request->total_questions;

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
            $totalQuestions = count($selectedQuestions) > 0 
                ? count($selectedQuestions) 
                : (int) $request->total_questions;

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
