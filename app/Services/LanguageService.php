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

        $userCatalogues = $this->languageRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/language/index'],
            );
           
        // dd($userCatalogues);

        return $userCatalogues;
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

}
