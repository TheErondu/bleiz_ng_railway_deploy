<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CapitalPoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('capital_pool')->insert([
    'total_amount' => 50000000,
    'available_amount' => 50000000,
    'created_at' => now(),
    'updated_at' => now(),
]);
    }
}
