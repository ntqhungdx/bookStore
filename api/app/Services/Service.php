<?php

namespace App\Services;

use App\Repositories\Repository;

class Service
{
    protected Repository $repository;

    /**
     * Set main repository
     *
     * @param Repository $repository
     * @return void
     */
    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }
}
