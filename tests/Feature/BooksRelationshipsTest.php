<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Tests\Testcase;


class BooksRelationshipsTest extends Testcase
{
  use DatabaseMigrations;

  public function test_it_returns_a_relationship_to_authors_adhering_to_json_api_spec()
  {
    $book = Book::factory()->create();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $authors = Author::factory(3)->create();

    $book->authors()->$authors->id;

    $this->getJson("/api/v1/books/{$book->id}",[
      'accept' => 'application/json',
      ''
    ])
         ->assertStatus(200)
         ->assertJson([

         ]);
  }
}