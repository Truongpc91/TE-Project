<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Http\Requests\Customer\StoreCustomerCatalogueRequest;
use App\Http\Requests\Customer\UpdateCustomerCatalogueRequest;
use App\Models\CustomerCatalogue;
use Illuminate\Http\Request;
use App\Services\Interfaces\CustomerCatalogueServiceInterface as CustomerCatalogueService;
use App\Repositories\Interfaces\CustomerCatalogueReponsitoryInterface as CustomerCatalogueReponsitory;

class CustomerCatalogueController extends Controller
{
    protected $CustomerCatalogueService;
    protected $CustomerCatalogueReponsitory;

    public function __construct(
        CustomerCatalogueService $CustomerCatalogueService,
        CustomerCatalogueReponsitory $CustomerCatalogueReponsitory,
    ){
        $this->CustomerCatalogueService = $CustomerCatalogueService;
        $this->CustomerCatalogueReponsitory = $CustomerCatalogueReponsitory;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.customer_catalogue.index');

        $customer_catalogues = $this->CustomerCatalogueService->paginate($request);
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

        $config['seo'] = __('messages.customerCatalogue');

        $template = 'backend.customer.catalogue.index';

        return view('backend.dashboard.layout', compact('template', 'config', 'customer_catalogues'));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.customer_catalogue.create');
        $config['seo'] = __('messages.customerCatalogue');
        $config['method'] = 'create';
        $template = 'backend.customer.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreCustomerCatalogueRequest $request)
    {   
        if ($this->CustomerCatalogueService->create($request)) {
            return redirect()->route('admin.customer_catalogue.index')->with('success', 'Thêm mới Nhóm khách hàng thành công !');
        } else {
            return redirect()->route('admin.customer_catalogue.index')->with('error', 'Thêm mới Nhóm khách hàng thất bại! Hãy thử lại');
        }
    }

    public function edit($id){
        $this->authorize('modules', 'admin.customer_catalogue.update');
        $customer_catalogue = $this->CustomerCatalogueReponsitory->findById($id);
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = __('messages.customerCatalogue');
        $config['method'] = 'edit';
        $template = 'backend.customer.catalogue.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customer_catalogue'
        ));
    }

    public function udpate(UpdateCustomerCatalogueRequest $request, $id){
        if ($this->CustomerCatalogueService->update($request,$id)) {
            return redirect()->route('admin.customer_catalogue.index')->with('success', 'Cập nhật Nhóm khách hàng thành công !');
        } else {
            return redirect()->route('admin.customer_catalogue.index')->with('error', 'Cập nhật Nhóm khách hàng thất bại! Hãy thử lại');
        }
    }

    public function delete($id){
        $this->authorize('modules', 'admin.customer_catalogue.destroy');
        $customer_catalogue = $this->CustomerCatalogueReponsitory->findById($id);

        $template = 'backend.customer.catalogue.delete';
        $config['seo'] = __('messages.customerCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customer_catalogue'
        ));
    }

    public function destroy($id){
       
        if ($this->CustomerCatalogueService->destroy($id)) {
            return redirect()->route('admin.customer_catalogue.index')->with('success', 'Xóa Nhóm khách hàng thành công !');
        } else {
            return redirect()->route('admin.customer_catalogue.index')->with('error', 'Xóa Nhóm khách hàng thất bại! Hãy thử lại');
        }
    }

    public function permission(){
        // $this->authorize('modules', 'admin.customer_catalogue.index');
        $Nhos = $this->CustomerCatalogueReponsitory->all(['permissions']);
        $config['seo'] = __('messages.customerCatalogue');
        $template = 'backend.customer.catalogue.permission';

        return view('backend.dashboard.layout', compact(
            'template',
            'Nhos',
            'config',
            'permissions'
        ));
    }
}
