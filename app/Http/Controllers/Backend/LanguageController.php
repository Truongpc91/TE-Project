<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use App\Repositories\Interfaces\LanguageReponsitoryInterface as LanguageReponsitory;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreLanguageRequest;
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
        // dd($user_catalogues);
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
        // dd($provinces);
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
        // dd($data);

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

    public function delete(Language $languages){
        $template = 'backend.language.delete';
        $config['seo'] = config('apps.language');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'languages'
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
}
