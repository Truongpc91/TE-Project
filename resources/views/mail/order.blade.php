<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mail đơn hàng</title>
</head>
<style>
    .cart-success {
        padding: 30px 10px;
    }

    @media (min-width: 1220px) {
        .cart-success {
            width: 800px;
            margin: 0 auto;
        }
    }

    .cart-success .cart-heading {
        text-align: center;
        margin-bottom: 30px;
    }

    .cart-success .cart-heading>span {
        text-transform: uppercase;
        font-weight: 700;
    }

    .discover-text>* {
        display: inline-block;
        padding: 10px 25px;
        background: #2f5acf;
        border-radius: 16px;
        cursor: pointer;
        color: #fff;
    }

    .discover-text {
        text-align: center;
    }

    .checkout-box {
        border: 1px solid #000;
        padding: 15px 20px;
        border-radius: 16px;
    }

    .cart-success .panel-body {
        margin-bottom: 40px;
    }

    .checkout-box-head {
        margin-bottom: 30px;
    }

    .checkout-box-head .order-title {
        border: 1px solid #000;
        border-radius: 16px;
        padding: 10px 15px;
        font-weight: 700;
        font-size: 16px;
    }

    .checkout-box-head .order-date {
        display: flex;
        align-items: center;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    .cart-success .table {
        width: 100%;
        border-spacing: 0;
        background: #d9d9d9;
        border-collapse: inherit;
    }

    .cart-success .table thead>tr th {
        color: #fff;
        background: #2f5acf;
        font-weight: 500;
        font-size: 14px;
        vertical-align: middle;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
        border: none;
        padding: 12px 15px;
    }

    .cart-success tbody tr td {
        padding: 12px 15px;
        vertical-align: middle;
        font-size: 14px;
        color: #000;
        border-bottom: 1px solid #ccc;
    }

    .cart-success tfoot {
        background: #fff;
    }

    .cart-success tfoot tr td {
        padding: 8px;
    }

    .cart-success .table td:last-child {
        text-align: right;
    }

    .cart-success .table tbody tr:nth-child(2n) td {
        background-color: #d9d9d9;
    }

    .total_payment {
        font-weight: bold;
        font-size: 24px;
    }

    .panel-foot .checkout-box div {
        margin-bottom: 15px;
        font-weight: 500;
    }
</style>

<body>
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
                                ĐƠN HÀNG #{{ $data['order']->code }}
                            </div>
                        </div>
                        <div class="uk-width-large-1-3">
                            <div class="order-date">{{ $data['order']->created_at }}</div>
                        </div>
                    </div>
                </div>
                <div class="checkout-box-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá bán</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['carts'] as $key => $val)
                                @php
                                    $name = $val->name;
                                    $qty = $val->qty;
                                    $price = $val->price;
                                @endphp
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td>{{ $qty }}</td>
                                    <td>{{ convertPrice(number_format($price)) }}</td>
                                    <td><strong>{{ convertPrice(number_format($price * $qty)) }} đ</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Mã giảm giá</td>
                                <td><strong>{{ $data['cartPromotion']['selectPromotion']->code }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3">Tổng giá trị sản phẩm</td>
                                <td>{{ convertPrice(number_format($data['cartCaculate']['cartTotal'])) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">Tổng giá khuyến mãi</td>
                                <td style="color: red">-
                                    {{ convertPrice(number_format($data['cartPromotion']['discount'])) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">Phí giao hàng</td>
                                <td>0 đ</td>
                            </tr>
                            <tr class="total_payment">
                                <td colspan="3"><a href="">Tổng thanh toán</a></td>
                                <td>{{ convertPrice(number_format($data['cartCaculate']['cartTotal'] - $data['cartPromotion']['discount'])) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-foot">
            <h2 class="cart-heading"><span>Thông tin nhận hàng</span></h2>
            <div class="checkout-box">
                <div class="">Tên người nhận : {{ $data['order']->fullName }}<span></span></div>
                <div class="">Email :{{ $data['order']->email }} <span></span></div>
                <div class="">Địa chỉ: {{ $data['order']->address }}<span></span></div>
                <div class="">Số điện thoại: {{ $data['order']->phone }} <span></span></div>
                <div class="">Hình thức thanh toán
                    :{{ array_column(__('payment.method'), 'title', 'name')[$data['order']->method] ?? '-' }}
                    <span></span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
