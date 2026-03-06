<?php
// update-cloudinary-urls.php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Brand;
use Cloudinary\Cloudinary;

echo "🔄 Updating URLs to Cloudinary...\n\n";

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET')
    ]
]);

// Update Products
$products = Product::whereNotNull('thumbnail')
    ->where('thumbnail', 'NOT LIKE', 'https://res.cloudinary.com%')
    ->get();

echo "Found " . $products->count() . " products to update\n\n";

foreach ($products as $product) {
    $oldUrl = $product->thumbnail;
    $filename = basename($oldUrl);

    // Get Cloudinary URL từ public_id
    $publicId = 'products/' . pathinfo($filename, PATHINFO_FILENAME);

    try {
        // Lấy resource info để có URL chính xác
        $result = $cloudinary->adminApi()->asset($publicId);
        $newUrl = $result['secure_url'];

        $product->update(['thumbnail' => $newUrl]);
        echo "✓ Product #{$product->id}: {$newUrl}\n";
    } catch (\Exception $e) {
        echo "✗ Product #{$product->id}: ERROR - {$e->getMessage()}\n";
    }
}

// Update Brands
$brands = Brand::whereNotNull('logo_url')
    ->where('logo_url', 'NOT LIKE', 'https://res.cloudinary.com%')
    ->get();

echo "\nFound " . $brands->count() . " brands to update\n\n";

foreach ($brands as $brand) {
    $oldUrl = $brand->logo_url;
    $filename = basename($oldUrl);
    $publicId = 'brands/' . pathinfo($filename, PATHINFO_FILENAME);

    try {
        $result = $cloudinary->adminApi()->asset($publicId);
        $newUrl = $result['secure_url'];

        $brand->update(['logo_url' => $newUrl]);
        echo "✓ Brand #{$brand->id}: {$newUrl}\n";
    } catch (\Exception $e) {
        echo "✗ Brand #{$brand->id}: ERROR - {$e->getMessage()}\n";
    }
}

echo "\n✅ Update completed!\n";
