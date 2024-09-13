<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\GenerateServiceInterface as GenerateService;
use App\Repositories\Interfaces\GenerateReponsitoryInterface as GenerateReponsitory;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreGenerateRequest;
use App\Http\Requests\UpdateGenerateRequest;
use App\Models\Generate;

class GenerateController extends Controller
{
    protected $GenerateService;
    protected $generateReponsitory;

    public function __construct(GenerateService $GenerateService, GenerateReponsitory $generateReponsitory)
    {
        $this->GenerateService = $GenerateService;
        $this->generateReponsitory = $generateReponsitory;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.generates.index');

        $generates = $this->GenerateService->paginate($request);

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

        $config['seo'] = __('messages.generate');

        $template = 'backend.generate.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'generates'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.generates.create');

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = __('messages.generate');
        $config['method'] = 'create';
        $template = 'backend.generate.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreGenerateRequest $request)
    {
              
        if ($this->GenerateService->create($request)) {
            return redirect()->route('admin.generates.index')->with('success', 'Thêm mới Generate thành công !');
        } else {
            return redirect()->route('admin.generates.index')->with('error', 'Thêm mới Generate thất bại! Hãy thử lại');
        }
    }

    public function edit(Generate $generate){
        // dd($user);
        $this->authorize('modules', 'admin.generates.update');

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = config('apps.generate');
        $config['method'] = 'edit';
        $template = 'backend.generate.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'generate'
        ));
    }

    public function udpate(UpdateGenerateRequest $request, Generate $generates){

        $data = $request->except('_token', 'send', '_method');

        if ($this->GenerateService->update($data, $generates)) {
            return redirect()->route('admin.generate.index')->with('success', 'Cập nhật Generate thành công !');
        } else {
            return redirect()->route('admin.generate.index')->with('error', 'Cập nhật Generate thất bại! Hãy thử lại');
        }
    }

    public function delete(Generate $generate){
        $this->authorize('modules', 'admin.generates.destroy');

        $template = 'backend.generate.delete';
        $config['seo'] = config('apps.generate');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'generate'
        ));
    }

    public function destroy(Generate $generate){
        $currentImage = $generate->image;

        if($currentImage && Storage::exists($currentImage)){
            Storage::delete($currentImage);
        }
       
        if ($this->GenerateService->destroy($generate)) {
            return redirect()->route('admin.generate.index')->with('success', 'Xóa Generate thành công !');
        } else {
            return redirect()->route('admin.generate.index')->with('error', 'Xóa Generate thất bại! Hãy thử lại');
        }
    }
}
