<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class UsersRegisterationTest extends TestCase
{
  use DatabaseMigrations;

  /**
   * @test
   *
   * @return void
   */
  public function UserRegistration()
  {
    $user = [
      'name' => 'Test user',
      'email' => 'test_user@testingmail.com',
      'password' => 'baddassTest#password'
    ];

    $response = $this->post('/api/v1/auth/register', $user);

    $response->assertResponseStatus(201);
  }

  /**
   * @test
   *
   * @return void
   */
  public function UserCanNotLoginBadPassword()
  {
    $user = factory(User::class)->create();
    $credentials = [
      'email' => $user->email,
      'password' => 'baddassTest#password'
    ];

    $response = $this->post('/api/v1/auth/login', $credentials);
    $response->assertResponseStatus(400);
  }

  /**
   * @test
   *
   * @return void
   */
  public function UserLoginCorrectly()
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
   * @test
   *
   * @return void
   */
  public function UserLoginWithWrongEmail()
  {
    factory(User::class)->create();
    $credentials = [
      'email' => 'fakeuser@gmail.com',
      'password' => 'badAss#password'
    ];

    $response = $this->post('/api/v1/auth/login', $credentials);
    $response->assertResponseStatus(400);
  }
}
