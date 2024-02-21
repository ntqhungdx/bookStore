<?php

namespace Tests\Feature\Repositories;

use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_will_return_enough_data()
    {
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $book3 = Book::factory()->create();

        $ids = [$book1->id, $book2->id, $book3->id];

        $repository = new BookRepository();

        $books = $repository->getByIds($ids);
        $this->assertCount(3, $books);

        $bookCollect = collect($books);

        $this->assertTrue($bookCollect->filter(fn($book) => $book->id == $book1->id)->isNotEmpty());
        $this->assertTrue($bookCollect->filter(fn($book) => $book->id == $book2->id)->isNotEmpty());
        $this->assertTrue($bookCollect->filter(fn($book) => $book->id == $book3->id)->isNotEmpty());
    }

    public function test_repository_will_return_empty()
    {
        // Create some example books in the database
        $book = Book::factory()->create();

        $repository = new BookRepository();
        $books = $repository->getByIds([999999]);

        $this->assertCount(0, $books);
    }

    public function test_repository_will_return_exactly_data()
    {
        // Create some example books in the database
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $book3 = Book::factory()->create();

        $ids = [$book1->id, $book2->id, 999999];

        $repository = new BookRepository();
        $books = $repository->getByIds($ids);

        $bookCollect = collect($books);

        $this->assertCount(2, $books);

        $this->assertTrue($bookCollect->filter(fn($book) => $book->id == $book1->id)->isNotEmpty());
        $this->assertTrue($bookCollect->filter(fn($book) => $book->id == $book2->id)->isNotEmpty());
        $this->assertTrue($bookCollect->filter(fn($book) => $book->id == $book3->id)->isEmpty());
        $this->assertTrue($bookCollect->filter(fn($book) => $book->id == 999999)->isEmpty());
    }
}
