<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\BankService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankingAccountTest extends TestCase
{
    use RefreshDatabase;

    public function testBankNumberControlSum()
    {
        $bankService = new BankService();

        $number = $bankService->generateBankNumberWithControlSum();
        $this->assertTrue($bankService->validateBankNumber($number));
    }

    public function testBankNumberControlSumGenerator()
    {
        $bankService = new BankService();
        $number = "1030194";

        $this->assertEquals("4", $bankService->generateControlSumBankNumber($number));
    }

    public function testGeneratingIban()
    {
        $bankService = new BankService();

        $iban = $bankService->generateIban();
        $this->assertTrue($bankService->validateIbanNumber($iban));
    }

    public function testIbanValidator()
    {
        $bankService = new BankService();

        $iban = "PL61187010452078100052700001";

        $this->assertTrue($bankService->validateIbanNumber($iban));
    }

    public function testIbanValidatorWithZero()
    {
        $bankService = new BankService();

        $iban = "PL04000000000000000000000000";

        $this->assertTrue($bankService->validateIbanNumber($iban));
    }

    public function testIbanControlSumGenerator()
    {
        $bankService = new BankService();

        $iban = "000000000000000000000000252100";

        $this->assertEquals("04", $bankService->generateControlSumOfIban($iban));
    }

    // public function testCreateBankingAccount()
    // {
    //     $initBalance = rand(0, 400000);
    //     $newAccountBalance = rand(0, 200000);;

    //     $bankService = new BankService();

    //     /** @var User $user */
    //     $user = User::factory()->count(1)->create()->first();

    //     $generalAccount = $bankService->initBank($initBalance);

    //     $bankingAccount = $bankService->createBankingAccount($user, $newAccountBalance);

    //     $generalAccount = $generalAccount->fresh();
    //     $generalBankingAccount = $generalAccount->bankingAccounts()->where('general_account', true)->first();

    //     $user = $user->fresh();

    //     $this->assertEquals($initBalance+$newAccountBalance, $generalBankingAccount->balance);
    //     $this->assertEquals($user->id, $bankingAccount->user_id);
    // }
}
