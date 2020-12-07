<?php

namespace Database\Seeders;

use App\Models\BankingAccount;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::factory(10)->make()->toArray();
        $userService = new UserService();

        foreach ($users as $user) {
            $userService->createUser($user['name'], $user['email'], rand(0, 3000000));
        }


    }
}
