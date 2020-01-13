<?php

use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transaction::truncate();
        $data = [
            ['no_transaction' => '000001', 'name' => 'Dapat uang diajalan', 'amount' => 30000, 'date' => '2020-01-01 00:00:00', 'is_active' => 1, 'user_id' => 1, 'type_id' => 1],
            ['no_transaction' => '000002', 'name' => 'Beli Mic', 'amount' => 10000, 'date' => '2020-01-01 00:00:00', 'is_active' => 1, 'user_id' => 1, 'type_id' => 2],
            ['no_transaction' => '000003', 'name' => 'Wedding', 'amount' => 40000, 'date' => null, 'is_active' => 0, 'user_id' => 1, 'type_id' => 3],
            ['no_transaction' => '000004', 'name' => 'Beli Salon untuk setel ceramah', 'amount' => null, 'date' => null, 'is_active' => 0, 'user_id' => 1, 'type_id' => 4],
        ];

        Transaction::insert($data);
    }
}
