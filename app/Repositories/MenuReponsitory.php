<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Repositories\Interfaces\MenuReponsitoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class MenuReponsitory extends BaseRepository implements MenuReponsitoryInterface
{
    protected $model;

    public function __construct(    
        Menu $model
    ){
        $this->model = $model;
    }

}
