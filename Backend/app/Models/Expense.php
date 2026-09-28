<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['amount', 'category', 'note', 'expense_date'];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date:Y-m-d',
    ];
}
