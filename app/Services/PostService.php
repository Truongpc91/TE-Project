<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\Interfaces\PostReponsitoryInterface as postRepository;
use App\Services\BaseService;
use App\Services\Interfaces\PostServiceInterface;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;


/**
 * Class UserService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{
    const PATH_UPLOAD = 'posts';
    protected $postRepository;
    protected $nestedset;
    protected $language;

    public function __construct(postRepository $postRepository, Nestedsetbie $nestedset)
    {
        $this->postRepository = $postRepository;
        $this->nestedset = $nestedset;
    }

    public function paginate($request, $languageId)
    {
        // $condition['keyword'] = addslashes($request->input('keyword'));
        // $condition['publish'] = $request->integer('publish');
        // $condition['post_catalogue_id'] = $request->input('post_catalogue_id');
        // $condition['where'] = [
        //     ['tb2.language_id', '=', $languageId],
        // ];
        // $perPage = addslashes($request->integer('per_page'));

        // $posts = $this->postRepository->pagination(
        //     ['*'],
        //     $condition,
        //     $perPage,
        //     ['path' => 'admin/posts/index'],
        //     ['posts.id', 'DESC'],
        //     [
        //         ['post_language as tb2', 'tb2.post_id', '=', 'id'],
        //         ['post_catalogue_post as tb3', 'posts.id', '=', 'tb3.post_id']
        //     ],
        //     ['post_catalogues'],
        //     $this->whereRaw($request, $languageId),
        // );
        // return $posts;
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => ($request->input('keyword')) ? addslashes($request->input('keyword')) : '',
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId],
            ],
        ];
        $paginationConfig = [
            'path' => 'post.index',
            'groupBy' => $this->paginateSelect()
        ];
        $orderBy = ['posts.id', 'DESC'];
        $relations = ['post_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId);
        // dd($rawQuery);
        $joins = [
            ['post_language as tb2', 'tb2.post_id', '=', 'posts.id'],
            ['post_catalogue_post as tb3', 'posts.id', '=', 'tb3.post_id'],
        ];

        $posts = $this->postRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            $paginationConfig,
            $orderBy,
            $joins,
            $relations,
            $rawQuery
        );
        return $posts;
    }

    public function create($data, $languageId)
    {
        DB::beginTransaction();
        try {
            $post = $this->createPost($data);
            if ($post->id > 0) {
                $this->createLanguageForPost($post, $data, $languageId);
                $this->createCatalogueForPost($post, $data);
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

    public function update($request, $post, $languageId)
    {
        DB::beginTransaction();
        try {
            if ($this->uploadPost($post, $request)) {
                $this->updateLanguageForPost($post, $request, $languageId);
                $this->updateCatalogueForPost($post, $request);
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

    public function destroy($post)
    {
        DB::beginTransaction();
        try {
            $destroyPost = $this->postRepository->destroy($post);
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
            $payload[$post['field']] = (($post['value'] == 1) ? 2 : 1);
            $post = Post::find($post['modelId']);
            $updatePost = $this->postRepository->update($post, $payload);
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
            $flag = $this->postRepository->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function whereRaw($request, $languageId)
    {
        $rawCondition = [];
        if ($request->integer('post_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.post_catalogue_id IN (
                        SELECT id
                        FROM post_catalogues
                        JOIN post_catalogue_language ON post_catalogues.id = post_catalogue_language.post_catalogue_id
                        WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
                        AND post_catalogue_language.language_id = ' . $languageId . '
                    )',
                    [$request->integer('post_catalogue_id'), $request->integer('post_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }

    private function createPost($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::user()->id;

        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }
        return $post = $this->postRepository->create($payload);
    }

    private function createLanguageForPost($post, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_id'] = $post->id;

        return  $this->postRepository->createPivot($post, $payload, 'languages');
    }

    private function createCatalogueForPost($post, $request)
    {
        $post->post_catalogues()->sync($this->catalogue($request));
    }

    private function uploadPost($post, $request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::user()->id;

        if ($request->hasFile('image')) {
            $payload['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }

        $currentImage = $post->image;

        if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }

        return $flag = $this->postRepository->update($post, $payload);
    }

    private function updateLanguageForPost($post, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($request['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_id'] = $post->id;
        $post->languages()->detach([$payload['language_id'], $post->id]);

        return $this->postRepository->createPivot($post, $payload, 'languages');
    }

    private function updateCatalogueForPost($post, $request)
    {
        $post->post_catalogues()->sync($this->catalogue($request));
    }

    private function catalogue($request)
    {
        return array_unique(array_merge($request->input('catalogue'), [$request->post_catalogue_id]));
    }

    private function payloadLanguage()
    {
        return ['name', 'description', 'content', 'meta_title', 'meta_keyword', 'meta_description', 'canonical'];
    }

    private function payload()
    {
        return [
            'image',
            'publish',
            'follow',
            'order',
            'user_id',
            'post_catalogue_id',
        ];
    }

    private function paginateSelect()
    {
        return [
            'posts.id',
            'posts.publish',
            'posts.image',
            'posts.order',
            'tb2.name',
            'tb2.canonical',
        ];
    }
}
