<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\BookController;
use App\Services\BookService;
use Illuminate\Http\Request;
use Tests\TestCase;
use Tests\Unit\Controllers\Mockery;

class BookControllerTest extends TestCase
{
    public function test_index_action_might_be_successfull()
    {
        $mock = \Mockery::mock('alias:App\Helpers\ElasticSearch');
        $mock->shouldReceive('search')
            ->with('', 20)
            ->andReturn([]);

        $response = $this->get('/search/book');

        $response->assertOk();

        \Mockery::close();
    }
}
