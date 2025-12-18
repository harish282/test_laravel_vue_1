<?php

namespace Database\Seeders;

use App\Models\Asset;
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
        // Create test users
        $users = [
            [
                'name' => 'Alice Trader',
                'email' => 'alice@example.com',
                'password' => Hash::make('password'),
                'balance' => 10000.00,
                'assets' => [
                    ['symbol' => 'BTC', 'amount' => 5, 'locked_amount' => 0],
                    ['symbol' => 'ETH', 'amount' => 2.0, 'locked_amount' => 0],
                ],
            ],
            [
                'name' => 'Bob Investor',
                'email' => 'bob@example.com',
                'password' => Hash::make('password'),
                'balance' => 5000.00,
                'assets' => [
                    ['symbol' => 'BTC', 'amount' => 2, 'locked_amount' => 0],
                ],
            ],
            [
                'name' => 'Charlie Speculator',
                'email' => 'charlie@example.com',
                'password' => Hash::make('password'),
                'balance' => 20000.00,
                'assets' => [
                    ['symbol' => 'ETH', 'amount' => 5.0, 'locked_amount' => 0],
                ],
            ],
        ];

        foreach ($users as $userData) {
            $assets = $userData['assets'];
            unset($userData['assets']);

            $user = User::create($userData);

            foreach ($assets as $assetData) {
                $user->assets()->create($assetData);
            }
        }
    }
}
