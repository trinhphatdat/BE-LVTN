<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding database...');

        // Seed master data in order (parent tables first)
        $this->call([
            RolesTableSeeder::class,
            BrandsTableSeeder::class,
            ColorsTableSeeder::class,
            SizesTableSeeder::class,
            UsersTableSeeder::class,
            ProductsTableSeeder::class,
            ProductVariantsTableSeeder::class,
            ProductReviewsTableSeeder::class,
            PromotionsTableSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('📝 Seeded tables:');
        $this->command->info('   - roles (3 records)');
        $this->command->info('   - brands (2 records)');
        $this->command->info('   - colors (14 records)');
        $this->command->info('   - sizes (3 records)');
        $this->command->info('   - users (3 records - Admin, Nhân viên, Khách hàng)');
        $this->command->info('   - products (12 records)');
        $this->command->info('   - product_variants (65 records - all sizes & colors)');
        $this->command->info('   - product_reviews (6 records)');
        $this->command->info('   - promotions (2 records)');
        $this->command->newLine();
        $this->command->warn('💡 Note: For full runtime data (orders, carts, return_requests, etc.), import database.sql file directly.');
    }
}
