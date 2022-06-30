<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiveTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create user
        $user = \App\Models\User::factory()->create([
            'name' => 'John Doe',
            'is_store' => false,
        ]);

        // Create account for user
        $user_account = \App\Models\Account::factory()->create([
            'user_id' => $user->id,
            'balance' => 100,
        ]);

        // Create store user

        $store = \App\Models\User::factory()->create([
            'name' => 'The super cool store',
            'is_store' => true,
        ]);

        // Create an account for store user
        $store_account = \App\Models\Account::factory()->create([
            'user_id' => $store->id,
            'balance' => 0,
        ]);

    }
}
