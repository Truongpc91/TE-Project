<?php

namespace App\Repositories;

use App\Models\ProductVariantAttribute;
use App\Repositories\Interfaces\ProductVariantAttributeReponsitoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class ProductVariantAttributeReponsitory extends BaseRepository implements ProductVariantAttributeReponsitoryInterface
{
    protected $model;

    public function __construct(
        ProductVariantAttribute $model
    ){
        $this->model = $model;
    }

}
