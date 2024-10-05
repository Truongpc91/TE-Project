<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\Interfaces\CustomerServiceInterface as CustomerService;

use App\Repositories\Interfaces\ProvinceReponsitoryInterface as provinceReponsitory;
use App\Repositories\Interfaces\CustomerCatalogueReponsitoryInterface as CustomerCatalogueReponsitory;
use App\Repositories\Interfaces\SourceReponsitoryInterface as SourceReponsitory;
use App\Repositories\Interfaces\CustomerReponsitoryInterface as CustomerReponsitory;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

class CustomerController extends Controller
{
    const PATH_UPLOAD = 'customers';

    protected $CustomerService;
    protected $provinceReponsitory;
    protected $CustomerCatalogueReponsitory;
    protected $CustomerReponsitory;
    protected $SourceReponsitory;

    public function __construct(
        CustomerService $CustomerService,
        provinceReponsitory $provinceReponsitory,
        CustomerCatalogueReponsitory $CustomerCatalogueReponsitory,
        CustomerReponsitory $CustomerReponsitory,
        SourceReponsitory $SourceReponsitory
    ){
        $this->CustomerService = $CustomerService;
        $this->provinceReponsitory = $provinceReponsitory;
        $this->CustomerCatalogueReponsitory = $CustomerCatalogueReponsitory;
        $this->CustomerReponsitory = $CustomerReponsitory;
        $this->SourceReponsitory = $SourceReponsitory;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.customer.index');
        $customers = $this->CustomerService->paginate($request);
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

        $config['seo'] = __('messages.customer');

        $template = 'backend.customer.customer.index';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customers'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'admin.customer.create');

        $provinces = $this->provinceReponsitory->all();
        $customer_catalogues = $this->CustomerCatalogueReponsitory->all();
        $sources = $this->SourceReponsitory->all();
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = __('messages.customer');
        $config['method'] = 'create';
        $template = 'backend.customer.customer.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'sources',
            'customer_catalogues'
        ));
    }

    public function store(StoreCustomerRequest $request)
    {
        if ($this->CustomerService->create($request)) {
            return redirect()->route('admin.customer.index')->with('success', 'Thêm mới Customer thành công !');
        } else {
            return redirect()->route('admin.customer.index')->with('error', 'Thêm mới Customer thất bại! Hãy thử lại');
        }
    }

    public function edit($id)
    {
        $this->authorize('modules', 'admin.customer.update');

        $provinces = $this->provinceReponsitory->all();
        $customer_catalogues = $this->CustomerCatalogueReponsitory->all();
        $customer = $this->CustomerReponsitory->findById($id);
        $sources = $this->SourceReponsitory->all();

        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js'
            ]
        ];

        $config['seo'] = __('messages.customer');
        $config['method'] = 'edit';
        $template = 'backend.customer.customer.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'customer',
            'sources',
            'customer_catalogues'
        ));
    }

    public function udpate(UpdateCustomerRequest $request, $id)
    {    

        if ($this->CustomerService->update($request, $id)) {
            return redirect()->route('admin.customer.index')->with('success', 'Cập nhật Customer thành công !');
        } else {
            return redirect()->route('admin.customer.index')->with('error', 'Cập nhật Customer thất bại! Hãy thử lại');
        }
    }

    public function delete(Customer $customer)
    {
        $this->authorize('modules', 'admin.customer.destroy');

        $template = 'backend.customer.customer.delete';
        $config['seo'] = __('messages.customer');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'customer'
        ));
    }

    public function destroy(Customer $customer)
    {
        $currentImage = $customer->image;

        if ($currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }

        if ($this->CustomerService->destroy($customer)) {
            return redirect()->route('admin.customer.index')->with('success', 'Xóa Customer thành công !');
        } else {
            return redirect()->route('admin.customer.index')->with('error', 'Xóa Customer thất bại! Hãy thử lại');
        }
    }

    // private function config(){
    //     return [
    //         'js' => [
    //             'backend/js/plugins/switchery/switchery.js'
    //         ],
    //         'css' => [
    //             'backend/css/plugins/switchery/switchery.css'
    //         ]
    //     ];
    // }
}
