<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class TransactionType extends Model
{
    protected $table = 'finance_transaction_types';
    protected $fillable = [
        'name',
        'category',
        'is_pending'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $appends = ['category_formatted', 'sum_transaction'];

    public $timestamps = true;

    public function transactions() {
        return $this->hasMany(Transaction::class, 'type_id');
    }

    public function countTransactionRelation()
    {
        return $this->transactions()->count();
    }

    public function getCategoryFormattedAttribute()
    {
        $category = $this->category;
        if ($category === "1") {
            $category = 'Plus';
        } else {
            $category = 'Minus';
        }

        return $category;
    }

    public function getSumTransactionAttribute()
    {
        $category = $this->transactions->where('is_active', true)->sum('amount');

        return $category;
    }

    public function scopeFilter($query, $request)
    {
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_pending')) {
            $query->where('is_pending', $request->is_pending);
        }

        return $query;
    }
}
