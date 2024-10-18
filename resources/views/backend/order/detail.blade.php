@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['detail']['title']])

<div class="order-wrapper">
    <div class="row mt20">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between"
                        style="display:flex; justify-content:space-between">
                        <div class="ibox-title-left">
                            <span>Chi tiết đơn hàng #{{ $order->code }}</span>
                            <span class="badge">
                                <div class="badge__tip"></div>
                                <div class="badge-text" data-field="delivery" data-value="{{ $order->delivery }}" data-title="Đã giao">{{ __('cart.delivery')[$order->delivery] }}</div>
                            </span>
                            <span class="badge">
                                <div class="badge__tip"></div>
                                <div class="badge-text">{{ __('cart.payment')[$order->payment] }}</div>
                            </span>
                        </div>
                        <div class="ibox-title-right">
                            Nguồn : Website
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table-order">
                        <tbody>
                            @php
                                $carts = $order->products;
                            @endphp
                            @foreach ($carts as $key => $val)
                                @php
                                    $name = $val->pivot->name;
                                    $image = $val->image;
                                    $qty = $val->pivot->qty;
                                    $price = $val->pivot->price;
                                    $priceOrigin = $val->price;
                                    $totalPrice = $price * $qty;
                                @endphp
                                <tr class="order-item">
                                    <td>
                                        <div class="image">
                                            <span class="image image-scaledown">
                                                <img src="{{ \Storage::url($image) }}" alt="" width="75">
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="order-item-name">{{ $name }}</div>
                                        <div class="order-item-voucher">Mã giảm giá: - HSSWAD</div>
                                    </td>
                                    <td>
                                        <div class="order-item-price">{{ convertPrice(number_format($price)) }}</div>
                                    </td>
                                    <td>
                                        <div class="order-item-times">x</div>
                                    </td>
                                    <td>
                                        <div class="order-item-qty">{{ $qty }}</div>
                                    </td>
                                    <td>
                                        <div class="order-item-subtotal">
                                            {{ convertPrice(number_format($totalPrice)) }} đ
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5"class="text-right">Tổng tạm</td>
                                <td class="text-right">{{ convertPrice(number_format($order->cart['cartTotal'])) }} d
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5"class="text-right">Giảm giá</td>
                                <td class="text-right">-
                                    {{ convertPrice(number_format($order->promotion['discount'])) }} đ</td>
                            </tr>
                            <tr>
                                <td colspan="5"class="text-right">Vận chuyển</td>
                                <td class="text-right">0 đ</td>
                            </tr>
                            <tr>
                                <td colspan="5"class="text-right"><strong>Tổng cuối </strong></td>
                                <td class="text-right">
                                    <strong>{{ convertPrice(number_format($order->cart['cartTotal'] - $order->promotion['discount'])) }}
                                        đ</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="payment-confirm confirm-box" style="padding-top:20px;border-top:1px solid #eaeaea">
                        <div class="" style="display:flex; justify-content:space-between">
                            <div class="" style="display:flex; justify-content:space-between">
                                <span class="icon"><i
                                        class="fa fa-{{ $order->confirm == 'pending' ? 'warning text-danger' : 'check text-success' }}"></i></span>
                                <div class="payment-title">
                                    <div class="text_1">
                                        <span class="isConfirm">{{ __('order.confirm')[$order->confirm] }}</span>
                                        {{ convertPrice(number_format($order->cart['cartTotal'] - $order->promotion['discount'])) }}
                                        đ
                                    </div>
                                    <div class="text_2">
                                        {{ array_column(__('payment.method'), 'title', 'name')[$order->method] ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="cancel-block">
                                {{ $order->confirm == 'cancel' ? 'Đơn hàng đã hủy' : '' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="payment-confirm">
                    <div class="" style="display:flex; justify-content:space-between">
                        <div class="" style="display:flex; justify-content:space-between">
                            <span class="icon"><i class="fa fa-truck"></i></span>
                            <div class="payment-title">
                                <div class="text_1">
                                    <span class="">Xác nhận đơn hàng</span>
                                </div>
                            </div>
                        </div>
                        <div class="confirm-block">
                            @if ($order->confirm == 'pending')
                                <button class="button confirm updateField" data-field="confirm" data-value="confirm"
                                    data-title="ĐÃ XÁC NHẬN ĐƠN HÀNG TRỊ GIÁ">Xác nhận</button>
                            @else
                                Đã xác nhận
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="" style="display:flex; justify-content:space-between">
                        <span>Ghi chú</span>
                        <div class="edit span edit-order" data-target="description">Sửa</div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="description">
                        {{ $order->description }}
                    </div>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    <div class="" style="display:flex; justify-content:space-between">
                        <h5>Thông tin khách hàng</h5>
                        <div class="edit span edit-order" data-target="customerInfo">Sửa</div>
                    </div>
                </div>
                <div class="ibox-content order-customer-information">
                    <div class="custom-line">
                        <strong>N:</strong>
                        <span class="fullName">{{ $order->fullName }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>E:</strong>
                        <span class="email">{{ $order->email }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>P:</strong>
                        <span class="phone">{{ $order->phone }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>A:</strong>
                        <span class="address">{{ $order->address }}</span>
                    </div>
                    <div class="custom-line">
                        <strong>P:</strong>
                        {{ $order->ward_name }}
                    </div>
                    <div class="custom-line">
                        <strong>Q:</strong>
                        {{ $order->district_name }}
                    </div>
                    <div class="custom-line">
                        <strong>T:</strong>
                        {{ $order->province_name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="orderId" value="{{ $order->id }}">
<input type="hidden" class="ward_id" value="{{ $order->ward_id }}">
<input type="hidden" class="district_id" value="{{ $order->district_id }}">
<input type="hidden" class="province_id" value="{{ $order->province_id }}">

<script>
    var provinces = @json(
        $provinces->map(function ($item) {
                return [
                    'id' => $item->code,
                    'name' => $item->name,
                ];
            })->values());
</script>
