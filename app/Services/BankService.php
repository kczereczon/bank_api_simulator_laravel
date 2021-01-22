<?php

namespace App\Services;

use App\Exceptions\NotFoundBankNumberInConfig;
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

        $this->createBankingAccount($user, $balance, true);

        return $user;
    }

    public function generateControlSumBankNumber(string $number) : int
    {
        //weight specified by iban standard
        $weights = [3, 9, 7, 1, 3, 9, 7];

        $sum = 0;

        if(!$number) {
            throw new NotFoundBankNumberInConfig("Not found bank number in config env('BANK_NUMBER'), make sure that you submited this key into .env file");
        }

        //sum all of weights time digits for obtain the control sum
        for ($i=0; $i < strlen($number); $i++) {
            $sum += $number[$i]*$weights[$i];
        }

        $controlSum = 10 - $sum%10;

        return $controlSum;
    }

    public function generateBankNumberWithControlSum(string $customNumber = NULL) : string
    {
        $bankNumber = $customNumber ? $customNumber : env('BANK_NUMBER', false);

        $controlSum = $this->generateControlSumBankNumber($bankNumber);

        return $bankNumber.$controlSum;
    }

    public function validateBankNumber(string $number) : bool
    {
        $controlSumCurrent = substr($number, -1);
        $controlSumExpected = $this->generateControlSumBankNumber(substr($number, 0, -1));

        if($controlSumCurrent == $controlSumExpected) {
            return true;
        }

        return false;
    }

    private function calcMod97(string $iban)
    {
        $mod = 0;
        for ($i=0; $i < round(strlen($iban)/6); $i++) {
            $currentString = substr($iban, $i*6, 6);
            $currentStringWithModulo = $mod.$currentString;
            $mod = $i ? ($mod . substr($iban, $i*6, 6))%97 : $currentString%97;
        }

        return $mod;
    }

    public function generateControlSumOfIban(string $iban) : string
    {
        $mod = $this->calcMod97($iban);

        return sprintf("%02d", 98 - $mod);
    }

    public function generateIban() : string
    {
        //bbbbbbb yyyymmdd => 16
        $iban = $this->generateBankNumberWithControlSum() . date("Ymd");

        $countOfBankingAccounts = BankingAccount::all()->count()+1;
        // 00000001
        $iban .= sprintf("%08d", $countOfBankingAccounts);
        // 25
        $p = ord("P")-55;
        // 21
        $l = ord("L")-55;

        $ibanWithCountry = $iban.$p.$l."00";

        $controlSum = $this->generateControlSumOfIban($ibanWithCountry);

        return "PL" . $controlSum . $iban;
    }

    public function validateIbanNumber(string $iban) : bool
    {
        $controlSum = substr($iban, 2, 2);
        $number = substr($iban, 4, 28);

        $p = ord("P")-55;
        $l = ord("L")-55;

        $toValidate = $this->calcMod97($number.$p.$l.$controlSum);

        if($toValidate == 1) {
            return true;
        }

        return false;
    }

    public function createBankingAccount(User $user, int $balance = 0, bool $general = false) : BankingAccount
    {
        $bankService = new BankService();

        $nrb = $bankService->generateIban();

        $bankingAccount = $user->bankingAccounts()->create([
            "nrb" => $nrb,
            "balance" => $general ? $balance : 0,
            "general_account" => $general
        ]);

        if(!$general){
            $generalAccount = BankingAccount::where('general_account', true)->first();
            $transactionService = new TransactionService();
            $transactionService = $transactionService->createTransaction($bankingAccount->nrb, $generalAccount->nrb, $balance, "Wpłata inicjalizująca");
        }

        return $bankingAccount;
    }
}
