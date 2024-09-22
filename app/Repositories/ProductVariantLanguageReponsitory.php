<?php

namespace App\Repositories;

use App\Models\ProductVariantLanguage;
use App\Repositories\Interfaces\ProductVariantLanguageReponsitoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class ProductVariantLanguageReponsitory extends BaseRepository implements ProductVariantLanguageReponsitoryInterface
{
    protected $model;

    public function __construct(
        ProductVariantLanguage $model
    ){
        $this->model = $model;
    }

}
