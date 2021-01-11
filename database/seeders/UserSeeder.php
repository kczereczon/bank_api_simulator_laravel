<?php

namespace Database\Seeders;

use App\Services\UserService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::factory(10)->make()->toArray();
        $userService = new UserService();

        foreach ($users as $user) {
            $userService->createUser($user['name'], $user['email'], rand(0, 30000));
        }
    }
}
