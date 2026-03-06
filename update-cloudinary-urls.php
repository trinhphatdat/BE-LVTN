<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

echo "🔄 Updating URLs to Cloudinary...\n\n";

DB::beginTransaction();

try {
    $cloudName = env('CLOUDINARY_CLOUD_NAME');

    if (!$cloudName) {
        throw new Exception("CLOUDINARY_CLOUD_NAME not found in .env");
    }

    echo "Using Cloudinary Cloud Name: {$cloudName}\n\n";

    // Update Products - Tìm tất cả URL không phải là URL đầy đủ của Cloudinary
    $products = Product::whereNotNull('thumbnail')
        ->where(function ($query) {
            $query->where('thumbnail', 'NOT LIKE', 'https://res.cloudinary.com%')
                ->orWhere('thumbnail', 'LIKE', 'products/thumbnails/%');
        })
        ->get();

    echo "Found " . $products->count() . " products to update\n\n";

    foreach ($products as $product) {
        $oldUrl = $product->thumbnail;

        // Lấy tên file từ path
        if (strpos($oldUrl, 'products/thumbnails/') !== false) {
            // Nếu là path như: products/thumbnails/HU3cse9OtbkYRg1mGyji...
            $filename = basename($oldUrl);
            $publicId = 'products/thumbnails/' . pathinfo($filename, PATHINFO_FILENAME);
        } else {
            // Nếu là URL đầy đủ hoặc format khác
            $filename = basename($oldUrl);
            $publicId = 'products/thumbnails/' . pathinfo($filename, PATHINFO_FILENAME);
        }

        $newUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$publicId}";

        $product->update(['thumbnail' => $newUrl]);
        echo "✓ Product #{$product->id}: {$oldUrl} -> {$newUrl}\n";
    }

    // Update Brands
    $brands = Brand::whereNotNull('logo_url')
        ->where(function ($query) {
            $query->where('logo_url', 'NOT LIKE', 'https://res.cloudinary.com%')
                ->orWhere('logo_url', 'LIKE', 'brands/%');
        })
        ->get();

    echo "\nFound " . $brands->count() . " brands to update\n\n";

    foreach ($brands as $brand) {
        $oldUrl = $brand->logo_url;

        if (strpos($oldUrl, 'brands/') !== false) {
            $filename = basename($oldUrl);
            $publicId = 'brands/' . pathinfo($filename, PATHINFO_FILENAME);
        } else {
            $filename = basename($oldUrl);
            $publicId = 'brands/' . pathinfo($filename, PATHINFO_FILENAME);
        }

        $newUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$publicId}";

        $brand->update(['logo_url' => $newUrl]);
        echo "✓ Brand #{$brand->id}: {$oldUrl} -> {$newUrl}\n";
    }

    // Auto commit (không cần xác nhận vì chạy trên Railway)
    DB::commit();
    echo "\n✅ Update completed and committed!\n";
    echo "Total products updated: " . $products->count() . "\n";
    echo "Total brands updated: " . $brands->count() . "\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
