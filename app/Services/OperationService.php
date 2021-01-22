<?php

namespace App\Services;

use App\Exceptions\BankNotInitializedException;
use App\Models\Transaction;
use App\Models\Operation;
use App\Models\BankingAccount;
use Exception;

class OperationService
{
    public function createOperationForClient(Transaction $transaction, bool $outside = false)
    {
        $operation = Operation::create([
            "transaction_id" => $transaction->id,
            "nrb_ben" => $transaction->nrb_ben,
            "name_ben" => $transaction->name_ben,
            "address_ben" => $transaction->address_ben,
            "amount" => $transaction->amount,
            "nrb_prin" => $transaction->nrb_prin,
            "address_prin" => $transaction->address_prin,
            "name_prin" => $transaction->name_prin,
            "prin_banking_account_id" => $transaction->prin_banking_account_id,
            "ben_banking_account_id" => $transaction->ben_banking_account_id
        ]);

        $moneySent = $transaction->amount;
        if (!empty($transaction->prin_banking_account_id)) {
            $prinBankingAccount = BankingAccount::find($transaction->prin_banking_account_id);
            $prinBankingAccount->balance -= $moneySent;
            $prinBankingAccount->save();
        }


        if (!empty($transaction->ben_banking_account_id)) {
            $benBankingAccount = BankingAccount::find($transaction->ben_banking_account_id);
            $benBankingAccount->balance += $moneySent;

            $benBankingAccount->save();
        }

        return $operation;
    }

    public function createOperationForMainAccount(Transaction $transaction, string $who)
    {
        $generalAccount = BankingAccount::where('general_account', true)->first();
        $moneySent = $transaction->amount;
        
        if($who == "prin") {
            $operation = Operation::create([
                "transaction_id" => $transaction->id,
                "nrb_ben" => $generalAccount->nrb,
                "name_ben" => "Bank A",
                "amount" => $transaction->amount,
                "nrb_prin" => $transaction->nrb_prin,
                "address_prin" => $transaction->address_prin,
                "name_prin" => $transaction->name_prin,
                // "prin_banking_account_id" => $generalAccount->prin_banking_account_id,
                "ben_banking_account_id" => $generalAccount->id
            ]);

            $generalAccount->balance += $moneySent;
        } else {
            $operation = Operation::create([
                "transaction_id" => $transaction->id,
                "nrb_ben" => $transaction->nrb_ben,
                "name_ben" => $transaction->name_ben,
                "amount" => $transaction->amount,
                "nrb_prin" => $generalAccount->nrb,
                "address_ben" => $transaction->address_ben,
                "name_prin" => "Bank A",
                "prin_banking_account_id" => $generalAccount->id,
                // "ben_banking_account_id" => $generalAccount->prin_banking_account_id
            ]);


            $generalAccount->balance -= $moneySent;
        }

        $generalAccount->save();

        return $operation;
    }
}
