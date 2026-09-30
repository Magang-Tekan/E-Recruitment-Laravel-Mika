<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyShowcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CompanyShowcaseController extends Controller
{
    /**
     * Store a newly created showcase in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'tag' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'img1' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'img2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'img3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'badge_title' => 'nullable|string|max:50',
            'badge_sub' => 'nullable|string|max:100',
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'img1.required' => 'Foto utama (poster) wajib diunggah.',
            'img1.image' => 'File foto utama harus berupa gambar.',
            'img1.max' => 'Ukuran foto utama maksimal 5 MB.',
        ]);

        $img1Path = $request->file('img1')->store('company-showcases', 'public');
        $img2Path = $request->hasFile('img2') ? $request->file('img2')->store('company-showcases', 'public') : null;
        $img3Path = $request->hasFile('img3') ? $request->file('img3')->store('company-showcases', 'public') : null;

        $companyId = $request->input('company_id');
        if (empty($companyId)) {
            $company = Company::where('name', 'like', '%Mitra Karya Analitika%')->first() ?? Company::first();
            $companyId = $company?->id;
        }

        $isActive = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        CompanyShowcase::create([
            'company_id' => $companyId,
            'tag' => $request->input('tag', 'Acara & Kolaborasi'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'img1' => $img1Path,
            'img2' => $img2Path,
            'img3' => $img3Path,
            'badge_title' => $request->input('badge_title', 'EVENT'),
            'badge_sub' => $request->input('badge_sub'),
            'is_active' => $isActive,
        ]);

        Cache::forget('frontend_company_showcases');

        return redirect()->route('admin.showcase')->with('create', 'Kegiatan showcase berhasil ditambahkan!');
    }

    /**
     * Update the specified showcase in storage.
     */
    public function update(Request $request, $id)
    {
        $showcase = CompanyShowcase::findOrFail($id);

        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'tag' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'img1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'img2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'img3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'badge_title' => 'nullable|string|max:50',
            'badge_sub' => 'nullable|string|max:100',
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'img1.image' => 'File foto utama harus berupa gambar.',
            'img1.max' => 'Ukuran foto utama maksimal 5 MB.',
        ]);

        // Upload img1 jika diganti
        if ($request->hasFile('img1')) {
            $this->deleteStoredImage($showcase->img1);
            $showcase->img1 = $request->file('img1')->store('company-showcases', 'public');
        }

        // Upload img2 jika diganti
        if ($request->hasFile('img2')) {
            $this->deleteStoredImage($showcase->img2);
            $showcase->img2 = $request->file('img2')->store('company-showcases', 'public');
        }

        // Upload img3 jika diganti
        if ($request->hasFile('img3')) {
            $this->deleteStoredImage($showcase->img3);
            $showcase->img3 = $request->file('img3')->store('company-showcases', 'public');
        }

        if ($request->filled('company_id')) {
            $showcase->company_id = $request->input('company_id');
        }

        $showcase->tag = $request->input('tag', $showcase->tag);
        $showcase->title = $request->input('title');
        $showcase->description = $request->input('description');
        $showcase->badge_title = $request->input('badge_title', $showcase->badge_title);
        $showcase->badge_sub = $request->input('badge_sub', $showcase->badge_sub);

        // Handle is_active: if submitted via form checkbox
        $showcase->is_active = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $showcase->save();

        Cache::forget('frontend_company_showcases');

        return redirect()->route('admin.showcase')->with('update', 'Kegiatan showcase berhasil diperbarui!');
    }

    /**
     * Remove the specified showcase from storage.
     */
    public function destroy($id)
    {
        $showcase = CompanyShowcase::findOrFail($id);

        $this->deleteStoredImage($showcase->img1);
        $this->deleteStoredImage($showcase->img2);
        $this->deleteStoredImage($showcase->img3);

        $showcase->delete();

        Cache::forget('frontend_company_showcases');

        return redirect()->route('admin.showcase')->with('delete', 'Kegiatan showcase berhasil dihapus!');
    }

    /**
     * Delete stored image safely (only delete uploaded company-showcases files, preserve seed assets)
     */
    private function deleteStoredImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        // Jangan hapus aset bawaan compro
        if (str_starts_with($path, 'asset-compro/')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
