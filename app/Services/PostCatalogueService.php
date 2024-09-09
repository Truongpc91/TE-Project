<?php

namespace App\Services;

use App\Classes\Nestedsetbie;
use App\Models\Language;
use App\Models\PostCatalogue;
use App\Repositories\Interfaces\PostCatalogueReponsitoryInterface as postCatalogueRepository;
use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/**
 * Class UserService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    const PATH_UPLOAD = 'post_catalogues';
    protected $postCatalogueRepository;
    protected $nestedset;
    protected $language;

    public function __construct(postCatalogueRepository $postCatalogueRepository, Nestedsetbie $nestedset)
    {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' =>  $this->currenLanguage(),
        ]);
        $this->language = $this->currenLanguage();
    }

    public function paginate($request)
    {
        // dd(123);

        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['where'] = [
            ['tb2.language_id', '=', $this->language]
        ];
        $perPage = addslashes($request->integer('per_page'));

        $postCatalogues = $this->postCatalogueRepository->pagination(
            ['*'],
            $condition,
            $perPage,
            ['path' => 'admin/post_catalogue/index'],
            [
                'post_catalogues.lft',
                'ASC',
            ],
            [
                ['post_catalogue_language as tb2','tb2.post_catalogue_id', '=' , 'post_catalogues.id']
            ],
            [],

            // array $column = ['*'],
            // array $condition = [],
            // int $perPage = 5,
            // array $extend = [],
            // array $orderBy = ['id', 'DESC'],
            // array $join = [],
            // array $relations = [],
            
        );

        // dd($postCatalogues);

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
            if ($postCatalogue->id > 0) {
                $payloadLanguage = $data->only($this->payloadLanguage());
                $payloadLanguage['canonical'] = Str::slug($payloadLanguage['canonical']);
                $payloadLanguage['language_id'] = $this->currenLanguage();
                $payloadLanguage['post_catalogue_id'] = $postCatalogue->id;

                // dd($payloadLanguage);

                $language = $this->postCatalogueRepository->createPivot($postCatalogue, $payloadLanguage,'languages');

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

    public function update($request, $post_catalogue)
    {
        DB::beginTransaction();
        try {
            
            // dd($post_catalogue);
            $data = $request->only(['parent_id', 'follow', 'publish', 'image', 'user_id']);
            $flag = $this->postCatalogueRepository->update($post_catalogue, $data);
            if($flag == true){
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['canonical'] = Str::slug($payloadLanguage['canonical']);
                $payloadLanguage['language_id'] = $this->currenLanguage();
                $payloadLanguage['post_catalogue_id'] = $post_catalogue->id;
                $post_catalogue->languages()->detach([$payloadLanguage['language_id'], $post_catalogue->id]);

                $response = $this->postCatalogueRepository->createLanguagePivot($post_catalogue, $payloadLanguage);
                $this->nestedset->Get('level ASC, order ASC');
                $this->nestedset->Recursive(0, $this->nestedset->Set());
                $this->nestedset->Action();
            }
            $data['user_id'] = Auth::user()->id;
            // dd($data);

            if ($request->hasFile('image')) {
                $data['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
            }

            $currentImage = $post_catalogue->image;

            if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
                Storage::delete($currentImage);
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

    public function destroy($post_catalogue)
    {
        DB::beginTransaction();
        try {
            $destroyPostCatalogue = $this->postCatalogueRepository->destroy($post_catalogue);
            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();
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
            $post_catalogue = PostCatalogue::find($post['modelId']);
            // dd($post_catalogue, $payload);   
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

    private function payloadLanguage()
    {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }
}
