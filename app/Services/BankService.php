<?php

namespace App\Services;

use App\Models\BankingAccount;
use App\Models\User;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Mix;
use phpDocumentor\Reflection\Types\Boolean;

class BankService
{

    public function getGeneralAccount()
    {
        $general = BankingAccount::where('general_account', true)->first();

        if($general) {
            return $general;
        } else {
            return false;
        }
    }

    public function initBank(int $balance) : User
    {
        $user = User::create([
            "name" => "Bank A",
            "email" => "bank-a@bank.com",
            "active" => 1
        ]);

        $this->createBankingAccount($balance, $user, true);

        return $user;
    }

    public function createBankingAccount(int $balance = 0, User $user, bool $general = false) : BankingAccount
    {
        $faker = Container::getInstance()->make(Generator::class);

        $nrb = $faker->regexify('[0-9]{2}', true) . env('BANK_NUMBER', "false") . $faker->unique()->regexify('[0-9]{16}', true);

        $bankingAccount = $user->bankingAccounts()->create([
            "balance" => $balance,
            "nrb" => $nrb,
            "user" => $user->id,
            "general_account" => $general
        ]);

        if(!$general){
            $generalAccount = BankingAccount::where('general_account', true)->first();

            $generalAccount->update([
                'balance' => $generalAccount->balance+$balance
            ]);
        }

        return $bankingAccount;
    }
}
