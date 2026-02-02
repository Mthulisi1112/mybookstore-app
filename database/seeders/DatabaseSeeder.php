<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([AuthorSeeder::class]);
        $this->call([BookSeeder::class]);

        // 3️⃣ Attach authors to books
        $authors = Author::all();
        $books = Book::all();

        foreach ($books as $book) {
            $book->authors()->attach($authors->random(rand(1, 3))->pluck('id')->toArray());
        }
    }
}
