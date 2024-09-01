<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface DistrictReponsitoryInterface
{
    public function all();

    public function findDistrictByProvinceId(int $province_id);
}
