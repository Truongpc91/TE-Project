@extends('frontend.homepage.layout')

@section('content')
    <div class="cart-success">
        <div class="panel-head">
            <h2 class="cart-heading"><span>
                    <h2>Đặt hàng thành công</h2>
                </span></h2>
            <div class="discover-text">
                <a href="{{ write_url('san-pham') }}">Khám phá thêm các sản phẩm khác tại đây</a>
            </div>
        </div>
        <div class="panel-body">
            <div class="cart-heading"><span>Thông tin đơn hàng</span></div>
            <div class="checkout-box">
                <div class="checkout-box-heading">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-large-1-3"></div>
                        <div class="uk-width-large-1-3">
                            <div class="order-title uk-text-center">
                                ĐƠN HÀNG #{{ $order->code }}
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="order-date">{{ $order->created_at }}</div>
                        </div>
                    </div>
                </div>
                <div class="checkout-box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá niêm yết</th>
                                <th>Giá bán</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $carts = $order->products;
                            @endphp
                            @foreach ($carts as $key => $val)
                            @php
                                $name = $val->pivot->name;
                                $qty = $val->pivot->qty;
                                $price =  $val->pivot->price;
                                $priceOrigin = $val->price;
                            @endphp
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td>{{ $qty }}</td>
                                    <td>{{ convertPrice(number_format($priceOrigin)) }}</td>
                                    <td>{{ convertPrice(number_format($price)) }}</td>
                                    <td><strong>{{ convertPrice(number_format($price * $qty)) }} đ</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">Mã giảm giá</td>
                                <td><strong>{{ $order->promotion['code'] }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá trị sản phẩm</td>
                                <td>{{ convertPrice(number_format($order->cart['cartTotal'])) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4">Tổng giá khuyến mãi</td>
                                <td style="color: red">- {{ convertPrice(number_format($order->promotion['discount'])) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4">Phí giao hàng</td>
                                <td>0 đ</td>
                            </tr>
                            <tr class="total_payment">
                                <td colspan="4"><a href="">Tổng thanh toán</a></td>
                                <td>{{ convertPrice(number_format($order->cart['cartTotal'] - $order->promotion['discount'] )) }} </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-foot">
            <h2 class="cart-heading"><span>Thông tin nhận hàng</span></h2>
            <div class="checkout-box">
                <div class="">Tên người nhận : {{ $order->fullName }}<span></span></div>
                <div class="">Email :{{ $order->email }} <span></span></div>
                <div class="">Địa chỉ: {{ $order->address }}<span></span></div>
                <div class="">Số điện thoại: {{ $order->phone }} <span></span></div>
                <div class="">Hình thức thanh toán :{{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-' }} <span></span></div>

               @include($templatePayment ?? '') 
            </div>
        </div>
    </div>
@endsection
