<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
            [
                'id' => 5,
                'name' => 'Nike',
                'description' => null,
                'logo_url' => 'brands/1qNE4vyq0IwJ3pukOKpbuxByyupp2c7vbW1uT6Kq.png',
                'status' => 1,
                'created_at' => '2025-11-12 09:51:35',
                'updated_at' => '2025-11-16 10:51:34',
            ],
            [
                'id' => 7,
                'name' => 'Adidas',
                'description' => null,
                'logo_url' => 'brands/ZtaWNXYVJT86CdtgTOmcdm9CBv17sk7VU4ph7eG5.jpg',
                'status' => 0,
                'created_at' => '2026-01-18 20:29:48',
                'updated_at' => '2026-01-18 20:29:48',
            ],
        ]);
    }
}
