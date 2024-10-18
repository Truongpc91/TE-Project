<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $OrderService;
    protected $OrderReponsitory;


    public function __construct(
        OrderService $OrderService,
        OrderReponsitory $OrderReponsitory
    ) {
        $this->OrderService = $OrderService;
        $this->OrderReponsitory = $OrderReponsitory;
    }
    public function update(Request $request)
    {
        if ($this->OrderService->update($request)) {
            $order = $this->OrderReponsitory->getOrderById($request->input('id'));
            return response()->json([
                'error' => 10,
                'messages' => 'Cập nhật dữ liệu thành công',
                'order' => $order
            ]);
        }
        return response()->json([
            'error' => 11,
            'messages' => 'Cập nhật dữ liệu không thành công'
        ]);
    }

    public function chart(Request $request){
        $chart = $this->OrderService->ajaxOrderChart($request);

        return response()->json($chart);
    }
}
