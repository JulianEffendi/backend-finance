<?php

namespace App\Http\Controllers;

use DB;
use ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Transaction;
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
}
