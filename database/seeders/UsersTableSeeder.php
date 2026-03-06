<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 3,
                'role_id' => 1,
                'fullname' => 'Trịnh Phát Đạt',
                'phone_number' => '0987070241',
                'email' => 'tpd@gmail.com',
                'password' => '$2y$12$vhrWVD8A1iRimh/i5peNzeuXDIToCs75Ov2Z83grMrvk8W9Efr5tO',
                'remember_token' => null,
                'address' => '27 Nguyễn Thiện Thuật',
                'status' => 1,
                'created_at' => '2025-09-24 19:48:36',
                'updated_at' => '2025-09-24 19:48:36',
            ],
            [
                'id' => 8,
                'role_id' => 3,
                'fullname' => 'Trinh Phat Dat',
                'phone_number' => '0987070245',
                'email' => 'trinhphatdat@gmail.com',
                'password' => '$2y$12$Qqep9iy1ixu6CAOns1gaBOeJ1laU6NMQarLAxMgdC5Q2LgkIJASz.',
                'remember_token' => null,
                'address' => '27 NTT',
                'status' => 1,
                'created_at' => '2025-11-16 11:03:54',
                'updated_at' => '2025-11-21 20:35:32',
            ],
            [
                'id' => 12,
                'role_id' => 2,
                'fullname' => 'test test test 123',
                'phone_number' => '0845975263',
                'email' => 'test@gmail.com',
                'password' => '$2y$12$ztjzLPCENIdbj0HjuNfQ4Ocz69RNO5lrGfmoi/DC9rTEMg9D2kWDG',
                'remember_token' => null,
                'address' => 'SG',
                'status' => 1,
                'created_at' => '2025-12-30 19:47:56',
                'updated_at' => '2026-01-05 21:38:06',
            ],
        ]);
    }
}
