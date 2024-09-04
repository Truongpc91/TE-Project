<?php

namespace App\Services;

use App\Classes\Nestedsetbie;
use App\Models\Language;
use App\Repositories\Interfaces\PostCatalogueReponsitoryInterface as postCatalogueRepository;
use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    const PATH_UPLOAD = 'post_catalogues';
    protected $postCatalogueRepository;
    protected $nestedset;

    public function __construct(postCatalogueRepository $postCatalogueRepository, Nestedsetbie $nestedset)
    {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' =>  $this->currenLanguage(),
        ]);
    }

    public function paginate($request)
    {
        // dd(123);

        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = addslashes($request->integer('per_page'));

        $postCatalogues = $this->postCatalogueRepository->pagination(
            ['*'], $condition, [], ['path' => 'admin/post_catalogue/index'], $perPage, []);

        return $postCatalogues;
    }

    public function create($data)
    {
        DB::beginTransaction();
        try {
            $payload = $data->only(['parent_id', 'follow', 'publish', 'image', 'user_id']);
            $payload['user_id'] = Auth::user()->id;
            if ($data->hasFile('image')) {
                $payload['image'] = Storage::put(self::PATH_UPLOAD, $data->file('image'));
            }

            // dd($data);

            $postCatalogue = $this->postCatalogueRepository->create($payload);
            if($postCatalogue->id > 0){
                $payloadLanguage = $data->only($this->payloadLanguage());
                $payloadLanguage['language_id'] = $this->currenLanguage();
                $payloadLanguage['post_catalogue_id'] = $postCatalogue->id;

                // dd($payloadLanguage);

                $language = $this->postCatalogueRepository->createLanguagePivot($postCatalogue, $payloadLanguage);

                // dd($language);
                // dd($this->nestedset);
                $this->nestedset->Get('level ASC, order ASC');
                $this->nestedset->Recursive(0, $this->nestedset->Set());
                $this->nestedset->Action();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($data, $post_catalogue)
    {
        DB::beginTransaction();
        try {
            $updateUserCatalogue = $this->postCatalogueRepository->update($post_catalogue, $data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($post_catalogue)
    {
        DB::beginTransaction();
        try {
            $destroyPostCatalogue = $this->postCatalogueRepository->destroy($post_catalogue);
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
            $post_catalogue = Language::find($post['modelId']);
            $updatePostCatalogue = $this->postCatalogueRepository->update($post_catalogue, $payload);
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
            $flag = $this->postCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function payloadLanguage() {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }

}
