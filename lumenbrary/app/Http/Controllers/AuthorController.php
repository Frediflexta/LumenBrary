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
}
