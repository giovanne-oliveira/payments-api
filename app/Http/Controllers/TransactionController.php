<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionDeleteRequest;
use App\Http\Requests\TransactionPostRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository;
use App\Models\Transaction;
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

    public function getById(Transaction $transaction): TransactionResource
    {
        try{
            return new TransactionResource($transaction);
        }catch (\Exception $exception){
            return response()->json(["message" => $exception->getMessage()], 500);
        }
    }

    public function delete(TransactionDeleteRequest $request): JsonResponse
    {
        try {
            $this->transactionRepository->deleteTransaction($request->transactionId);
            return response()->json(["success" => true, "message" => "Transaction deleted successfully"], 200);
        } catch(\Exception $exception){
            return response()->json(["message" => "Unexpected error occurred when deleting the transaction. Try again in a few seconds."], 500);
        }
    }
}
