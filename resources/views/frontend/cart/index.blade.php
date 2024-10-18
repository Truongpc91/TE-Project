@extends('frontend.homepage.layout')

@section('content')
    <div class="cart-container" style="padding-left: 20px; padding-right:20px">
        <div class="uk-container uk-container-center">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('cart.store') }}" class="uk-form form" method="POST">
                @csrf
                <div class="cart-wrapper">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-large-3-5">
                            <div class="panel-cart cart-left">
                                @include('frontend.cart.components.information')

                                @include('frontend.cart.components.method')

                                <button type="submit" class="cart-checkout" value="create" name="create"> Thanh toán đơn
                                    hàng</button>
                            </div>
                        </div>
                        <div class="uk-width-large-2-5">
                            <div class="panel-cart">
                                <div class="panel-head">
                                    <h2 class="cart-heading"><span>Giỏ hàng</span></h2>
                                </div>
                                <div class="panel-body mb30">
                                    @if (count($carts) && !is_null($carts))
                                        <div class="cart-list">
                                            @php
                                                $cartTotal = 0;
                                            @endphp
                                            @foreach ($carts as $key => $cart)
                                                @php
                                                    $cartTotal += $cart->price * $cart->qty;
                                                @endphp
                                                <div class="cart-item">
                                                    <div class="uk-grid uk-grid-item">
                                                        <div class="uk-width-small-1-1 uk-width-medium-1-5">
                                                            <div class="cart-item-image">
                                                                <span class="image img-scaledown">
                                                                    <img src="{{ $cart->image ? \Storage::url($cart->image) : 'https://e7.pngegg.com/pngimages/892/744/png-clipart-new-product-development-pricing-strategies-marketing-marketing-company-label.png' }}"
                                                                        alt="">
                                                                </span>
                                                                <span class="cart-item-number">{{ $cart->qty }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="uk-width-small-1-1 uk-width-medium-4-5">
                                                            <div class="cart-item-info">
                                                                <h3 class="title"><span>{{ $cart->name }}</span></h3>
                                                                <div class="cart-item-action uk-flex uk-flex-middle"
                                                                    style="display: flex; justify-content:space-between">
                                                                    <div class="cart-item-qty">
                                                                        <button type="button"
                                                                            class="btn-qty minus">-</button>
                                                                        <input type="text" class="input-qty"
                                                                            value="{{ $cart->qty }}">
                                                                        <button type="button"
                                                                            class="btn-qty plus">+</button>
                                                                        <input type="hidden" class="rowId"
                                                                            value="{{ $cart->rowId }}">
                                                                    </div>
                                                                    <div class="cart-item-price">
                                                                        {{ convertPrice(number_format($cart->price * $cart->qty)) }}
                                                                    </div>
                                                                    <div class="cart-item-remove"
                                                                        data-row-id="{{ $cart->rowId }}">
                                                                        <span style="color: red"><i
                                                                                class="fa fa-trash"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @include('frontend.cart.components.voucher')
                                @include('frontend.cart.components.summary')
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection

<script>
    var province_id = '{{ isset($order->province_id) ? $order->province_id : old('province_id') }}';
    var district_id = '{{ isset($order->district_id) ? $order->district_id : old('district_id') }}'
    var ward_id = '{{ isset($order->ward_id) ? $order->ward_id : old('ward_id') }}'
</script>
