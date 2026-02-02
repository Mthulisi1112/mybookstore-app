<?php


use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookAuthorController;



Route::prefix('v1')
  ->middleware('auth:sanctum')
  ->group( function () {
       Route::get('/user', fn(Request $request) => $request->user());
       Route::apiResource('authors',AuthorController::class);
       Route::apiResource('books', BookController::class);
       Route::post('books/{book}/authors', [BookAuthorController::class, 'attach']); //attach
       Route::delete('books/{book}/authors/{author}', [BookAuthorController::class, 'detach']); // detach
       Route::put('books/{book}/authors', [BookAuthorController::class, 'sync']); //sync
       Route::get('books/{book}', [BookAuthorController::class, 'index']);

});
