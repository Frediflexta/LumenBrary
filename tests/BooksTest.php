<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\User;
use App\Author;
use App\Book;

class BooksTest extends TestCase
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

  public function testViewAllBooks()
  {
    $books = factory(Book::class)->create();
     $response = $this->get('/api/v1/books');
     $response->assertResponseStatus(200);
     $response->seeJson([
       'title' => $books->title
     ]);
  }

  public function testSortBooks()
  {
    $books = factory(Book::class)->create();
    $response = $this->get('api/v1/books?sort=genre_asc');

    $response->assertResponseStatus(200);
    $response->seeJson([
      'title' => $books->title
    ]);
  }

  public function testFilterBooks()
  {
    $books = factory(Book::class)->create();

    $response = $this->get('/api/v1/books?availability=false');
    $response->assertResponseStatus(200);
  }

  public function testSearchBooksByTitle()
  {
    $books = factory(Book::class)->create();

    $response = $this->get("api/v1/books?search={$books->title}");
    $response->assertResponseStatus(200);
  }

  public function testBooksPaginationByLimitsOffset()
  {
    factory(Book::class, 50)->create();

    $response = $this->get('/api/v1/books?limit=5&offset=10');
    $response->assertResponseStatus(200);
  }

  public function testViewOneBook()
  {
    factory(Book::class)->create();

    $response = $this->get('/api/v1/books/1');
    $response->assertResponseStatus(200);
  }

  public function testInvalidParamsOnBooks()
  {
    factory(Book::class)->create();

    $response = $this->get('/api/v1/books/one');
    $response->assertResponseStatus(400);
    $response->seeJson([
      'error' => 'Params must be an integer'
    ]);
  }

  public function testNonExistingBook()
  {
    factory(Book::class)->create();

    $response = $this->get('/api/v1/books/10');
    $response->assertResponseStatus(404);
    $response->seeJson([
      'message' => 'Book does not exist'
    ]);
  }

  public function testCreatingBooksWithoutAStoredAuthor()
  {
    $header = ['token' => $this->token];
    $newBook = [
      'title' => 'test this',
      'description' => 'destriptive test',
      'genre' => 'ficton',
      'availability' => true,
      'author_id' => 1
    ];

    $response = $this->post('/api/v1/books', $newBook, $header);
    $response->assertResponseStatus(400);
  }

  public function testCreatingBooks()
  {
    $header = ['token' => $this->token];
    $author = factory(Author::class)->create();

    $newBook = [
      'title' => 'test this',
      'description' => 'destcriptive test',
      'genre' => 'fiction',
      'availability' => true,
      'author_id' => $author->id
    ];

    $response = $this->post('/api/v1/books', $newBook, $header);
    $response->assertResponseStatus(201);
  }

  public function testUpdatingBookWithInvalidParams()
  {
    $header = ['token' => $this->token];
    $author = factory(Author::class)->create();

    $newBook = [
      'title' => 'test this',
      'description' => 'destcriptive test',
      'genre' => 'fiction',
      'availability' => true,
      'author_id' => $author->id
    ];

    $response = $this->put('/api/v1/books/one', $newBook, $header);
    $response->assertResponseStatus(400);
  }

  public function testEditBooks()
  {
    $header = ['token' => $this->token];
    $author = factory(Author::class)->create();
    factory(Book::class)->create();
    $newBook = [
      'title' => 'test this',
      'description' => 'destcriptive test',
      'genre' => 'fiction',
      'availability' => false,
      'author_id' => $author->id
    ];
    $response = $this->put('/api/v1/books/1', $newBook, $header);
    $response->assertResponseStatus(200);
  }

  public function testDestroyBooksNonExisting()
  {
    $author = factory(Author::class)->create();
    factory(Book::class)->create();
    $header = ['token' => $this->token];

    $response = $this->delete('/api/v1/books/9', [], $header);
    $response->assertResponseStatus(404);
  }

  public function testDestroyAuthor()
  {
    $book = factory(Book::class)->create();
    $header = ['token' => $this->token];

    $response = $this->delete("/api/v1/books/{$book->id}", [], $header);
    $response->assertResponseStatus(204);
  }

  public function testTokenNotProvided()
  {
    $book = factory(Book::class)->create();
    $header = ['token' => ''];

    $response = $this->delete("/api/v1/books/{$book->id}", [], $header);
    $response->assertResponseStatus(401);
    $response->seeJson([
      'message' => 'Please provide a token'
    ]);
  }

  public function testTokenExpired()
  {
    $book = factory(Book::class)->create();
    $header = ['token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOls2LCIkMnkkMTAkNjY0c1JJekYwMU0wRzRmMlwvME0xQ2VkWk1McWdpMVQ0YTUxVW5laUZUckFYOTVicm43XC9BUyIsInJheV9jaGFybGVzQGdtYWlsLmNvbSJdLCJpYXQiOjE1NjA3Nzc0NDYsImV4cCI6MTU2MDc4MTA0Nn0.c4fIisDxZ600bOUN0K2v99dTZ6zEUTAKUiSEGqe8jV4'];

    $response = $this->delete("/api/v1/books/{$book->id}", [], $header);
    $response->assertResponseStatus(400);
  }

  public function testTokenDecodingError()
  {
    $book = factory(Book::class)->create();
    $header = ['token' => 'icm43XC9BUyIsInJheV9jaGFybGVzQGdtYWlsLmNvbSJdLCJpYXQiOjE1NjA3Nzc0NDYsImV4cCI6MTU2MDc4MTA0Nn0.c4fIisDxZ600bOUN0K2v99dTZ6zEUTAKUiSEGqe8jV4'];

    $response = $this->delete("/api/v1/books/{$book->id}", [], $header);
    $response->assertResponseStatus(400);
  }
}