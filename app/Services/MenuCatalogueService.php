<?php

namespace App\Services;

use App\Services\Interfaces\MenuCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\MenuCatalogueReponsitoryInterface as MenuCatalogueReponsitory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class MenuCatalogueService extends BaseService implements MenuCatalogueServiceInterface
{
    protected $MenuCatalogueReponsitory;
    protected $nestedset;
    protected $language;
    

    public function __construct(
        MenuCatalogueReponsitory $MenuCatalogueReponsitory,
    ){
        $this->MenuCatalogueReponsitory = $MenuCatalogueReponsitory;
    }

    public function paginate($request, $languageId){
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $menuCatalogues = $this->MenuCatalogueReponsitory->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/menu/index'],
            ['id','ASC',],
            [],
            [],
        );
           
        return $menuCatalogues;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->only(['name','keyword']);
            $payload['keyword'] = Str::slug($payload['keyword']);
            $menuCatalogue = $this->MenuCatalogueReponsitory->create($payload);
            DB::commit();
            return [
                'flag' => TRUE,
                'id' => $menuCatalogue->id,
                'name' => $menuCatalogue->name,
            ];
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

    private function paginateSelect(){
        return [
            'attribute_catalogues.id', 
            'attribute_catalogues.publish',
            'attribute_catalogues.image',
            'attribute_catalogues.level',
            'attribute_catalogues.order',
            'tb2.name', 
            'tb2.canonical',
        ];
    }

}
