<?php

namespace App\Services;

use App\Helpers\ElasticSearch;
use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
        $bookIds = collect($hits)->pluck('_id')->all();

        // Get the books from database to get all necessary information
        $books = $this->repository->getByIds($bookIds);

        // Transform result before return to controller
        return array_map([$this, 'formatBook'], $books->all());
    }

    /**
     * @param array $books
     * @return void
     */
    public function createBulk(array $books): void
    {
        // Use query builder to insert data instead of Eloquent model via factory
        // To prevent out of memory in low memory instance
        DB::table('books')->insert($books);

        // Re-select inserted data to indexing
        $indexData = collect($books)->pluck('all_data')->all();
        $insertedBooks = $this->repository->getByIndexData($indexData)->all();

        ElasticSearch::bulkIndex($insertedBooks);
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
