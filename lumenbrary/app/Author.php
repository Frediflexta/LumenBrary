<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
      'name', 'email', 'bio'
  ];


  /**
   * Relationship: Books
   *
  //  * @return Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function books()
  {
      return $this->hasMany('App\Book', 'author_id');
  }
}
