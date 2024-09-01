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
    $url =
        $config['method'] == 'create'
            ? route('admin.user_catalogue.store')
            : route('admin.user_catalogue.udpate', $user_catalogue);
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
                        <p>Nhập thông tin của người nhóm thành viên</p>
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
                                    <label for="" class="control-label text-right">Tên Nhóm
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="name" placeholder=""
                                        autocomplete="off" value="{{ old('name', $user_catalogue->name ?? '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Ghi chú
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="description" placeholder=""
                                        autocomplete="off" value="{{ old('description', $user_catalogue->description ?? '') }}">
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

