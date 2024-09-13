<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\PermissionServiceInterface as permissionService;
use App\Repositories\Interfaces\PermissionReponsitoryInterface as PermissionReponsitory;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;

class PermissionsController extends Controller
{
    protected $permissionService;
    protected $permissionReponsitory;

    public function __construct(permissionService $permissionService, PermissionReponsitory $permissionReponsitory)
    {
        $this->permissionService = $permissionService;
        $this->permissionReponsitory = $permissionReponsitory;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.permissions.index');
        $permissions = $this->permissionService->paginate($request);

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

        $config['seo'] = __('messages.permission');

        $template = 'backend.permission.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'permissions'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.permissions.create');

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = __('messages.permission');
        $config['method'] = 'create';
        $template = 'backend.permission.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StorePermissionRequest $request)
    {
        // dd($request);

        $data = $request->except('_token','send');
       
        if ($this->permissionService->create($data)) {
            return redirect()->route('admin.permissions.index')->with('success', 'Thêm mới Permission thành công !');
        } else {
            return redirect()->route('admin.permissions.index')->with('error', 'Thêm mới Permission thất bại! Hãy thử lại');
        }
    }

    public function edit(Permission $permission){
        $this->authorize('modules', 'admin.permissions.update');


        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = __('messages.permission');
        $config['method'] = 'edit';
        $template = 'backend.permission.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'permission'
        ));
    }

    public function udpate(UpdatePermissionRequest $request, Permission $permission){
        // dd($permission);

        $data = $request->except('_token', 'send', '_method');

        if ($this->permissionService->update($data, $permission)) {
            return redirect()->route('admin.permissions.index')->with('success', 'Cập nhật Permisstion thành công !');
        } else {
            return redirect()->route('admin.permissions.index')->with('error', 'Cập nhật Permisstion thất bại! Hãy thử lại');
        }
    }

    public function delete(Permission $permission){
        $this->authorize('modules', 'admin.permissions.destroy');
        $template = 'backend.permission.delete';
        $config['seo'] = config('apps.permissions');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'permission'
        ));
    }

    public function destroy(Permission $permissions){
        $currentImage = $permissions->image;

        if($currentImage && Storage::exists($currentImage)){
            Storage::delete($currentImage);
        }
       
        if ($this->permissionService->destroy($permissions)) {
            return redirect()->route('admin.permissions.index')->with('success', 'Xóa Permisstion thành công !');
        } else {
            return redirect()->route('admin.permissions.index')->with('error', 'Xóa Permisstion thất bại! Hãy thử lại');
        }
    }
}
