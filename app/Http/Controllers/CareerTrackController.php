<?php

namespace App\Http\Controllers;

use App\Models\CareerTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerTrackController extends Controller
{
    /**
     * Store a newly created career track in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'num' => 'required|string|max:10',
            'badge' => 'required|string|max:100',
            'prefix' => 'required|string|max:100',
            'accent' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'back_badge' => 'nullable|string|max:100',
            'back_title' => 'required|string|max:255',
            'highlights' => 'required|string',
            'cta_text' => 'required|string|max:100',
            'experience_level' => 'nullable|string|max:100',
            'custom_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ], [
            'num.required' => 'Nomor urut (misal 01) wajib diisi.',
            'badge.required' => 'Badge kategori wajib diisi.',
            'prefix.required' => 'Prefix judul wajib diisi.',
            'accent.required' => 'Aksen judul wajib diisi.',
            'back_title.required' => 'Judul kartu belakang wajib diisi.',
            'image.required' => 'Foto kartu wajib diunggah.',
            'image.image' => 'File foto harus berupa gambar valid.',
            'image.max' => 'Ukuran foto maksimal 5 MB.',
            'highlights.required' => 'Poin keunggulan/highlight program wajib diisi (minimal 1 baris).',
            'cta_text.required' => 'Label tombol CTA wajib diisi.',
        ]);

        $imagePath = $request->file('image')->store('career-tracks', 'public');

        // Parse highlights: newline separated string to array
        $highlights = $this->parseHighlights($request->input('highlights'));

        $isDark = $request->has('is_dark') ? (bool) $request->input('is_dark') : false;
        $isActive = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        CareerTrack::create([
            'num' => $request->input('num'),
            'badge' => $request->input('badge'),
            'prefix' => $request->input('prefix'),
            'accent' => $request->input('accent'),
            'description' => $request->input('description'),
            'image' => $imagePath,
            'back_badge' => $request->input('back_badge'),
            'back_title' => $request->input('back_title'),
            'highlights' => $highlights,
            'cta_text' => $request->input('cta_text'),
            'experience_level' => $request->filled('experience_level') ? $request->input('experience_level') : null,
            'custom_url' => $request->filled('custom_url') ? $request->input('custom_url') : null,
            'is_dark' => $isDark,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $isActive,
        ]);

        CareerTrack::clearCaches();

        return redirect()->route('admin.career-track')->with('create', 'Jalur karir baru berhasil ditambahkan!');
    }

    /**
     * Update the specified career track in storage.
     */
    public function update(Request $request, $id)
    {
        $track = CareerTrack::findOrFail($id);

        $request->validate([
            'num' => 'required|string|max:10',
            'badge' => 'required|string|max:100',
            'prefix' => 'required|string|max:100',
            'accent' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'back_badge' => 'nullable|string|max:100',
            'back_title' => 'required|string|max:255',
            'highlights' => 'required|string',
            'cta_text' => 'required|string|max:100',
            'experience_level' => 'nullable|string|max:100',
            'custom_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ], [
            'num.required' => 'Nomor urut wajib diisi.',
            'badge.required' => 'Badge kategori wajib diisi.',
            'prefix.required' => 'Prefix judul wajib diisi.',
            'accent.required' => 'Aksen judul wajib diisi.',
            'back_title.required' => 'Judul kartu belakang wajib diisi.',
            'image.image' => 'File foto harus berupa gambar valid.',
            'image.max' => 'Ukuran foto maksimal 5 MB.',
            'highlights.required' => 'Poin keunggulan/highlight program wajib diisi.',
            'cta_text.required' => 'Label tombol CTA wajib diisi.',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($track->image);
            $track->image = $request->file('image')->store('career-tracks', 'public');
        }

        $track->num = $request->input('num');
        $track->badge = $request->input('badge');
        $track->prefix = $request->input('prefix');
        $track->accent = $request->input('accent');
        $track->description = $request->input('description');
        $track->back_badge = $request->input('back_badge');
        $track->back_title = $request->input('back_title');
        $track->highlights = $this->parseHighlights($request->input('highlights'));
        $track->cta_text = $request->input('cta_text');
        $track->experience_level = $request->filled('experience_level') ? $request->input('experience_level') : null;
        $track->custom_url = $request->filled('custom_url') ? $request->input('custom_url') : null;
        $track->is_dark = $request->has('is_dark') ? (bool) $request->input('is_dark') : false;
        $track->sort_order = (int) $request->input('sort_order', 0);
        $track->is_active = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $track->save();

        CareerTrack::clearCaches();

        return redirect()->route('admin.career-track')->with('update', 'Jalur karir berhasil diperbarui!');
    }

    /**
     * Remove the specified career track from storage.
     */
    public function destroy($id)
    {
        $track = CareerTrack::findOrFail($id);

        $this->deleteStoredImage($track->image);
        $track->delete();

        CareerTrack::clearCaches();

        return redirect()->route('admin.career-track')->with('delete', 'Jalur karir berhasil dihapus!');
    }

    /**
     * Parse multiline text to clean string array
     */
    private function parseHighlights(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($text));
        $cleaned = [];
        foreach ($lines as $line) {
            $line = trim($line);
            $line = ltrim($line, '-•*0123456789. ');
            if (!empty($line)) {
                $cleaned[] = $line;
            }
        }
        return !empty($cleaned) ? $cleaned : [$text];
    }

    /**
     * Delete stored image safely
     */
    private function deleteStoredImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
