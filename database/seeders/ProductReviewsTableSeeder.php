<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_reviews')->insert([
            ['id' => 1, 'user_id' => 8, 'product_id' => 7, 'product_variant_id' => null, 'rating' => 4, 'comment' => 'Sản phẩm tốt', 'status' => 1, 'approved_at' => null, 'created_at' => '2025-11-21 15:34:19', 'updated_at' => '2025-11-21 15:34:19'],
            ['id' => 2, 'user_id' => 8, 'product_id' => 7, 'product_variant_id' => 69, 'rating' => 5, 'comment' => 'áo đẹp', 'status' => 1, 'approved_at' => null, 'created_at' => '2025-11-21 15:35:40', 'updated_at' => '2025-11-21 15:35:40'],
            ['id' => 3, 'user_id' => 8, 'product_id' => 7, 'product_variant_id' => null, 'rating' => 1, 'comment' => 'áo không giống như mô tả', 'status' => 1, 'approved_at' => null, 'created_at' => '2025-12-10 20:25:19', 'updated_at' => '2025-12-10 20:25:19'],
            ['id' => 4, 'user_id' => 8, 'product_id' => 21, 'product_variant_id' => 120, 'rating' => 4, 'comment' => 'tốt', 'status' => 1, 'approved_at' => null, 'created_at' => '2025-12-31 13:16:13', 'updated_at' => '2025-12-31 13:16:13'],
            ['id' => 5, 'user_id' => 8, 'product_id' => 21, 'product_variant_id' => null, 'rating' => 5, 'comment' => 'sản phẩm này đẹp quá', 'status' => 1, 'approved_at' => null, 'created_at' => '2025-12-31 13:48:10', 'updated_at' => '2025-12-31 13:48:10'],
            ['id' => 6, 'user_id' => 8, 'product_id' => 21, 'product_variant_id' => null, 'rating' => 3, 'comment' => 'sản phẩm này đẹp quá', 'status' => 1, 'approved_at' => null, 'created_at' => '2025-12-31 13:48:33', 'updated_at' => '2025-12-31 13:48:33'],
        ]);
    }
}
