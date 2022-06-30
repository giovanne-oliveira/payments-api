<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{
    public function __construct()
    {

    }

    public function createAccount(array $data): Account
    {
        return Account::create(
            [
                'userId' => $data['ownerId'],
                'balance' => $data['initialBalance'],
                'isActive' => 0
            ]
        );
    }

    public function activateAccount(array $data): Account
    {
        $account = Account::find($data['accountId']);
        $account->isActive = 1;
        $account->save();
        return $account;
    }

    public function deposit(Account $account, $value): void
    {
        $account->update([
            'balance' => $account->balance + $value,
        ]);
    }

    public function withdraw(Account $account, $value): void
    {
        $account->update([
            'balance' => $account->balance - $value,
        ]);
    }

    public function checkAccountBalance(Account $account, $value): bool
    {
        return $account->balance >= $value;
    }

    public function checkAccountExists($user_id): bool
    {
        $account = Account::where('user_id', $user_id)->first();
        return (bool)$account;
    }
}