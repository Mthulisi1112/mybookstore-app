<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Author;


class BookAuthorController extends Controller
{
    public function index(Book $book)
    {
        return response()->json([
            'data' => $book->authors
        ], 200);
    }

    public function attach(Book $book, Request $request)
    {
        // attach multiple authors ,why we use $request->authors
        $book->authors()->attach($request->authors);

        return response()->json([
            'message' => 'Author attached to book'
        ], 200);
    }

    public function detach(Book $book, Author $author)
    {
        //delete single author, why we use author->id
         $book->authors()->detach($author->id);

         return response()->json([
            'message' => 'Author detached from book'
         ], 204);
    }

    public function sync(Book $book, Request $request)
    {
    // sync in multiple authors , why we use $request->authors
       $book->authors()->sync($request->authors);

       return response()->json([
         'message' => 'Author/authors replaced from a book'
       ]);
    }


}
