<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{

  protected $primaryKey = 'id';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'bio'
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = ['created_at', 'updated_at'];

  /**
   * Relationship: Books
   *
   * @return Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function books()
  {
    return $this->hasMany('App\Book', 'author_id', 'id');
  }
}
