<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->createUser([
            'document' => 123456789
        ]);
        
        $this->assertTrue($response->exists());
    }

    public function test_user_cant_register_with_existing_document()
    {
        try{
            $user1 = $this->createUser([
                'document' => 123456789
            ]);
    
            $user2 = $this->createUser([
                'document' => 123456789
            ]);
            $this->assertTrue(false);
        }catch(\Exception $e){
            $this->assertTrue(true);
        }
    }
}