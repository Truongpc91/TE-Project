<?php

namespace App\Services;

use App\Services\Interfaces\ProductCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductCatalogueReponsitoryInterface as ProductCatalogueReponsitory;
// use App\Repositories\Interfaces\RouteReponsitoryInterface as RouterReponsitory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class ProductCatalogueService
 * @package App\Services
 */
class ProductCatalogueService extends BaseService implements ProductCatalogueServiceInterface
{
    const PATH_UPLOAD = 'product_catalogues';

    protected $productCatalogueReponsitory;
    // protected $routerReponsitory;
    protected $nestedset;
    protected $language;
    protected $controllerName = 'ProductCatalogueController';
    

    public function __construct(
        ProductCatalogueReponsitory $productCatalogueReponsitory,
        // RouterReponsitory $routerReponsitory,
    ){
        $this->productCatalogueReponsitory = $productCatalogueReponsitory;
        // $this->routerReponsitory = $routerReponsitory;
    }

    public function paginate($request, $languageId){
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $productCatalogues = $this->productCatalogueReponsitory->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            ['path' => 'product.catalogue.index'],  
            ['product_catalogues.lft', 'ASC'],
            [
                ['product_catalogue_language as tb2','tb2.product_catalogue_id', '=' , 'product_catalogues.id']
            ], 
            ['languages']
        );
        // dd($productCatalogues);
        return $productCatalogues;
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            if ($request->hasFile('image')) {
                $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
            }
            $productCatalogue = $this->createCatalogue($request);
            // dd($productCatalogue);
            if($productCatalogue->id > 0){
                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                // $this->createRouter($productCatalogue, $request, $this->controllerName);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'product_catalogues',
                    'foreignkey' => 'product_catalogue_id',
                    'language_id' =>  $languageId ,
                ]);
                $this->nestedset();
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request, $languageId){
        DB::beginTransaction();
        try{

            $productCatalogue = $this->productCatalogueReponsitory->findById($id);

            $flag = $this->updateCatalogue($productCatalogue, $request);
            if($flag == TRUE){
                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                // $this->updateRouter(
                //     $productCatalogue, $request, $this->controllerName
                // );
                $this->nestedset = new Nestedsetbie([
                    'table' => 'product_catalogues',
                    'foreignkey' => 'product_catalogue_id',
                    'language_id' =>  $languageId ,
                ]);
                $this->nestedset();
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id, $languageId){
        DB::beginTransaction();
        try{
            $productCatalogue = $this->productCatalogueReponsitory->findById($id);
            $deleteproductCatalogue = $this->productCatalogueReponsitory->destroy($productCatalogue);
            $this->nestedset = new Nestedsetbie([
                'table' => 'product_catalogues',
                'foreignkey' => 'product_catalogue_id',
                'language_id' =>  $languageId ,
            ]);
            $this->nestedset();
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function createCatalogue($request){
        // dd($request);
        $payload = $request->only($this->payload());
        // $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        $productCatalogue = $this->productCatalogueReponsitory->create($payload);
        // dd($productCatalogue);
        return $productCatalogue;
    }

    private function updateCatalogue($productCatalogue, $request){
        $payload = $request->only($this->payload());
        // dd($request);
        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }

        $currentImage = $productCatalogue->image;

        if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }
        // $payload['album'] = $this->formatAlbum($request);
        $flag = $this->productCatalogueReponsitory->update($productCatalogue, $payload);
        return $flag;
    }

    private function updateLanguageForCatalogue($productCatalogue, $request, $languageId){
        $payload = $this->formatLanguagePayload($productCatalogue, $request, $languageId);
        $productCatalogue->languages()->detach([$languageId, $productCatalogue->id]);
        $language = $this->productCatalogueReponsitory->createPivot($productCatalogue, $payload, 'languages');
        return $language;
    }

    private function formatLanguagePayload($productCatalogue, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] =  $languageId;
        $payload['product_catalogue_id'] = $productCatalogue->id;
        return $payload;
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $productCatalogue = $this->productCatalogueReponsitory->findById($post['modelId']);
            $postCatalogue = $this->productCatalogueReponsitory->update($productCatalogue, $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatusAll($post){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = $post['value'];
            $flag = $this->productCatalogueReponsitory->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function nestedset(){
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }

    // public function formatRouterPayload($model, $request, $controllerName, $languageId){
    //     $router = [
    //         'canonical' => Str::slug($request->input('canonical')),
    //         'module_id' => $model->id,
    //         'language_id' => $languageId,
    //         'controllers' => 'App\Http\Controllers\Frontend\\'.$controllerName.'',
    //     ];
    //     return $router;
    // }

    // public function createRouter($model, $request, $controllerName, $languageId){
    //     $router = $this->formatRouterPayload($model, $request, $controllerName, $languageId);
    //     $this->routerRepository->create($router);
    // }
    

    private function paginateSelect(){
        return [
            'product_catalogues.id', 
            'product_catalogues.publish',
            'product_catalogues.image',
            'product_catalogues.level',
            'product_catalogues.order',
            'tb2.name', 
            'tb2.canonical',
        ];
    }

    private function payload(){
        return [
            'parent_id',
            'follow',
            'publish',
            'image',
            'album',
        ];
    }
    private function payloadLanguage(){
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


}
