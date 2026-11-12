<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Imports\QuestionBankImport;
use App\Exports\QuestionBankTemplateExport;
use App\Exports\QuestionBankFatTemplateExport;
use App\Exports\QuestionBankTkkTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class QuestionBankController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'required|exists:test_categories,id',
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay,disc',
            'points' => 'required|integer|min:1',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,csv|max:10240',
        ];

        if ($request->question_type === 'multiple_choice') {
            $rules['options'] = 'required|array|min:4|max:4';
            $rules['options.*'] = 'required|string';
            $rules['correct_option'] = 'required|integer|in:0,1,2,3';
        }

        $request->validate($rules, [
            'image.mimes' => 'Format file pendukung harus berupa gambar (JPG, PNG, WEBP), PDF, Word (.doc/.docx), atau Excel (.xls/.xlsx/.csv).',
            'image.max' => 'Ukuran file pendukung maksimal 10 MB.',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('question_attachments', 'public');
            }

            $question = QuestionBank::create([
                'category_id' => $request->category_id,
                'question' => $request->question,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'image_path' => $imagePath,
            ]);

            if ($request->question_type === 'multiple_choice') {
                foreach ($request->options as $index => $optionText) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => ($index == $request->correct_option),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.question_bank')
                ->with('create', 'Soal berhasil ditambahkan ke bank soal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.question_bank')
                ->with('error', 'Gagal menambahkan soal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $question = QuestionBank::findOrFail($id);

        $rules = [
            'category_id' => 'required|exists:test_categories,id',
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay',
            'points' => 'required|integer|min:1',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,csv|max:10240',
        ];

        if ($request->question_type === 'multiple_choice') {
            $rules['options'] = 'required|array|min:4|max:4';
            $rules['options.*'] = 'required|string';
            $rules['correct_option'] = 'required|integer|in:0,1,2,3';
        }

        $request->validate($rules, [
            'image.mimes' => 'Format file pendukung harus berupa gambar (JPG, PNG, WEBP), PDF, Word (.doc/.docx), atau Excel (.xls/.xlsx/.csv).',
            'image.max' => 'Ukuran file pendukung maksimal 10 MB.',
        ]);

        try {
            DB::beginTransaction();

            $imagePath = $question->image_path;
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('question_attachments', 'public');
            } elseif ($request->boolean('remove_image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = null;
            }

            $question->update([
                'category_id' => $request->category_id,
                'question' => $request->question,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'image_path' => $imagePath,
            ]);

            // Clear old options and re-create if multiple choice
            $question->options()->delete();

            if ($request->question_type === 'multiple_choice') {
                foreach ($request->options as $index => $optionText) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => ($index == $request->correct_option),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.question_bank')
                ->with('update', 'Soal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.question_bank')
                ->with('error', 'Gagal memperbarui soal: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $question = QuestionBank::findOrFail($id);

        if ($question->image_path && Storage::disk('public')->exists($question->image_path)) {
            Storage::disk('public')->delete($question->image_path);
        }

        if (!$question->delete()) {
            return redirect()->route('admin.question_bank')
                ->with('error', 'Soal gagal dihapus.');
        }

        return redirect()->route('admin.question_bank')
            ->with('delete', 'Soal berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $import = new QuestionBankImport();
            Excel::import($import, $request->file('excel_file'));

            return redirect()->route('admin.question_bank')
                ->with('create', "Berhasil mengimpor {$import->importedCount} soal dari file Excel.");
        } catch (\Exception $e) {
            return redirect()->route('admin.question_bank')
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new QuestionBankTemplateExport(), 'template_import_bank_soal.xlsx');
    }

    public function downloadFatTemplate()
    {
        return Excel::download(new QuestionBankFatTemplateExport(), 'template_import_bank_soal_fat.xlsx');
    }

    public function downloadTkkTemplate()
    {
        return Excel::download(new QuestionBankTkkTemplateExport(), 'template_import_soal_tkk.xlsx');
    }
}
