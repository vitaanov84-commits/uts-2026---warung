<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@warung.test'], [
            'name' => 'Admin Warung',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::updateOrCreate(['email' => 'pembeli@warung.test'], [
            'name' => 'Pembeli Warung',
            'password' => Hash::make('pembeli123'),
            'role' => 'pembeli',
        ]);

        $this->call(ProductSeeder::class);
    }
}
