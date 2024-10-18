<div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right">Tháng</span>
                <h5>Đơn hàng trong tháng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $orderStatistic['orderCurrentMonth'] }}</h1>
                {!! growHtml($orderStatistic['grow']) !!}
                <small>Tăng trưởng so với tháng trước</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-info pull-right">Tổng số đơn hàng</span>
                <h5>Tổng số đơn hàng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $orderStatistic['totalOrders'] }}</h1>
                <div class="stat-percent font-bold text-info">{{ number_format($orderStatistic['totalCancelOrders']/ $orderStatistic['totalOrders'] *100, 1) }} % <i class="fa fa-level-up"></i></div>
                <small>Số đơn hủy : {{ $orderStatistic['totalCancelOrders'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-primary pull-right">total</span>
                <h5>Tổng doanh thu</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ number_format($orderStatistic['revenueOrders']) }} đ</h1>
                <small>Tổng doanh thu</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-danger pull-right">Customer</span>
                <h5>Tổng số khách hàng</h5>
            </div>
            <div class="ibox-content">
                <h1 class="no-margins">{{ $customerStatistic['totalCustomers'] }}</h1>
                <small>Tổng số khách hàng</small>
            </div>
        </div>
    </div>
</div>