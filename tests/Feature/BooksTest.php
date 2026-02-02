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

       $response = $this->getJson("/api/v1/books/{$book->id}");
       $response->assertStatus(200);
       $response->assertJsonStructure([
                'data' => [
                      'id',
                      'title',
                      'description',
                      'publication_year',
                      'created_at',
                      'updated_at', 
                ]
        ]);

    }

    public function test_it_returns_all_books_as_a_collection_of_resource_objects()   
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Book::factory(3)->create();

        $response = $this->get('/api/v1/books');

        $response->assertStatus(200);

        $response->assertJsonStructure([
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

        $response =  $this->postJson('/api/v1/books', [
                'title' => 'A winter proposal',
                'description' => "Stockbroker Roscoe is struggling to fight his attention to his solicitor
                 Pippa.He can't decide whether to kiss her senseless or make a more permanent proposal!
                ",
                'publication_year' => '2010'
       ]);

        $response->assertStatus(201);

    }

    public function test_it_can_update_a_book_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $book = Book::factory()->create();

        $response = $this->patchJson("/api/v1/books/{$book->id}",[
                'title' => 'A winter proposal',
                'description' => "Stockbroker Roscoe is struggling to fight his attention to his solicitor
                 Pippa.He can't decide whether to kiss her senseless or make a more permanent proposal!
                ",
                'publication_year' => '2010'
        ]);
        
        $response->assertStatus(200);
    }

    public function test_it_can_delete_a_book_through_a_delete_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', [
                'id' => $book->id
             ]);
    }

    public function test_it_validates_that_a_title_attribute_is_given_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/books', [
           'title' => null, 

        ]);
        
        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor(     
                'title'   
            );     
    }

    public function test_it_validates_that_a_title_attribute_is_a_string_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/books', [
             'title' => 12345
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor(
          'title'
          );
    }

    public function test_it_validates_that_a_description_attribute_is_given_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/books', [
            'description' => null, 

        ]);
        
        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor(     
                  'description'    
              );
            
    }

    public function test_it_validates_that_a_description_attribute_is_a_string_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/books', [
             'description' => 12345
        ]);
        
        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor(
             'description'
        );
    }

    public function test_it_validates_that_publication_year_attribute_is_given_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/books', [
            'publication_year' => null, 

        ]);
        
        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor(     
                  'publication_year'    
              );
            
    }

    public function test_it_validates_that_publication_year_attribute_is_a_string_when_creating_a_book()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/books', [
             'publication_year' => 1980
        ]);
        
        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor(
             'publication_year'
        );
    }


    public function test_it_paginates_books()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Book::factory(15)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links',
                'path',
                'per_page',
                'to',
                'total'
            ],  
        ]);

        $this->assertCount(5, $response->Json('data'));
       
    }

    public function test_pagination_links_exist()
    {
      Book::factory(7)->create();

      $user = User::factory()->create();
      Sanctum::actingAs($user);

      $response = $this->getJson('/api/v1/books');

      $meta = $response->json('meta');

      $this->assertEquals(1, $meta['current_page']);
      $this->assertEquals(2, $meta['last_page']);
      $this->assertNotNull($meta['path']);
    }

    public function test_it_returns_latest_books_first()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $oldBook = Book::factory()->create([
            'title' => 'A numquam et quos minima nihil',
            'description' => 'Iste ad nemo laborum autem',
            'publication_year' => '1982',
            'created_at' => now()->subDays(2)
        ]);

        $newBook = Book::factory()->create([
            'title' => 'A numquam et quos minima nihil',
            'description' => 'Provident error consequatur optio repellat minus aut quas.',
            'publication_year' => '1983',
            'created_at' => now()
        ]);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);

        $responseData = $response->json('data');

        $this->assertEquals($newBook->title, $responseData[0]['title']);
        $this->assertEquals($oldBook->title, $responseData[1]['title']);
    }


}

