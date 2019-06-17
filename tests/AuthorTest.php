<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\User;
use App\Author;

class AuthorTest extends TestCase
{
  use DatabaseMigrations;

  public function setUp() : void
  {
    parent::setUp();

    $user = factory(User::class)->create();
    $credentials = [
      'email' => $user->email,
      'password' => 'badAss#password'
    ];

    $result = $this->post('/api/v1/auth/login', $credentials)->response->getContent();
    $this->token = json_decode(collect($result)->toArray()[0])->data->token;
  }

  /**
   * Successfully get all authors
   *
   * @return void
   */
  public function testViewAllAuthors()
  {
    $authors = factory(Author::class)->create();
    $response = $this->get('/api/v1/authors');

    $response->assertResponseStatus(200);
    $response->seeJson([
      'name' => $authors->name,
      'email' => $authors->email,
      'bio' => $authors->bio,
      'books' => []
    ]);
  }

  /**
   * Successfully sort authors
   *
   * @return void
   */
  public function testSortAuthors()
  {
    $authors = factory(Author::class)->create();
    $response = $this->get('/api/v1/authors?sort=name_desc');

    $response->assertResponseStatus(200);
    $response->seeJson([
      'name' => $authors->name,
      'email' => $authors->email,
      'bio' => $authors->bio,
      'books' => []
    ]);
  }

  /**
   * Successfully filters authors
   *
   * @return void
   */
  public function testFilterAuthors()
  {
    $authors = factory(Author::class)->create();
    $response = $this->get("/api/v1/authors?name={$authors->name}");

    $response->assertResponseStatus(200);
    $response->seeJson([
      'name' => $authors->name,
      'email' => $authors->email,
      'bio' => $authors->bio,
      'books' => []
    ]);
  }

  /**
   * Successfully paginate by limit and offset
   *
   * @return void
   */
  public function testLimitOffsetAuthors()
  {
    factory(Author::class, 9)->create();
    $response = $this->get('/api/v1/authors?limit=2&offset=2');

    $response->assertResponseStatus(200);
  }

  /**
   * Successfully view an authors
   *
   * @return void
   */
  public function testViewSingleAuthor()
  {
    $authors = factory(Author::class)->create();
    $response = $this->get('/api/v1/authors/1');

    $response->assertResponseStatus(200);
    $response->seeJson([
      'name' => $authors->name,
      'email' => $authors->email,
      'bio' => $authors->bio,
    ]);
  }

  /**
   * fail to view an authors
   *
   * @return void
   */
  public function testViewSingleAuthorNotExisting()
  {
    factory(Author::class)->create();
    $response = $this->get('/api/v1/authors/9');

    $response->assertResponseStatus(404);
    $response->seeJson([
      'error' => 'Author does not exist'
    ]);
  }

  /**
   * create new author
   *
   * @return void
   */
  public function testCreatingAuthor()
  {
    $header =  ['token' => $this->token];

    $createAuthor = [
      'name' => 'new author',
      'email' => 'new_authors@gmail.com',
      'bio' => 'Fruitcake sugar plum jelly beans jelly-o caramels chocolate bar bear claw gingerbread     topping. Donut dessert icing topping. '
    ];

    $response = $this->post('/api/v1/authors', $createAuthor, $header);
    $response->assertResponseStatus(201);
    $response->seeJson($createAuthor);
  }

  public function testUpdatingAuthor()
  {
    factory(Author::class)->create();
    $header = ['token' => $this->token];

    $updatedAuthor = [
      'name' => 'updated author',
      'email' => 'updated_authors@gmail.com',
      'bio' => 'Fruitcake sugar plum jelly beans jelly-o caramels chocolate bar bear claw gingerbread     topping. Donut dessert icing topping.'
    ];

    $response = $this->put('/api/v1/authors/1', $updatedAuthor, $header);
    $response->assertResponseStatus(200);
    $response->seeJson($updatedAuthor);
  }

  public function testDestroyAuthor()
  {
    $author = factory(Author::class)->create();
    $header = ['token' => $this->token];

    $response = $this->delete("/api/v1/authors/{$author->id}", [], $header);
    $response->assertResponseStatus(204);
  }

  public function testDestroyAuthorNonExisting()
  {
    $author = factory(Author::class)->create();
    $header = ['token' => $this->token];

    $response = $this->delete('/api/v1/authors/9', [], $header);
    $response->assertResponseStatus(404);
  }
}