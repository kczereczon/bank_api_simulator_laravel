<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundBankNumberInConfig;
use App\Http\Requests\CreateTransactionRequest;
use App\Models\BankingAccount;
use App\Models\Transaction;
use App\Services\BankService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use function PHPUnit\Framework\throwException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function createTransaction(CreateTransactionRequest $request){
        $transactions = new Transaction();
        /** @var Builder $prin_account */
        $prin_account = new BankingAccount();
        $ben_account = new BankingAccount();

        $bankingAccountService = new BankService();
        $transactionService = new TransactionService();

        if($bankingAccountService->validateBankNumber($request->nrb_prin)){
            $prin_account = $prin_account->where('nrb', 'LIKE', "%". $request->nrb_prin)->firstOrFail();

        }else{
            throw new Exception("Nieprawidłowy numer konta zleceniodawcy!");
        }

        if($bankingAccountService->validateBankNumber($request->nrb_ben)){
            $ben_account = $ben_account->where('nrb', 'LIKE', "%". $request->nrb_ben)->first();

        }else{
            throw new Exception("Nieprawidłowy numer konta beneficjenta!");
        }

        if($ben_account!=null){
            $transactionService->createInternalTransaction($ben_account->id,$prin_account->id, $request->amount, $request->title);
            response()->json('Sukces',200);
        }

       
    }


}
