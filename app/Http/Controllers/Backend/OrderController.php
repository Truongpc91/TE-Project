<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use App\Repositories\Interfaces\ProvinceReponsitoryInterface as ProvinceReponsitory;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $language;
    protected $OrderService;
    protected $OrderReponsitory;
    protected $userCatalogueReponsitory;
    protected $ProvinceReponsitory;

    public function __construct(
        OrderService $OrderService,
        OrderReponsitory $OrderReponsitory,
        ProvinceReponsitory $ProvinceReponsitory,
    ) {
        $this->OrderService = $OrderService;
        $this->OrderReponsitory = $OrderReponsitory;
        $this->ProvinceReponsitory = $ProvinceReponsitory;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'admin.orders.index');
        $orders = $this->OrderService->paginate($request, $this->language);
        $config = [
            'js' => [
                'backend/library/order.js',
                'backend/js/plugins/switchery/switchery.js',
                'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js',
                'backend/js/plugins/daterangepicker/daterangepicker.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'backend/css/plugins/daterangepicker/daterangepicker-bs3.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ]
        ];

        $config['seo'] = __('messages.order');

        $template = 'backend.order.index';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orders'
        ));
    }

    public function detail($id)
    {
        $this->authorize('modules', 'admin.orders.update');
        $order = $this->OrderReponsitory->getOrderById($id, ['products']);
        $provinces = $this->ProvinceReponsitory->all();
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                // 'backend/library/location.js',
                'backend/library/order.js'
            ]
        ];

        $config['seo'] = __('messages.order');
        $config['method'] = 'edit';
        $template = 'backend.order.detail';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'order',
            'provinces'
        ));
    }

    public function udpate(UpdateUserRequest $request, $id)
    {

        $data = $request->except('_token', 'send', '_method');
        $data['password'] = $order->password;
        // dd($data);


        if ($request->hasFile('image')) {
            $data['image'] = Storage::put(self::PATH_UPLOAD, $request->file('image'));
        }

        $currentImage = $order->image;

        if ($request->hasFile('image') && $currentImage && Storage::exists($currentImage)) {
            Storage::delete($currentImage);
        }

        if ($this->UserService->update($data, $order)) {
            return redirect()->route('admin.orders.index')->with('success', 'Cập nhật User thành công !');
        } else {
            return redirect()->route('admin.orders.index')->with('error', 'Cập nhật User thất bại! Hãy thử lại');
        }
    }
}
