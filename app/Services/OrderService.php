<?php

namespace App\Services;

use App\Services\BaseService;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use App\Repositories\Interfaces\RouterReponsitoryInterface as RouterReponsitory;
use App\Classes\Nestedsetbie;
use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class OrderService extends BaseService implements OrderServiceInterface
{


    protected $OrderReponsitory;
    protected $routerReponsitory;
    protected $nestedset;
    protected $language;
    protected $controllerName = 'AttributeCatalogueController';


    public function __construct(
        OrderReponsitory $OrderReponsitory,
        // RouterReponsitory $routerReponsitory,
    ) {
        $this->OrderReponsitory = $OrderReponsitory;
        // $this->routerReponsitory = $routerReponsitory;
    }

    public function paginate($request, $languageId)
    {
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
        ];

        foreach (__('cart') as $key => $val) {
            $condition['dropdown'][$key] = $request->string($key);
        }

        $orders = $this->OrderReponsitory->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'admin/order/index'],
        );

        return $orders;
    }



    public function update($request)
    {
        DB::beginTransaction();
        try {
            $orderId = $request->input('id');
            $payload = $request->input('payload');
            $orders = $this->OrderReponsitory->update($orderId, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateVnpay($payload, $order)
    {
        DB::beginTransaction();
        try {
            $orders = $this->OrderReponsitory->update($order->id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function orderStatistic()
    {
        $month = now()->month;
        $year = now()->year;
        $previousMoth = ($month == 1) ? 12 : $month - 1;
        $previousYear = ($month == 1) ? $year - 1 : $year;

        $orderCurrentMonth = $this->OrderReponsitory->getOrderByTime($month, $year, $previousMoth, $previousYear);
        $orderPreviousMonth = $this->OrderReponsitory->getOrderByTime($previousMoth, $previousYear);
        return [
            'orderCurrentMonth' => $orderCurrentMonth,
            'orderPreviousMonth' => $orderPreviousMonth,
            'grow' => growth($orderCurrentMonth, $orderPreviousMonth),
            'totalOrders' => $this->OrderReponsitory->getTotalOrders(),
            'totalCancelOrders' => $this->OrderReponsitory->getCancelOrders(),
            'revenueOrders' => $this->OrderReponsitory->revenueOrders(),
            'revenueChart' => convaertRevenueChartData($this->OrderReponsitory->revenueByYear($year))
        ];
    }

    public function ajaxOrderChart($request)
    {
        $type = $request->input('chartType');
        switch ($type) {
            case 1:
                $year = now()->year;
                $response = convaertRevenueChartData($this->OrderReponsitory->revenueByYear($year));
                break;
            case 7:
                $response = convaertRevenueChartData($this->OrderReponsitory->revenue7Day(), 'daily_revenue', 'date', 'Ngày ');
                break;
            case 30:
                $currentMonth = now()->month;
                $currentYear = now()->year;

                $dayInMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;

                $allDays = range(1, $dayInMonth);

                $temp = $this->OrderReponsitory->revenueCurrentMonth($currentMonth, $currentYear);

                $label = [];
                $data = [];

                $temp2 = array_map(function ($day) use ($temp, &$label, &$data) {
                    $found = collect($temp)->first(function ($record) use ($day) {
                        return $record['day'] == $day;
                    });

                    $label[] = 'Ngày ' . $day;
                    $data[] = $found ? $found['daily_revenue'] : 0;
                }, $allDays);
                $response = [
                    'label' => $label,
                    'data' => $data,
                ];
                break;
        }

        return $response;
    }


    private function paginateSelect()
    {
        return [
            'id',
            'code',
            'fullName',
            'phone',
            'email',
            'province_id',
            'district_id',
            'ward_id',
            'address',
            'description',
            'promotion',
            'cart',
            'customer_id',
            'guest_cookie',
            'method',
            'confirm',
            'payment',
            'delivery',
            'shipping',
            'created_at',
        ];
    }
}
