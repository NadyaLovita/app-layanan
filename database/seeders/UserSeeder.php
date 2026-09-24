<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $users = [
            [
                'name' => 'Administrator SIPAMPAH',
                'username' => 'admin',
                'email' => 'admin@sipampah.trenggalekkab.go.id',
                'phone' => '081234567001',
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
            [
                'name' => 'Nadya Lovita Sari, A.Md.Kom',
                'username' => 'petugas',
                'email' => 'petugas@sipampah.trenggalekkab.go.id',
                'phone' => '081234567002',
                'role' => UserRole::Officer,
                'is_active' => true,
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'operator',
                'email' => 'operator@sipampah.trenggalekkab.go.id',
                'phone' => '081234567003',
                'role' => UserRole::Officer,
                'is_active' => true,
            ],
            [
                'name' => 'Agus Setiawan, S.T.',
                'username' => 'koordinator',
                'email' => 'koordinator@sipampah.trenggalekkab.go.id',
                'phone' => '081234567004',
                'role' => UserRole::Coordinator,
                'is_active' => true,
            ],
            [
                'name' => 'Ir. Suyitno, M.M.',
                'username' => 'pimpinan',
                'email' => 'pimpinan@sipampah.trenggalekkab.go.id',
                'phone' => '081234567005',
                'role' => UserRole::Head,
                'is_active' => true,
            ],
            [
                'name' => 'Supriyadi',
                'username' => 'supriyadi',
                'email' => 'supriyadi@sipampah.trenggalekkab.go.id',
                'phone' => '081234567011',
                'role' => UserRole::Driver,
                'is_active' => true,
            ],
            [
                'name' => 'Bambang Hermanto',
                'username' => 'bambang',
                'email' => 'bambang@sipampah.trenggalekkab.go.id',
                'phone' => '081234567012',
                'role' => UserRole::Driver,
                'is_active' => true,
            ],
            [
                'name' => 'Sugeng Riyadi',
                'username' => 'sugeng',
                'email' => 'sugeng@sipampah.trenggalekkab.go.id',
                'phone' => '081234567013',
                'role' => UserRole::Driver,
                'is_active' => true,
            ],
            [
                'name' => 'Totok Prasetyo',
                'username' => 'totok',
                'email' => 'totok@sipampah.trenggalekkab.go.id',
                'phone' => '081234567014',
                'role' => UserRole::Driver,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                array_merge($userData, ['password' => $defaultPassword])
            );
        }
    }
}
