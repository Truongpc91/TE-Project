<?php

namespace App\Repositories;

use App\Models\Province;
use App\Models\User;
use App\Repositories\Interfaces\ProvinceReponsitoryInterface;

/**
 * Class UserService
 * @package App\Services
 */
class ProvinceReponsitory extends BaseRepository implements ProvinceReponsitoryInterface
{
    protected $model;

    public function __construct(Province $model){
       $this->model = $model; 
    }

    // public function all(){
    //     return Province::all(); 
    // }
}
