<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ✅ Đăng ký scheduled task
Schedule::command('orders:cancel-expired-payments')
    ->hourly()                      // Chạy mỗi giờ
    ->withoutOverlapping()          // Tránh chạy đồng thời
    ->runInBackground()             // Chạy nền
    ->onOneServer()                 // Chỉ chạy trên 1 server (nếu có nhiều)
    ->emailOutputOnFailure('admin@example.com') // Gửi email khi lỗi
    ->sendOutputTo(storage_path('logs/cancel-expired-orders.log')); // Lưu log

// ✅ Hoặc các cách khác:

// Chạy mỗi 30 phút
// Schedule::command('orders:cancel-expired-payments')->everyThirtyMinutes();

// Chạy mỗi ngày lúc 02:00 AM
// Schedule::command('orders:cancel-expired-payments')->dailyAt('02:00');

// Chạy mỗi 6 giờ
// Schedule::command('orders:cancel-expired-payments')->everySixHours();

// Artisan commands (existing)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
