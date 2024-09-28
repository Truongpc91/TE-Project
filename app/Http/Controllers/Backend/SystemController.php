<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\SystemServiceInterface as SystemService;
use App\Repositories\Interfaces\SystemReponsitoryInterface as systemRepositories;
use Illuminate\Http\Request;
use App\Classes\System;
use App\Models\Language;

class SystemController extends Controller
{
    protected $systemLibrary;
    protected $systemService;
    protected $systemRepositories;
    protected $language;

    public function __construct(
        System $systemLibrary, 
        SystemService $systemService,
        systemRepositories $systemRepositories,
    ){

        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        
        $this->systemLibrary = $systemLibrary;
        $this->systemService = $systemService;
        $this->systemRepositories = $systemRepositories;

    }

    public function index(Request $request){
        $this->authorize('modules', 'admin.system.index');
        $systemConfig = $this->systemLibrary->config();
        $system = convert_array($this->systemRepositories->findByCondition([
            ['language_id', '=', $this->language]   
        ],TRUE), 'keyword', 'content');
        $config = $this->configData();
        $config['seo'] = __('messages.system');
        $template = 'backend.system.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'system',
        ));
    }

    public function store(Request $request){
        if($this->systemService->save($request, $this->language)){    
            return redirect()->route('admin.system.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('admin.system.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');    
    }

    public function translate($languageId = 0){
        $systemConfig = $this->systemLibrary->config();
        $system = convert_array($this->systemRepositories->findByCondition([
            ['language_id', '=', $languageId]   
        ],TRUE), 'keyword', 'content');
        $config = $this->configData();
        $config['seo'] = __('messages.system');
        $config['method'] = 'translate';
        $template = 'backend.system.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'languageId',
            'system',
        ));
    }

    public function saveTranslate(Request $request, $languageId){
        if($this->systemService->save($request, $languageId)){    
            return redirect()->route('admin.system.translate', ['languageId' => $languageId])->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('admin.system.translate', ['languageId' => $languageId])->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');   
    }

    private function configData(){
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/library/seo.js',
                'backend/library/finder.js',
                'backend/plugins/ckeditor/ckeditor.js',
            ]
        ];
    }
}
