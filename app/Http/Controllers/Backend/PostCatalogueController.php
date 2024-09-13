<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\PostCatalogueServiceInterface as PostCatalogueService;
use App\Repositories\Interfaces\PostCatalogueReponsitoryInterface as PostCatalogueReponsitory;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StorePostCatalogueRequest;
use App\Http\Requests\UpdatePostCatalogueRequest;
use App\Http\Requests\DeletePostCatalogueRequest;

use App\Models\PostCatalogue;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;

class PostCatalogueController extends Controller
{
    const PATH_UPLOAD = 'post_catalogues';

    protected $postCatalogueService;
    protected $postCatalogueReponsitory;
    protected $nestedset;
    protected $language;

    public function __construct(PostCatalogueService $postCatalogueService, PostCatalogueReponsitory $postCatalogueReponsitory, Nestedsetbie $nestedset)
    {
        $this->postCatalogueService = $postCatalogueService;
        $this->postCatalogueReponsitory = $postCatalogueReponsitory;
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' =>  1,
        ]);
        $this->language = $this->currentLanguage();
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.post_catalogue.index');
        $post_catalogues = $this->postCatalogueService->paginate($request);
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
        $config['seo'] = __('messages.postCatalogue');

        $template = 'backend.post.catalogue.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'post_catalogues'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.post_catalogue.create');
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

        $config['seo'] = __('messages.postCatalogue');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.post.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StorePostCatalogueRequest $request)
    {    
        if ($this->postCatalogueService->create($request)) {
            return redirect()->route('admin.post_catalogue.index')->with('success', 'Thêm mới Post Catalogue thành công !');
        } else {
            return redirect()->route('admin.post_catalogue.index')->with('error', 'Thêm mới Post Catalogue thất bại! Hãy thử lại');
        }
    }

    public function edit(PostCatalogue $post_catalogue){
        $this->authorize('modules', 'admin.post_catalogue.update');

        $postCatalogue = $this->postCatalogueReponsitory->getPostCatalogueById($post_catalogue->id, $this->language);

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

        $config['seo'] = __('messages.postCatalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.post.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'postCatalogue'
        ));
    }

    public function udpate(UpdatePostCatalogueRequest $request, PostCatalogue $post_catalogue){
       
        if ($this->postCatalogueService->update($request, $post_catalogue)) {
            return redirect()->route('admin.post_catalogue.index')->with('success', 'Cập nhật Post Catalogue thành công !');
        } else {
            return redirect()->route('admin.post_catalogue.index')->with('error', 'Cập nhật Post Catalogue thất bại! Hãy thử lại');
        }
    }

    public function delete(PostCatalogue $post_catalogue){
        $this->authorize('modules', 'admin.post_catalogue.destroy');
        $template = 'backend.post.catalogue.delete';
        $config['seo'] = __('messages.postCatalogue');

        $postCatalogue = $this->postCatalogueReponsitory->getPostCatalogueById($post_catalogue->id, $this->language);

        // dd($postCatalogue);
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'postCatalogue'
        ));
    }

    public function destroy(DeletePostCatalogueRequest $request, PostCatalogue $postCatalogue){
        
        $currentImage = $postCatalogue->image;

        if($currentImage && Storage::exists($currentImage)){
            Storage::delete($currentImage);
        }
       
        if ($this->postCatalogueService->destroy($postCatalogue)) {
            return redirect()->route('admin.post_catalogue.index')->with('success', 'Xóa Post Catalogue thành công !');
        } else {
            return redirect()->route('admin.post_catalogue.index')->with('error', 'Xóa Post Catalogue thất bại! Hãy thử lại');
        }
    }
}
