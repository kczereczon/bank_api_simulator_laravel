<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class UnitOfAccountService
{
    public function sendToUnit(Transaction $transaction)
    {
        $bankService = new BankService();
        $bankNumber = $bankService->generateBankNumberWithControlSum();

        // //prepare json
        // {
        //     "BankNo": "00000000",
        //     "PaymentSum": 20,
        //     "Payments": [
        //         {
        //             "DebitedAccountNumber": "PL04000000000000000000000000",
        //             "DebitedNameAndAddress": "nadawca",
        //             "CreditedAccountNumber": "PL61109010140000071219812874",
        //             "CreditedNameAndAddress": "odbiorca",
        //             "Title": "tytul",
        //             "Amount": 20
        //         },
        // {
        //             "DebitedAccountNumber": "PL04000000000000000000000000",
        //             "DebitedNameAndAddress": "nadawca",
        //             "CreditedAccountNumber": "PL61109010140000071219812874",
        //             "CreditedNameAndAddress": "odbiorca",
        //             "Title": "tytul",
        //             "Amount": 12
        //         }
        //     ]
        // }


        $data = [
            "BankNo" => $bankNumber,
            "PaymentSum" => $transaction->amount,
            "Payments" => [
                [
                    "DebitedAccountNumber" => $transaction->nrb_prin,
                    "DebitedNameAndAddress" => $transaction->name_prin . "; " . $transaction->address_prin,
                    "CreditedAccountNumber" => $transaction->nrb_ben,
                    "CreditedNameAndAddress" => $transaction->name_ben . "; " . $transaction->address_ben,
                    "Title" => $transaction->title,
                    "Amount" => $transaction->amount
                ]
            ]
        ];

        if (!empty(env("UNIT_OF_ACCOUNT_API", null))) {
            $response = Http::post(env("UNIT_OF_ACCOUNT_API"), $data);
            $payments = $response->json("Payments");

            $transactionService = new TransactionService();
            foreach ($payments as $payment) {
                $split = explode(';', $payment['DebitedNameAndAddress']);

                $transactionService->createTransaction(
                    $payment['CreditedAccountNumber'],
                    $payment['DebitedAccountNumber'],
                    $payment['Amount']*2,
                    $payment['Title'],
                    $split[0],
                    $split[1]
                );
            }
        }
    }
}
