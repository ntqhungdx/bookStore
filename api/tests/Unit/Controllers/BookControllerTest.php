<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\BookController;
use App\Services\BookService;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery;

class BookControllerTest extends TestCase
{
    public function test_index_action()
    {
        $bookService = Mockery::mock(BookService::class);
        $bookService->shouldReceive('searchByKeyword')
            ->with('keyword', 10)
            ->andReturn($this->mockingData());


        $controller = new BookController($bookService);

        $request = Request::create('/search/book', 'GET', [
            'q' => 'keyword',
            'limit' => 10,
        ]);

        $response = $controller->index($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(response()->json($this->mockingData()), $response);

        Mockery::close();
    }

    private function mockingData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Harry Potter and the Sorcerer\'s Stone',
                'summary' => 'Harry Potter and the Sorcerer\'s Stone summary content',
                'publisher' => 'Demo publisher',
                'authors' => [
                    'J. K. Rowling'
                ],
            ]
        ];
    }
}
