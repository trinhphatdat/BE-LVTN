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
    // Update Products
    $products = Product::whereNotNull('thumbnail')
        ->where('thumbnail', 'NOT LIKE', 'https://res.cloudinary.com%')
        ->get();

    echo "Found " . $products->count() . " products to update\n\n";

    foreach ($products as $product) {
        $oldUrl = $product->thumbnail;
        $filename = basename($oldUrl);

        // Tạo Cloudinary URL trực tiếp
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $publicId = 'products/' . pathinfo($filename, PATHINFO_FILENAME);
        $newUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$publicId}";

        $product->update(['thumbnail' => $newUrl]);
        echo "✓ Product #{$product->id}: {$filename} -> {$newUrl}\n";
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

        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $newUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$publicId}";

        $brand->update(['logo_url' => $newUrl]);
        echo "✓ Brand #{$brand->id}: {$filename} -> {$newUrl}\n";
    }

    echo "\n❓ Do you want to commit these changes? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $confirm = trim(fgets($handle));

    if (strtolower($confirm) === 'yes') {
        DB::commit();
        echo "\n✅ Update completed and committed!\n";
    } else {
        DB::rollBack();
        echo "\n↩️  Changes rolled back!\n";
    }
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
