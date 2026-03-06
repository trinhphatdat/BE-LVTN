<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('promotions')->insert([
            [
                'id' => 1,
                'code' => 'freeship123',
                'name' => 'Miễn phí vận chuyển',
                'url_image' => 'promotions/OcXSDUy8LFXhEQi9fmmOnqfYmpyNign5y7aeCxZU.jpg',
                'description' => null,
                'discount_type' => 'free_shipping',
                'discount_value' => 0.00,
                'min_order_value' => 150000.00,
                'usage_limit' => 10,
                'used_count' => 7,
                'start_date' => '2026-01-01 15:55:34',
                'end_date' => '2026-01-31 15:55:34',
                'status' => 1,
                'created_at' => '2025-11-12 15:55:54',
                'updated_at' => '2026-01-19 22:43:24',
            ],
            [
                'id' => 2,
                'code' => 'discount10',
                'name' => 'Giảm 10% cho đơn hàng trên 200k',
                'url_image' => 'promotions/YWOsuObBBIXKFMzLrikJFEW7U1dE2pkSQDgMFl17.jpg',
                'description' => null,
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_order_value' => 0.00,
                'usage_limit' => 10,
                'used_count' => 2,
                'start_date' => '2026-01-01 20:12:44',
                'end_date' => '2026-01-31 20:12:44',
                'status' => 1,
                'created_at' => '2025-11-12 20:14:27',
                'updated_at' => '2026-01-04 20:36:28',
            ],
        ]);
    }
}
