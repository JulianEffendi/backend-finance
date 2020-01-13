<?php

use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionType::truncate();
        $data = [
            ['name' => 'Pemasukan', 'category' => 1],
            ['name' => 'Pengeluaran', 'category' => 2],
            ['name' => 'Piutang', 'category' => 1],
            ['name' => 'Target', 'category' => 2],
        ];

        TransactionType::insert($data);
    }
}
