<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\PostServiceInterface as PostService;
use App\Repositories\Interfaces\PostReponsitoryInterface as PostReponsitory;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as languageReponsitory;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\DeletePostRequest;

use App\Models\Post;
use App\Models\Language;

use App\Classes\Nestedsetbie;

class PostController extends Controller
{
    const PATH_UPLOAD = 'posts';

    protected $postService;
    protected $postReponsitory;
    protected $nestedset;
    protected $language;
    protected $languageReponsitory;

    public function __construct(PostService $postService, PostReponsitory $postReponsitory, Nestedsetbie $nestedset, languageReponsitory $languageReponsitory)
    {
        $this->postService = $postService;
        $this->postReponsitory = $postReponsitory;
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' =>  1,
        ]);
        $this->language = $this->currentLanguage();
        $this->languageReponsitory = $this->languageReponsitory;
    }

    public function index(Request $request)
    {
        $posts = $this->postService->paginate($request);


        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];

        $config['model'] = 'Post';
        $config['seo'] = config('apps.post');
        $dropDown = $this->nestedset->Dropdown();
        $template = 'backend.post.post.index';
        $languages = Language::all();
        // dd($language);
        // dd($posts);
        return view('backend.dashboard.layout', compact(
            'template', 'config', 'posts', 'dropDown','languages'
            )
        );
    }

    public function create()
    {
        // dd($user_s);
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/library/seo.js',
                'backend/library/finder.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = config('apps.post');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StorePostRequest $request)
    {
        // $data = $request->except('_token','send');

        // $data['user_id'] = Auth::user()->id;
        // dd($data);

        if ($this->postService->create($request)) {
            return redirect()->route('admin.posts.index')->with('success', 'Thêm mới Post  thành công !');
        } else {
            return redirect()->route('admin.posts.index')->with('error', 'Thêm mới Post  thất bại! Hãy thử lại');
        }
    }

    public function edit(Post $post){
        // dd($post_->id);
        $post = $this->postReponsitory->getPostById($post->id, $this->language);
        
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/library/seo.js',
                'backend/library/finder.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
            ]
        ];

        $config['seo'] = config('apps.post');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'post'
        ));
    }

    public function udpate(UpdatePostRequest $request, Post $post){
        // echo 123;die();
        // dd($request);
       
        if ($this->postService->update($request, $post)) {
            return redirect()->route('admin.posts.index')->with('success', 'Cập nhật Post thành công !');
        } else {
            return redirect()->route('admin.posts.index')->with('error', 'Cập nhật Post thất bại! Hãy thử lại');
        }
    }

    public function delete(Post $post){
        $template = 'backend.post.post.delete';
        $config['seo'] = config('apps.post');

        $post = $this->postReponsitory->getPostById($post->id, $this->language);

        // dd($post);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'post'
        ));
    }

    public function destroy(Post $post){
        
        $currentImage = $post->image;

        if($currentImage && Storage::exists($currentImage)){
            Storage::delete($currentImage);
        }
       
        if ($this->postService->destroy($post)) {
            return redirect()->route('admin.posts.index')->with('success', 'Xóa Post  thành công !');
        } else {
            return redirect()->route('admin.posts.index')->with('error', 'Xóa Post  thất bại! Hãy thử lại');
        }
    }
}
