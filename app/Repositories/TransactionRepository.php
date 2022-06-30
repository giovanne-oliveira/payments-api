<?php

namespace App\Repositories;

use App\Exceptions\AuthorizeServiceUnavailableException;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\AuthorizeTransactionService;
use App\Services\NotificationService;
use App\Services\ServiceTransactionValidate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    protected $serviceAuthorizeTransaction;
    protected $serviceNotification;
    protected $accountRepository;
    protected $userRepository;
    protected $validateService;
    public function __construct(
        AuthorizeTransactionService $serviceAuthorizeTransaction,
        NotificationService $serviceNotification,
        AccountRepository $accountRepository,
        UserRepository $userRepository,
        ServiceTransactionValidate $validateService
    ) {
        $this->serviceAuthorizeTransaction = $serviceAuthorizeTransaction;
        $this->serviceNotification = $serviceNotification;
        $this->accountRepository = $accountRepository;
        $this->userRepository = $userRepository;
        $this->validateService = $validateService;
    }

    public function all(): AnonymousResourceCollection
    {
        return TransactionResource::collection(Transaction::all());
    }
    public function createTransaction(array $data): Transaction
    {
        $this->validateService->validateExecute($data);

        if (!$this->verifyAuthorizeTransaction()) {
            throw new AuthorizeServiceUnavailableException('The authorization service is unavailable. Please try again later.', 503);
        }
        $payer = $this->userRepository->find($data['payerId']);
        $payee = $this->userRepository->find($data['payeeId']);

        $transaction = $this->makeTransaction($payer, $payee, $data['amount']);

        $this->sendNotification();

        return $transaction;
    }

    public function makeTransaction($payer, $payee, $value): Transaction
    {
        $payload = [
            'payerId' => $payer->account->id,
            'payeeId' => $payee->account->id,
            'amount' => $value
        ];
        return DB::transaction(function () use($payer, $payee, $payload) {
            $transaction = Transaction::create($payload);
            $this->accountRepository->withdraw($payer->account, $payload['amount']);
            $this->accountRepository->deposit($payee->account, $payload['amount']);
            return $transaction;
        });
    }

    public function verifyAuthorizeTransaction():bool
    {
       $response = $this->serviceAuthorizeTransaction->verifyAuthorizeTransaction();
       return $response['message'] === 'Autorizado';
    }

    public function sendNotification():bool
    {
        $response = $this->serviceNotification->sendNotification();
        return $response['message'] === 'Success';
    }
}