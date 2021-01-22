<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Models\BankingAccount;
use App\Models\Transaction;
use App\Services\BankService;
use App\Services\OperationService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $tran = new Transaction();
        $tran = $tran->where("ben_banking_account_id", $id)->orWhere("prin_banking_account_id", $id)->orderBy('realisation_date', 'desc')->paginate(10);

        return response()->json($tran);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tran = new Transaction();
        $tran = $tran->findOrFail($id);

        return response()->json($tran);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createTransaction(CreateTransactionRequest $request, $id)
    {
        $transactions = new Transaction();
        /** @var Builder $prin_account */
        $prin_account = new BankingAccount();
        $prin_account = $prin_account->findOrFail($id);
        $ben_account = new BankingAccount();

        $bankingAccountService = new BankService();
        $transactionService = new TransactionService();

        $transaction = null;

        if($prin_account->balance >= $request->amount) { 
            if ($bankingAccountService->validateIbanNumber($request->nrb_ben)) {
                $ben_account = $ben_account->where('nrb', 'LIKE', "%" . $request->nrb_ben)->first();
                $transaction = $transactionService->createTransaction($request->nrb_ben, $prin_account->nrb, $request->amount, $request->title);
            } else {
                $transaction = $transactionService->createTransaction($request->nrb_ben, $prin_account->nrb, $request->amount, $request->title, $request->name_ben, $request->address_ben);
            }
        }
        response()->json('Sukces', 200);
    }
}
