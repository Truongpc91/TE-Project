<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\ProductCatalogueReponsitoryInterface as ProductCataloguesReponsitory;
use App\Repositories\Interfaces\ProductReponsitoryInterface as ProductReponsitory;
use App\Repositories\Interfaces\ReviewReponsitoryInterface as ReviewReponsitory;

use App\Services\Interfaces\ProductServiceInterface as ProductService;

use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;


class ProductController extends FrontendController
{
    protected $language;
    protected $ProductCataloguesReponsitory;
    protected $ProductReponsitory;
    protected $ProductService;
    protected $SlideService;
    protected $ProductCatalogueService;
    protected $ReviewReponsitory;

    public function __construct(
        ProductCataloguesReponsitory $ProductCataloguesReponsitory,
        ProductReponsitory $ProductReponsitory,
        ProductService $ProductService,
        SlideService $SlideService,
        ProductCatalogueService $ProductCatalogueService,
        ReviewReponsitory $ReviewReponsitory,
    ) {
        parent::__construct();
        $this->ProductCataloguesReponsitory = $ProductCataloguesReponsitory;
        $this->ProductReponsitory = $ProductReponsitory;
        $this->ProductService = $ProductService;
        $this->SlideService = $SlideService;
        $this->ProductCatalogueService = $ProductCatalogueService;
        $this->ReviewReponsitory = $ReviewReponsitory;
    }

    public function index($id, $request)
    {

        $product = $this->ProductReponsitory->getProductById($id, $this->language);
        $product = $this->ProductService->combineproductAndPromotion([$id], $product, false, true);

        $productCatalogue = $this->ProductCataloguesReponsitory->getProductCatalogueById($product->product_catalogue_id, $this->language);
        $breadcrumb = $this->ProductCataloguesReponsitory->breadcrumb($productCatalogue, $this->language);
        $product = $this->ProductService->getAttribute($product, $this->language);
        $language = $this->language;
        $category = recursive($this->ProductCataloguesReponsitory->all(
            [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', '=', $language);
                }
            ],
            categorySelectRaw('product')
        ));

        $reviews = $this->ReviewReponsitory->getReviewByproduct($product->id, 'App\Product');
        // dd($product);
        $config = $this->config();
        $template = 'frontend.product.product.index';
        return view($template, compact(
            'template',
            'config',
            'productCatalogue',
            'breadcrumb',
            'product',
            'category',
            'language',
            'reviews'
        ));
    }

    private function config()
    {
        return [
            'js' => [
                'frontend/core/library/cart.js',
                'frontend/core/library/product.js',
                'frontend/core/library/review.js',
            ]
        ];
    }

    private function categeorySelectRaw() {}
}
