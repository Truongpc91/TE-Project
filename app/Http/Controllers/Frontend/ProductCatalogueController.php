<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\ProductCatalogueReponsitoryInterface as ProductCataloguesReponsitory;
use App\Services\Interfaces\ProductServiceInterface as ProductService;

use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;

use App\Enums\SlideEnums;
use Illuminate\Http\Request;

class ProductCatalogueController extends FrontendController
{
    protected $language;
    protected $ProductCataloguesReponsitory;
    protected $ProductService;
    protected $SlideService;
    protected $ProductCatalogueService;

    
    public function __construct( 
        ProductCataloguesReponsitory $ProductCataloguesReponsitory,
        ProductService $ProductService,
        SlideService $SlideService,
        ProductCatalogueService $ProductCatalogueService,
    ) {
        parent::__construct();
        $this->ProductCataloguesReponsitory = $ProductCataloguesReponsitory; 
        $this->ProductService = $ProductService;
        $this->SlideService = $SlideService;
        $this->ProductCatalogueService = $ProductCatalogueService;
    }

    public function index($id, $request, $page = 1){
        $productCatalogue = $this->ProductCataloguesReponsitory->getProductCatalogueById($id,$this->language);
       
        $filters = $this->filter($productCatalogue);

        $breadcrumb = $this->ProductCataloguesReponsitory->breadcrumb($productCatalogue, $this->language);
        $products = $this->ProductService->paginate(
            $request,
            $this->language,
            $productCatalogue,
            $page,
            ['path' => $productCatalogue->canonical],
        );
        // dd($products);
        $productId = $products->pluck('id')->toArray();
        if(count($productId) && !is_null($productId)){
            $products = $this->ProductService->combineproductAndPromotion($productId, $products, true);
        }

        $config = $this->config();
        $template = 'frontend.product.catalogue.index';
        return view($template, compact(
            'template',
            'config',
            'productCatalogue',
            'breadcrumb',
            'products',
            'filters'
        ));
    }   

    private function filter($productCatalogue) {
        $filters = null;
        
        if(isset($productCatalogue->attribute) && !is_null($productCatalogue->attribute) && count($productCatalogue->attribute)){
            $filters = $this->ProductCatalogueService->getFilterList($productCatalogue->attribute, $this->language);
        }

        return $filters;
    }

    private function config()
    {
        return [
            'js' => [
                'frontend/core/library/filter.js'
            ],
        ];
    }
}
