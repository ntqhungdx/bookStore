<?php

namespace App\Repositories;

use App\Models\Book;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;

class BookRepository extends Repository
{
    protected $model = Book::class;

    /**
     * @param array $ids
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getByIds(array $ids): Collection
    {
        return $this->query()->whereIn('id', $ids)->get();
    }

    /**
     * @param array $indexData
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getByIndexData(array $indexData): Collection
    {
        $builder = $this->query()->selectRaw('id, all_data AS text');

        foreach ($indexData as $bookData) {
            $builder->orWhere('all_data', '=', $bookData);
        }

        return $builder->get();
    }
}
