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
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $appends = ['category_formatted'];

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

    public function scopeFilter($query, $request)
    {
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return $query;
    }
}
