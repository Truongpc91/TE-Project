<?php

namespace App\Services;

use App\Services\Interfaces\SourceServiceInterface;
use App\Services\BaseService;

use App\Repositories\Interfaces\SourceReponsitoryInterface as SourceReponsitory;

use Illuminate\Support\Facades\DB;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class SourceService extends BaseService implements SourceServiceInterface
{


    protected $SourceReponsitory;
    protected $language;
    

    public function __construct(
        SourceReponsitory $SourceReponsitory,
    ){
        $this->SourceReponsitory = $SourceReponsitory;
    }

    public function paginate($request, $languageId){
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
        ];
        $sources = $this->SourceReponsitory->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            ['path' => 'admin/source/index'],  
        );

        return $sources;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $request->only('name','keyword','description');
            $source = $this->SourceReponsitory->create($payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($request, $id){
        DB::beginTransaction();
        try{
            $payload = $request->only('name','keyword','description');
            $source = $this->SourceReponsitory->update($id,$payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $source = $this->SourceReponsitory->findById($id);
            $sourceDelete = $this->SourceReponsitory->destroy($source);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }


    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $postCatalogue = $this->SourceReponsitory->update($post['modelId'], $payload);
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
            $flag = $this->SourceReponsitory->updateByWhereIn('id', $post['id'], $payload);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function paginateSelect(){
        return [
           'id',
           'name',
           'keyword',
           'publish',
           'description',
        ];
    }

}
