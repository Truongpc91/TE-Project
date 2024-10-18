<?php

namespace App\Repositories;

use App\Models\AttributeCatalogue;
use App\Models\Order;
use App\Repositories\Interfaces\OrderReponsitoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class OrderReponsitory extends BaseRepository implements OrderReponsitoryInterface
{
    protected $model;

    public function __construct(
        Order $model
    ) {
        $this->model = $model;
    }

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 5,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = [],
    ) {
        $query = $this->model->select($column);
        return $query
            ->keyword($condition['keyword'] ?? null, ['fullName', 'phone', 'email', 'address', 'code'])
            ->publish($condition['publish'] ?? null)
            ->customDropdownFilter($condition['dropdown'] ?? null)
            ->relationCount($relations ?? null)
            ->CustomWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->customJoin($join ?? null)
            ->customGroupBy($extend['groupBy'] ?? null)
            ->customOrderBy($orderBy ?? null)
            ->paginate($perPage)
            ->withQueryString()->withPath(env('APP_URL') . $extend['path']);
    }

    public function getOrderById($id)
    {
        // return $this->model->select(
        //     [
        //         'orders.*',
        //         'provinces.name',
        //         'districts.name',
        //         'wards.name',
        //     ]
        // )
        //     ->join('provinces', 'orders.province_id', 'provinces.code')
        //     ->join('districts', 'orders.district_id', 'districts.code')
        //     ->join('wards','orders.ward_id', 'wards.code')
        //     ->with(['products'])
        //     ->find($id);

        return $this->model->select(
            'orders.*',
            'provinces.name as province_name',
            'districts.name as district_name',
            'wards.name as ward_name'
        )
            ->join('provinces', DB::raw('orders.province_id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('provinces.code COLLATE utf8mb4_unicode_ci'))
            ->join('districts', DB::raw('orders.district_id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('districts.code COLLATE utf8mb4_unicode_ci'))
            ->join('wards', DB::raw('orders.ward_id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('wards.code COLLATE utf8mb4_unicode_ci'))
            ->where('orders.id', $id)
            ->limit(1)
            ->first();
    }

    public function getOrderByTime($month, $year)
    {
        return $this->model
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();
    }

    public function getTotalOrders()
    {
        return $this->model->count();
    }

    public function getCancelOrders()
    {
        return $this->model->where('confirm', '=', 'cancel')->count();
    }

    public function revenueOrders()
    {
        return $this->model
            ->join('order_product', 'order_product.order_id', '=', 'orders.id')
            ->where('orders.payment', '=', 'paid')
            ->sum(DB::raw('order_product.price * order_product.qty'));
    }

    public function revenueByYear($year)
    {
        return $this->model->select(
            DB::raw('
            months.month, 
            COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.cart, "$.cartTotal"))),0) as monthly_revenue
        ')
        )
            ->from(DB::raw('(
                SELECT 1 AS month
                UNION SELECT 2
                UNION SELECT 3
                UNION SELECT 4
                UNION SELECT 5
                UNION SELECT 6
                UNION SELECT 7
                UNION SELECT 8
                UNION SELECT 9
                UNION SELECT 10
                UNION SELECT 11
                UNION SELECT 12
            ) as months'))
            ->leftJoin('orders', function ($join) use ($year) {
                $join->on(DB::raw('months.month'), '=', DB::raw('MONTH(orders.created_at)'))
                    ->where('orders.payment', '=', 'paid')
                    ->where(DB::raw('YEAR(orders.created_at)'), '=', $year);
            })
            ->groupBy('months.month')
            ->get();
    }

    public function revenue7Day()
    {
        return $this->model
            ->select(DB::raw('
            dates.date,
            COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.cart, "$.cartTotal"))),0) as daily_revenue
        '))
            ->from(DB::raw('(
            SELECT CURDATE() - INTERVAL (a.a + (10*b.a) + (100*c.a)) DAY as date
            FROM (
             SELECT 0 AS a UNION ALL
                SELECT 1 UNION ALL
                SELECT 2 UNION ALL
                SELECT 3 UNION ALL
                SELECT 4 UNION ALL
                SELECT 5 UNION ALL
                SELECT 6 UNION ALL
                SELECT 7 UNION ALL
                SELECT 8 UNION ALL
                SELECT 9
            ) as a
            CROSS JOIN (
                 SELECT 0 AS a UNION ALL
                SELECT 1 UNION ALL
                SELECT 2 UNION ALL
                SELECT 3 UNION ALL
                SELECT 4 UNION ALL
                SELECT 5 UNION ALL
                SELECT 6 UNION ALL
                SELECT 7 UNION ALL
                SELECT 8 UNION ALL
                SELECT 9
            ) as b
             CROSS JOIN (
                 SELECT 0 AS a UNION ALL
                SELECT 1 UNION ALL
                SELECT 2 UNION ALL
                SELECT 3 UNION ALL
                SELECT 4 UNION ALL
                SELECT 5 UNION ALL
                SELECT 6 UNION ALL
                SELECT 7 UNION ALL
                SELECT 8 UNION ALL
                SELECT 9
            ) as c
        ) as dates'))
        ->leftJoin('orders', function ($join) {
            $join->on(DB::raw('DATE(orders.created_at)'), '=', DB::raw('dates.date'))
            ->where('orders.payment', '=', 'paid');
        })
        ->where(DB::raw('dates.date'), '>=', DB::raw('CURDATE() - INTERVAL 6 DAY'))
        ->groupBy(DB::raw('dates.date'))
        ->orderBy(DB::raw('dates.date'), 'ASC')
        ->get();
    }

    public function revenueCurrentMonth($currentMonth, $currentYear) {
        return $this->model->select(
            DB::raw('DAY(created_at) as day'),
            DB::raw('COALESCE(SUM(JSON_UNQUOTE(JSON_EXTRACT(orders.cart, "$.cartTotal"))),0) as daily_revenue')
        )
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->where('orders.payment', '=', 'paid')
        ->groupBy('day')
        ->orderBy('day')
        ->get()->toArray();
    }
}
