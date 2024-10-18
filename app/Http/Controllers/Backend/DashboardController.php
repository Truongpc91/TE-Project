<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use App\Services\Interfaces\CustomerServiceInterface as CustomerService;

use App\Models\Language;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    protected $OrderReponsitory;
    protected $OrderService;
    protected $CustomerService;

    public function __construct(
        OrderReponsitory $OrderReponsitory,
        OrderService $OrderService,
        CustomerService $CustomerService

    ) {
        $this->OrderReponsitory = $OrderReponsitory;
        $this->OrderService = $OrderService;
        $this->CustomerService = $CustomerService;
    }

    public function index()
    {

        $orderStatistic = $this->OrderService->orderStatistic();
        $customerStatistic = $this->CustomerService->customerStatistic();
        $config = $this->config();

        $template = 'backend.dashboard.home.index';

        $languages = Language::all();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'languages',
            'orderStatistic',
            'customerStatistic'
        ));
    }

    private function config()
    {
        return [
            'js' => [
                'backend/js/plugins/chartJs/Chart.min.js',
                'backend/library/dashboard.js',
                // 'backend/js/plugins/flot/jquery.flot.js',
                // 'backend/js/plugins/flot/jquery.flot.tooltip.min.js',
                // 'backend/js/plugins/flot/jquery.flot.spline.js',
                // 'backend/js/plugins/flot/jquery.flot.resize.js',
                // 'backend/js/plugins/flot/jquery.flot.pie.js',
                // 'backend/js/plugins/flot/jquery.flot.symbol.js',
                // 'backend/js/plugins/flot/jquery.flot.time.js',
                // 'backend/js/plugins/peity/jquery.peity.min.js',
                // 'backend/js/demo/peity-demo.js',
                // 'backend/js/inspinia.js',
                // 'backend/js/plugins/pace/pace.min.js',
                // 'backend/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js',
                // 'backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
                // 'backend/js/plugins/easypiechart/jquery.easypiechart.js',
                // 'backend/js/plugins/sparkline/jquery.sparkline.min.js',
                // 'backend/js/demo/sparkline-demo.js'
            ]
        ];
    }
}
