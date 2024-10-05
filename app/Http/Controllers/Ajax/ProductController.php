<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Models\Language;
use App\Repositories\Interfaces\ProductReponsitoryInterface as ProductReponsitory;


use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productReponsitory;
    protected $languge;

    public function __construct(
        ProductReponsitory $productReponsitory,
    ) {
        $this->productReponsitory = $productReponsitory;

        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function loadProductPromotion(Request $request){
        $get = $request->input();
        $loadClass = loadClass($get['model']);
        $condition = [
            ['tb2.language_id', '=', $this->language]
        ];
        if(isset($get['keyword']) && $get['keyword'] != ''){
            $keywordCondition = ['tb2.name', 'LIKE', '%'.$get['keyword'].'%'];
            array_push($condition,$keywordCondition);
        }
        if($get['model'] == 'Product') {
            $objects = $loadClass->findProductForPromotion($condition);
        }else if($get['model'] == 'ProductCatalogue'){

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
                ['path' => 'product.catalogue.index'],  
                ['product_catalogues.id', 'ASC'],
                [
                    ['product_catalogue_language as tb2','tb2.product_catalogue_id', '=' , 'product_catalogues.id']
                ], 
                []
            );
        }

        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects
        ]);
    }
}
