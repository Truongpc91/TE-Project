<?php

namespace App\Repositories;

use App\Models\CustomerCatalogue;
use App\Repositories\Interfaces\CustomerCatalogueReponsitoryInterface;

/**
 * Class UserService
 * @package App\Services
 */
class CustomerCatalogueReponsitory extends BaseRepository implements CustomerCatalogueReponsitoryInterface
{
    protected $model;

    public function __construct(CustomerCatalogue $model){
        $this->model = $model;
    }
}
