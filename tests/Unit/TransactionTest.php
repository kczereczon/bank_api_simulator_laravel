<?php

namespace Tests\Unit;

use App\Exceptions\NoMoneyOnAccount;
use App\Models\BankingAccount;
use App\Models\User;
use App\Services\BankService;
use App\Services\TransactionService;
use App\Services\UserService;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testTransactionInternalWithMoney()
    {
        $bankingService = new BankService();
        $bankingService->initBank(500000);
        $this->seed(StatusSeeder::class);
        
        $userService = new UserService();
        
        $userFakePrim = User::factory()->make();
        $userFakeBen = User::factory()->make();

        $bankingAccountPrim = $userService->createUser($userFakePrim->name, $userFakePrim->email, 1000, true)->fresh();
        $this->assertEquals(1000, $bankingAccountPrim->balance);
        $bankingAccountBen = $userService->createUser($userFakeBen->name, $userFakeBen->email, 1000, true)->fresh();
        $this->assertEquals(1000, $bankingAccountBen->balance);

        $transactionService = new TransactionService();
        $transaction = $transactionService->createTransaction($bankingAccountBen->nrb, $bankingAccountPrim->nrb, 500, "test");
    }
    
    public function testTransactionInternalWithoutMoney()
    {
        $bankingService = new BankService();
        $bankingService->initBank(500000);
        $this->seed(StatusSeeder::class);

        $userService = new UserService();
        $transactionService = new TransactionService();
        
        $userFakePrim = User::factory()->make();
        $userFakeBen = User::factory()->make();

        $bankingAccountPrim = $userService->createUser($userFakePrim->name, $userFakePrim->email, 0, true)->fresh();
        $bankingAccountBen = $userService->createUser($userFakeBen->name, $userFakeBen->email, 0, true)->fresh();

        $this->expectException(NoMoneyOnAccount::class);

        $transaction = $transactionService->createTransaction($bankingAccountBen->nrb, $bankingAccountPrim->nrb, 500, "test");
    }

    public function testTransactionOutsideWithoutMoney()
    {
        $bankingService = new BankService();
        $bankingService->initBank(500000);
        $this->seed(StatusSeeder::class);

        $userService = new UserService();
        $transactionService = new TransactionService();
        
        $userFakePrim = User::factory()->make();
        $ben_nrb = "PL86104253892907300740976838";
        $ben_name = "Test";
        $ben_adress = "Test 23, Test";

        $bankingAccountPrim = $userService->createUser($userFakePrim->name, $userFakePrim->email, 0, true)->fresh();

        $this->expectException(NoMoneyOnAccount::class);

        $transaction = $transactionService->createTransaction($ben_nrb, $bankingAccountPrim->nrb, 500, "test", $ben_name, $ben_adress);
    }

    public function testTransactionOutside()
    {
        $bankingService = new BankService();
        $bankingService->initBank(500000);
        $this->seed(StatusSeeder::class);

        $userService = new UserService();
        $transactionService = new TransactionService();
        
        $userFakePrim = User::factory()->make();

        $ben_nrb = "PL86104253892907300740976838";
        $ben_name = "Test";
        $ben_adress = "Test 23, Test";

        $bankingAccountPrim = $userService->createUser($userFakePrim->name, $userFakePrim->email, 1000, true)->fresh();

        $generalAccount = BankingAccount::where('general_account', true)->first();
        $generalMoneyBeforeTransaction = $generalAccount->balance;

        $transaction = $transactionService->createTransaction($ben_nrb, $bankingAccountPrim->nrb, 500, "test", $ben_name, $ben_adress);

        $this->assertTrue($transaction);
        $bankingAccountPrim = $bankingAccountPrim->fresh();
        $this->assertEquals(500, $bankingAccountPrim->balance);
        $generalAccount = $generalAccount->fresh();
        $this->assertEquals($generalMoneyBeforeTransaction-500, $generalAccount->balance);
    }

    public function testTransactionOutsideButInto()
    {
        $bankingService = new BankService();
        $bankingService->initBank(500000);
        $this->seed(StatusSeeder::class);

        $userService = new UserService();
        $transactionService = new TransactionService();
        
        $userFakeBen = User::factory()->make();

        $prin_nrb = "PL86104253892907300740976838";
        $prin_name = "Test";
        $prin_adress = "Test 23, Test";

        $bankingAccountBen = $userService->createUser($userFakeBen->name, $userFakeBen->email, 1000, true)->fresh();

        $generalAccount = BankingAccount::where('general_account', true)->first();
        $generalMoneyBeforeTransaction = $generalAccount->balance;

        $transaction = $transactionService->createTransaction($bankingAccountBen->nrb, $prin_nrb, 500, "test", $prin_name, $prin_adress);

        $this->assertTrue($transaction);
        $bankingAccountBen = $bankingAccountBen->fresh();
        $this->assertEquals(1500, $bankingAccountBen->balance);
        $generalAccount = $generalAccount->fresh();
        $this->assertEquals($generalMoneyBeforeTransaction+500, $generalAccount->balance);
    }
}
