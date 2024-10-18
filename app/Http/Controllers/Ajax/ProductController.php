<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Models\Language;
use App\Repositories\Interfaces\ProductReponsitoryInterface as ProductReponsitory;
use App\Repositories\Interfaces\ProductVariantReponsitoryInterface as ProductVariantReponsitory;
use App\Repositories\Interfaces\PromotionReponsitoryInterface as PromotionReponsitory;
use App\Repositories\Interfaces\AttributeReponsitoryInterface as AttributeReponsitory;
use App\Services\Interfaces\PromotionServiceInterface as PromotionService;
use App\Services\Interfaces\ProductServiceInterface as ProductService;


use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productReponsitory;
    protected $ProductVariantReponsitory;
    protected $PromotionReponsitory;
    protected $AttributeReponsitory;
    protected $PromotionService;
    protected $ProductService;
    protected $languge;

    public function __construct(
        ProductReponsitory $productReponsitory,
        ProductVariantReponsitory $ProductVariantReponsitory,
        PromotionReponsitory $PromotionReponsitory,
        AttributeReponsitory $AttributeReponsitory,
        PromotionService $PromotionService,
        ProductService $ProductService,
    ) {
        $this->productReponsitory = $productReponsitory;
        $this->ProductVariantReponsitory = $ProductVariantReponsitory;
        $this->PromotionReponsitory = $PromotionReponsitory;
        $this->AttributeReponsitory = $AttributeReponsitory;
        $this->PromotionService = $PromotionService;
        $this->ProductService = $ProductService;

        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function loadProductPromotion(Request $request)
    {
        $get = $request->input();
        $loadClass = loadClass($get['model']);
        $condition = [
            ['tb2.language_id', '=', $this->language]
        ];
        if (isset($get['keyword']) && $get['keyword'] != '') {
            $keywordCondition = ['tb2.name', 'LIKE', '%' . $get['keyword'] . '%'];
            array_push($condition, $keywordCondition);
        }
        if ($get['model'] == 'Product') {
            $objects = $loadClass->findProductForPromotion($condition);
        } else if ($get['model'] == 'ProductCatalogue') {

            $conditionArray['keyword'] = ($get['keyword']) ?? null;
            $conditionArray['where'] = [
                ['tb2.language_id', '=', $this->language]
            ];

            $objects = $loadClass->pagination(
                [
                    'product_catalogues.id',
                    'tb2.name'
                ],
                $conditionArray,
                20,
                ['path' => 'val.catalogue.index'],
                ['product_catalogues.id', 'ASC'],
                [
                    ['product_catalogue_language as tb2', 'tb2.product_catalogue_id', '=', 'product_catalogues.id']
                ],
                []
            );
        }

        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects
        ]);
    }

    public function loadVariant(Request $request)
    {
        $get = $request->input();
        $attributeId = $get['attribute_id'];

        $attributeId = sortAttributeId($attributeId);

        $variant = $this->ProductVariantReponsitory->findVariant($attributeId, $get['product_id'], $get['language_id']);
        $variantPromotion = $this->PromotionReponsitory->findPromotionByVariantUuid($variant->uuid);
        $variantPrice = getVariantPrice($variant, $variantPromotion);

        return response()->json([
            'variant' => $variant,
            'variantPrice' => $variantPrice
        ]);
    }

    public function filter(Request $request)
    {
        $products = $this->ProductService->filter($request);

        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->ProductService->combineproductAndPromotion($productId, $products, true);
        }
        $html = $this->renderFilter($products);
        // dd($html);
        return response()->json([
            'data' => $html
        ]);
    }

    public function renderFilter($products)
    {
        $html = '';
        if (!is_null($products) && count($products)) {
            $html .= '<div class="uk-grid uk-grid-medium">';
            foreach ($products as $key => $val) {
                $name = $val->languages->first()->pivot->name;
                $canonical = write_url($val->languages->first()->pivot->canonical);
                $image = $val->image;
                $price = getPrice($val);
                foreach ($val->product_catalogues as $catalogue) {
                    if ($catalogue->languages->isNotEmpty()) {
                        $catName = $catalogue->languages->first()->pivot->name;
                    }
                }
                $review = getReview($val);
                $html .= '<div class="uk-width-large-1-5 mb20">';
                $html .= '<div class="product-item product">';
                if ($price['percent'] > 0) {
                    $html .= "<div class='badge badge-bg1'>-{$price['percent']}%</div>";
                }
                $html .= "<a href='$canonical' class='image img-cover'><img src='http://shopprojectt.test//storage/{$image}' alt=''></a>";
                $html .= "<div class='info'>";
                $html .= "<div class='category-title'><a href=''' title=''>$catName</a></div>";
                $html .= "<h3 class=' title'><a href='$canonical' title=''>$name</a></h3>";
                $html .= "<div class='rating'>";
                $html .= "<div class='uk-flex uk-flex-middle'>";
                $html .= "<div class='star'>";
                for ($i = 0; $i < $review['stars']; $i++) {
                    $html .= "<i class='fa fa-star'></i>";
                }
                $html .= "</div>";
                $html .= "<span class='rate-number'>{$review['count']}</span>";
                $html .= " </div>";
                $html .= "</div>";
                $html .= "<div class='product-group'>";
                $html .= "<div class='uk-flex uk-flex-middle uk-flex-space-between'>";
                $html .= $price['html'];
                $html .= "<div class='addcart'>";
                $html .= renderQuickBuy($val, $canonical, $name);
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
                $html .= "<div class='tools'>";
                $html .= "<a href='title='><img src='../frontend/resources/img/trend.svg' alt=''></a>";
                $html .= "<a href='title='><img src='../frontend/resources/img/wishlist.svg' alt=''></a>";
                $html .= "<a href='title='><img src='../frontend/resources/img/compare.svg' alt=''></a>";
                $html .= "<a href='#popup' data-uk-modal title=''><img src='../frontend/resources/img/view.svg' alt=''></a>";
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
            }
            $html .= "</div>";

            // $html .= $products->links('pagination: bootstrap-4');

        } else {
            $html = "<div class='non-result'>
                Không có sản phẩm nào giống với mô tả
            </div>";
        }

        return $html;
    }
}
