# Database Setup Guide

## 📋 Tổng quan

Project sử dụng Laravel 12 + MySQL với database `db_testshopquanao`.

## 🗂️ Cấu trúc Database

**Bảng chính:**
- `roles` - Vai trò người dùng (Admin, Nhân viên, Khách hàng)
- `users` - Người dùng
- `brands` - Thương hiệu
- `colors` - Màu sắc
- `sizes` - Kích thước
- `products` - Sản phẩm
- `product_variants` - Biến thể sản phẩm (size + color)
- `product_reviews` - Đánh giá sản phẩm
- `promotions` - Mã khuyến mãi
- `carts` - Giỏ hàng
- `cart_items` - Sản phẩm trong giỏ
- `orders` - Đơn hàng
- `order_details` - Chi tiết đơn hàng
- `return_requests` - Yêu cầu trả hàng
- `return_request_items` - Sản phẩm trả
- `return_request_images` - Hình ảnh trả hàng

## 🚀 Cài đặt Database

### Option 1: Chạy Migrations + Seeders (Khuyến nghị cho development)

```bash
# 1. Tạo database mới
mysql -u root -p
CREATE DATABASE db_testshopquanao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 2. Cấu hình .env
cp .env.example .env
# Sửa DB_DATABASE=db_testshopquanao

# 3. Chạy migrations
php artisan migrate:fresh

# 4. Seed dữ liệu cơ bản
php artisan db:seed

# Hoặc chạy cả 2 lệnh một lúc:
php artisan migrate:fresh --seed
```

**Seeders sẽ tạo:**
- ✅ 3 roles (Admin, Nhân viên, Khách hàng)
- ✅ 2 brands (Nike, Adidas)
- ✅ 14 colors
- ✅ 3 sizes (S, M, L)
- ✅ 3 users mẫu
- ✅ 12 products
- ✅ 65 product_variants (tất cả các biến thể: size, color, price, stock)
- ✅ 6 product_reviews (đánh giá sản phẩm mẫu)
- ✅ 2 promotions

### Option 2: Import Full Database từ SQL (Khuyến nghị cho production/demo)

**Để có TOÀN BỘ dữ liệu** (orders, carts, product_variants, reviews, etc.):

```bash
# 1. Tạo database
mysql -u root -p -e "CREATE DATABASE db_testshopquanao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Import file SQL
mysql -u root -p db_testshopquanao < database.sql

# Hoặc trong phpMyAdmin:
# - Chọn database
# - Tab Import
# - Chọn file database.sql
# - Click Go
```

## 👤 Tài khoản mẫu (sau khi seed)

### Admin
- Email: `tpd@gmail.com`
- Password: (từ database - đã hash)

### Nhân viên
- Email: `test@gmail.com`
- Password: (từ database - đã hash)

### Khách hàng
- Email: `trinhphatdat@gmail.com`
- Password: (từ database - đã hash)

**Lưu ý:** Passwords đã được hash bằng bcrypt. Để tạo password mới trong seeder:
```php
'password' => Hash::make('your_password'),
```

## 🔄 Reset Database

```bash
# Xóa toàn bộ và tạo lại
php artisan migrate:fresh --seed

# Hoặc chỉ reset seeders
php artisan db:seed --force
```

## 📦 File quan trọng

- `database.sql` - Full backup database
- `database/migrations/` - Migration files
- `database/seeders/` - Seeder files
  - `DatabaseSeeder.php` - Main seeder
  - `RolesTableSeeder.php` - Seed roles
  - `UsersTableSeeder.php` - Seed users
  - `BrandsTableSeeder.php` - Seed brands
  - `ColorsTableSeeder.php` - Seed colors
  - `SizesTableSeeder.php` - Seed sizes
  - `ProductsTableSeeder.php` - Seed products
  - `PromotionsTableSeeder.php` - Seed promotions

## 🛠️ Commands hữu ích

```bash
# Xem trạng thái migrations
php artisan migrate:status

# Rollback migration gần nhất
php artisan migrate:rollback

# Rollback tất cả migrations
php artisan migrate:reset

# Tạo seeder mới
php artisan make:seeder ProductVariantsTableSeeder

# Chạy seeder cụ thể
php artisan db:seed --class=RolesTableSeeder
```

## ⚠️ Lưu ý Deploy

Khi deploy lên production:

1. **KHÔNG chạy** `migrate:fresh` (sẽ xóa data)
2. Chỉ chạy: `php artisan migrate` (chỉ chạy migrations mới)
3. Import `database.sql` nếu cần full data
4. Backup database trước khi migrate:
   ```bash
   mysqldump -u root -p db_testshopquanao > backup_$(date +%Y%m%d).sql
   ```

## 📊 Storage Requirements

Đảm bảo có thư mục storage cho uploads:
```bash
php artisan storage:link
```

**Thư mục cần:**
- `storage/app/public/brands/` - Logo thương hiệu
- `storage/app/public/products/thumbnails/` - Thumbnail sản phẩm
- `storage/app/public/products/variants/` - Ảnh biến thể
- `storage/app/public/promotions/` - Banner khuyến mãi
- `storage/app/public/return_requests/` - Ảnh trả hàng
