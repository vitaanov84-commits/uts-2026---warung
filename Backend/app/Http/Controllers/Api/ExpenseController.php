<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return response()->json(Expense::latest('expense_date')->latest()->get());
    }

    public function store(Request $request)
    {
        $expense = Expense::create($this->validated($request));

        return response()->json(['message' => 'Pengeluaran berhasil ditambahkan', 'data' => $expense], 201);
    }

    public function update(Request $request, Expense $expense)
    {
        $expense->update($this->validated($request));

        return response()->json(['message' => 'Pengeluaran berhasil diubah', 'data' => $expense]);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json(['message' => 'Pengeluaran berhasil dihapus']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'note' => 'nullable|string',
            'expense_date' => 'required|date',
        ]);
    }
}
