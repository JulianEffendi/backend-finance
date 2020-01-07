<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionType;

class Transaction extends Model
{
    protected $table = 'finance_transactions';
    protected $fillable = [
        'no_transaction',
        'user_id',
        'type_id',
        'amount',
        'file',
        'date',
        'status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function type() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter($query, $request)
    {
        if ($request->has('no_transaction')) {
            $query->where('no_transaction', 'like', '%' . $request->no_transaction . '%');
        }

        if ($request->has('user')) {
            $query->whereHas('user', function($q) use($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->has('type')) {
            $query->whereHas('type', function($q) use($request) {
                $q->where('name', 'like', '%' . $request->type . '%');
            });
        }

        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        return $query;
    }

    // Nnti ini pake Helper Aja buat hitung Sum nya
    // public function scopeSumActiveAmount() {}
}
