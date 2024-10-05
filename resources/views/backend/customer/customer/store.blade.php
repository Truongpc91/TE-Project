@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')

@php
    $url = ($config['method'] == 'create') ? route('admin.customer.store') : route('admin.customer.udpate',$customer);
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
                                        autocomplete="off" value="{{ old('email', $customer->email ?? '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Họ và tên
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="name" placeholder=""
                                        autocomplete="off" value="{{ old('name', $customer->name ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Nhóm thành viên
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <select name="customer_catalogue_id" id="" class="form-control setupSelect2">
                                        @foreach ($customer_catalogues as $item)
                                            <option
                                                {{ $item->id == old('customer_catalogue_id', isset($customer->customer_catalogue_id) ? $customer->customer_catalogue_id : '')
                                                    ? 'selected'
                                                    : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Nguồn khách
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <select name="source_id" id="" class="form-control setupSelect2">
                                        <option value="0">[Chon nguồn khách]</option>
                                        @foreach ($sources as $item)
                                            <option
                                                {{ $item->id == old('source_id', isset($customer->source_id) ? $customer->source_id : '')
                                                    ? 'selected'
                                                    : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
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
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Ảnh đại diện
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="file" class="form-control" name="image" placeholder=""
                                        autocomplete="off" value="{{ old('image', $customer->image ?? '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Ngày sinh</label>
                                    <input type="date" class="form-control" name="birthday" placeholder=""
                                        autocomplete="off"
                                        value="{{ old('birthday', (isset($customer->birthday)) ? date('Y-m-d', strtotime($customer->birthday)) : '') }}">
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
                                        autocomplete="off" value="{{ old('address', $customer->address ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Số điện thoại</label>
                                    <input type="text" class="form-control" name="phone" placeholder=""
                                        autocomplete="off" value="{{ old('phone', $customer->phone ?? '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Ghi chú</label>
                                    <input type="text" class="form-control" name="description" placeholder=""
                                        autocomplete="off"
                                        value="{{ old('description', $customer->description ?? '') }}">
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
    var province_id = '{{ (isset($customer->province_id)) ? $customer->province_id : old('province_id') }}';
    var district_id = '{{ (isset($customer->district_id)) ? $customer->district_id : old('district_id') }}'
    var ward_id     = '{{ (isset($customer->ward_id))     ? $customer->ward_id : old('ward_id') }}'
</script>
