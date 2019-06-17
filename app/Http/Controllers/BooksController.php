<?php

namespace App\Http\Controllers;

use App\Book;
use App\Author;
use Illuminate\Http\Request;

class BooksController extends Controller
{
  /**
   * Returns all the books
   *
   * @return void
   */
  public function index(Request $request)
  {
    $books = Book::with('author');
    if($request->has('availability')) {
      $books->where('availability', $request->availability === 'true' ? true: false);
    }

    if($request->has('sort')) {
      $values = explode('_', $request->sort);
      $books->orderBy($values[0], $values[1]);
    }

    if($request->has('search')) {
      $keyword = strtolower($request->search);
      $books->whereRaw('LOWER(title) like (?)', "%{$keyword}%");
    }

    if ($request->has('limit')) {
      $books->limit($request->limit);
    }

    if ($request->has('offset')) {
      $books->offset($request->offset);
    }

    return $books->get();
  }

  /**
   * Retrieve the book for the given ID.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    if((int)$id === 0) {
      return response()->json(['error' => 'Params must be an integer'], 400);
    }

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

    if(!Author::find($request->author_id)){
      return response()->json(['error' => 'Author does not exist, please create an author first'], 400);
    }

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
    if((int)$id === 0) {
      return response()->json(['error' => 'Params must be an integer'], 400);
    }

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
