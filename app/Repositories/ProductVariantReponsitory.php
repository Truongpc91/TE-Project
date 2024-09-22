<?php

namespace App\Repositories;

use App\Models\ProductVariant;
use App\Repositories\Interfaces\ProductVariantReponsitoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class ProductVariantReponsitory extends BaseRepository implements ProductVariantReponsitoryInterface
{
    protected $model;

    public function __construct(
        ProductVariant $model
    ){
        $this->model = $model;
    }

}
