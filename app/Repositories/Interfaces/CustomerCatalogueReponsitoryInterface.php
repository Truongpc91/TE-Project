<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface CustomerCatalogueReponsitoryInterface
{
    public function all();

    public function create($data);

}
