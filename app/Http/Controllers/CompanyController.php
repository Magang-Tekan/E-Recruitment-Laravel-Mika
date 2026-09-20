<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use Cloudinary\Cloudinary;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Company::all());
    }

    /**
     * Upload logo ke penyimpanan server lokal (storage/app/public/logo).
     * Jika ingin kembali menggunakan Cloudinary di masa depan, aktifkan blok kode Cloudinary di bawah.
     */
    private function uploadLogo($file)
    {
        // Simpan langsung ke penyimpanan server lokal (public disk)
        return $file->store('logo', 'public');

        /*
        // Konfigurasi Cloudinary (dapat diaktifkan kembali jika dibutuhkan di masa mendatang):
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        if (!empty($cloudName) && !empty($apiKey) && !empty($apiSecret)) {
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key'    => $apiKey,
                        'api_secret' => $apiSecret,
                    ]
                ]);

                $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'e-recruitment/logos',
                    'transformation' => [
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                        'width' => 400,
                        'height' => 400,
                        'crop' => 'limit'
                    ]
                ]);

                return $result['secure_url'] ?? null;
            } catch (\Exception $e) {
                // Fallback ke penyimpanan lokal jika gagal koneksi
            }
        }
        return $file->store('logo', 'public');
        */
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'website' => 'nullable|url|max:255',
            'about' => 'nullable|string',
            'vision' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',    
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $path = $this->uploadLogo($request->file('logo'));

        // Handle missions array / JSON
        $missions = $this->parseArrayInput($request->input('missions'));
        $coreValues = $this->parseArrayInput($request->input('core_values'));

        $data = Company::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'tagline' => $request->tagline,
            'logo' => $path,
            'website' => $request->website,
            'about' => $request->about,
            'vision' => $request->vision,
            'missions' => $missions,
            'core_values' => $coreValues,
            'address' => $request->address, 
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        if (!$data) {
            return redirect()->route('admin.company')->with('error', 'Company gagal dibuat');
        }
        return redirect()->route('admin.company')->with('create', 'Company berhasil dibuat');
    }

    public function show(string $id)
    {
        $data = Company::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, string $id)
    {
        $data = Company::findOrFail($id);

        $request->validate([
            'role_id' => 'sometimes|required|exists:roles,id',
            'name' => 'sometimes|required|string|max:255',
            'tagline' => 'sometimes|nullable|string|max:255',
            'logo' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:4096',
            'website' => 'sometimes|nullable|url|max:255',
            'about' => 'sometimes|nullable|string',
            'vision' => 'sometimes|nullable|string',
            'address' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'province' => 'sometimes|nullable|string|max:255',
            'postal_code' => 'sometimes|nullable|string|max:20',
            'phone' => 'sometimes|nullable|string|max:50',
            'email' => 'sometimes|nullable|email|max:255',
        ]);

        if ($request->hasFile('logo')) {
            // Hapus file lama jika lokal
            if ($data->logo && !str_starts_with($data->logo, 'http') && Storage::disk('public')->exists($data->logo)) {
                Storage::disk('public')->delete($data->logo);
            }
            $path = $this->uploadLogo($request->file('logo'));
        } else {
            $path = $data->logo;
        }

        // Parse missions & core values if sent
        $missions = $request->has('missions') ? $this->parseArrayInput($request->input('missions')) : $data->missions;
        $coreValues = $request->has('core_values') ? $this->parseArrayInput($request->input('core_values')) : $data->core_values;

        $data->update([
            'role_id' => $request->input('role_id', $data->role_id),
            'name' => $request->input('name', $data->name),
            'tagline' => $request->input('tagline', $data->tagline),
            'logo' => $path,
            'website' => $request->input('website', $data->website),
            'about' => $request->input('about', $data->about),
            'vision' => $request->input('vision', $data->vision),
            'missions' => $missions,
            'core_values' => $coreValues,
            'address' => $request->input('address', $data->address),
            'city' => $request->input('city', $data->city),
            'province' => $request->input('province', $data->province),
            'postal_code' => $request->input('postal_code', $data->postal_code),
            'phone' => $request->input('phone', $data->phone),
            'email' => $request->input('email', $data->email),
        ]);

        return response()->json(['message' => 'Company berhasil diperbarui', 'data' => $data]);
    }

    /**
     * Helper to safely parse stringified JSON or array inputs
     */
    private function parseArrayInput($input)
    {
        if (is_null($input)) {
            return null;
        }

        if (is_string($input)) {
            $trimmed = trim($input);
            if ($trimmed === '') {
                return null;
            }

            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            // Fallback split newline
            return array_values(array_filter(array_map('trim', explode("\n", $trimmed))));
        }

        if (is_array($input)) {
            return array_values($input);
        }

        return null;
    }

    public function destroy(string $id)
    {
        $data = Company::findOrFail($id);
        if ($data->logo && !str_starts_with($data->logo, 'http') && Storage::disk('public')->exists($data->logo)) {
            Storage::disk('public')->delete($data->logo);
        }
        
        if (!$data->delete()){
            return redirect()->route('admin.company')->with('error', 'Company gagal dihapus');
        }
        return redirect()->route('admin.company')->with('delete', 'Company berhasil dihapus');
    }
}
