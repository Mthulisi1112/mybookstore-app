<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Book;



class Author extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = ['name'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'author_book');
    }
}
