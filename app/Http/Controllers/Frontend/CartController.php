<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendController;
use App\Http\Requests\StoreCartRequest;
use App\Repositories\Interfaces\ProvinceReponsitoryInterface as ProvinceReponsitory;
use App\Repositories\Interfaces\PromotionReponsitoryInterface as PromotionReponsitory;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use App\Services\Interfaces\CartServiceInterface as CartService;
use Illuminate\Http\Request;
use Cart;
use App\Classes\Vnpay;
use App\Classes\Momo;
use App\Classes\Paypal;


class CartController extends FrontendController
{
    protected $language;
    protected $ProvinceReponsitory;
    protected $PromotionReponsitory;
    protected $OrderReponsitory;
    protected $CartService;
    protected $Vnpay;
    protected $Momo;
    protected $Paypal;


    public function __construct(
        ProvinceReponsitory $ProvinceReponsitory,
        PromotionReponsitory $PromotionReponsitory,
        OrderReponsitory $OrderReponsitory,
        CartService $CartService,
        Vnpay $Vnpay,
        Momo $Momo,
        Paypal $Paypal,

    ) {
        $this->ProvinceReponsitory = $ProvinceReponsitory;
        $this->PromotionReponsitory = $PromotionReponsitory;
        $this->OrderReponsitory = $OrderReponsitory;
        $this->CartService = $CartService;
        $this->Vnpay = $Vnpay;
        $this->Momo = $Momo;
        $this->Paypal = $Paypal;
    }


    public function checkout()
    {
        $provinces = $this->ProvinceReponsitory->all();
        $carts = Cart::instance('shopping')->content();
        $carts = $this->CartService->remakeCart($carts, $this->language);
        $cartTotal = $this->CartService->reCaculateCart();
        $cartPromotion = $this->CartService->cartPromotion($cartTotal['cartTotal']);
        $config = $this->config();
        $template = 'frontend.cart.index';
        return view($template, compact(
            'config',
            'provinces',
            'carts',
            'cartPromotion',
            'cartTotal'
        ));
    }

    public function store(StoreCartRequest $request)
    {
        $order = $this->CartService->order($request);
        if ($order['flag']) {
            $response = $this->paymentOnline($order);
            if ($response['errorCode'] == 0) {
                return redirect()->away($response['url']);
            }
            return redirect()->route('cart.success', ['code' => $order['order']->code])->with('success', 'Đặt hàng thành công!');
        }
        return redirect()->route('cart.checkout')->with('error', 'Đặt hàng thất bại! Hãy thử lại');
    }

    public function success($code)
    {
        $order = $this->OrderReponsitory->findByCondition([
            ['code', '=', $code]
        ], FALSE, ['products']);

        $config = $this->config();
        $template = 'frontend.cart.success';
        return view($template, compact(
            'config',
            'order',
        ));
    }

    public function paymentOnline($order = null)
    {
        // $class = $order['order']->method;

        // $response = $this->{$class}->payment($order['method']);

        switch ($order['order']->method) {
            case 'vnpay':
                $response = $this->Vnpay->payment($order['order']);
                break;
            case 'momo':
                $response = $this->Momo->payment($order['order']);
                break;
            case 'paypal':
                $response = $this->Paypal->payment($order['order']);
                break;
        }

        return $response;
    }

    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/location.js',
                'frontend/core/library/product.js',
            ]
        ];
    }
}
