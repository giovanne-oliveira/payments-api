<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_money()
    {
        $person = $this->createUser([
            'document' => 12345678900,
            'is_store' => false,
        ]);

        $store = $this->createUser([
            'document' => 98765432100,
            'is_store' => true,
        ]);

        $person_account = $this->createAccount([
            'userId' => $person->id,
            'isActive' => true
        ]);

        $store_account = $this->createAccount([
            'userId' => $store->id,
            'isActive' => true
        ]);

        $this->setAccountBalance($person_account, 100);
        $this->setAccountBalance($store_account, 0);

        $response = $this->post('/api/transaction/create', [
            'payer' => $person->id,
            'payee' => $store->id,
            'amount' => 50,
        ]);
    
        $this->assertTrue($response->status() == 201);
        $this->assertEquals(50, $person->fresh()->account()->first()->balance);
        $this->assertEquals(50, $store->fresh()->account()->first()->balance);
    }

    public function test_store_cant_transfer_money_to_user()
    {
        $person = $this->createUser([
            'document' => 12345678900,
            'is_store' => false,
        ]);

        $store = $this->createUser([
            'document' => 98765432100,
            'is_store' => true,
        ]);

        $person_account = $this->createAccount([
            'userId' => $person->id,
            'isActive' => true
        ]);

        $store_account = $this->createAccount([
            'userId' => $store->id,
            'isActive' => true
        ]);

        $this->setAccountBalance($person_account, 0);
        $this->setAccountBalance($store_account, 100);

        $response = $this->post('/api/transaction/create', [
            'payer' => $store->id,
            'payee' => $person->id,
            'amount' => 50,
        ]);

        $this->assertTrue($response->status() == 401);
        $this->assertEquals(0, $person->fresh()->account()->first()->balance);
        $this->assertEquals(100, $store->fresh()->account()->first()->balance);
    }

    public function test_user_cant_transfer_money_without_balance()
    {
        $person = $this->createUser([
            'document' => 12345678900,
            'is_store' => false,
        ]);

        $store = $this->createUser([
            'document' => 98765432100,
            'is_store' => true,
        ]);

        $person_account = $this->createAccount([
            'userId' => $person->id,
            'isActive' => true
        ]);

        $store_account = $this->createAccount([
            'userId' => $store->id,
            'isActive' => true
        ]);

        $this->setAccountBalance($person_account, 0);
        $this->setAccountBalance($store_account, 100);

        $response = $this->post('/api/transaction/create', [
            'payer' => $person->id,
            'payee' => $store->id,
            'amount' => 50,
        ]);

        $this->assertTrue($response->status() == 422);
        $this->assertEquals(0, $person->fresh()->account()->first()->balance);
        $this->assertEquals(100, $store->fresh()->account()->first()->balance);
    }

    public function test_revert_transaction()
    {
        $person = $this->createUser([
            'document' => 12345678900,
            'is_store' => false,
        ]);

        $store = $this->createUser([
            'document' => 98765432100,
            'is_store' => true,
        ]);

        $person_account = $this->createAccount([
            'userId' => $person->id,
            'isActive' => true
        ]);

        $store_account = $this->createAccount([
            'userId' => $store->id,
            'isActive' => true
        ]);

        $this->setAccountBalance($person_account, 100);
        $this->setAccountBalance($store_account, 0);

        $createTransactionResponse = $this->post('/api/transaction/create', [
            'payer' => $person->id,
            'payee' => $store->id,
            'amount' => 50,
        ]);

        $transactionId = $createTransactionResponse->decodeResponseJson()['data']['id'];

        $revertTransactionResponse = $this->delete('/api/transaction/'.$transactionId);

        $this->assertTrue($revertTransactionResponse->status() == 200);
        $this->assertEquals(100, $person->fresh()->account()->first()->balance);

    }
}