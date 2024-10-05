<div class="ibox">
    <div class="ibox-title">
        <h5>Cài đặt thông tin chi tiết khuyến mãi</h5>
    </div>
    <div class="ibox-content">
        <div class="form-row" style="display:flex;justify-content:space-between">
            <div for="" class="fix-label">Chọn hình thức khuyến mãi</div>
            <select name="method" id="" class="setupSelect2 promotionMethod">
                <option value="">Chọn hình thức</option>
                @foreach (__('module.promotion') as $key => $val)
                    <option value="{{ $key }}">{{ $val }}</option>
                @endforeach
            </select>
        </div>
        <div class="promotion-container">
            <div class="product-quantity-variant">
                <div class="choose-module">
                    <div class="fix-label">
                        <h5>Sản phẩm áp dụng</h5>
                    </div>
                    <select name="" id=""
                        class="setupSelect2 select-product-and-quantity">
                        @foreach (__('module.item') as $key => $val)
                            <option value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>
</div>