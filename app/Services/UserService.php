<?php

namespace App\Services;

use App\Exceptions\BankNotInitializedException;
use App\Models\BankingAccount;
use App\Models\User;
use Exception;

class UserService
{
    public function createUser($name, $email, $balance): User
    {
        $bankService = new BankService();
        $generalAccount = BankingAccount::where('general_account', true)->first();
        if ($generalAccount) {
            $user = User::create([
                "name" => $name,
                "email" => $email,
                "active" => 1
            ]);

            $bankService->createBankingAccount($user, !empty($balance) ? $balance : 0);

            return $user->fresh();
        } else {
            throw new BankNotInitializedException("Initial bank not found!", 500);
        }
    }
}
