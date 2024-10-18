<?php

namespace App\Services;

use App\Services\Interfaces\CartServiceInterface;
use App\Services\Interfaces\ProductServiceInterface as ProductService;

use App\Repositories\Interfaces\ProductReponsitoryInterface as ProductReponsitory;
use App\Repositories\Interfaces\ProductVariantReponsitoryInterface as ProductVariantReponsitory;
use App\Repositories\Interfaces\PromotionReponsitoryInterface as PromotionReponsitory;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use App\Mail\OrderMail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Cart;

// use Gloudemans\Shoppingcart\Facades\Cart as FacadesCart;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class CartService implements CartServiceInterface
{
    protected $ProductReponsitory;
    protected $ProductVariantReponsitory;
    protected $PromotionReponsitory;
    protected $OrderReponsitory;
    protected $ProductService;

    protected $image;

    public function __construct(
        ProductReponsitory $ProductReponsitory,
        ProductVariantReponsitory $ProductVariantReponsitory,
        PromotionReponsitory $PromotionReponsitory,
        OrderReponsitory $OrderReponsitory,
        ProductService $ProductService,
    ) {
        $this->ProductReponsitory = $ProductReponsitory;
        $this->ProductVariantReponsitory = $ProductVariantReponsitory;
        $this->PromotionReponsitory = $PromotionReponsitory;
        $this->OrderReponsitory = $OrderReponsitory;
        $this->ProductService = $ProductService;
    }

    public function create($request, $language)
    {
        try {
            $payload = $request->input();
            $product = $this->ProductReponsitory->findById(
                $payload['id'],
                ['*'],
                ['languages' => function ($query) use ($language) {
                    $query->where('language_id', '=', $language);
                }]
            );
            $data = [
                'id' => $product->id,
                'name' => $product->languages->first()->pivot->name,
                'qty' => $payload['quantity'],
            ];
            if (isset($payload['attribute_id']) && count($payload['attribute_id'])) {
                $attributeId = sortAttributeId($payload['attribute_id']);
                $variant = $this->ProductVariantReponsitory->findVariant($attributeId, $payload['id'], $language);
                $variantPromotion = $this->PromotionReponsitory->findPromotionByVariantUuid($variant->uuid);
                $variantPrice = getVariantPrice($variant, $variantPromotion);

                $data['id'] = $product->id . '_' . $variant->uuid;
                $data['name'] = $product->languages->first()->pivot->name . ' ' . $variant->languages->first()->pivot->name;
                $data['price'] = ($variantPrice['priceSale'] > 0) ? $variantPrice['priceSale'] : $variantPrice['price'];
                $data['options'] = [
                    'attribute' => $payload['attribute_id'],
                ];
            } else {
                $product = $this->ProductService->combineproductAndPromotion([$product->id], $product, false, true);
                $price = getPrice($product);
                $data['price'] = ($price['priceSale'] > 0) ? $price['priceSale']  : $price['price'];
            }

            Cart::instance('shopping')->add($data);

            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function remakeCart($carts, $language = 3)
    {
        if (!is_null($carts)) {
            foreach ($carts as $key => $val) {
                $extractCartId = explode('_', $val->id);

                if (count($extractCartId) == 2) {
                    $object = $this->ProductReponsitory->findById(
                        $extractCartId[0],
                        ['*'],
                        ['languages' => function ($query) use ($language) {
                            $query->where('language_id', '=', $language);
                        }]
                    );
                }
            }
        }
        return $carts;
    }

    public function update($request)
    {
        try {
            $payload = $request->input();
            Cart::instance('shopping')->update($payload['rowId'], $payload['qty']);
            $carts = Cart::instance('shopping')->content();
            $cartCaculate = $this->cartAndPromotion();
            $cartItem = Cart::instance('shopping')->get($payload['rowId']);
            $cartItemSubTotal = $cartItem->price * $cartItem->qty;
            $cartCaculate['cartItemSubTotal'] = $cartItemSubTotal;
            return $cartCaculate;
        } catch (\Throwable $e) {
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function cartAndPromotion()
    {
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $cartPromotion['discount'];
        $cartCaculate['cartDiscount'] = $cartPromotion['discount'];
        return $cartCaculate;
    }

    public function delete($request)
    {

        $payload = $request->input();
        $carts = Cart::instance('shopping')->remove($payload['rowId']);
        $cartCaculate = $this->cartAndPromotion();
        $cartItem = Cart::instance('shopping')->get($payload['rowId']);
        $cartItemSubTotal = $cartItem->price * $cartItem->qty;
        $cartCaculate['cartItemSubTotal'] = $cartItemSubTotal;
        return $cartCaculate;
        // try {
        //     $payload = $request->input();
        //     $carts = Cart::instance('shopping')->remove($payload['rowId']);
        //     $cartCaculate = $this->cartAndPromotion();
        //     $cartItem = Cart::instance('shopping')->get($payload['rowId']);
        //     $cartItemSubTotal = $cartItem->price * $cartItem->qty;
        //     $cartCaculate['cartItemSubTotal'] = $cartItemSubTotal;
        //     return $cartCaculate;
        // } catch (\Throwable $e) {
        //     echo $e->getMessage();
        //     die();
        //     return false;
        // }
    }

    public function reCaculateCart()
    {
        $carts = Cart::instance('shopping')->content();
        $total = 0;
        $totalItems = 0;
        foreach ($carts as $cart) {
            $total += $cart->price * $cart->qty;
            $totalItems += $cart->qty;
        }

        return [
            'cartTotal' => $total,
            'cartTotalItems' => $totalItems
        ];
    }

    public function cartPromotion($cartTotal)
    {
        $maxDiscount = 0;
        $selectPromotion = null;
        $promotions = $this->PromotionReponsitory->getPromotionByCartTotal();

        if (!is_null($promotions)) {
            foreach ($promotions as $promotion) {
                $discount = $promotion->discountInformation['info'];
                $amountFrom = $discount['amountFrom'] ?? [];
                $amountTo = $discount['amountTo'] ?? [];
                $amountValue = $discount['amountValue'] ?? [];
                $amountType = $discount['amountType'] ?? [];

                if (!empty($amountFrom) && count($amountFrom) == count($amountTo) && count($amountTo) == count($amountValue)) {
                    for ($i = 0; $i < count($amountFrom); $i++) {
                        $currentAmountFrom = $amountFrom[$i];
                        $currentAmountTo = $amountTo[$i];
                        $currentAmountValue = $amountValue[$i];
                        $currentAmountType = $amountType[$i];

                        if ($cartTotal > $currentAmountFrom && ($cartTotal <=  $currentAmountTo) || $cartTotal > $currentAmountTo) {
                            if ($currentAmountType == 'cash') {
                                $maxDiscount = max($maxDiscount, $currentAmountValue);
                            } else if ($currentAmountType == 'percent') {
                                $discountValue = ($currentAmountValue / 100) * $cartTotal;
                                $maxDiscount = max($maxDiscount, $discountValue);
                            }
                            $selectPromotion = $promotion;
                        }
                    }
                }
            }
        }
        return [
            'discount' => $maxDiscount,
            'selectPromotion' => $selectPromotion
        ];
    }

    public function order($request)
    {
        DB::beginTransaction();
        try {
            $payload = $this->request($request);
            $order = $this->OrderReponsitory->create($payload);

            if ($order->id) {
                $this->createOrderProduct($payload, $order, $request);
                // $this->paymentOnline($payload['method']);

                // $this->mail($order);
                // Cart::instance('shopping')->destroy();
            }
            DB::commit();
            return [
                'order' => $order,
                'flag' => TRUE
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return [
                'order' => NULL,
                'flag' => FALSE
            ];
        }
    }

    private function mail($order) {
        $to = $order->email;
        $cc = 'truonqpc91@gmail.com';
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);

        $data = [
            'order' => $order,
            'carts' => $carts,
            'cartCaculate' => $cartCaculate,
            'cartPromotion' => $cartPromotion
        ];
        
        \Mail::to($to)->cc($cc)->send(new OrderMail($data));
    }

    private function paymentOnline($method = '')
    {
        switch ($method) {
            case 'zalo':
                $this->zaloPay();
                break;
            case 'momo':
                $this->momoPay();
                break;
            case 'shopee':
                $this->shopeePay();
                break;
            case 'vnpay':
                $this->vnPay();
                break;
            case 'paypal':
                $this->paypal();
                break;
        }
    }

    private function createOrderProduct($payload, $order, $request)
    {
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $temp = [];
        if (!is_null($carts)) {
            foreach ($carts as $key => $val) {
                $extract = explode('_', $val->id);
                $temp[] = [
                    'product_id' => $extract[0],
                    'uuid' => $extract[1] ?? null,
                    'name' => $val->name,
                    'qty' => $val->qty,
                    'price' => $val->price,
                    'option' => json_encode($val->options),
                ];
            }
        }
        $order->products()->sync($temp);
    }

    private function request($request)
    {
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
       
        $payload = $request->except(['_token', 'voucher', 'create']);
        $payload['cart'] = $cartCaculate;
        $payload['code'] = time();
        $payload['promotion']['discount'] = $cartPromotion['discount'];
        $payload['promotion']['name'] = $cartPromotion['selectPromotion']->name;
        $payload['promotion']['code'] = $cartPromotion['selectPromotion']->code;
        $payload['promotion']['startDate'] = $cartPromotion['selectPromotion']->startDate;
        $payload['promotion']['endDate'] = $cartPromotion['selectPromotion']->endDate;
        $payload['confirm'] = 'pending';
        $payload['delivery'] = 'pending';
        $payload['payment'] = 'unpaid';
        return $payload;
    }
}
