<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    // create users
    public function testRegisterSuccess()
    {
        $this->post('api/users', [
            'username' => "SmokeBeef",
            'password' => "12345678",
            'name' => "Deva Nanda Alfarizi",
        ])->assertStatus(201)->assertJson([
            "data" => [
                'username' => "SmokeBeef",
                'name' => "Deva Nanda Alfarizi",
            ]
        ]);
    }
    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => "",
            'password' => "",
            'name' => ""
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "username" => [
                    "The username field is required."
                ],
                "password" => [
                    "The password field is required."
                ],
                "name" => [
                    "The name field is required."
                ]
            ]
        ]);
    }
    public function testRegisterUsernameAlreadyExist()
    {
        $this->testRegisterSuccess();
        $this->post('api/users', [
            'username' => "SmokeBeef",
            'password' => "12345678",
            'name' => "Deva Nanda Alfarizi",
        ])->assertStatus(400)->assertJson([
            "errors" => [
                'username' => [
                    'username already registered'
                ]
            ]
        ]);
    }
    // end of create users
    
}
