<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Services\BookService;
use App\Repositories\BookRepository;
use App\Models\Book;

class BookServiceTest extends TestCase
{
    protected BookService $bookService;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Mock the BookRepository dependency
        $this->bookRepository = Mockery::mock(BookRepository::class);
        $this->bookService = new BookService($this->bookRepository);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @return void
     */
    public function testSearchByKeywordWithResult(): void
    {
        // Mock the ElasticSearch::search method
        $keyword = 'Harry Potter';
        $limit = 10;
        $result = [
            'hits' => [
                'total' => ['value' => 1],
                'hits' => [['_source' => ['book_id' => 1]]]
            ]
        ];
        $this->mockElasticSearchSearch($keyword, $limit, $result);

        // Mock the BookRepository::getByIds method
        $book = new Book([
            'title'     =>  'Harry Potter and the Sorcerer\'s Stone',
            'summary'   =>  'Harry Potter and the Sorcerer\'s Stone summary content',
            'publisher' =>  'Demo publisher',
            'authors'   =>  [
                'J. K. Rowling'
            ],
        ]);
        $book->id = 1;
        $this->bookRepository->shouldReceive('getByIds')
            ->with([1])
            ->andReturn([$book]);

        // Perform the test
        $searchResult = $this->bookService->searchByKeyword($keyword, $limit);

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
     * @return void
     */
    public function testSearchByKeywordWithNoMatching(): void
    {
        // Mock the ElasticSearch::search method
        $keyword = 'Harry Potter';
        $limit = 10;
        $result = [
            'hits' => [
                'total' => ['value' => 0],
                'hits' => []
            ]
        ];
        $this->mockElasticSearchSearch($keyword, $limit, $result);

        // Perform the test
        $searchResult = $this->bookService->searchByKeyword($keyword, $limit);

        // Assert the result
        $expectedResult = [];
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
