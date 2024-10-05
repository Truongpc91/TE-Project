<?php

namespace App\Services;

use App\Models\Language;
use App\Models\ProductGallery;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductReponsitoryInterface as ProductReponsitory;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterReponsitory;
use App\Repositories\Interfaces\ProductVariantLanguageReponsitoryInterface as ProductVariantLanguageReponsitory;
use App\Repositories\Interfaces\ProductVariantAttributeReponsitoryInterface as ProductVariantAttributeReponsitory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
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

    public function __construct(
        ProductReponsitory $productReponsitory,
        RouterReponsitory $routerReponsitory,
        ProductVariantLanguageReponsitory $productVariantLanguageReponsitory,
        ProductVariantAttributeReponsitory $productVariantAttributeReponsitory,
    ) {
        $this->productReponsitory = $productReponsitory;
        $this->productVariantLanguageReponsitory = $productVariantLanguageReponsitory;
        $this->productVariantAttributeReponsitory = $productVariantAttributeReponsitory;
        $this->routerReponsitory = $routerReponsitory;
        $this->controllerName = 'ProductController';
    }

    public function paginate($request, $languageId)
    {
        // dd($languageId);
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId],
            ],
        ];
        $paginationConfig = [
            'path' => 'product.index',
            'groupBy' => $this->paginateSelect()
        ];
        $orderBy = ['products.id', 'DESC'];
        $relations = ['product_catalogues','languages'];
        $rawQuery = $this->whereRaw($request, $languageId);
        $joins = [
            ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
            ['product_catalogue_product as tb3', 'products.id', '=', 'tb3.product_id'],
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
                if($request->input('attribute')){
                    $this->createVariant($product, $request, $languageId);
                }

               
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
            $product = $this->productReponsitory->findById($id);
            if ($this->uploadProduct($product, $request)) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter(
                    $product, $request, $this->controllerName, $languageId
                );
                $product->product_variants()->each(function($variant){
                    $variant->languages()->detach();
                    $variant->attributes()->detach();
                    $variant->delete();
                });
                if($request->input('attribute')){
                    $this->createVariant($product, $request, $languageId);
                }
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
        $payload['attribute'] = $this->formatJson($request, 'attribute');   
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

    private function uploadProduct($product, $request)
    {
        $payload = $request->only($this->payload());

        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }

        $currentImage = $product->image;

        if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }

        return $this->productReponsitory->update($product, $payload);
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

    private function whereRaw($request, $languageId)
    {
        $rawCondition = [];
        if ($request->integer('product_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        JOIN product_catalogue_language ON product_catalogues.id = product_catalogue_language.product_catalogue_id
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                        AND product_catalogue_language.language_id = ' . $languageId . '
                    )',
                    [$request->integer('product_catalogue_id'), $request->integer('product_catalogue_id')]
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
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS,$product->id.', '.$payload['productVariant']['id'][$key]);

                $variant[] = [
                    'uuid' => $uuid,
                    'code' => ($payload['productVariant']['id'][$key]) ?? '',
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
}
