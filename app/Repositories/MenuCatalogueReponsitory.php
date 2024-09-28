<?php

namespace App\Repositories;

use App\Models\Menu;
use App\Models\MenuCatalogue;
use App\Repositories\Interfaces\MenuCatalogueReponsitoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class MenuCatalogueReponsitory extends BaseRepository implements MenuCatalogueReponsitoryInterface
{
    protected $model;

    public function __construct(    
        MenuCatalogue $model
    ){
        $this->model = $model;
    }

}
