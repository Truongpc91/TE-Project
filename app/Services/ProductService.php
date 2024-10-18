<?php

namespace App\Services;

use App\Models\Language;
use App\Models\ProductGallery;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Repositories\Interfaces\ProductReponsitoryInterface as ProductReponsitory;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterReponsitory;
use App\Repositories\Interfaces\ProductVariantLanguageReponsitoryInterface as ProductVariantLanguageReponsitory;
use App\Repositories\Interfaces\ProductVariantAttributeReponsitoryInterface as ProductVariantAttributeReponsitory;
use App\Repositories\Interfaces\PromotionReponsitoryInterface as PromotionReponsitory;
use App\Repositories\Interfaces\AttributeReponsitoryInterface as AttributeReponsitory;
use App\Repositories\Interfaces\AttributeCatalogueReponsitoryInterface as AttributeCatalogueReponsitory;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;


/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    const PATH_UPLOAD = 'products';

    protected $productReponsitory;
    protected $routerReponsitory;
    protected $productVariantLanguageReponsitory;
    protected $productVariantAttributeReponsitory;
    protected $PromotionReponsitory;
    protected $AttributeReponsitory;
    protected $AttributeCatalogueReponsitory;
    protected $ProductCatalogueService;

    public function __construct(
        ProductReponsitory $productReponsitory,
        RouterReponsitory $routerReponsitory,
        ProductVariantLanguageReponsitory $productVariantLanguageReponsitory,
        ProductVariantAttributeReponsitory $productVariantAttributeReponsitory,
        PromotionReponsitory $PromotionReponsitory,
        AttributeReponsitory $AttributeReponsitory,
        AttributeCatalogueReponsitory $AttributeCatalogueReponsitory,
        ProductCatalogueService $ProductCatalogueService,
    ) {
        $this->productReponsitory = $productReponsitory;
        $this->productVariantLanguageReponsitory = $productVariantLanguageReponsitory;
        $this->productVariantAttributeReponsitory = $productVariantAttributeReponsitory;
        $this->routerReponsitory = $routerReponsitory;
        $this->PromotionReponsitory = $PromotionReponsitory;
        $this->AttributeReponsitory = $AttributeReponsitory;
        $this->AttributeCatalogueReponsitory = $AttributeCatalogueReponsitory;
        $this->ProductCatalogueService = $ProductCatalogueService;
        $this->controllerName = 'ProductController';
    }

    public function paginate($request, $languageId, $modelCatalogue = null, $page = 1, $extend = [])
    {
        if (!is_null($modelCatalogue)) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }


        $perPage = $request->integer('perpage');
        $perPage = 5;
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId],
            ],
        ];
        $paginationConfig = [
            'path' => ($extend['path']) ?? 'admin/product/index',
            'groupBy' => $this->paginateSelect()
        ];
        $orderBy = ['products.id', 'DESC'];
        $relations = ['product_catalogues', 'languages'];
        $rawQuery = $this->whereRaw($request, $languageId, $modelCatalogue);
        $joins = [
            ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
            ['product_catalogues as tb3', 'products.product_catalogue_id', '=', 'tb3.id'],
        ];

        $products = $this->productReponsitory->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            $joins,
            $relations,
            $rawQuery
        );

        return $products;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $product = $this->createProduct($request);
            if ($product->id > 0) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->createRouter($product, $request, $this->controllerName, $languageId);
                if ($request->input('attribute')) {
                    $this->createVariant($product, $request, $languageId);
                }

                $this->ProductCatalogueService->setAttribute($product);

                // $this->createVariant($product, $request, $languageId);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($id, $request, $languageId)
    {
        DB::beginTransaction();
        try {
            $product = $this->uploadProduct($id, $request);
            if ($product) {
                $this->updateLanguageForProduct($product, $request, $languageId);

                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter(
                    $product,
                    $request,
                    $this->controllerName,
                    $languageId
                );
                $product->product_variants()->each(function ($variant) {
                    $variant->languages()->detach();
                    $variant->attributes()->detach();
                    $variant->delete();
                });
                if ($request->input('attribute')) {
                    $this->createVariant($product, $request, $languageId);
                }
                $this->ProductCatalogueService->setAttribute($product);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productReponsitory->findById($id);

            $deleteProduct = $this->productReponsitory->destroy($product);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            // echo $e->getMessage();die();
            return false;
        }
    }

    private function createProduct($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }
        $payload['price'] = convert_price(($payload['price']) ?? 0);

        $payload['attributeCatalogue'] = $this->formatJson($request, 'attributeCatalogue');
        $payload['attribute'] = $request->input('attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');

        $product = $this->productReponsitory->create($payload);
        $this->createProductGallery($product->id, $request);
        return $product;
    }

    private function createProductGallery($productId, $request)
    {
        $dataProductGalleries = $request->product_galleries ?: [];

        foreach ($dataProductGalleries as $image) {
            ProductGallery::query()->create([
                'product_id' => $productId,
                'image' => Storage::put(self::PATH_UPLOAD, $image)
            ]);
        }

        return true;
    }

    private function uploadProduct($id, $request)
    {
        $payload = $request->only($this->payload());

        $product = $this->productReponsitory->findById($id);

        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }

        $currentImage = $product->image;

        if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }

        return $this->productReponsitory->update($id, $payload);
    }

    private function updateLanguageForProduct($product, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $product->id, $languageId);

        // $locale = app()->getLocale();
        // $language = Language::where('canonical', $locale)->first();
        // dd($this->currenLanguage());

        $product->languages()->detach([$this->currenLanguage(), $product->id]);
        return $this->productReponsitory->createPivot($product, $payload, 'languages');
    }

    private function updateCatalogueForProduct($product, $request)
    {
        $product->product_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $productId, $languageId)
    {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] =  $languageId;
        $payload['product_id'] = $productId;
        return $payload;
    }

    // private function formatJson($request, $inputName)
    // {
    //     return ($request->input($inputName) && !empty($request->input($inputName))) ? json_encode($request->input($inputName)) : '';
    // }


    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->product_catalogue_id]));
        }
        return [$request->product_catalogue_id];
    }

    public function updateStatus($post = [])
    {
        $product = $this->productReponsitory->findById($post['modelId']);
        // dd($product);
        DB::beginTransaction();
        try {
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $post = $this->productReponsitory->update($product, $payload);
            // $this->changeUserStatus($post, $payload[$post['field']]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatusAll($post)
    {
        DB::beginTransaction();
        try {
            dd($post);

            $payload[$post['field']] = $post['value'];
            $flag = $this->productReponsitory->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeUserStatus($post, $post['value']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function whereRaw($request, $languageId, $productCatalogue = null)
    {
        $rawCondition = [];
        if ($request->integer('product_catalogue_id') > 0 || !is_null($productCatalogue)) {
            $catId = ($request->integer('product_catalogue_id') > 0) ? $request->integer('product_catalogue_id') : $productCatalogue->id;
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
        return $rawCondition;
    }

    private function createVariant($product, $request, $languageId)
    {
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload, $product);
        $product->product_variants()->delete();
        $varriants = $product->product_variants()->createMany($variant);
        $variantId = $varriants->pluck('id');
        $productVariantLanguage = [];
        $variantAttribute = [];
        $attributeCombines = $this->combineAttributes(array_values($payload['attribute']));
        if (count($variantId)) {
            foreach ($variantId as $key => $val) {
                $productVariantLanguage[] = [
                    'product_variant_id' => $val,
                    'language_id' => $languageId,
                    'name' => $payload['productVariant']['name'][$key]
                ];

                if (count($attributeCombines)) {
                    foreach ($attributeCombines[$key] as $attributeId) {
                        $variantAttribute[] = [
                            'product_variant_id' => $val,
                            'attribute_id' => $attributeId
                        ];
                    }
                }
            }
        }
        $variantLanguage = $this->productVariantLanguageReponsitory->createBatch($productVariantLanguage);
        $variantAttributes = $this->productVariantAttributeReponsitory->createBatch($variantAttribute);
    }

    public function combineAttributes(array $attribute = [], $index = 0)
    {
        if ($index === count($attribute)) return [[]];

        $subCombines = $this->combineAttributes($attribute, $index + 1);
        $combines = [];

        foreach ($attribute[$index] as $key => $val) {
            foreach ($subCombines as $keySub => $valSub) {
                $combines[] = array_merge([$val], $valSub);
            }
        }

        return $combines;
    }

    private function createVariantArray(array $payload = [], $product): array
    {
        $variant = [];
        if (isset($payload['variant']['sku']) && count($payload['variant']['sku'])) {
            foreach ($payload['variant']['sku'] as $key => $val) {
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $payload['productVariant']['id'][$key]);
                $vId = ($payload['productVariant']['id'][$key]) ?? '';
                $productVariantId = sortString($vId);
                $variant[] = [
                    'uuid' => $uuid,
                    'code' => $productVariantId,
                    'quantity' => ($payload['variant']['quantity'][$key]) ?? '',
                    'sku' => $val,
                    'price' => ($payload['variant']['price'][$key]) ?  $this->convert_price($payload['variant']['price'][$key]) : '',
                    'barcode' => ($payload['variant']['barcode'][$key]) ?? '',
                    'file_name' => ($payload['variant']['file_name'][$key]) ?? '',
                    'file_url' => ($payload['variant']['file_url'][$key]) ?? '',
                    'album' => ($payload['variant']['album'][$key]) ?? '',
                    'user_id' => Auth::user()->id
                ];
            }
        }

        return $variant;
    }

    public function filter($request)
    {
        $perpage = $request->input('perpage');

        $param['priceQuery'] = $this->priceQuery($request);
        $param['attributeQuery'] = $this->attributeQuery($request);
        $param['rateQuery'] = $this->rateQuery($request);
        $param['productCatalogueQuery'] = $this->productCatalogueQuery($request);

        $query = $this->combineFilterQuery($param);

        $products = $this->productReponsitory->filter($query, $perpage);

        return $products;
    }

    private function combineFilterQuery($param){
        $query = [];
        foreach ($param as $array) {
            foreach ($array as $key => $value) {
                if (!isset($query[$key])) {
                    $query[$key] = [];
                }

                if(is_array($value)){
                    $query[$key] = array_merge($query[$key], $value);
                }else {
                    $query[$key][] = $value;
                }
            }
        }

        return $query;
    }

    public function priceQuery($request)
    {
        $price = $request->input('price');
        $priceMin = convert_price($price['price_min']);
        $priceMax = convert_price($price['price_max']);
        $query['select'] = null;
        $query['join'] = null;
        $query['where'] = null;

        if ($priceMax > $priceMin) {
            $query['join'] = [
                ['promotion_product_variant as ppv', 'ppv.product_id', '=', 'products.id'],
                ['promotions', 'ppv.promotion_id', '=', 'promotions.id'],
            ];

            $query['select'] = "
                (products.price - MAX(
                    IF(promotions.maxDiscountValue != 0,
                        LEAST(
                            CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN products.price * discountValue/100
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
                )) as discounted_price";
            $query['groupBy'] = ['products.id', 'products.price', 'products.image', 'pv.price', 'pv.sku'];
            $query['having'] = function($query) use ($priceMin, $priceMax) {
                $query->havingRaw('discounted_price >= ? AND discounted_price <= ?', [$priceMin, $priceMax]);
            };
        }

        return $query;
    }

    public function attributeQuery($request)
    {
        $attributes = $request->input('attributes');
        $query['select'] = null;
        $query['join'] = null;
        $query['where'] = null;

        if (!is_null($attributes) && count($attributes)) {
            $query['select'] = 'pv.price as variant_price, pv.sku as variant_sku';

            $query['join'] = [
                ['product_variants as pv', 'pv.product_id', '=', 'products.id']
            ];

            foreach ($attributes as $key => $attribute) {
                $joinKey = 'tb' . $key;
                $query['join'][] = ["product_variant_attribute as {$joinKey}", "$joinKey.product_variant_id", "=", "pv.id"];
                $query['where'][] = function ($query) use ($joinKey, $attribute) {
                    foreach ($attribute as $attr) {
                        $query->orWhere("$joinKey.attribute_id", "=", $attr);
                    }
                };
            }
        }

        return $query;
    }

    private function rateQuery($request){
        $rates = $request->input('rate');
        // $query['select'] = null;
        $query['join'] = null;
        $query['having'] = null;

        if(!is_null($rates) && count($rates)){
            $query['join'] = [
                ['reviews', 'reviews.reviewable_id', '=', 'products.id']
            ];
            $rateCondition = [];
            $bindings = [];

            foreach ($rates as $rate) {
                if($rate != 5){
                    $minRate = $rate.'.1';
                    $maxRate = $rate.'.9';
                    $rateCondition[] = '(AVG(reviews.score) >= ? AND AVG(reviews.score) <= ?)';
                    $bindings[] = $minRate;
                    $bindings[] = $maxRate;
                }else {
                    $rateCondition[] = 'AVG(reviews.score) = ?';
                    $bindings[] = 5;
                }
            }
            $query['where'] = function($query) {
                $query->where('reviews.reviewable_type', '=', 'App\\Models\\Product');
            };
            $query['having'] = function($query) use ($rateCondition, $bindings) {
                $query->havingRaw(implode(' OR ',$rateCondition) ,$bindings);
            };
        }

        return $query;
    }

    private function productCatalogueQuery($request){
        $productCatalogueId = $request->input('productCatalogueId');
        $query['join'] = null;
        $query['whereRaw'] = null;
        $query['where'] = function($query) use ($productCatalogueId){
            $query->where('products.product_catalogue_id', '=', $productCatalogueId);
        };
        // if($productCatalogueId > 0) {
        //     $query['join'] = [
        //         ['product_catalogue_product as pcp', 'pcp.product_id', '=', 'products.id']
        //     ];
        //     $query['whereRaw'] = [
        //         'pcp.product_id IN (
        //             SELECT id
        //             FROM product_catalogues 
        //             WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
        //             AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
        //         )',
        //         [$productCatalogueId, $productCatalogueId]
        //     ];
        // }

        return $query;

    }

    private function paginateSelect()
    {
        return [
            'products.id',
            'products.publish',
            'products.image',
            'products.order',
            'products.price',
            'tb2.name',
            'tb2.canonical',
        ];
    }

    private function payload()
    {
        return [
            'follow',
            'publish',
            'image',
            'album',
            'product_catalogue_id',
            'code',
            'made_in',
            'price',
            'attributeCatalogue',
            'attribute',
            'variant',
        ];
    }

    private function payloadLanguage()
    {
        return [
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }

    function convert_price(string $price = '')
    {
        return str_replace('.', '', $price);
    }

    public function combineproductAndPromotion($productId = [], $products, $checkDuplicate = false, $flag = false)
    {
        $promotions = $this->PromotionReponsitory->finByProduct($productId);
        // dd($promotions);
        if ($flag == true) {
            $products->promotions = $promotions;
            return $products;
        }
        if ($promotions) {
            foreach ($products as $index => $product) {
                foreach ($promotions as $key => $promotion) {
                    if ($promotion->product_id == $product->id) {
                        $products[$index]->promotions = $promotion;
                    }
                }
            }
        }
        if ($checkDuplicate == false) {
            $temp = [];
            foreach ($products as $key => $val) {
                $temp[$val->id] = $val;
            }
        } else {
            $temp = $products;
        }

        return $temp;
    }

    public function getAttribute($product, $language)
    {
        // dd($product->attribute);
        if (!is_null($product->attribute)) {
            $attributeCatalogueId = array_keys($product->attribute);
            $attrCatalogues = $this->AttributeCatalogueReponsitory->getAttributeCatalogueWhereIn(
                $attributeCatalogueId,
                'attribute_catalogues.id',
                $language
            );
            $attributeId = array_merge(...$product->attribute);
            $attrs = $this->AttributeReponsitory->findAttributeByIdArray($attributeId, $language);
            if (!is_null($attrCatalogues)) {
                foreach ($attrCatalogues as $key => $val) {
                    $temAttributes = [];
                    foreach ($attrs as $attr) {
                        if ($val->id == $attr->attribute_catalogue_id) {
                            $temAttributes[] = $attr;
                        }
                    }
                    $val->attributes = $temAttributes;
                }
            }
            $product->attributeCatalogue = $attrCatalogues;
        }
        return $product;
    }

    // public function paginateIndex($productCatalogue = null) {
    //     $products = $this->productReponsitory->paginationIndex($productCatalogue);
    // }
}
