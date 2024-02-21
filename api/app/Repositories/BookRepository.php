<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Contracts\Container\BindingResolutionException;

class BookRepository extends Repository
{
    protected $model = Book::class;

    /**
     * @param array $ids
     * @return array
     * @throws BindingResolutionException
     */
    public function getByIds(array $ids): array
    {
        return $this->query()->whereIn('id', $ids)->get()->all();
    }
}
