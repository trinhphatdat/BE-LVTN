# ☁️ Hướng dẫn Setup Cloudinary cho Project

## 📌 Tại sao cần Cloudinary?

Railway **không hỗ trợ persistent file storage**. Khi service restart, mọi file trong `storage/` sẽ **MẤT**!

**Cloudinary Free Tier:**
- ✅ 25GB storage
- ✅ 25GB bandwidth/tháng  
- ✅ CDN toàn cầu (tốc độ nhanh)
- ✅ Tự động optimize images
- ✅ HTTPS miễn phí

---

## 🚀 BƯỚC 1: Tạo tài khoản Cloudinary

1. Truy cập: https://cloudinary.com/users/register/free
2. Điền form đăng ký (hoặc login với Google/GitHub)
3. Verify email
4. Login vào Dashboard

---

## 🔑 BƯỚC 2: Lấy API Credentials

1. Sau khi login, bạn sẽ thấy **Dashboard**
2. Ở phần **Product Environment Credentials**, copy:
   ```
   Cloud Name: dvhj3abcd (ví dụ)
   API Key: 123456789012345
   API Secret: Click "Reveal" để xem
   ```

3. Copy 3 thông tin này, sẽ dùng ở bước sau

---

## ⚙️ BƯỚC 3: Cấu hình Local (.env)

Mở file `.env` trong project BE-LVTN, thêm:

```env
# Cloudinary Configuration
CLOUDINARY_CLOUD_NAME=dvhj3abcd
CLOUDINARY_API_KEY=123456789012345
CLOUDINARY_API_SECRET=abc_xyz_your_secret_here
CLOUDINARY_URL=cloudinary://123456789012345:abc_xyz_your_secret@dvhj3abcd
```

**Lưu ý:** Thay bằng credentials thực tế của bạn!

---

## 📦 BƯỚC 4: Install Cloudinary Package

```bash
cd BE-LVTN
composer require cloudinary/cloudinary_php
```

Đợi cài đặt xong (~30 giây).

---

## 🖼️ BƯỚC 5: Upload existing images lên Cloudinary

### Option A: Nếu bạn đã có images trong storage/app/public

```bash
# Chạy seeder để upload tất cả images
php artisan db:seed --class=UploadImagesToCloudinarySeeder
```

Seeder sẽ:
- Upload tất cả brand images
- Upload tất cả product images  
- Upload tất cả product variant images (65 images)
- Upload tất cả promotion images
- Tự động cập nhật URLs trong database

### Option B: Nếu chưa có images

Bỏ qua bước này. Khi upload images mới qua admin panel, sẽ tự động lưu lên Cloudinary.

---

## 🔄 BƯỚC 6: Update Controllers để dùng Cloudinary

**File đã được tạo sẵn:** `app/Services/CloudinaryService.php`

**Cách sử dụng trong Controller:**

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
        // Upload image to Cloudinary
        if ($request->hasFile('image')) {
            $imageUrl = $this->cloudinary->upload(
                $request->file('image'), 
                'products' // folder name
            );
            
            // Save Cloudinary URL to database
            Product::create([
                'name' => $request->name,
                'image_url' => $imageUrl, // Full URL từ Cloudinary
                // ... other fields
            ]);
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete from Cloudinary
        if ($product->image_url) {
            $this->cloudinary->deleteByUrl($product->image_url);
        }
        
        $product->delete();
    }
}
```

---

## 🔧 BƯỚC 7: Cấu hình Railway (Production)

Khi deploy lên Railway, thêm Environment Variables:

```env
CLOUDINARY_CLOUD_NAME=dvhj3abcd
CLOUDINARY_API_KEY=123456789012345
CLOUDINARY_API_SECRET=abc_xyz_your_secret_here
CLOUDINARY_URL=cloudinary://123456789012345:abc_xyz_your_secret@dvhj3abcd
```

**Cách thêm:**
1. Railway Dashboard → Service BE-LVTN
2. Tab **"Variables"**
3. Copy paste config trên
4. Click **"Save"**

---

## ✅ BƯỚC 8: Test Upload

### Test Local:

```bash
# Start server
php artisan serve

# Test upload qua Postman/Thunder Client:
POST http://localhost:8000/api/products
Headers:
  Authorization: Bearer <your_jwt_token>
  Content-Type: multipart/form-data
Body (form-data):
  name: Test Product
  image: [chọn file ảnh]
```

Kiểm tra response, `image_url` phải có dạng:
```
https://res.cloudinary.com/dvhj3abcd/image/upload/v1234567890/shop-quan-ao/products/abc123.jpg
```

### Test trên Cloudinary Dashboard:

1. Login Cloudinary
2. Menu **"Media Library"**
3. Sẽ thấy folder `shop-quan-ao/products/` với ảnh vừa upload

---

## 📊 Kiểm tra Usage

1. Cloudinary Dashboard
2. Menu **"Usage"**
3. Xem:
   - Storage used / 25GB
   - Bandwidth used / 25GB
   - Transformations used

**Lưu ý:** Free tier có giới hạn, nên:
- Optimize images trước khi upload (max 2MB/file)
- Xóa images cũ khi không dùng
- Không upload quá nhiều ảnh test

---

## 🎯 Checklist

- [ ] Tạo tài khoản Cloudinary
- [ ] Copy API credentials (Cloud Name, API Key, Secret)
- [ ] Thêm vào .env local
- [ ] Chạy `composer require cloudinary/cloudinary_php`
- [ ] Chạy seeder upload images (nếu có)
- [ ] Test upload 1 ảnh mới
- [ ] Kiểm tra ảnh hiển thị trong frontend
- [ ] Thêm credentials vào Railway
- [ ] Test upload trên production

---

## ⚠️ Lưu ý quan trọng

1. **Không commit API Secret** lên GitHub (.env đã được gitignore)
2. **URLs trong database** phải là full Cloudinary URLs, không phải local paths
3. **Khi xóa record** trong database, nhớ xóa image trên Cloudinary để tiết kiệm storage
4. **Free tier 25GB** = ~5000-10000 product images (tùy kích thước)
5. **Bandwidth 25GB/tháng** = ~25000 lượt view ảnh

---

## 🆘 Troubleshooting

### Lỗi: "Cloudinary not configured"
✅ Kiểm tra `.env` có đầy đủ 4 dòng CLOUDINARY_*
✅ Restart server: `php artisan serve`

### Lỗi: "Invalid API credentials"
✅ Kiểm tra lại Cloud Name, API Key, Secret trong Dashboard
✅ Copy paste cẩn thận, không có dấu cách thừa

### Upload thành công nhưng không hiển thị ảnh
✅ Kiểm tra CORS: Cloudinary URLs phải accessible từ frontend
✅ Check browser Console → Network tab
✅ Verify URL trong database đúng format Cloudinary

### Seeder báo "File not found"
✅ Images phải có trong `storage/app/public/`
✅ Chạy `php artisan storage:link` trước
✅ Hoặc copy images từ database.sql vào đúng folder

---

## 📸 Tối ưu Images

Cloudinary tự động optimize, nhưng để tốt hơn:

```php
// Upload với options optimize
$result = $this->cloudinary->upload($file, 'products', [
    'quality' => 'auto:good',
    'fetch_format' => 'webp', // Convert to WebP
    'transformation' => [
        ['width' => 800, 'crop' => 'limit'], // Max width 800px
    ]
]);
```

---

🎉 **Setup Cloudinary hoàn tất!** Giờ project đã sẵn sàng deploy lên Railway với file storage bền vững!
