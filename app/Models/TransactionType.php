<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class TransactionType extends Model
{
    protected $table = 'finance_transaction_Types';
    protected $fillable = [
        'name',
        'category',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public $timestamps = true;

    public function transactions() {
        return $this->hasMany(Transaction::class, 'type_id');
    }

    public function countTransactionRelation()
    {
        return $this->transactions()->count();
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
