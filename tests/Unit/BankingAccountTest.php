<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\BankService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankingAccountTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateBankingAccount()
    {
        $initBalance = rand(0, 400000);
        $newAccountBalance = rand(0, 200000);;

        $bankService = new BankService();

        /** @var User $user */
        $user = User::factory()->count(1)->create()->first();

        $generalAccount = $bankService->initBank($initBalance);

        $bankingAccount = $bankService->createBankingAccount($newAccountBalance, $user);

        $generalAccount = $generalAccount->fresh();
        $generalBankingAccount = $generalAccount->bankingAccounts()->where('general_account', true)->first();

        $user = $user->fresh();

        $this->assertEquals($initBalance+$newAccountBalance, $generalBankingAccount->balance);
        $this->assertEquals($user->id, $bankingAccount->user_id);
    }
}
