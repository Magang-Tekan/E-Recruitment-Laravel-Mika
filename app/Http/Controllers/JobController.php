<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        return response()->json(Job::with(['company', 'department'])->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'employment_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'deadline' => 'required|date',
            'status' => 'required|in:Open,Closed,Draft',
        ]);

        $data = Job::create([
            'company_id' => $request->company_id,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id ?: null,
            'title' => $request->title,
            'description' => !empty($request->description) ? $request->description : '-',
            'employment_type' => $request->employment_type,
            'location' => !empty($request->location) ? $request->location : 'Indonesia',
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'quota' => (int) $request->quota,
            'deadline' => $request->deadline,
            'status' => $request->status,
        ]);

        if (!$data) {
            return redirect()->route('admin.job')->with('error', 'Lowongan pekerjaan gagal dibuat');
        }

        return redirect()->route('admin.job')->with('create', 'Lowongan pekerjaan berhasil dibuat');
    }

    public function show(string $id)
    {
        $data = Job::with(['company', 'department'])->findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, string $id)
    {
        $data = Job::findOrFail($id);

        $request->validate([
            'company_id' => 'sometimes|required|exists:companies,id',
            'department_id' => 'sometimes|required|exists:departments,id',
            'position_id' => 'sometimes|nullable|exists:positions,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'employment_type' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'salary_min' => 'sometimes|nullable|numeric|min:0',
            'salary_max' => 'sometimes|nullable|numeric|min:0',
            'quota' => 'sometimes|required|integer|min:1',
            'deadline' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:Open,Closed,Draft',
        ]);

        $newDesc = $request->input('description', $data->description);
        $newLoc = $request->input('location', $data->location);

        $data->update([
            'company_id' => $request->input('company_id', $data->company_id),
            'department_id' => $request->input('department_id', $data->department_id),
            'position_id' => $request->has('position_id') ? ($request->position_id ?: null) : $data->position_id,
            'title' => $request->input('title', $data->title),
            'description' => !empty($newDesc) ? $newDesc : ($data->description ?? '-'),
            'employment_type' => $request->input('employment_type', $data->employment_type),
            'location' => !empty($newLoc) ? $newLoc : ($data->location ?? 'Indonesia'),
            'salary_min' => $request->input('salary_min', $data->salary_min),
            'salary_max' => $request->input('salary_max', $data->salary_max),
            'quota' => $request->filled('quota') ? (int) $request->quota : ($data->quota ?? 1),
            'deadline' => $request->input('deadline', $data->deadline),
            'status' => $request->input('status', $data->status),
        ]);

        return redirect()->route('admin.job')->with('update', 'Lowongan pekerjaan berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $data = Job::findOrFail($id);

        // Proteksi: Cegah hapus jika lowongan sudah memiliki pelamar terdaftar
        $applicationCount = $data->jobApplications()->count();
        if ($applicationCount > 0) {
            return redirect()->route('admin.job')
                ->with('error', "Lowongan pekerjaan tidak dapat dihapus karena sudah memiliki {$applicationCount} pelamar terdaftar beserta riwayat evaluasinya.");
        }

        if (!$data->delete()) {
            return redirect()->route('admin.job')->with('error', 'Lowongan pekerjaan gagal dihapus');
        }

        return redirect()->route('admin.job')->with('delete', 'Lowongan pekerjaan berhasil dihapus');
    }
}
