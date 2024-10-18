<div class="text-danger">
    <i>* Tổng cuối là tổng chưa bao gồm giảm giá</i>
</div>
<table class="table table-striped table-bodered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" name="" id="checkAll" class="input-checkbox">
            </th>
            <th>Mã đơn hàng</th>
            <th>Ngày tạo</th>
            <th>Khách hàng</th>
            <th>Giảm giá</th>
            <th>Phí Ship</th>
            <th>Tổng cuối</th>
            <th>Thanh toán</th>
            <th>Giao hàng</th>
            <th>Trạng thái</th>
            <th>Hình thức</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($orders) && is_object($orders))
            @foreach ($orders as $order)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $order->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        <a href="{{ route('admin.order.detail', $order->id) }}">{{ $order->code }}</a>
                    </td>
                    <td>
                        {{ convertDateTime($order->created_at, 'H:i d-m-Y') }}

                    </td>
                    <td>
                        <div class="infor-item name"><b>N :</b> {{ $order->fullName }}</div>
                        <div class="infor-item email"><b>E:</b> {{ $order->email }}</div>
                        <div class="infor-item phone"><b>P :</b> {{ $order->phone }}</div>
                    </td>
                    <td class="text-danger">
                        {{ convertPrice(number_format($order->promotion['discount'])) }}
                    </td>
                    <td class="maintitle" style="color: #2962ff;font-weight: 700;">
                        {{ convertPrice(number_format($order->shipping)) }}
                    </td>
                    <td class="maintitle" style="color: #2962ff;font-weight: 600;">
                        {{ convertPrice(number_format($order->cart['cartTotal'])) }}
                    </td>
                    @foreach (__('cart') as $keyItem => $item)
                        @if ($keyItem == 'confirm')
                            @continue
                        @endif
                        <td class="text-center">
                            @if ($order->confirm != 'cancel')
                                <select name="{{ $keyItem }}" class="setupSelect2 updateBadge"
                                    data-field="{{ $keyItem }}">
                                    @foreach ($item as $keyOption => $option)
                                        @if ($keyOption === 'none')
                                            @continue
                                        @endif
                                        <option {{ $keyOption == $order->{$keyItem} ? 'selected' : '' }}
                                            value="{{ $keyOption }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            @else
                                -
                            @endif
                        <input type="hidden" class="changeOrderStatus" value="{{ $order->{$keyItem} }}">

                        </td>
                    @endforeach
                    <td>
                        {!! $order->confirm != 'cancel'
                            ? __('cart.confirm')[$order->confirm]
                            : '<span class="cancel-badge">' . __('cart.confirm')[$order->confirm] . '</span>' !!}
                    </td>
                    <td class="text-center">
                        <img src="{{ array_column(__('payment.method'), 'image', 'name')[$order->method] ?? '-' }}"
                            alt="" style="max-width: 38px">
                        <input type="hidden" class="confirm" value="{{ $order->confirm }}">
                    </td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>

{{ $orders->links('pagination::bootstrap-4') }}
