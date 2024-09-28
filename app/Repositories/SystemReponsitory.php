<?php

namespace App\Repositories;

use App\Models\System;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\SystemReponsitoryInterface;

/**
 * Class UserService
 * @package App\Services
 */
class SystemReponsitory extends BaseRepository implements SystemReponsitoryInterface
{
    protected $model;

    public function __construct(
        System $model
    ){
        $this->model = $model;
    }

}
