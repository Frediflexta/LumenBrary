<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
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
}
