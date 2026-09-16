<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    public function index()
    {
        return response()->json(
            Pemasukan::latest()->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total' => 'required|numeric',
            'source' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $pemasukan = Pemasukan::create($validated);

        return response()->json([
            'message' => 'Pemasukan berhasil ditambahkan',
            'data' => $pemasukan
        ], 201);
    }

    public function show($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);

        return response()->json($pemasukan);
    }

    public function update(Request $request, $id)
    {
        $pemasukan = Pemasukan::findOrFail($id);

        $validated = $request->validate([
            'total' => 'sometimes|numeric',
            'source' => 'sometimes|string',
            'note' => 'nullable|string',
        ]);

        $pemasukan->update($validated);

        return response()->json([
            'message' => 'Pemasukan berhasil diubah',
            'data' => $pemasukan
        ]);
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);

        $pemasukan->delete();

        return response()->json([
            'message' => 'Pemasukan berhasil dihapus'
        ], 200);
    }
}