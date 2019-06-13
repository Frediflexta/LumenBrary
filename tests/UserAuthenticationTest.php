<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class UserAuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * successfully register a user
     *
     * @return void
     */
    public function testUserRegistrationSuccess()
    {
        $user = [
            'name' => 'Test User',
            'email' => 'testemail@trythis.com',
            'password' => 'badAss#tesPassword'
        ];
        $response = $this->post('/api/v1/auth/register', $user);
        $response->assertResponseStatus(201);
    }

    /**
     * successfully login a user
     *
     * @return void
     */
    public function testUserLoginSuccess()
    {
        $user = factory(User::class)->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'badAss#password'
        ];

        $response = $this->post('/api/v1/auth/login', $credentials);
        $response->assertResponseStatus(200);
    }

    /**
     * fail login a user
     *
     * @return void
     */
    public function testUserLoginWrongPassword()
    {
        $user = factory(User::class)->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'badAss#Wrongpassword'
        ];

        $response = $this->post('/api/v1/auth/login', $credentials);
        $response->assertResponseStatus(400);
    }

        /**
     * fail login a user
     *
     * @return void
     */
    public function testUserLoginInvalidCredentials()
    {
        $user = factory(User::class)->create();

        $credentials = [
            'email' => 'nonexisting@testuser.com',
            'password' => 'badAss#Wrongpassword'
        ];

        $response = $this->post('/api/v1/auth/login', $credentials);
        $response->assertResponseStatus(400);
    }
}
