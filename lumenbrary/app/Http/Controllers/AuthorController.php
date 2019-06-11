<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
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
