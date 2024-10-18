<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendController;
use App\Services\CartService;
use Illuminate\Http\Request;
use Cart;

class CartController extends FrontendController
{
    protected $language;
    protected $CartService;

    public function __construct(
        CartService $CartService
    ) {
        $this->CartService = $CartService;
        parent::__construct();
    }

    public function create(Request $request)
    {
        // $post = $request->input();
        $flag = $this->CartService->create($request, $this->language);

        $cart = Cart::instance('shopping')->content();

        return response()->json([
            'cart' => $cart,
            'message' => 'Thêm mới sản phẩm vào giỏ hàng thành công',
            'code' => ($flag) ? 10 : 11,
        ]);
    }

    public function update(Request $request)
    {
        $response = $this->CartService->update($request);
        return response()->json([
            'response' => $response,
            'message' => 'Cập nhật giỏ hàng thành công',
            'code' => ($response) ? 10 : 11,
        ]);
    }

    public function delete(Request $request)
    {
        $response = $this->CartService->delete($request);
        return response()->json([
            'response' => $response,
            'message' => 'Xóa sản phẩm thành công',
            'code' => ($response) ? 10 : 11,
        ]);
    }
}
