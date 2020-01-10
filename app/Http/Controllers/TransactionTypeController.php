<?php

namespace App\Http\Controllers;

use ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TransactionType;
use App\Http\Requests\TransactionTypeRequest;

class TransactionTypeController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->per_page ?? 10;
        $data = TransactionType::filter($request)->orderBy('name', 'ASC')->paginate($page);

        return ApiResponse::success($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionTypeRequest $request)
    {
        DB::beginTransaction();
        try {
            $create = TransactionType::create($request->all());
            DB::commit();
            
            return ApiResponse::store($create);
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionTypeRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = TransactionType::find($id);
            if ($data == null) { return ApiResponse::error(); }

            $data->update($request->all());
            DB::commit();
    
            return ApiResponse::update($data);
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = TransactionType::find($id);
            if ($data == null) { return ApiResponse::error(); }
            if ($data->countTransactionRelation() > 0) {
                return ApiResponse::error_relation();
            }
            
            $data->delete();

            DB::commit();
            return ApiResponse::delete();
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}


