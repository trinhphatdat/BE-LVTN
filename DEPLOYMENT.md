# 🚀 Hướng dẫn Deploy Project lên Railway (Miễn phí)

## 📋 Tổng quan

Project bao gồm:
- **Backend**: Laravel 12 + MySQL (Railway - Free tier)
- **Frontend**: Vue.js 3 (Vercel - Free)
- **Storage**: Cloudinary (File uploads - Free tier)
- **Database**: MySQL on Railway (512MB)

## ✅ Chuẩn bị trước khi deploy

### 1. Kiểm tra files đã tạo
- [x] `Procfile` - Railway run command
- [x] `nixpacks.toml` - Build configuration
- [x] `railway.json` - Deploy settings
- [x] `.env.production.example` - Environment template
- [x] `config/cors.php` - CORS configuration
- [x] `bootstrap/app.php` - CORS middleware enabled

### 2. Push code lên GitHub
```bash
git add .
git commit -m "chore: add Railway deployment configs"
git push origin main
```

---

## 🎯 BƯỚC 1: Deploy Backend lên Railway

### 1.1 Tạo tài khoản Railway
1. Truy cập: https://railway.app
2. Click **"Login"** → Chọn **"Login with GitHub"**
3. Authorize Railway access to GitHub

### 1.2 Tạo Project mới
1. Dashboard → Click **"New Project"**
2. Chọn **"Deploy from GitHub repo"**
3. Chọn repository `BE-LVTN`
4. Railway sẽ tự động detect Laravel project

### 1.3 Add MySQL Database
1. Trong Project, click **"New"** → **"Database"** → **"Add MySQL"**
2. Railway tự động tạo database và generate credentials
3. Đợi database khởi tạo (1-2 phút)

### 1.4 Configure Environment Variables

Click vào service **BE-LVTN** → Tab **"Variables"** → **"RAW Editor"**

Copy paste config sau và điền thông tin:

```env
APP_NAME="Shop Quần Áo LVTN"
APP_ENV=production
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

# Database - Railway auto-inject
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Session/Cache/Queue
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=public

# CORS - Điền sau khi deploy Frontend
FRONTEND_URL=https://your-frontend.vercel.app

# JWT - Generate riêng (xem bên dưới)
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60

# Cloudinary (điền sau bước 3)
CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
CLOUDINARY_URL=

# Mail (optional - nếu dùng forgot password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@shopquanao.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 1.5 Generate APP_KEY và JWT_SECRET

**Option 1: Local (Recommended)**
```bash
# Trong folder BE-LVTN local
php artisan key:generate --show
# Copy output vào APP_KEY

php artisan jwt:secret --show
# Copy output vào JWT_SECRET
```

**Option 2: Trên Railway Console**
1. Service BE-LVTN → Tab **"Deployments"** → Latest deployment
2. Click **"View Logs"**
3. Ở góc trên phải, click **3 dots** → **"Service Shell"**
4. Chạy:
```bash
php artisan key:generate --show
php artisan jwt:secret --show
```
5. Copy outputs vào Variables

### 1.6 Enable Public Domain
1. Service BE-LVTN → Tab **"Settings"**
2. Section **"Networking"** → Click **"Generate Domain"**
3. Copy domain (vd: `be-lvtn-production.up.railway.app`)
4. Update biến `APP_URL` với domain này

### 1.7 Deploy & Run Migrations
Railway tự động deploy. Kiểm tra logs:
1. Tab **"Deployments"** → Latest deployment
2. Click **"View Logs"**
3. Đợi build xong (3-5 phút)

**Chạy migrations & seeders:**
```bash
# Trong Service Shell
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

✅ **Backend deployed!** Test: `https://your-domain.up.railway.app/api/products`

---

## 🎨 BƯỚC 2: Deploy Frontend lên Vercel

### 2.1 Tạo tài khoản Vercel
1. Truy cập: https://vercel.com
2. **"Sign Up"** → **"Continue with GitHub"**

### 2.2 Import Project
1. Dashboard → **"Add New..."** → **"Project"**
2. Import repo `FE-LVTN`
3. Configure:
   - **Framework Preset**: Vite
   - **Root Directory**: `./` (keep default)
   - **Build Command**: `npm run build`
   - **Output Directory**: `dist`

### 2.3 Environment Variables
Thêm biến môi trường:
```env
VITE_API_URL=https://your-backend.up.railway.app/api
```

### 2.4 Deploy
1. Click **"Deploy"**
2. Đợi build (2-3 phút)
3. Copy domain (vd: `fe-lvtn.vercel.app`)

### 2.5 Update Backend CORS
Quay lại Railway → Service BE-LVTN → Variables:
```env
FRONTEND_URL=https://fe-lvtn.vercel.app
```

✅ **Frontend deployed!**

---

## ☁️ BƯỚC 3: Setup Cloudinary cho File Storage

### 3.1 Tại sao cần Cloudinary?
Railway **không có persistent storage**. Khi service restart, tất cả files trong `storage/app/public` sẽ **MẤT**!

**Cloudinary Free Tier:**
- ✅ 25GB storage
- ✅ 25GB bandwidth/month
- ✅ Unlimited transformations
- ✅ CDN toàn cầu

### 3.2 Tạo tài khoản Cloudinary
1. Truy cập: https://cloudinary.com/users/register/free
2. Điền thông tin đăng ký
3. Verify email

### 3.3 Lấy Credentials
1. Login → Dashboard
2. Copy thông tin:
   - **Cloud Name**: (ví dụ: `dvhj3abcd`)
   - **API Key**: (ví dụ: `123456789012345`)
   - **API Secret**: (click "reveal" để xem)

### 3.4 Update Railway Environment
Vào Railway → BE-LVTN → Variables, thêm:
```env
CLOUDINARY_CLOUD_NAME=dvhj3abcd
CLOUDINARY_API_KEY=123456789012345
CLOUDINARY_API_SECRET=your_api_secret_here
CLOUDINARY_URL=cloudinary://123456789012345:your_api_secret@dvhj3abcd
```

### 3.5 Install Cloudinary Package
```bash
# Trong local BE-LVTN
composer require cloudinary/cloudinary_php
```

### 3.6 Tạo Cloudinary Service

**File: `app/Services/CloudinaryService.php`**
```php
<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    /**
     * Upload file to Cloudinary
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder (e.g., 'products', 'brands')
     * @return string Public URL
     */
    public function upload($file, $folder = 'uploads')
    {
        try {
            $uploadApi = new UploadApi();
            $result = $uploadApi->upload($file->getRealPath(), [
                'folder' => 'shop-quan-ao/' . $folder,
                'resource_type' => 'auto',
                'quality' => 'auto:good',
            ]);

            return $result['secure_url'];
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete file from Cloudinary
     * 
     * @param string $publicId
     * @return bool
     */
    public function delete($publicId)
    {
        try {
            $uploadApi = new UploadApi();
            $uploadApi->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            \Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }
}
```

### 3.7 Update Controllers để dùng Cloudinary

**Ví dụ: ProductController**
```php
use App\Services\CloudinaryService;

class ProductController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function store(Request $request)
    {
        // ... validation ...

        // Upload image
        if ($request->hasFile('image')) {
            $imageUrl = $this->cloudinary->upload($request->file('image'), 'products');
            
            Product::create([
                'name' => $request->name,
                'image_url' => $imageUrl, // Save full URL
                // ... other fields ...
            ]);
        }
    }
}
```

### 3.8 Upload existing images to Cloudinary

**Tạo script upload:**

**File: `database/seeders/UploadImagesToCloudinary.php`**
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\CloudinaryService;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Brand;
use App\Models\Promotion;
use Illuminate\Support\Facades\Storage;

class UploadImagesToCloudinary extends Seeder
{
    protected $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function run(): void
    {
        $this->command->info('🚀 Uploading images to Cloudinary...');

        // Upload Product images
        $this->uploadProductImages();
        
        // Upload Product Variant images
        $this->uploadProductVariantImages();
        
        // Upload Brand images
        $this->uploadBrandImages();
        
        // Upload Promotion images
        $this->uploadPromotionImages();

        $this->command->info('✅ All images uploaded successfully!');
    }

    protected function uploadProductImages()
    {
        $products = Product::whereNotNull('image_url')->get();
        
        foreach ($products as $product) {
            $localPath = storage_path('app/public/' . $product->image_url);
            
            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'products');
                    
                    $product->update(['image_url' => $url]);
                    $this->command->info("✓ Product #{$product->id}: {$product->name}");
                } catch (\Exception $e) {
                    $this->command->error("✗ Product #{$product->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function uploadProductVariantImages()
    {
        $variants = ProductVariant::whereNotNull('image_url')->get();
        
        foreach ($variants as $variant) {
            $localPath = storage_path('app/public/' . $variant->image_url);
            
            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'product_variants');
                    
                    $variant->update(['image_url' => $url]);
                    $this->command->info("✓ Variant #{$variant->id}");
                } catch (\Exception $e) {
                    $this->command->error("✗ Variant #{$variant->id}: " . $e->getMessage());
                }
            }
        }
    }

    protected function uploadBrandImages()
    {
        $brands = Brand::whereNotNull('image_url')->get();
        
        foreach ($brands as $brand) {
            $localPath = storage_path('app/public/' . $brand->image_url);
            
            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'brands');
                    
                    $brand->update(['image_url' => $url]);
                    $this->command->info("✓ Brand: {$brand->name}");
                } catch (\Exception $e) {
                    $this->command->error("✗ Brand {$brand->name}: " . $e->getMessage());
                }
            }
        }
    }

    protected function uploadPromotionImages()
    {
        $promotions = Promotion::whereNotNull('url_image')->get();
        
        foreach ($promotions as $promotion) {
            $localPath = storage_path('app/public/' . $promotion->url_image);
            
            if (file_exists($localPath)) {
                try {
                    $file = new \Illuminate\Http\File($localPath);
                    $url = $this->cloudinary->upload($file, 'promotions');
                    
                    $promotion->update(['url_image' => $url]);
                    $this->command->info("✓ Promotion: {$promotion->name}");
                } catch (\Exception $e) {
                    $this->command->error("✗ Promotion {$promotion->name}: " . $e->getMessage());
                }
            }
        }
    }
}
```

**Chạy upload (Local):**
```bash
# Copy files storage từ local lên trước
php artisan db:seed --class=UploadImagesToCloudinary
```

---

## 🔧 Troubleshooting

### Railway Deployment Failed
```bash
# Check logs
railway logs

# Restart service
railway restart

# Re-deploy
git commit --allow-empty -m "redeploy"
git push origin main
```

### Database Connection Error
- Kiểm tra biến môi trường DB_* đã đúng chưa
- MySQL service đã start chưa
- Check logs: `railway logs --service mysql`

### CORS Error
- Kiểm tra `FRONTEND_URL` trong Railway variables
- Clear cache: `php artisan config:clear`
- Kiểm tra `config/cors.php`

### Storage/Files không hiển thị
- ✅ Đã dùng Cloudinary chưa?
- ✅ Đã update image URLs trong database?
- ✅ Cloudinary credentials đúng chưa?

---

## 💰 Chi phí (Free Tier)

| Service | Free Tier | Giới hạn |
|---------|-----------|----------|
| **Railway** | $5 credit/tháng | ~500 hours, 512MB RAM, 1GB storage |
| **Vercel** | Free forever | 100GB bandwidth, unlimited sites |
| **Cloudinary** | Free forever | 25GB storage, 25GB bandwidth |
| **MySQL (Railway)** | Included | 512MB database |

**Tổng: $0/tháng** (đủ cho demo/thực tập)

---

## 📊 Monitoring

### Railway Dashboard
- CPU usage
- Memory usage
- Database size
- Request count

### Logs
```bash
# Backend logs
railway logs --service be-lvtn

# Database logs
railway logs --service mysql
```

---

## 🚨 Lưu ý quan trọng

1. **Railway Free Tier**: Chỉ 512MB RAM → Tối ưu code, không upload quá nhiều ảnh trong seeders
2. **Persistent Storage**: Chỉ database là persistent, files phải dùng Cloudinary
3. **Environment Variables**: Không commit `.env` lên GitHub!
4. **JWT Secret**: Phải generate riêng, không dùng chung
5. **Database Backup**: Export database định kỳ từ Railway
6. **Cloudinary Quota**: Free tier 25GB → Optimize images trước khi upload

---

## 🎯 Checklist hoàn thành

- [ ] Railway: Backend deployed
- [ ] Railway: MySQL database created
- [ ] Railway: Environment variables configured
- [ ] Railway: Domain generated & tested
- [ ] Vercel: Frontend deployed
- [ ] Vercel: API URL configured
- [ ] Cloudinary: Account created
- [ ] Cloudinary: Credentials added to Railway
- [ ] Cloudinary: Images uploaded
- [ ] CORS: Frontend can call backend API
- [ ] Test: Login/Register works
- [ ] Test: Products display with images
- [ ] Test: Cart & Checkout working

---

## 📞 Support

Nếu gặp lỗi, check:
1. Railway Deployment Logs
2. Browser Console (Frontend errors)
3. Network tab (API calls)
4. Database connection (phpMyAdmin alternative: Railway DB viewer)

**Các lỗi thường gặp đã có giải pháp trong phần Troubleshooting phía trên!**

---

🎉 **Chúc bạn deploy thành công!**
