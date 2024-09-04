<?php

namespace App\Repositories;

use App\Models\PostCatalogue;
use App\Repositories\Interfaces\PostCatalogueReponsitoryInterface;

/**
 * Class UserService
 * @package App\Services
 */
class PostCatalogueReponsitory extends BaseRepository implements PostCatalogueReponsitoryInterface
{
    protected $model;

    public function __construct(PostCatalogue $model){
        $this->model = $model;
    }

}


