<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all(array $relation);

    public function findById(int $id);

    // public function update(array $a = [], array $payload = []);
    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 5,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
    );

    public function findByCondition($condition = [], $relation = [] ,$flag = false);

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], $data);

    public function createPivot($model, array $payload = [], string $relation = '');

    public function createBatch(array $payload = []);

    public function updateOrInsert(array $payload = [], array $condition = []);

    public function findByWhereHas(array $condition = [], string $relation = '',string $alias = '');
}
