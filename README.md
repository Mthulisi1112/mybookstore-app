Bookstore Management App

A full-stack bookstore application that provides a RESTful API for managing books and authors, including their relationships.
Built to demonstrate clean API design, relational data modeling, and full-stack development skills.

This project focuses on building a scalable backend architecture with proper testing and a structured frontend integration approach.

Features
Books API

Create books

View all books

View single book

Update book details

Delete books

Pagination & sorting

Relationship loading (authors linked to books)

Authors API

Create authors

View all authors

View single author

Update author details

Delete authors

Many-to-many relationship with books

Relationships

Many-to-Many relationship between Books ↔ Authors

Eager loading with API resources

Clean JSON API responses

Relationship testing

Tech Stack

Backend

Laravel

PHP

REST API

Laravel API Resources

Pest / PHPUnit Testing

Frontend

React (planned / in progress)

Database

SQLlite & Tableplus

Tools

Git

GitHub

Composer

Postman

Herd (local dev)

Project Structure
/app
/Models
/Http/Controllers
/Http/Resources
/routes
/tests
/database

Installation & Setup

# Clone repository

git clone https://github.com/Mthulisi1112/bookstore-app.git

# Enter project folder

cd bookstore-app

# Install backend dependencies

composer install

# Copy env file

cp .env.example .env

# Generate app key

php artisan key:generate

# Run migrations

php artisan migrate

# Start server

php artisan serve

Testing

# Run all tests

php artisan test

# Or with Pest

vendor/bin/pest

Includes tests for:

Books API

Authors API

Relationships

Resource responses

Pagination

Validation

Purpose of the Project

This project was built as part of my journey into full-stack software development, focusing on:

RESTful API design

Database relationships

Clean architecture

Test-driven development (TDD)

API resources & response consistency

Scalable backend systems

It also serves as a foundation for a future SaaS-style bookstore platform.

Roadmap / Future Features

Authentication (JWT / Sanctum)

User roles (Admin, Staff, User)

Frontend dashboard (React)

Search & filtering

API documentation (Swagger/OpenAPI)

SaaS multi-tenant architecture

Payment integration

Book inventory management

Author

Mthulisi Ndhlovu
Aspiring Full-Stack Developer | Junior Software Developer

Email: mthulisi.ndhlovu123@gmail.com

GitHub: https://github.com/Mthulisi1112

Why this project matters

This project demonstrates:

Real-world API structure

Professional backend architecture

Proper relationship handling

Clean code practices

Testing culture

Production-style development flow
