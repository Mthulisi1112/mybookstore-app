<?php


use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthorController;




Route::prefix('v1')
  ->middleware('auth:sanctum')
  ->group( function () {
       Route::get('/user', fn(Request $request) => $request->user());
       Route::apiResource('authors', AuthorController::class);
       Route::apiResource('books', BookController::class);
});
