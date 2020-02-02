<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use App\Service\ApiResponse;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\ValidationRequest;
use App\Models\TransactionType;

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
            $request->merge([
                'no_transaction' => $this->getLastNoTransaction(),
                'user_id'        => auth()->user()->id
            ]);

            if ($request->image) {
              $img = $request->file('image');
              $dir = "1cI87KE6A4Zciq2OANSbEJLpH9-9YNXQ3/"; 
              $imgName = uniqid() . '_' . $img->getClientOriginalName();
              Storage::disk('google')->put($dir . $imgName, fopen($img, 'r+'));
              $url = Storage::disk('google')->url($dir . $imgName);
              $request->merge(['file' => $url]);
            }

            $create = Transaction::create($request->all());
            $this->updateAmountType($request, $create);
            
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

            // Cari Cara ngecek dia ada atau tidak lalu hapus
            if ($request->image) {
              $img = $request->file('image');
              $imgName = uniqid() . '_' . $img->getClientOriginalName();
              $dir = "1cI87KE6A4Zciq2OANSbEJLpH9-9YNXQ3/"; 
              Storage::disk('google')->put($dir . $imgName, fopen($img, 'r+'));
              $url = Storage::disk('google')->url($dir . $imgName);
              
              $request->merge(['file' => $url]);
            }
            
            $data = $this->updated($data, $request, $id);
            DB::commit();

            return ApiResponse::update($data);

        } catch (\Throwable $e) {
            dd($e->getMessage());
            DB::rollback();
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function done(ValidationRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Transaction::find($id);
            if ($data == null) { return ApiResponse::error(); }

            if ($request->image) {
              $img = $request->file('image');
              $imgName = uniqid() . '_' . $img->getClientOriginalName();
              $dir = "1cI87KE6A4Zciq2OANSbEJLpH9-9YNXQ3/"; 
              Storage::disk('google')->put($dir . $imgName, fopen($img, 'r+'));
              $url = Storage::disk('google')->url($dir . $imgName);
              
              $request->merge(['file' => $url]);
            }
            
            $data = $this->updated($data, $request, $id, "merged");

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

    public function sum_amount() {
      $data = collect();
      $transType = TransactionType::get();
      foreach ($transType as $value) {
        $data->put($value->name, (float)str_replace('-', '', $value->sum_transaction));
      }

      $amount = Transaction::orderBy('updated_at', 'DESC')->where('is_active', true)->sum('amount');
      $data = $data->put('Balance', $amount);
      return ApiResponse::success($data);
    }

    public function lastOrderNumber()
    {
        return ApiResponse::success([
            'no_transaction' => $this->getLastNoTransaction()
        ]);
    }

    private function updated($data, $request, $id, $merged = null) {
        if ($merged !== null) {
            $request->merge(['is_active' => true]);
        }
        
        $data->update($request->all());
        $this->updateAmountType($request, $data);

        return $data;
        
    }

    private function updateAmountType($request, $data) {
        if (isset($request->amount)) {
            if ($data->type->category === "2") {
                $data->update(['amount' => -$data->amount]);
            }
        }
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
