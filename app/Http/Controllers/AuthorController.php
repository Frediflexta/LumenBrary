<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
  /**
   * Returns all the Authors
   *
   * @return void
   */
  public function index(Request $request)
  {
    $authors = Author::with('books');

    if($request->has('sort')) {
      $values = explode('_', $request->sort);
      $authors->orderBy($values[0], $values[1]);
    }

    if($request->has('name')) {
      $keyword = strtolower($request->name);
      $authors->whereRaw('LOWER(name) like (?)', "%{$keyword}%");
    }

    if($request->has('limit')) {
      $authors->limit($request->limit);
    }

    if($request->has('offset')) {
      $authors->offset($request->offset);
    }

    return $authors->get();
  }

  /**
   * Retrieve the auhtor for the given ID.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    if(empty(Author::find($id))) {
      return response()->json(['error' => 'Author does not exist'], 404);
    }
    return response()->json(Author::find($id), 200);
  }

  /**
   * Store a new author.
   *
   * @param  Request  $request
   * @return Response
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'name' => 'required',
      'email' => 'required|email|unique:authors',
      'bio' => 'required',
    ]);

    $author = Author::create($request->all());
    return response()->json($author, 201);
  }

  /**
   * Update the given author.
   *
   * @param  Request  $request
   * @param  string  $id
   * @return Response
  */
  public function update(Request $request, $id)
  {
    $author = Author::findOrFail($id);

    $this->validate($request, [
      'name' => 'required',
      'email' => 'required|email',
      'bio' => 'required',
    ]);
    $author->update($request->all());

    return response()->json($author, 200);
  }

  /**
   * Delete an Author
   *
   * @param int $id
   * @return void
   */
  public function destroy($id)
  {
    if(!Author::find($id)) {
      return response()->json(['message' => 'Author not found'], 404);
    }

    Author::findOrFail($id)->delete();
    return response()->json([], 204);
  }
}
