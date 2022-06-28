<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TransactionPostRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function listAll(): TransactionCollection
    {
        return new TransactionCollection($this->transactionRepository->all());
    }

    public function create(TransactionPostRequest $request)
    {
        try{
            return new TransactionResource($this->transactionRepository->createTransaction([
                'payerId' => $request->payer,
                'payeeId' => $request->payee,
                'amount' => $request->amount
            ]));
        }catch(\Exception $e){
            die(var_dump($e));
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
