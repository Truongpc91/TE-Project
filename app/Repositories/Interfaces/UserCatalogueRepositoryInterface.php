<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface UserCatalogueRepositoryInterface
{
    public function all();

    public function create($data);

    // public function update($data, $user);
}
