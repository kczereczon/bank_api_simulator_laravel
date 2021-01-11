<?php

namespace App\Http\Controllers;

use App\Exceptions\BankNotInitializedException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BankingAccount;

class UserController extends Controller
{

    public function store(StoreUserRequest $request)
    {
        $userService = new UserService();

        try{
            $user = $userService->createUser($request->name, $request->email, $request->balance);
        } catch (BankNotInitializedException $exception) {
            return response($exception->getMessage(), $exception->getCode());
        }

        $user->bankingAccounts();

        return response()->json($user);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->with('bankingAccounts')->firstOrFail();

        return response()->json(['status'=>"success", 'user' => $user]);
    }

    public function show($id)
    {
        $user = new User();
        $user = $user->with('bankingAccounts')->findOrFail($id);

        return response()->json($user);
    }

    public function getInfoBal($id)
    {
        $user = User::where('id', '=', $id)->with("bankingAccounts:id,user_id,nrb,transaction")->paginate();

        return response()->json($user);
    }
}
