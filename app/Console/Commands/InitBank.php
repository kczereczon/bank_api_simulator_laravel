<?php

namespace App\Console\Commands;

use App\Services\BankService;
use Illuminate\Console\Command;

class InitBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will initialize basic general account of bank';

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
        $bankService = new BankService();
        $generalUser = $bankService->initBank(50000000);
        $this->info("Bank initialized!");
        $this->line($generalUser);
        $this->info("Banking accounts!");
        $this->line($generalUser->bankingAccounts);
        return 0;
    }
}
