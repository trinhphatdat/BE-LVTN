<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\CloudinaryService;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Brand;
use App\Models\Promotion;
use Illuminate\Support\Facades\Storage;

class UploadImagesToCloudinarySeeder extends Seeder
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting upload images to Cloudinary...');
        $this->command->newLine();

        // Check if Cloudinary is configured
        if (!env('CLOUDINARY_CLOUD_NAME')) {
            $this->command->error('❌ Cloudinary not configured!');
            $this->command->warn('Please set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET in .env');
            return;
        }

        // Upload images
        $this->uploadBrandImages();
        $this->uploadProductImages();
        $this->uploadProductVariantImages();
        $this->uploadPromotionImages();

        $this->command->newLine();
        $this->command->info('✅ All images uploaded successfully!');
    }

    protected function uploadBrandImages()
    {
        $this->command->info('📦 Uploading Brand images...');

        $brands = Brand::whereNotNull('logo_url')->get();  // ← Đổi image_url thành logo_url
        $count = 0;

        foreach ($brands as $brand) {
            // Skip if already Cloudinary URL
            if (str_contains($brand->logo_url, 'cloudinary.com')) {  // ← Đổi
                $this->command->warn("  ⏭️  Brand #{$brand->id} ({$brand->name}) already on Cloudinary");
                continue;
            }

            $localPath = storage_path('app/public/' . $brand->logo_url);  // ← Đổi

            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'brands');

                    $brand->update(['logo_url' => $url]);  // ← Đổi image_url thành logo_url
                    $count++;
                    $this->command->info("  ✓ Brand #{$brand->id}: {$brand->name}");
                } catch (\Exception $e) {
                    $this->command->error("  ✗ Brand #{$brand->id} ({$brand->name}): " . $e->getMessage());
                }
            } else {
                $this->command->warn("  ⚠ Brand #{$brand->id} ({$brand->name}): File not found at {$localPath}");
            }
        }

        $this->command->info("  📊 Uploaded {$count} brand images");
        $this->command->newLine();
    }

    protected function uploadProductImages()
    {
        $this->command->info('🛍️  Uploading Product images...');

        $products = Product::whereNotNull('thumbnail')->get();
        $count = 0;

        foreach ($products as $product) {
            // Skip if already Cloudinary URL
            if (str_contains($product->thumbnail, 'cloudinary.com')) {
                continue;
            }

            $localPath = storage_path('app/public/' . $product->thumbnail);

            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'products');

                    $product->update(['thumbnail' => $url]);
                    $count++;
                    $this->command->info("  ✓ Product #{$product->id}: {$product->name}");
                } catch (\Exception $e) {
                    $this->command->error("  ✗ Product #{$product->id} ({$product->name}): " . $e->getMessage());
                }
            } else {
                $this->command->warn("  ⚠ Product #{$product->id}: File not found");
            }
        }

        $this->command->info("  📊 Uploaded {$count} product images");
        $this->command->newLine();
    }

    protected function uploadProductVariantImages()
    {
        $this->command->info('🎨 Uploading Product Variant images...');

        $variants = ProductVariant::whereNotNull('image_url')->get();
        $count = 0;
        $total = $variants->count();

        foreach ($variants as $index => $variant) {
            // Skip if already Cloudinary URL
            if (str_contains($variant->image_url, 'cloudinary.com')) {
                continue;
            }

            $localPath = storage_path('app/public/' . $variant->image_url);

            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'product_variants');

                    $variant->update(['image_url' => $url]);
                    $count++;

                    // Show progress every 10 uploads
                    if ($count % 10 == 0) {
                        $this->command->info("  ⏳ Progress: {$count}/{$total} variants uploaded...");
                    }
                } catch (\Exception $e) {
                    $this->command->error("  ✗ Variant #{$variant->id}: " . $e->getMessage());
                }
            } else {
                $this->command->warn("  ⚠ Variant #{$variant->id}: File not found");
            }
        }

        $this->command->info("  📊 Uploaded {$count} product variant images");
        $this->command->newLine();
    }

    protected function uploadPromotionImages()
    {
        $this->command->info('🎉 Uploading Promotion images...');

        $promotions = Promotion::whereNotNull('url_image')->get();
        $count = 0;

        foreach ($promotions as $promotion) {
            // Skip if already Cloudinary URL
            if (str_contains($promotion->url_image, 'cloudinary.com')) {
                continue;
            }

            $localPath = storage_path('app/public/' . $promotion->url_image);

            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'promotions');

                    $promotion->update(['url_image' => $url]);
                    $count++;
                    $this->command->info("  ✓ Promotion #{$promotion->id}: {$promotion->name}");
                } catch (\Exception $e) {
                    $this->command->error("  ✗ Promotion #{$promotion->id} ({$promotion->name}): " . $e->getMessage());
                }
            } else {
                $this->command->warn("  ⚠ Promotion #{$promotion->id}: File not found");
            }
        }

        $this->command->info("  📊 Uploaded {$count} promotion images");
        $this->command->newLine();
    }
}
