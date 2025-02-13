<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\ProductServiceInterface  as ProductService;
use App\Repositories\Interfaces\ProductReponsitoryInterface  as ProductReponsitory;
use App\Repositories\Interfaces\AttributeCatalogueReponsitoryInterface  as AttributeCatalogueRepository;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Classes\Nestedsetbie;
use App\Models\AttributeCatalogue;
use App\Models\AttributeCatalogueLanguage;
use App\Models\Language;

class ProductController extends Controller
{
    protected $productService;
    protected $productReponsitory;
    protected $languageReponsitory;
    protected $nestedset;
    protected $language;
    protected $attributeCatalogue;

    public function __construct(
        ProductService $productService,
        ProductReponsitory $productReponsitory,
        AttributeCatalogueRepository $attributeCatalogue,
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->productService = $productService;
        $this->productReponsitory = $productReponsitory;
        $this->attributeCatalogue = $attributeCatalogue;
        $this->initialize();
        
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 

    public function index(Request $request){
        $this->authorize('modules', 'admin.product.index');
        $products = $this->productService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Product'
        ];
        $config['seo'] = __('messages.product');
        $template = 'backend.product.product.index';
        $dropdown  = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'products'
        ));
    }

    public function create(){
        $this->authorize('modules', 'admin.product.create');
        // $attributeCatalogue = $this->attributeCatalogue->getAll($this->language);
        $attributeCatalogue = AttributeCatalogue::with('attribute_catalogue_language')->get();
        // dd($attributeCatalogue[0]['attribute_catalogue_language']);
        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $config['method'] = 'create';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'backend.product.product.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'dropdown',
            'attributeCatalogue',
            'config',
        ));
    }

    public function store(StoreProductRequest $request){
        // dd($request);

        if($this->productService->create($request, $this->language)){
            return redirect()->route('admin.product.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('admin.product.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        // dd($id);
        $this->authorize('modules', 'admin.product.update');
        $attributeCatalogue = AttributeCatalogue::with('attribute_catalogue_language')->get();
        $product = $this->productReponsitory->getProductById($id, $this->language);

        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $config['method'] = 'edit';
        $dropdown  = $this->nestedset->Dropdown();
        // $album = json_decode($product->album);
        $template = 'backend.product.product.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'attributeCatalogue',
            'product',
            // 'album',
        ));
    }

    public function update($id, UpdateProductRequest $request){
        if($this->productService->update($id, $request, $this->language)){
            return redirect()->route('admin.product.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('admin.product.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'admin.product.destroy');
        $config['seo'] = __('messages.product');
        $product = $this->productReponsitory->getProductById($id, $this->language);
        $template = 'backend.product.product.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'product',
            'config',
        ));
    }

    public function destroy($id){
        if($this->productService->destroy($id, $this->language)){
            return redirect()->route('admin.product.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('admin.product.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData(){
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'backend/library/variant.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/nice-select/js/jquery.nice-select.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugins/nice-select/css/nice-select.css',
                'backend/css/plugins/switchery/switchery.css',
            ]
          
        ];
    }

   

}
