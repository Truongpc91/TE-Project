<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all();

    public function findById(int $id);

    // public function update(array $a = [], array $payload = []);
    public function pagination(
        array $column = ['*'],
        array $condition = [],
        array $join = [],
        array $extend = [],
        int $perpage = 20,
        array $relations = []
    );

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], $data);
}
