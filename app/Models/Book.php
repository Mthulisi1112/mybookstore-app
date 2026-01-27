<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Book extends Model
{
     use HasFactory, HasApiTokens;

     protected $fillable = ['title', 'description', 'publication_year'];

     

}
