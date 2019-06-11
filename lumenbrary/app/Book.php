<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'title', 'description', 'genre', 'availability', 'author_id',
  ];

  /**
   * Relationship: Author
   *
   * @return Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function author()
  {
    return $this->belongsTo('App\Author');
  }
}
