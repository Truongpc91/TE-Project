<?php

namespace App\Services;

use App\Services\Interfaces\AttributeServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\AttributeReponsitoryInterface as AttributeReponsitory;
use App\Repositories\Interfaces\RouterReponsitoryInterface as RouterReponsitory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeService extends BaseService implements AttributeServiceInterface
{
    const PATH_UPLOAD = 'attributes';

    protected $attributeReponsitory;
    protected $routerReponsitory;
    
    public function __construct(
        AttributeReponsitory $attributeReponsitory,
        // RouterReponsitory $routerReponsitory,
    ){
        $this->attributeReponsitory = $attributeReponsitory;
        // $this->routerReponsitory = $routerReponsitory;
        $this->controllerName = 'AttributeController';
    }

    public function paginate($request, $languageId){
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId],
            ],
        ];
        $paginationConfig = [
            'path' => 'attribute.index', 
            'groupBy' => $this->paginateSelect()
        ];
        $orderBy = ['attributes.id', 'DESC'];
        $relations = ['attribute_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId);
        // dd($rawQuery);
        $joins = [
            ['attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id'],
            ['attribute_catalogue_attribute as tb3', 'attributes.id', '=', 'tb3.attribute_id'],
        ];

        $attributes = $this->attributeReponsitory->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            $paginationConfig,  
            $orderBy,
            $joins,  
            $relations,
            $rawQuery
        ); 
        return $attributes;
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $attribute = $this->createAttribute($request);
            if($attribute->id > 0){
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                // $this->createRouter($attribute, $request, $this->controllerName);
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

    public function update($attribute, $request, $languageId){
        DB::beginTransaction();
        try{
            $attribute = $this->attributeReponsitory->findById($attribute);
            if($this->uploadAttribute($attribute, $request)){
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                // $this->updateRouter(
                //     $attribute, $request, $this->controllerName
                // );
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

    public function destroy($attribute){
        DB::beginTransaction();
        try{
            $attribute = $this->attributeReponsitory->destroy($attribute);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            // echo $e->getMessage();die();
            return false;
        }
    }

    private function createAttribute($request){
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }
        $attribute = $this->attributeReponsitory->create($payload);
        return $attribute;
    }

    private function uploadAttribute($attribute, $request){
        $payload = $request->only($this->payload());
        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }

        $currentImage = $attribute->image;

        if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }
        return $this->attributeReponsitory->update($attribute, $payload);
    }

    private function updateLanguageForAttribute($attribute, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $attribute->id, $languageId);
        $attribute->languages()->detach([$this->language, $attribute->id]);
        return $this->attributeReponsitory->createPivot($attribute, $payload, 'languages');
    }

    private function updateCatalogueForAttribute($attribute, $request){
        $attribute->attribute_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $attributeId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] =  $languageId;
        $payload['attribute_id'] = $attributeId;
        return $payload;
    }


    private function catalogue($request){
        if($request->input('catalogue') != null){
            return array_unique(array_merge($request->input('catalogue'), [$request->attribute_catalogue_id]));
        }
        return [$request->attribute_catalogue_id];
    }
    
    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $postFind = $this->attributeReponsitory->findById($post['modelId']);
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $changStatusPost = $this->attributeReponsitory->update($postFind, $payload);

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
            // dd($post);

            $payload[$post['field']] = $post['value'];
            // dd($post['id'],$payload);
            $flag = $this->attributeReponsitory->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeUserStatus($post, $post['value']);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function whereRaw($request, $languageId){
        $rawCondition = [];
        if($request->integer('attribute_catalogue_id') > 0){
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.attribute_catalogue_id IN (
                        SELECT id
                        FROM attribute_catalogues
                        JOIN attribute_catalogue_language ON attribute_catalogues.id = attribute_catalogue_language.attribute_catalogue_id
                        WHERE lft >= (SELECT lft FROM attribute_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM attribute_catalogues as pc WHERE pc.id = ?)
                        AND attribute_catalogue_language.language_id = '.$languageId.'
                    )',
                    [$request->integer('attribute_catalogue_id'), $request->integer('attribute_catalogue_id')]
                ]
            ];
            
        }
        return $rawCondition;
    }

    private function paginateSelect(){
        return [
            'attributes.id', 
            'attributes.publish',
            'attributes.image',
            'attributes.order',
            'tb2.name', 
            'tb2.canonical',
        ];
    }

    private function payload(){
        return [
            'follow',
            'publish',
            'image',
            'album',
            'attribute_catalogue_id',
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
