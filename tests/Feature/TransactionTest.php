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

        /*$response = $this->createTransaction([
            'payerId' => $person_account->id,
            'payeeId' => $store_account->id,
            'amount' => 50,
        ]);*/

        $response = $this->post('/api/transaction/create', [
            'payer' => $person->id,
            'payee' => $store->id,
            'amount' => 50,
        ]);
    
        $this->assertTrue($response->status() == 201);
        $this->assertEquals(50, $person->fresh()->account()->first()->balance);
        $this->assertEquals(50, $store->fresh()->account()->first()->balance);
    }
}