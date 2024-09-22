<?php

namespace App\Services;

use App\Models\Language;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as languageRepository;
use App\Services\Interfaces\LanguageServiceInterface;
use Illuminate\Support\Facades\DB;


/**
 * Class UserService
 * @package App\Services
 */
class LanguageService implements LanguageServiceInterface
{
    protected $languageRepository;

    public function __construct(languageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function paginate($request)
    {
        // dd(123);
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
       
        $perPage = addslashes($request->integer('per_page'));

        $languages = $this->languageRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/post_catalogue/index'],
            ['id','ASC',],
            [],
            [],
        );
           
        // dd($languages);

        return $languages;
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $language = $this->languageRepository->create($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($data, $language)
    {
        DB::beginTransaction();
        try {
            // dd($data, $language);

            $updateUserCatalogue = $this->languageRepository->update($language, $data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($language)
    {
        DB::beginTransaction();
        try {
            $destroyLanguage = $this->languageRepository->destroy($language);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function switch($language){
        DB::beginTransaction();
        try {
            $updateLanguage= $this->languageRepository->update($language, ['current' => 1]);

            $payload = ['current' => 0];
            $where = [
                ['id', '!=', $language->id],
            ];
            $this->languageRepository->updateByWhere($where, $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
      
    }

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            // dd($post);   
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $language = Language::find($post['modelId']);
            $updateLanguage = $this->languageRepository->update($language, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatusAll($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];
            $flag = $this->languageRepository->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function saveTranslate($option, $request){
        DB::beginTransaction();
        try{
            $payload = [
                'name' => $request->input('translate_name'),
                'description' => $request->input('translate_description'),
                'content' => $request->input('translate_content'),
                'meta_title' => $request->input('translate_meta_title'),
                'meta_keyword' => $request->input('translate_meta_keyword'),
                'meta_description' => $request->input('translate_meta_description'),
                'canonical' => $request->input('translate_canonical'),
                $this->converModelToField($option['model']) => $option['id'],
                'language_id' => $option['languageId']
            ];
            // dd($payload);
            $controllerName = $option['model'].'Controller';
            $repositoryNamespace = '\App\Repositories\\' . ucfirst($option['model']) . 'Reponsitory';

            if (class_exists($repositoryNamespace)) {
                $repositoryInstance = app($repositoryNamespace);
            }

            $model = $repositoryInstance->findById($option['id']);

            $model->languages()->detach([$option['languageId'], $model->id]);
            $repositoryInstance->createPivot($model, $payload,'languages');
            

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function converModelToField($model){
        $temp = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model));
        return $temp.'_id';
    }

}
