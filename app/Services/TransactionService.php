<?php
namespace App\Services;

use App\Models\BankingAccount;
use App\Models\Transaction;
use App\Models\Status;

class TransactionService
{
    public function createInternalTransaction(int $idBenAcc, int $idPrinAcc, float $amount, string $title){
        $status = Status::where("name", "=", "Oczekujący")->firstOrFail();
        $benAcc = BankingAccount::firstOrFail($idBenAcc);
        $prinAcc = BankingAccount::firstOrFail($idPrinAcc);

        $transaction = new Transaction();
        $transaction = $transaction->create(['nrb_ben' => $benAcc->nrb, 'name_ben' => $benAcc->name, 'amount' => $amount, 'title' => $title, 'prin_banking_account_id' => $prinAcc->id, 'ben_banking_account_id' => $benAcc->id, 'nrb_prin' => $prinAcc->nrb, 'name_prin' => $prinAcc->name, 'status_id' => $status->id]);
        $transaction = $transaction->update(['status_id'=> Status::where("name", "=", "W trakcie realizacji")->firstOrFail()->id]);

        if($prinAcc){
            $sum = $prinAcc->get('balance');
            $operationService = new OperationService();
            if ($sum >= $amount){
                $operationService->createOperationForClient($transaction);
                $transaction = $transaction->update(['status_id'=> Status::where("name", "=", "Zakończony pomyślnie")->firstOrFail()->id]);
            } else {
                $transaction = $transaction->update(['status_id'=> Status::where("name", "=", "Odzrzucony")->firstOrFail()->id]);
            }
        } else {
            $transaction = $transaction->update(['status_id'=> Status::where("name", "=", "Odzrzucony")->firstOrFail()->id]);
        }
    }

}
