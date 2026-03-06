<?php
// Script để update local URLs thành Cloudinary URLs
// Chạy: railway run php update-cloudinary-urls.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Brand;

echo "🔄 Updating URLs to Cloudinary...\n\n";

// Update Products
$products = Product::whereNotNull('thumbnail')
    ->where('thumbnail', 'NOT LIKE', 'https://res.cloudinary.com%')
    ->get();

foreach ($products as $product) {
    $oldUrl = $product->thumbnail;

    // Extract filename from local path
    // VD: "products/thumbnails/abc.png" -> "abc.png"
    $filename = basename($oldUrl);

    // Construct Cloudinary URL
    // Pattern: https://res.cloudinary.com/du2fyema7/image/upload/v1/products/filename
    $newUrl = "https://res.cloudinary.com/du2fyema7/image/upload/v1/products/" . $filename;

    $product->update(['thumbnail' => $newUrl]);
    echo "✓ Product #{$product->id}: {$filename}\n";
}

// Update Brands
$brands = Brand::whereNotNull('logo_url')
    ->where('logo_url', 'NOT LIKE', 'https://res.cloudinary.com%')
    ->get();

foreach ($brands as $brand) {
    $oldUrl = $brand->logo_url;
    $filename = basename($oldUrl);
    $newUrl = "https://res.cloudinary.com/du2fyema7/image/upload/v1/brands/" . $filename;

    $brand->update(['logo_url' => $newUrl]);
    echo "✓ Brand #{$brand->id}: {$filename}\n";
}

echo "\n✅ Update completed!\n";
