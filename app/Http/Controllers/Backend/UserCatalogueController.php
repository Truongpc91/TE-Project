<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserCatalogueRequest;
use App\Http\Requests\UpdateUserCatalogueRequest;
use App\Models\UserCatalogue;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserCatalogueServiceInterface as UserCatalogueService;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
use App\Repositories\Interfaces\PermissionReponsitoryInterface as PermissionReponsitory;
use Illuminate\Support\Facades\Storage;

class UserCatalogueController extends Controller
{
    const PATH_UPLOAD = 'user_catalogues';

    protected $UserCatalogueService;
    protected $UserCatalogueRepository;
    protected $PermissionReponsitory;

    public function __construct(
        UserCatalogueService $UserCatalogueService,
        UserCatalogueRepository $UserCatalogueRepository,
        PermissionReponsitory $PermissionReponsitory
    ){
        $this->UserCatalogueService = $UserCatalogueService;
        $this->UserCatalogueRepository = $UserCatalogueRepository;
        $this->PermissionReponsitory = $PermissionReponsitory;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.user_catalogue.index');

        $user_catalogues = $this->UserCatalogueService->paginate($request);

        // $users = UserCatalogue::paginate(1);

        // dd($user_catalogues);
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

        $config['seo'] = config('apps.userCatalogue');

        $template = 'backend.user.catalogue.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'user_catalogues'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.user_catalogue.create');
        $config['seo'] = config('apps.userCatalogue');
        $config['method'] = 'create';
        $template = 'backend.user.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreUserCatalogueRequest $request)
    {
        $data = $request->except('_token','send');

        // dd($data);

        if ($this->UserCatalogueService->create($data)) {
            return redirect()->route('admin.user_catalogue.index')->with('success', 'Thêm mới UserCatalogue thành công !');
        } else {
            return redirect()->route('admin.user_catalogue.index')->with('error', 'Thêm mới UserCatalogue thất bại! Hãy thử lại');
        }
    }

    public function edit(UserCatalogue $user_catalogue){
        $this->authorize('modules', 'admin.user_catalogue.update');

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = config('apps.userCatalogue');
        $config['method'] = 'edit';
        $template = 'backend.user.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'user_catalogue'
        ));
    }

    public function udpate(UpdateUserCatalogueRequest $request, UserCatalogue $user_catalogue){

        $data = $request->except('_token', 'send', '_method');
       
        if ($this->UserCatalogueService->update($data,$user_catalogue)) {
            return redirect()->route('admin.user_catalogue.index')->with('success', 'Cập nhật UserCatalogue thành công !');
        } else {
            return redirect()->route('admin.user_catalogue.index')->with('error', 'Cập nhật UserCatalogue thất bại! Hãy thử lại');
        }
    }

    public function delete(UserCatalogue $user_catalogue){
        $this->authorize('modules', 'admin.user_catalogue.destroy');
        $template = 'backend.user.catalogue.delete';
        $config['seo'] = config('apps.userCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'user_catalogue'
        ));
    }

    public function destroy(UserCatalogue $user_catalogue){
       
        if ($this->UserCatalogueService->destroy($user_catalogue)) {
            return redirect()->route('admin.user_catalogue.index')->with('success', 'Xóa UserCatalogue thành công !');
        } else {
            return redirect()->route('admin.user_catalogue.index')->with('error', 'Xóa UserCatalogue thất bại! Hãy thử lại');
        }
    }

    public function permission(){
        // $this->authorize('modules', 'admin.user_catalogue.index');
        $userCatalogues = $this->UserCatalogueRepository->all(['permissions']);
        $permissions = $this->PermissionReponsitory->all();
        $config['seo'] = __('messages.userCatalogue');
        $template = 'backend.user.catalogue.permission';

        return view('backend.dashboard.layout', compact(
            'template',
            'userCatalogues',
            'config',
            'permissions'
        ));
    }

    public function updatePermission(Request $request){
        if($this->UserCatalogueService->setPermission($request)){
            return redirect()->route('admin.user_catalogue.index')->with('success','Cập nhật quyền thành công');
        }
        return redirect()->route('admin.user_catalogue.index')->with('error','Có vấn đề xảy ra, Hãy thử lại');
    }
}
