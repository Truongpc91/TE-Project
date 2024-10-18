@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url = ($config['method'] == 'create') ? route('admin.order.store') : route('admin.order.udpate',$order);
@endphp  

<form action="{{ $url }}" method="POST" class="box" enctype="multipart/form-data">
    @csrf
    @if ($config['method'] == 'edit')
       @method('PUT') 
    @endif
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của người sử dụng</p>
                        <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Email
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="email" placeholder=""
                                        autocomplete="off" value="{{ old('email', $order->email ?? '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Họ và tên
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="name" placeholder=""
                                        autocomplete="off" value="{{ old('name', $order->name ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Nhóm thành viên
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <select name="user_catalogue_id" id="" class="form-control setupSelect2">
                                        @foreach ($user_catalogues as $item)
                                            <option
                                                {{ $item->id == old('user_catalogue_id', isset($order->user_catalogue_id) ? $order->user_catalogue_id : '')
                                                    ? 'selected'
                                                    : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Ngày sinh</label>
                                    <input type="date" class="form-control" name="birthday" placeholder=""
                                        autocomplete="off"
                                        value="{{ old('birthday', (isset($order->birthday)) ? date('Y-m-d', strtotime($order->birthday)) : '') }}">
                                </div>
                            </div>
                        </div>
                        @if ($config['method'] == 'create')
                            <div class="row mb15">
                                <div class="col-lg-6">
                                    <div class="form-row">
                                        <label for="" class="control-label text-right">Mật khẩu
                                            <span class="text-danger">(*)</span>
                                        </label>
                                        <input type="password" class="form-control" name="password" placeholder=""
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-row">
                                        <label for="" class="control-label text-right">Nhập lại mật khẩu
                                            <span class="text-danger">(*)</span>
                                        </label>
                                        <input type="password" class="form-control" name="re_password" placeholder=""
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Ảnh đại diện
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="file" class="form-control" name="image" placeholder=""
                                        autocomplete="off" value="{{ old('image', $order->image ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin liên hệ</div>
                    <div class="panel-description">Nhập thông tin liên hệ của người dùng</div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Chọn thành phố

                                    </label>
                                    <select name="province_id"
                                        class="form-control 
                                    setupSelect2 province location"
                                        data-target="districts">
                                        <option value="0">[Chọn thành phố]</option>
                                        @if (isset($provinces))
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->code }}">{{ $province->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Quận/Huyện
                                    </label>
                                    <select name="district_id" class="form-control districts setupSelect2 location"
                                        data-target="wards">
                                        <option value="0">[Chọn Quận/Huyện]</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Phường/Xã
                                    </label>
                                    <select name="ward_id" class="form-control setupSelect2 wards">
                                        <option value="0">[Chọn Phường/Xã]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Địa chỉ</label>
                                    <input type="text" class="form-control" name="address" placeholder=""
                                        autocomplete="off" value="{{ old('address', $order->address ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Số điện thoại</label>
                                    <input type="text" class="form-control" name="phone" placeholder=""
                                        autocomplete="off" value="{{ old('phone', $order->phone ?? '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Ghi chú</label>
                                    <input type="text" class="form-control" name="description" placeholder=""
                                        autocomplete="off"
                                        value="{{ old('description', $order->description ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

<script>
    var province_id = '{{ (isset($order->province_id)) ? $order->province_id : old('province_id') }}';
    var district_id = '{{ (isset($order->district_id)) ? $order->district_id : old('district_id') }}'
    var ward_id     = '{{ (isset($order->ward_id))     ? $order->ward_id : old('ward_id') }}'
</script>
