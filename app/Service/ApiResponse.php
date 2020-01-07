<?php

namespace App\Service;

use \Illuminate\Support\Facades\Response;

class ApiResponse {

    public static function success($data, $message = 'Sukses', $code = 200)
    {
        return Response::json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message = 'Tidak Ditemukan', $code = 404)
    {
        return Response::json([
            'code' => $code,
            'message' => $message
        ], $code);
    }

    public static function error_validation($validator, $message = 'Validasi Gagal!')
    {
        return Response::json([
            'code' => 400,
            'message' => $message,
            'errors' => $validator->errors()
        ], 400);
    }

    public static function error_relation($message = 'Modul ini memiliki relasi dengan modul lain!')
    {
        return Response::json([
            'code' => 400,
            'message' => $message,
            'errors' => null
        ], 400);
    }

    public static function store($data, $message = 'Berhasil Menambah Data')
    {
        return static::success($data, $message, 201);
    }

    public static function update($data, $message = 'Berhasil Mengubah Data')
    {
        return static::success($data, $message);
    }

    public static function delete($message = 'Berhasil Menghapus Data')
    {
        return static::success(null, $message);
    }

    public static function delete_alt()
    {
        return Response::json(null, 204);
    }

}
