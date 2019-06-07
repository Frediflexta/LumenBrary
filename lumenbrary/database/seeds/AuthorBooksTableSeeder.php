<?php

use Illuminate\Database\Seeder;

class AuthorBooksTableSeeder extends Seeder
{
      /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Author::truncate();

        factory(App\Author::class, 10)->create()->each(function ($author) {
            factory(App\Book::class, 50)->create(["author_id"=>$author->getKey()]);
        });
    }
}
