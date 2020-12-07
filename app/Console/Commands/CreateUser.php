<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user {name} {email} {balance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will create user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userService = new UserService();
        $user = $userService->createUser($this->argument("name"), $this->argument("email"), $this->argument("balance"));
        $this->info("User created!");
        $this->line($user);
        $this->info("Banking accounts:");
        $this->line($user->bankingAccounts);
        return 0;
    }
}
