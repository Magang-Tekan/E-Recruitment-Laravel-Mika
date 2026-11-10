<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestSubCategory;

class TestSubCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:test_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = TestSubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()
            ->with('create', 'Sub-kategori / topik berhasil ditambahkan');
    }

    public function update(Request $request, string $id)
    {
        $data = TestSubCategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:test_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()
            ->with('update', 'Sub-kategori / topik berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $data = TestSubCategory::findOrFail($id);
        $data->delete();

        return redirect()->back()
            ->with('delete', 'Sub-kategori / topik berhasil dihapus');
    }
}

