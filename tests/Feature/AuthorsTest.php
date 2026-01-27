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

       $author = Author::create([
        'name' => 'John Doe',
       ]);

       $user = User::factory()->create();
       Sanctum::actingAs($user);

       $this->getJson("/api/v1/authors/{$author->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $author->id,
                    'name' => $author->name,
                    'created_at' => $author->created_at->toJSON(),
                    'updated_at' => $author->updated_at->toJSON(),
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

        $this->postJson('/api/v1/authors', [
                'name' => 'Jane Doe'
        ])->assertStatus(201);
    }

    public function test_it_can_update_an_author_from_a_resource_object()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $author = Author::factory()->create();

        $this->patchJson("/api/v1/authors/{$author->id}",[
            'name' => 'June Doe'
        ])->assertStatus(200);
    }

    public function test_it_can_delete_an_author_through_a_delete_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $author = Author::factory()->create();

        $this->deleteJson("/api/v1/authors/{$author->id}")
              ->assertStatus(204);

        $this->assertDatabaseMissing('authors', [
                'id' => $author->id
             ]);
    }

    public function test_it_validates_that_a_name_attribute_is_given_when_creating_an_author()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

       $this->postJson('/api/v1/authors', [
        'data' => [
           'name' => '', 
          ]

        ])->assertStatus(422)
          ->assertJsonValidationErrorFor(     
                'name'   
            );
            
    }

    public function test_it_validates_that_a_name_attribute_is_a_string_when_creating_an_author()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/authors', [
             'name' => 12345
        ])->assertStatus(422)
          ->assertJsonValidationErrorFor(
            'name'
          );

    }

    public function test_it_paginates_authors()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Author::factory(15)->create();

        $response = $this->getJson('/api/v1/authors?per_page=5');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total'
            ],  
        ]);

        $this->assertCount(5, $response->Json('data'));
       
    }

}

