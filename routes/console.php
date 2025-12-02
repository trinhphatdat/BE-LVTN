<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ✅ Hủy đơn hàng quá hạn thanh toán - Chạy mỗi giờ
Schedule::command('orders:cancel-expired-payments')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// ✅ Đồng bộ trạng thái đơn hàng từ GHN - Chạy mỗi 30 phút
Schedule::command('orders:sync-ghn-status')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Artisan commands
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
