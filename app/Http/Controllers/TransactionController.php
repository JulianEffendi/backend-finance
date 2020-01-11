<?php

namespace App\Http\Controllers;

use DB;
use App\Service\ApiResponse;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
     /**
     * Fetch Data, Pending Menu => GET Pending
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->per_page ?? 10;
        $data = Transaction::filter($request)->orderBy('updated_at', 'DESC')->with('type', 'user');
        if ($request->has('pending')) {
            $data = $data->where('is_active', 1);
        } else {
            $data = $data->where('is_active', 0);
        }

        $data = $data->paginate($page);

        return ApiResponse::success($data);
    }

    /**
     * Store Data, Pending Menu => GET Pending
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge(['no_transaction' => $this->getLastNoTransaction()]);
            $create = Transaction::create($request->all());
            if (isset($request->mount)) {
                if ($create->type->category === 2) {
                    $create->update(['amount' => -$create->amount]);
                }
            }
            
            DB::commit();

            return ApiResponse::store($create);
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }

    }

    /**
     * Update Data, Pending Menu => GET Pending
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Transaction::find($id);
            if ($data == null) { return ApiResponse::error(); }

            $data->update($request->all());
            if (isset($request->mount)) {
                if ($data->type->category === 2) {
                    $data->update(['amount' => -$data->amount]);
                }
            }

            DB::commit();

            return ApiResponse::update($data);

        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }

    }

    /**
     * Delete And Cancel Transaction
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Transaction::find($id);
            if ($data == null) { return ApiResponse::error(); }
    
            $data->delete();
            DB::commit();
            
            return ApiResponse::delete();
        } catch (\Throwable $e) {
            DB::rollback();
        return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function done($id)
    {
        DB::beginTransaction();
        try {
            $data = Transaction::find($id);
            if ($data == null) { return ApiResponse::error(); }
            
            $request->merge(['is_active' => true]);
            $data->update($request->all());
            if (isset($request->mount)) {
                if ($data->type->category === 2) {
                    $data->update(['amount' => -$data->amount]);
                }
            }

            DB::commit();

            return ApiResponse::update($data);
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function sum_amount() {
        $data = Transaction::orderBy('updated_at', 'DESC')->where('is_active', true)->sum('amount');
        return ApiResponse::success($data);
    }

    public function lastOrderNumber()
    {
        return ApiResponse::success([
            'no_transaction' => $this->getLastNoTransaction()
        ]);
    }

    public function getLastNoTransaction()
    {
        $last_data = Transaction::orderBy('id', 'desc')->first();
        if ($last_data !== null) {
            $last_no_transaction = $this->splitNoTransaction($last_data->no_transaction);
        } else {
            $last_no_transaction = '001';
        }

        return date('Ym') . str_pad($last_no_transaction + 1, 3, '0', STR_PAD_LEFT);
    }

    private function splitNoTransaction($last_no_transaction)
    {
        return (int) substr($last_no_transaction, -3);
    }
}
