<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as LanguageReponsitory;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\TranslateRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    const PATH_UPLOAD = 'languages';

    protected $LanguageService;
    protected $languageReponsitory;

    public function __construct(LanguageService $LanguageService, LanguageReponsitory $languageReponsitory)
    {
        $this->LanguageService = $LanguageService;
        $this->languageReponsitory = $languageReponsitory;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.language.index');

        $languages = $this->LanguageService->paginate($request);

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

        $config['seo'] = config('apps.language');

        $template = 'backend.language.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'languages'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.language.create');

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = config('apps.language');
        $config['method'] = 'create';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreLanguageRequest $request)
    {
        
        $data = $request->except('_token','send');

        $data['user_id'] = Auth::user()->id;
        // dd($data);

        if ($request->hasFile('image')) {
            $data['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }
       
        if ($this->LanguageService->create($data)) {
            return redirect()->route('admin.language.index')->with('success', 'Thêm mới Language thành công !');
        } else {
            return redirect()->route('admin.language.index')->with('error', 'Thêm mới Language thất bại! Hãy thử lại');
        }
    }

    public function edit(Language $language){
        // dd($user);
        $this->authorize('modules', 'admin.language.update');

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = config('apps.language');
        $config['method'] = 'edit';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language'
        ));
    }

    public function udpate(UpdateLanguageRequest $request, Language $languages){

        $data = $request->except('_token', 'send', '_method');
        $data['user_id'] = Auth::user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }

        $currentImage = $languages->image;

        if($request->hasFile('image') && $currentImage && Storage::exists($currentImage)){
            Storage::delete($currentImage);
        }

        if ($this->LanguageService->update($data, $languages)) {
            return redirect()->route('admin.language.index')->with('success', 'Cập nhật Language thành công !');
        } else {
            return redirect()->route('admin.language.index')->with('error', 'Cập nhật Language thất bại! Hãy thử lại');
        }
    }

    public function delete(Language $language){
        $this->authorize('modules', 'admin.language.destroy');

        $template = 'backend.language.delete';
        $config['seo'] = config('apps.language');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language'
        ));
    }

    public function destroy(Language $language){
        $currentImage = $language->image;

        if($currentImage && Storage::exists($currentImage)){
            Storage::delete($currentImage);
        }
       
        if ($this->LanguageService->destroy($language)) {
            return redirect()->route('admin.language.index')->with('success', 'Xóa Language thành công !');
        } else {
            return redirect()->route('admin.language.index')->with('error', 'Xóa Language thất bại! Hãy thử lại');
        }
    }

    public function swicthBackendLanguage(Language $language){
        if($this->LanguageService->switch($language)){
            // dd($language->canonical);
            session(['app_locale' => $language->canonical]);
            \App::setLocale($language->canonical);
        }

        return redirect()->back();
    }

    public function translate($id = 0, $languageId = 0, $model = ''){
        $repositoryInstance = $this->respositoryInstance($model);

        $languageInstance = $this->respositoryInstance('Language');
        $currentLanguage = $languageInstance->findByCondition([
            ['canonical' , '=', session('app_locale')]
        ]);
        $method = 'get'.$model.'ById';
        $object = $repositoryInstance->{$method}($id, $currentLanguage->id);
        // dd($object);

        $objectTranslate = $repositoryInstance->{$method}($id, $languageId);

        $this->authorize('modules', 'admin.language.translate');
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

        $config['seo'] = config('apps.language');
        $template = 'backend.language.translate';
        $option = [
            'id' => $id,
            'languageId' => $languageId,
            'model' => $model,
        ];
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'option',
            'object',
            'objectTranslate'
        ));
    }

    private function respositoryInstance($model){
        $repositoryNamespace = '\App\Repositories\\' . ucfirst($model) . 'Reponsitory';
        if (class_exists($repositoryNamespace)) {
            $repositoryInstance = app($repositoryNamespace);
        }
        return $repositoryInstance ?? null;
    }

    public function storeTranslate(TranslateRequest $request){
        // dd($request);
        $option = $request->input('option');
        if($this->LanguageService->saveTranslate($option, $request)){
            return redirect()->back()->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->back()->with('error','Có vấn đề xảy ra, Hãy Thử lại');
    }
}
