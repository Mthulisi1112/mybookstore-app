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

  public function test_it_returns_a_relationship_to_authors()
  {
    // arrange
    $book = Book::factory()->create();
    $authors = Author::factory(3)->create();
    $book->authors()->sync($authors->pluck('id')); // cleaner

    // authenticate
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // act
    $response = $this->getJson("/api/v1/books/{$book->id}");

    // assert
    $response->assertStatus(200);

    $response->assertJsonStructure([
        'data' => [
            'id',
            'title',
            'description',
            'publication_year',
            'authors' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]
    ]);

    // ensure correct data
    $response->assertJsonFragment([
        'id' => $authors[0]->id,
        'name' => $authors[0]->name
    ]);
  }

  public function test_it_can_attach_authors_to_a_book()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $book = Book::factory()->create();
    $authors = Author::factory(3)->create();

    $response = $this->postJson("/api/v1/books/{$book->id}/authors", [
          'authors' => $authors->pluck('id')->toArray()
    ]);

    $response->assertStatus(200);

    foreach ($authors as $author) {
        $this->assertDatabaseHas('author_book', [
            'book_id' => $book->id,
            'author_id' => $author->id,
        ]);
    }
  }


  // replace authors or to sync
  public function test_it_can_sync_authors_on_a_book()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $book = Book::factory()->create();
    $oldAuthors = Author::factory(2)->create();
    $newAuthors = Author::factory(2)->create();

    $book->authors()->sync($oldAuthors->pluck('id'));

    $response = $this->putJson("/api/v1/books/{$book->id}/authors", [
        'authors' => $newAuthors->pluck('id')->toArray()
    ]);

    $response->assertStatus(200);

    // Old removed
    foreach ($oldAuthors as $author) {
        $this->assertDatabaseMissing('author_book', [
            'book_id' => $book->id,
            'author_id' => $author->id,
        ]);
    }

    // New attached
    foreach ($newAuthors as $author) {
        $this->assertDatabaseHas('author_book', [
            'book_id' => $book->id,
            'author_id' => $author->id,
        ]);
    }
  }

  public function test_it_can_detach_an_author_from_a_book()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $book = Book::factory()->create();
    $author = Author::factory()->create();

    $book->authors()->attach($author->id);

    $response = $this->deleteJson("/api/v1/books/{$book->id}/authors/{$author->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('author_book', [
        'book_id' => $book->id,
        'author_id' => $author->id,
    ]);
  }
  public function test_it_returns_empty_authors_array_when_no_relationships_exist()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $book = Book::factory()->create();

    $response = $this->getJson("/api/v1/books/{$book->id}");

    $response->assertStatus(200);

    $this->assertEquals([], $response->json('data.authors'));
  }
  
  public function test_author_returns_books_relationship()
{
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $author = Author::factory()->create();
    $books = Book::factory(2)->create();

    $author->books()->sync($books->pluck('id'));

    $response = $this->getJson("/api/v1/authors/{$author->id}");

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'data' => [
            'id',
            'name',
            'books' => [
                '*' => [
                    'id',
                    'title',
                ]
            ]
        ]
    ]);
}


}