<?php

namespace App\Services;

use App\Exceptions\NoMoneyOnAccount;
use App\Models\BankingAccount;
use App\Models\Transaction;
use App\Models\Status;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionService
{
    public function createTransactionModel($prinAcc, $benAcc, float $amount, string $title, string $name = "", string $address = "")
    {
        $status = Status::where("name", "=", "Oczekujący")->firstOrFail();

        $transaction = new Transaction();
        $transaction = $transaction->create([
            'nrb_ben' => $benAcc->nrb,
            'name_ben' => $benAcc->user->name,
            'amount' => $amount,
            'title' => $title,
            'prin_banking_account_id' => $prinAcc->id,
            'ben_banking_account_id' => $benAcc->id,
            'nrb_prin' => $prinAcc->nrb,
            'name_prin' => $prinAcc->user->name,
            'status_id' => $status->id
        ]);

        $transaction->update(['status_id' => Status::where("name", "=", "W trakcie realizacji")->firstOrFail()->id]);

        return $transaction;
    }

    public function createTransaction(string $ben_nrb, string $prin_nrb, float $amount, string $title, string $name = "", string $address = "")
    {
        $outside = null;

        $benAcc = BankingAccount::with('user')->where("nrb", "=", $ben_nrb)->first();
        $prinAcc = BankingAccount::with('user')->where("nrb", "=", $prin_nrb)->first();

        if (empty($prinAcc)) {
            $prinAcc = $this->createOutsideModel($prin_nrb, $name, $address);
            $outside = "prin";
        } else {
            if($prinAcc->balance < $amount) {
                throw new NoMoneyOnAccount("Brak środków na koncie bankowym");
            }
        }
        
        if (empty($benAcc)) {
            $benAcc = $this->createOutsideModel($ben_nrb, $name, $address);
            $outside = "ben";
        }

        if(empty($outside)) {
            $transaction = $this->createTransactionModel($prinAcc, $benAcc, $amount, $title);
            $this->createOperations($transaction);
            
            return true;
        } else {
            $transaction = $this->createTransactionModel($prinAcc, $benAcc, $amount, $title, $name, $address);
            $this->createOperations($transaction, true, $outside);

            return true;
        }
    }

    public function createOperations(Transaction $transaction, bool $outside = false, string $who = "")
    {
        $operationService = new OperationService();
        $operationService->createOperationForClient($transaction);
        if ($outside) {
            $operationService->createOperationForMainAccount($transaction, $who);
        }
        $transaction->update(['status_id' => Status::where("name", "=", "Zakończony pomyślnie")->firstOrFail()->id, 'realisation_date' => new DateTime()]);
        
        return true;
    }

    public function createOutsideModel(string $ben_nrb, string $name, string $address)
    {
        return (object) [
            "id" => null,
            "nrb" => $ben_nrb,
            "user" => (object) [
                "name" => $name,
                "address" => $address
            ]
        ];
    }
}
