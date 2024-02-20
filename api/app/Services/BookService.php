<?php

namespace App\Services;

use App\Helpers\ElasticSearch;
use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Support\Arr;

class BookService extends Service
{
    /**
     * @param BookRepository $repository
     */
    public function __construct(BookRepository $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * Search book by keyword
     *
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function searchByKeyword(string $keyword, int $limit): array
    {
        $result = ElasticSearch::search($keyword, $limit);

        if (empty($result) || !$result['hits']['total']['value']) {
            return [];
        }
        $hits = $result['hits']['hits'];
        $bookIds = collect($hits)->pluck('_source.book_id')->all();

        // Get the books from database to get all necessary information
        $books = $this->repository->getByIds($bookIds);

        // Transform result before return to controller
        return array_map([$this, 'formatBook'], $books->all());
    }

    /**
     * Format book data
     *
     * @param Book $book
     * @return array
     */
    protected function formatBook(Book $book): array
    {
        return Arr::except($book->toArray(), [
            'all_data',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
    }
}
