<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sizes')->insert([
            [
                'id' => 1,
                'name' => 'S',
                'length' => '66',
                'width' => '52',
                'sleeve' => '17',
                'order' => 1,
                'status' => 1,
                'created_at' => '2025-11-11 10:00:08',
                'updated_at' => '2025-11-24 19:16:09',
            ],
            [
                'id' => 2,
                'name' => 'M',
                'length' => '68',
                'width' => '54',
                'sleeve' => '18',
                'order' => 2,
                'status' => 1,
                'created_at' => '2025-11-11 10:01:24',
                'updated_at' => '2025-11-24 19:16:25',
            ],
            [
                'id' => 3,
                'name' => 'L',
                'length' => '70',
                'width' => '56',
                'sleeve' => '19',
                'order' => 3,
                'status' => 1,
                'created_at' => '2025-11-11 10:31:54',
                'updated_at' => '2026-01-18 20:43:13',
            ],
        ]);
    }
}
