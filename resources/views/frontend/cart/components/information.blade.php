<div class="panel-head">
    <div class="" style="display: flex; justify-content: space-between">
        <h3 class="cart-heading">
            <span>Thông tin đặt hàng</span>
        </h3>
        <span class="has-account">Bạn đã có tài khoản ? <a href=""
                title="Đăng nhập ngay">Đăng nhập
                ngay</a></span>
    </div>
</div>
<div class="panel-body mb20">
    <div class="cart-information">
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input type="text" name="fullName" value="{{ old('fullName') }}"
                        placeholder="Nhập vào Họ và Tên" class="input-text">
                </div>
            </div>
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="Nhập vào Số điện thoại" class="input-text">
                </div>
            </div>
           
        </div>
        <div class="uk-width-large-2-2 mb20">
            <div class="form-row">
                <input type="text" name="email" value="{{ old('email') }}"
                    placeholder="Nhập vào Email" class="input-text">
            </div>
        </div>
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-3">
                <select name="province_id" id=""
                    class="setupSelect2 province location" data-target="districts">
                    <option value="0">Chọn Thành phố</option>
                    @foreach ($provinces as $key => $val)
                        <option value="{{ $val->code }}">{{ $val->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select name="district_id" id=""
                    class="setupSelect2 districts location" data-target="wards">
                    <option value="">Chọn Quận huyện</option>
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select name="ward_id" id="" class="setupSelect2 wards location">
                    <option value="">Chọn Xã phường</option>
                </select>
            </div>
        </div>
        <div class="form-row mb20">
            <div class="form-row">
                <input type="text" name="address" value="{{ old('address') }}"
                    placeholder="Nhập vào địa chỉ (Ví dụ đường ...)" class="input-text">
            </div>
        </div>
        <div class="form-row">
            <div class="form-row">
                <input type="text" name="description" value="{{ old('description') }}"
                    placeholder="Nhập vào Ghi chú (Ví dú:  Giao hàng vào lúc 3 giờ chiều )"
                    class="input-text">
            </div>
        </div>
    </div>
</div>