<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\DatabaseMigrations;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Author;
use App\Models\User;

 
class AuthorsTest extends TestCase
{

    use DatabaseMigrations;

    /** @watch  */
   
    public function test_it_returns_an_author_as_a_resource_object(): void
    {
       $user = User::factory()->create();
       Sanctum::actingAs($user);

       $author = Author::create([
        'name' => 'John Doe',
       ]);

    
       $response = $this->getJson("/api/v1/authors/{$author->id}");

       $response->assertStatus(200);

       $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'created_at',
                'updated_at',
            ]
        ]);

    }

    public function test_it_returns_all_authors_as_a_collection_of_resource_objects()   
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Author::factory(3)->create();

        $this->getJson('/api/v1/authors')
             ->assertStatus(200)
             ->assertJsonStructure([
                "data" => [
                    '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                    ]
                ],
                

             ]);
    }

    public function test_it_can_create_an_author_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/authors', [
                'name' => 'Jane Doe'
        ]);

        $response->assertStatus(201);
    }

    public function test_it_can_update_an_author_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $author = Author::factory()->create();

        $response = $this->patchJson("/api/v1/authors/{$author->id}",[
            'name' => 'June Doe'
        ]);
        
        $response->assertStatus(200);
    }

    public function test_it_can_delete_an_author_through_a_delete_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $author = Author::factory()->create();

        $response = $this->getJson("/api/v1/authors/{$author->id}");


        $response->assertStatus(204);

        $this->assertDatabaseMissing('authors', [
                'id' => $author->id
             ]);
    }

    public function test_it_validates_that_a_name_attribute_is_given_when_creating_an_author()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/authors', [
           'name' => null, 

        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor(     
                'name'   
            );
            
    }

    public function test_it_validates_that_a_name_attribute_is_a_string_when_creating_an_author()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/authors', [
             'name' => 12345
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor(
            'name'
          );

    }

    public function test_it_paginates_authors()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Author::factory(15)->create();

        $response = $this->getJson('/api/v1/authors');

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
      Author::factory(7)->create();

      $user = User::factory()->create();
      Sanctum::actingAs($user);

      $response = $this->getJson('/api/v1/authors');

      $meta = $response->json('meta');

      $this->assertEquals(1, $meta['current_page']);
      $this->assertEquals(2, $meta['last_page']);
      $this->assertNotNull($meta['path']);
    }

    public function test_it_returns_latest_authors_first()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $oldAuthor = Author::factory()->create([
            'name' => 'Shanon Torphy',
            'created_at' => now()->subDays(2)
        ]);

        $newAuthor = Author::factory()->create([
            'name' => 'Ulises Schumm',
            'created_at' => now()
        ]);

        $response = $this->getJson('/api/v1/authors');

        $response->assertStatus(200);

        $responseData = $response->json('data');

        $this->assertEquals($newAuthor->name, $responseData[0]['name']);
        $this->assertEquals($oldAuthor->name, $responseData[1]['name']);
    }

}

