<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\User;

class MakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:userAccount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user with an account';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->ask('What is the name of the user?');
        $isStore = $this->confirm('Is the user a store?');
        $balance = $this->ask('What is the balance of the account?', 100.00);

        try{
            $user = \App\Models\User::factory()->create([
                'name' => $name,
                'is_store' => $isStore,
            ]);
    
            // Create account for user
            $user_account = \App\Models\Account::factory()->create([
                'userId' => $user->id,
                'balance' => $balance,
            ]);
        }catch(\Exception $e){
            $this->error($e->getMessage());
        }

        $this->info('User created successfully!');



    }
}
