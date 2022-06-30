<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuthorizeTransactionService;
use App\Services\NotificationService;
use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use App\Services\ServiceTransactionValidate;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUser(array $options = [])
    {
        return User::factory()->create($options);
    }

    protected function createAccount(array $options = [])
    {
        return Account::factory()->create($options);
    }

    protected function createTransaction(array $options = [])
    {
        //return Transaction::factory()->create($options);
        $authorizeTransactionService = new AuthorizeTransactionService();
        $notificationService = new NotificationService();
        $accountRepository = new AccountRepository();
        $userRepository = new UserRepository();
        $serviceTransactionValidate = new ServiceTransactionValidate($accountRepository, $userRepository);
        $repo = new TransactionRepository($authorizeTransactionService, $notificationService, $accountRepository, $userRepository, $serviceTransactionValidate);

        return $repo->createTransaction($options);
    }

    protected function setAccountBalance(Account $account, $amount)
    {
        $account->balance = $amount;
        $account->save();
    }

    protected function saveEntity($model)
    {
        $model->save();
    }
}
