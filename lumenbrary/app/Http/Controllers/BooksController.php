<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class BooksController extends Controller
{
  /**
   * Retrieve the book for the given ID.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    if(!Book::find($id)) {
      return response()->json(['message' => 'Book does not exist'], 404);
    }
    return response()->json(Book::find($id), 200);
  }

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

  /**
   * Delete a book
   *
   * @param int $id
   * @return void
   */
  public function destroy($id)
  {
    if(!Book::find($id)) {
      return response()->json(['message' => 'Book does not exist'], 404);
    }

    Book::findOrFail($id)->delete();
    return response()->json(['message' => 'Book has been deleted successfully'], 204);
  }
}
