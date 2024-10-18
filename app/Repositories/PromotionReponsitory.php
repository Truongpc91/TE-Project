<?php

namespace App\Repositories;

use App\Models\Promotion;
use App\Repositories\Interfaces\PromotionReponsitoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class PromotionReponsitory extends BaseRepository implements PromotionReponsitoryInterface
{
    protected $model;

    public function __construct(
        Promotion $model
    ) {
        $this->model = $model;
    }

    public function getAttributeCatalogueById(int $id = 0, $language_id = 0)
    {
        return $this->model->select(
            [
                'attribute_catalogues.id',
                'attribute_catalogues.parent_id',
                'attribute_catalogues.image',
                'attribute_catalogues.icon',
                'attribute_catalogues.album',
                'attribute_catalogues.publish',
                'attribute_catalogues.follow',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
            ->join('attribute_catalogue_language as tb2', 'tb2.attribute_catalogue_id', '=', 'attribute_catalogues.id')
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }

    public function getAll(int $languageId = 0)
    {
        return $this->model->with(['attribute_catalogue_language' => function ($query) use ($languageId) {
            // $query->where('language_id', $languageId);
        },])->get();
    }

    public function finByProduct(array $productId = [])
    {
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
            'products.id as product_id',
            'products.price',
        )
            ->selectRaw(
                "
                MAX(
                    IF(promotions.maxDiscountValue != 0, 
                    LEAST (
                            CASE 
                            WHEN discountType = 'cash' THEN discountValue
                            WHEN discountType = 'percent' THEN products.price * discountValue / 100
                            ELSE 0
                            END,
                            promotions.maxDiscountValue
                        ),
                        CASE 
                            WHEN discountType = 'cash' THEN discountValue
                            WHEN discountType = 'percent' THEN products.price * discountValue / 100
                            ELSE 0
                            END
                    )
                ) as discount
            "
            )
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('products', 'products.id', '=', 'ppv.product_id')
            ->where('products.publish', 1)
            ->where('promotions.publish', 1)
            ->whereIn('products.id', $productId)
            ->whereDate('promotions.endDate', '>', now())
            ->groupBy( 
                'products.id', 
                'promotions.id', 
                'promotions.discountValue', 
                'promotions.discountType', 
                'promotions.maxDiscountValue', 
                'products.price',
            )
            ->get()
        ;
    }

    public function findPromotionByVariantUuid($uuid = ''){
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
        )
            ->selectRaw(
                "
                MAX(
                    IF(promotions.maxDiscountValue != 0, 
                    LEAST (
                            CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN pv.price * discountValue / 100
                            ELSE 0
                            END,
                            promotions.maxDiscountValue
                        ),
                            CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN pv.price * discountValue / 100
                            ELSE 0
                            END
                    )
                ) as discount
            "
            )
            ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
            ->join('product_variants as pv', 'pv.uuid', '=', 'ppv.variant_uuid')
            ->where('promotions.publish', 1)
            ->where('ppv.variant_uuid', '=', $uuid)
            ->whereDate('promotions.endDate', '>', now())
            ->whereDate('promotions.startDate', '<', now())
            ->groupBy( 
                'promotions.id',
                'promotions.discountValue',
                'promotions.discountType',
                'promotions.maxDiscountValue',
            )
            ->first()
        ;
    }

    public function getPromotionByCartTotal($cartTotal = 0) {
        return $this->model
        ->where('promotions.publish', 1)
        ->where('promotions.method', '=', 'order_amount_range')
        ->whereDate('promotions.endDate', '>', now())
        ->whereDate('promotions.startDate', '<', now())
        ->get();
    }
}
