<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductReponsitoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class ProductReponsitory extends BaseRepository implements ProductReponsitoryInterface
{
    protected $model;

    public function __construct(
        Product $model
    ) {
        $this->model = $model;
    }



    public function getProductById(int $id = 0, $language_id = 0)
    {
        return $this->model->select(
            [
                'products.id',
                'products.product_catalogue_id',
                'products.image',
                'products.icon',
                'products.album',
                'products.publish',
                'products.follow',
                'products.code',
                'products.made_in',
                'products.price',
                'products.attributeCatalogue',
                'products.attribute',
                'products.variant',
                'tb2.name',
                'tb2.description',
                'tb2.content',
                'tb2.meta_title',
                'tb2.meta_keyword',
                'tb2.meta_description',
                'tb2.canonical',
            ]
        )
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->with([
                'product_catalogues',
                'product_variants' => function ($query) use ($language_id) {
                    $query->with(['attributes' => function ($query) use ($language_id) {
                        $query->with(['attribute_language' => function ($query) use ($language_id) {
                            $query->where('language_id', '=', $language_id);
                        }]);
                    }]);
                },
                'galleries',
                'reviews'
            ])
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }

    public function findProductForPromotion($condition = [], $relation = [])
    {
        $query = $this->model->newQuery();
        $query->select([
            'products.id',
            'products.image',
            'products.price',
            'tb2.name',
            'tb3.uuid',
            'tb3.id as product_variant_id',
            DB::raw("CONCAT(tb2.name, ' - ', COALESCE(tb4.name, ' Default')) as variant_name"),
            DB::raw("COALESCE(tb3.sku, products.code) as sku"),
            DB::raw("COALESCE(tb3.price, products.price) as price")
        ]);
        $query->join('product_language as tb2', 'products.id', '=', 'tb2.product_id');
        $query->leftJoin('product_variants as tb3', 'products.id', '=', 'tb3.product_id');
        $query->leftJoin('product_variant_language as tb4', 'tb3.id', '=', 'tb4.product_variant_id');

        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }

        if (count($relation)) {
            $query->with($relation);
        }
        $query->orderBy('id', 'DESC');

        return $query->paginate(20);
    }

    public function filter($param, $perpage)
    {
        $query = $this->model->newQuery();

        $query->select([
            'products.id',
            'products.price',
            'products.image',
            'products.product_catalogue_id',
        ]);

        if (isset($param['select']) && count($param['select'])) {
            foreach ($param['select'] as $key => $val) {
                $query->selectRaw($val);
            }
        }

        if (isset($param['join']) && count($param['join'])) {
            foreach ($param['join'] as $key => $val) {
                if (is_null($val)) continue;
                $query->leftJoin($val[0], $val[1], $val[2], $val[3]);
            }
        }

        $query->where('products.publish', '=', 1);

        if (isset($param['where']) && count($param['where'])) {
            foreach ($param['where'] as $key => $val) {
                $query->where($val);
            }
        }

        if (isset($param['having']) && count($param['having'])) {
            foreach ($param['having'] as $key => $val) {
                if (is_null($val)) continue;
                $query->having($val);
            
            }
        }
        if (isset($param['groupBy']) && count($param['groupBy'])) {
            foreach ($param['groupBy'] as $column) {
                $query->groupBy($column);
            }
        }

        // if (isset($param['whereRaw']) && count($param['whereRaw'])) {
        //     $query->whereRaw($param['whereRaw'][0][0],$param['whereRaw'][0][1]);
        // }
        $query->with(['reviews', 'languages', 'product_catalogues']);
        return $query->paginate($perpage);
    }

    private function convertAttributeFilter($attribute)
    {
        $rawCondition['whereRaw'] =  [
            [
                'tb3.id IN (
                    SELECT id
                    FROM product_catalogues
                    JOIN product_catalogue_language ON product_catalogues.id = product_catalogue_language.product_catalogue_id
                    WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                    AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                    AND product_catalogue_language.language_id = ' . $languageId . '
                )',
                [$catId, $catId]
            ]
        ];
    }

    private function convertPriceFilter($price) {}

    private function convertRateFilter($rate) {}
}
