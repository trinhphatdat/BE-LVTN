<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('colors')->insert([
            ['id' => 1, 'name' => 'Trắng', 'hex_code' => '#FFFFFF', 'status' => 1, 'created_at' => '2025-11-11 14:48:58', 'updated_at' => '2025-11-17 10:54:59'],
            ['id' => 2, 'name' => 'Xanh navy', 'hex_code' => '#000080', 'status' => 1, 'created_at' => '2025-11-11 14:48:58', 'updated_at' => '2025-11-17 10:55:58'],
            ['id' => 5, 'name' => 'Đen', 'hex_code' => '#000000', 'status' => 1, 'created_at' => '2025-11-20 19:41:38', 'updated_at' => '2025-11-20 19:41:38'],
            ['id' => 6, 'name' => 'Hồng', 'hex_code' => '#D4C3CD', 'status' => 1, 'created_at' => '2025-11-22 19:54:13', 'updated_at' => '2025-11-22 19:55:04'],
            ['id' => 7, 'name' => 'Nâu', 'hex_code' => '#43201A', 'status' => 1, 'created_at' => '2025-11-22 19:54:56', 'updated_at' => '2025-11-22 19:54:56'],
            ['id' => 8, 'name' => 'Cam Brandied', 'hex_code' => '#D96F40', 'status' => 1, 'created_at' => '2025-11-22 19:55:39', 'updated_at' => '2025-11-22 19:55:39'],
            ['id' => 9, 'name' => 'Xám Castle Rock', 'hex_code' => '#403D46', 'status' => 1, 'created_at' => '2025-11-22 19:56:03', 'updated_at' => '2025-11-22 19:56:03'],
            ['id' => 10, 'name' => 'Vàng Sundress', 'hex_code' => '#F7E5AB', 'status' => 1, 'created_at' => '2025-11-22 20:14:31', 'updated_at' => '2025-11-22 20:14:31'],
            ['id' => 11, 'name' => 'Xanh Everglade', 'hex_code' => '#01615F', 'status' => 1, 'created_at' => '2025-11-22 20:16:00', 'updated_at' => '2025-11-22 20:16:00'],
            ['id' => 12, 'name' => 'Xám Granite', 'hex_code' => '#7A7395', 'status' => 1, 'created_at' => '2025-11-22 20:16:24', 'updated_at' => '2025-11-22 20:16:24'],
            ['id' => 13, 'name' => 'Đỏ tươi', 'hex_code' => '#AD1E24', 'status' => 1, 'created_at' => '2025-11-27 20:28:07', 'updated_at' => '2025-11-27 20:28:07'],
            ['id' => 14, 'name' => 'Xanh Estate', 'hex_code' => '#173663', 'status' => 1, 'created_at' => '2025-11-27 21:11:22', 'updated_at' => '2025-11-27 21:11:22'],
            ['id' => 15, 'name' => 'Hồng Sea Pink', 'hex_code' => '#FD9DC2', 'status' => 1, 'created_at' => '2025-11-27 21:11:39', 'updated_at' => '2025-11-27 21:11:39'],
            ['id' => 16, 'name' => 'Màu test', 'hex_code' => '#F58741', 'status' => 0, 'created_at' => '2025-12-10 22:27:14', 'updated_at' => '2026-01-18 20:45:12'],
        ]);
    }
}
