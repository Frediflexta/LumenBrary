<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class BooksController extends Controller
{
  /**
   * Store a new book.
   *
   * @param  Request  $request
   * @return Response
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'title' => 'required|unique:books',
      'description' => 'required',
      'genre' => 'required',
      'availability' => 'required',
      'author_id' => 'required'
    ]);

    $book = Book::create($request->all());
    return response()->json($book, 201);
  }

  /**
   * Update the given book.
   *
   * @param  Request  $request
   * @param  string  $id
   * @return Response
  */
  public function update(Request $request, $id)
  {
    $book = Book::findOrFail($id);

    $this->validate($request, [
      'title' => 'required',
      'description' => 'required',
      'genre' => 'required',
      'availability' => 'required',
      'author_id' => 'required'
    ]);
    $book->update($request->all());

    return response()->json($book, 200);
  }
}
