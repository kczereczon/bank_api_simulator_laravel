<?php

namespace Tests\Unit;

use App\Models\BankingAccount;
use App\Models\User;
use App\Services\BankService;
use App\Services\OperationService;
use App\Services\TransactionService;
use App\Services\UserService;
use Database\Seeders\StatusSeeder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testInternalOperation()
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
        $transaction = $transactionService->createTransactionModel($bankingAccountPrim, $bankingAccountBen, 500, "test");
        
        $operationService = new OperationService();
        $operationService->createOperationForClient($transaction);
        
        $bankingAccountBen = $bankingAccountBen->fresh();
        $bankingAccountPrim = $bankingAccountPrim->fresh();

        $this->assertEquals(1500, $bankingAccountBen->balance);
        $this->assertEquals(500, $bankingAccountPrim->balance);
    }

    public function testOutsideOperation()
    {
        $bankingService = new BankService();
        $bankingService->initBank(500000);
        $this->seed(StatusSeeder::class);
        
        $userService = new UserService();
        
        $userFakePrim = User::factory()->make();
        
        $generalAccount = BankingAccount::where('general_account', true)->first();
        $generalMoneyBeforeTransaction = $generalAccount->balance;

        $bankingAccountPrim = $userService->createUser($userFakePrim->name, $userFakePrim->email, 1000, true)->fresh();
        $generalAccount = $generalAccount->fresh();

        $this->assertEquals($generalMoneyBeforeTransaction-1000, $generalAccount->balance);
        $this->assertEquals(1000, $bankingAccountPrim->balance);

        $ben_nrb = "PL86104253892907300740976838";
        $ben_name = "Test";
        $ben_address = "Test 23, Test";

        $generalMoneyBeforeTransaction = $generalMoneyBeforeTransaction - 1000;

        $transactionService = new TransactionService();
        $bankAccountBen = $transactionService->createOutsideModel($ben_nrb, $ben_name, $ben_address);
        $transaction = $transactionService->createTransactionModel($bankingAccountPrim, $bankAccountBen, 500, "test", $ben_name, $ben_address);
        
        $operationService = new OperationService();
        $operationService->createOperationForClient($transaction, true);
        $operationService->createOperationForMainAccount($transaction, "ben");
        
        $bankingAccountPrim = $bankingAccountPrim->fresh();
        $generalAccount = $generalAccount->fresh();


        $this->assertEquals(500, $bankingAccountPrim->balance);
        $this->assertEquals($generalMoneyBeforeTransaction-500, $generalAccount->balance);
    }

    public function testOutsideOperationButToInside()
    {
        $bankingService = new BankService();
        $bankingService->initBank(500000);
        $this->seed(StatusSeeder::class);
        
        $userService = new UserService();
        
        $userFakeBen = User::factory()->make();
        $generalAccount = BankingAccount::where('general_account', true)->first();
        $generalMoneyBeforeTransaction = $generalAccount->balance;

        $bankingAccountBen = $userService->createUser($userFakeBen->name, $userFakeBen->email, 1000, true)->fresh();
        $generalAccount = $generalAccount->fresh();

        $this->assertEquals(1000, $bankingAccountBen->balance);
        $this->assertEquals($generalMoneyBeforeTransaction-1000, $generalAccount->balance);

        $generalMoneyBeforeTransaction = $generalAccount->balance;

        $prin_nrb = "PL86104253892907300740976838";
        $prin_name = "Test";
        $prin_address = "Test 23, Test";

        $transactionService = new TransactionService();
        $bankAccountPrin = $transactionService->createOutsideModel($prin_nrb, $prin_name, $prin_address);
        $transaction = $transactionService->createTransactionModel($bankAccountPrin, $bankingAccountBen, 500, "test", $prin_name, $prin_address);
        
        $operationService = new OperationService();
        $operationService->createOperationForClient($transaction, true);
        $operationService->createOperationForMainAccount($transaction, "prin");
        
        $bankingAccountBen = $bankingAccountBen->fresh();
        $generalAccount = $generalAccount->fresh();


        $this->assertEquals(1500, $bankingAccountBen->balance);
        $this->assertEquals($generalMoneyBeforeTransaction+500, $generalAccount->balance);
    }
}
