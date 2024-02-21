<?php

namespace Tests\Feature\Services;

use Mockery;
use Tests\TestCase;
use App\Services\BookService;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $bookService;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->bookService = app()->make(BookService::class);
    }

    /**
     * @return void
     */
    public function testSearchByKeyword(): void
    {
        // Create a sample book in the database
        Book::factory()->create([
            'title'     =>  'Harry Potter and the Sorcerer\'s Stone',
            'summary'   =>  'Harry Potter and the Sorcerer\'s Stone summary content',
            'publisher' =>  'Demo publisher',
            'authors'   =>  [
                'J. K. Rowling'
            ],
        ]);

        // Mock the ElasticSearch::search method because logstash is not running for testing database
        $keyword = 'Harry Potter';
        $limit = 10;
        $result = [
            'hits' => [
                'total' => ['value' => 1],
                'hits' => [['_source' => ['book_id' => 1]]]
            ]
        ];
        $this->mockElasticSearchSearch($keyword, $limit, $result);

        // Perform the test
        $searchResult = $this->bookService->searchByKeyword('Harry Potter', 10);

        // Assert the result
        $expectedResult = [
            [
                'id'        =>  1,
                'title'     =>  'Harry Potter and the Sorcerer\'s Stone',
                'summary'   =>  'Harry Potter and the Sorcerer\'s Stone summary content',
                'publisher' =>  'Demo publisher',
                'authors'   =>  [
                    'J. K. Rowling'
                ],
            ]
        ];
        $this->assertEquals($expectedResult, $searchResult);
    }

    /**
     * @param string $keyword
     * @param int $limit
     * @param array $result
     * @return void
     */
    protected function mockElasticSearchSearch(string $keyword, int $limit, array $result): void
    {
        $mock = Mockery::mock('alias:App\Helpers\ElasticSearch');
        $mock->shouldReceive('search')
            ->with($keyword, $limit)
            ->andReturn($result);
    }
}
