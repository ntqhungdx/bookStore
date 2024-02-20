<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use App\Services\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @var BookService
     */
    protected Service $service;

    /**
     * @param BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->service = $bookService;
    }

    /**
     * Search book
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $keyword = $request->get('q') ?? '';
        $limit = $request->get('limit') ?? config('app.page_size');

        return response()->json($this->service->searchByKeyword($keyword, $limit));
    }
}
