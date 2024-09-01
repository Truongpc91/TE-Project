<?php

namespace App\Repositories;

use App\Models\District;
use App\Models\User;
use App\Repositories\Interfaces\DistrictReponsitoryInterface;

/**
 * Class UserService
 * @package App\Services
 */
class DistrictReponsitory extends BaseRepository implements DistrictReponsitoryInterface
{
    protected $model;

    public function __construct(District $model){
        $this->model = $model;
    }

    public function findDistrictByProvinceId(int $province_id = 0){

        return $this->model->where('province_code', '=', $province_id)->get();
    }
}
