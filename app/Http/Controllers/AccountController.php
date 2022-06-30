<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AccountRepository;
use App\Http\Resources\AccountResource;
use App\Http\Requests\AccountPostRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getById(string $account): AccountResource
    {
        try{
            $account = Account::find($account);
            return new AccountResource($account);
        }catch (\Exception $exception){
            return response()->json(["message" => $exception->getMessage()], 500);
        }
    }

    public function create(AccountPostRequest $request): AccountResource
    {
        try{
            return new AccountResource($this->accountRepository->createAccount($request->all()));
        }catch(\Exception $e){
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
