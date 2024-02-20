<?php

namespace App\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Specify Model class name
     * @throws BindingResolutionException
     */
    public function model(): Model
    {
        return $this->makeModel();
    }

    /**
     * Specify Model class name
     * @throws BindingResolutionException
     */
    public function query(): Builder
    {
        return $this->makeModel()->newQuery();
    }

    /**
     * Make model.
     *
     * @throws BindingResolutionException
     */
    private function makeModel(): Model
    {
        if (empty($this->model)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw (new ModelNotFoundException())->setModel($this->model);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $model = app()->make($this->model);

        if (! $model instanceof Model) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw (new ModelNotFoundException(
                "Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            ))
                ->setModel($this->model);
        }

        return $model;
    }
}
