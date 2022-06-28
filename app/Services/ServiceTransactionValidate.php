<?php

namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Exceptions\UserTypeNotAuthorizedException;
use App\Models\Account;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;

class ServiceTransactionValidate
{
    protected $accountRepository;
    protected $userRepository;

    public function __construct( AccountRepository $accountRepository, UserRepository $userRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->userRepository = $userRepository;
    }

    public function validateExecute(array $data)
    {
        $this->validatePayerIsCommercial($data['payerId']);
        $this->validateCheckBalance($data['payerId'], $data['amount']);
    }

    private function validatePayerIsCommercial($user)
    {
        if ($this->userRepository->isCommercial($user)) {
            throw new UserTypeNotAuthorizedException('This user is not allowed to make outbound transactions.', 401);
        }
    }

    private function validateCheckBalance($user, $value)
    {
        if (!$this->accountRepository->checkAccountBalance(Account::where('userId', $user)->first(), $value)) {
            throw new InsufficientFundsException('The user has no balance to complete this transaction', 422);
        }
    }
}