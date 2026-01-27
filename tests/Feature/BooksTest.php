<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;




class BooksTest extends TestCase
{

    use DatabaseMigrations;

    /** @watch  */
   
    public function test_it_returns_a_book_as_a_resource_object(): void
    {

       $book = Book::create([
        'title' => 'A winter proposal',
        'description' => "Stockbroker Roscoe is struggling to fight his attention to his solicitor
         Pippa.He can't decide whether to kiss her senseless or make a more permanent proposal!
         ",
         'publication_year' => '2010'
       ]);

       $user = User::factory()->create();
       Sanctum::actingAs($user);

       $this->getJson("/api/v1/books/{$book->id}", [
             'accept' => 'application/json',
             'content_type' => 'application/json'
            ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'description' => $book->description,
                    'publication_year' => $book->publication_year,
                    'created_at' => $book->created_at->toJSON(),
                    'updated_at' => $book->updated_at->toJSON(),
                 ]
        ]);

    }

    public function test_it_returns_all_books_as_a_collection_of_resource_objects()   
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Book::factory(3)->create();

        $this->get('/api/v1/books')
             ->assertStatus(200)
             ->assertJsonStructure([
                "data" => [
                    '*' => [
                    'id',
                    'title',
                    'description',
                    'publication_year',
                    'created_at',
                    'updated_at',
                    ]
                ],

             ]);
    }

    public function test_it_can_create_a_book_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/books', [
                'title' => 'A winter proposal',
                'description' => "Stockbroker Roscoe is struggling to fight his attention to his solicitor
                 Pippa.He can't decide whether to kiss her senseless or make a more permanent proposal!
                ",
                'publication_year' => '2010'
        ])->assertStatus(201);
    }

    public function test_it_can_update_a_book_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create();

        $this->patchJson("/api/v1/books/{$book->id}",[
                'title' => 'A winter proposal',
                'description' => "Stockbroker Roscoe is struggling to fight his attention to his solicitor
                 Pippa.He can't decide whether to kiss her senseless or make a more permanent proposal!
                ",
                'publication_year' => '2010'
        ])->assertStatus(200);
    }

    public function test_it_can_delete_a_book_through_a_delete_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create();

        $this->deleteJson("/api/v1/books/{$book->id}")
              ->assertStatus(204);

        $this->assertDatabaseMissing('books', [
                'id' => $book->id
             ]);
    }

    public function test_it_validates_that_a_title_attribute_is_given_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

       $this->postJson('/api/v1/books', [
        'data' => [
           'title' => 'A winter proposal', 
          ]

        ])->assertStatus(422)
          ->assertJsonValidationErrors([      
                'title' => ['required']    
            ]);
            
    }

    public function test_it_validates_that_a_title_attribute_is_a_string_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/books', [
             'title' => 12345
        ])->assertStatus(422)
          ->assertJsonValidationErrorFor(
            'title'
          );

    }

    public function test_it_validates_that_a_description_attribute_is_given_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

       $this->postJson('/api/v1/books', [
        'data' => [
           'description' => "Stockbroker Roscoe is struggling to fight his attention to his solicitor
                 Pippa.He can't decide whether to kiss her senseless or make a more permanent proposal!
                ", 
          ]

        ])->assertStatus(422)
          ->assertJsonValidationErrors([      
                'description' => ['required']    
            ]);
            
    }

    public function test_it_validates_that_a_description_attribute_is_a_text_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/books', [
             'description' => 12345
        ])->assertStatus(422)
          ->assertJsonValidationErrorFor(
            'description'
          );

    }

}

