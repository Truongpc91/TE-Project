<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Interfaces\{$class}ServiceInterface  as {$class}Service;
use App\Repositories\Interfaces\{$class}ReponsitoryInterface  as {$class}Reponsitory;
use App\Http\Requests\Store{$class}Request;
use App\Http\Requests\Update{$class}Request;
use App\Http\Requests\Delete{$class}Request;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class {$class}Controller extends Controller
{
    protected ${module}Service;
    protected ${module}Reponsitory;
    protected $languageReponsitory;
    protected $language;

    public function __construct(
        {$class}Service ${module}Service,
        {$class}Reponsitory ${module}Reponsitory,
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->{module}Service = ${module}Service;
        $this->{module}Reponsitory = ${module}Reponsitory;
        $this->initialize();
        
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => '{module}_catalogues',
            'foreignkey' => '{module}_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 

    public function index(Request $request){
        $this->authorize('modules', 'admin.{module}.index');
        ${module}s = $this->{module}Service->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => '{$class}'
        ];
        $config['seo'] = __('messages.{module}');
        $template = 'backend.{module}.{module}.index';
        $dropdown  = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            '{module}s'
        ));
    }

    public function create(){
        $this->authorize('modules', 'admin.{module}.create');
        $config = $this->configData();
        $config['seo'] = __('messages.{module}');
        $config['method'] = 'create';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'backend.{module}.{module}.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'dropdown',
            'config',
        ));
    }

    public function store(Store{$class}Request $request){
        if($this->{module}Service->create($request, $this->language)){
            return redirect()->route('admin.{module}.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('admin.{module}.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'admin.{module}.update');
        ${module} = $this->{module}Reponsitory->get{$class}ById($id, $this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.{module}');
        $config['method'] = 'edit';
        $dropdown  = $this->nestedset->Dropdown();
        $album = json_decode(${module}->album);
        $template = 'backend.{module}.{module}.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            '{module}',
            'album',
        ));
    }

    public function update($id, Update{$class}Request $request){
        if($this->{module}Service->update($id, $request)){
            return redirect()->route('admin.{module}.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('admin.{module}.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'admin.{module}.destroy');
        $config['seo'] = __('messages.{module}');
        ${module} = $this->{module}Reponsitory->get{$class}ById($id, $this->language);
        $template = 'backend.{module}.{module}.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            '{module}',
            'config',
        ));
    }

    public function destroy($id){
        if($this->{module}Service->destroy($id, $this->language)){
            return redirect()->route('admin.{module}.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('admin.{module}.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData(){
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
          
        ];
    }

   

}
