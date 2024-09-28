@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')
@php
    $url = $config['method'] == 'create' ? route('admin.slide.store') : route('admin.slide.udpate', $slide);
@endphp

<form action="{{ $url }}" method="POST" class="box" enctype="multipart/form-data">
    @csrf
    @if ($config['method'] == 'edit')
        @method('PUT')
    @endif
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <div style="display:flex;justify-content: space-between;">
                            <h5>Danh sách Slides</h5>
                            <button type="button" class="addSlide btn">Thêm Slide</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="slide-item">
                                    <div class="row custom-row">
                                        <div class="col-lg-3 mb-10"><span class="slide-image img-cover"><img
                                                    src="https://png.pngtree.com/thumb_back/fh260/background/20230511/pngtree-nature-background-sunset-wallpaer-with-beautiful-flower-farms-image_2592160.jpg"
                                                    alt=""></span></div>
                                        <div class="col-lg-9">
                                            <div class="tabs-container">
                                                <ul class="nav nav-tabs">
                                                    <li class="active"><a data-toggle="tab" href="#tab-1"
                                                            aria-expanded="true"> Thông tin chung</a></li>
                                                    <li class=""><a data-toggle="tab" href="#tab-2"
                                                            aria-expanded="false">SEO</a></li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div id="tab-1" class="tab-pane active">
                                                        <div class="panel-body">
                                                            <div class="label-text">
                                                                Mô tả
                                                            </div>
                                                            <div class="form-row mb10">
                                                                <textarea name="" id="" class="form-control"></textarea>
                                                            </div>
                                                            <div class="form-row form-row-url">
                                                                <input type="text" name=""
                                                                    class="form-control" placeholder="URL">
                                                                <div class="overlay">
                                                                    <div class="uk-flex uk-flex-middle">
                                                                        <span>Mở trong tab mới</span>
                                                                        <input type="checkbox" name=""
                                                                            value="" class="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="tab-2" class="tab-pane">
                                                        <div class="panel-body">
                                                            <div class="form-row form-row-url slide-seo-tab">
                                                                <div class="label-text">
                                                                    Tiêu đề ảnh
                                                                </div>
                                                                <input type="text" name=""
                                                                    class="form-control" placeholder="Tiêu đề ảnh">
                                                                <div class="label-text">
                                                                    Mô tả ảnh
                                                                </div>
                                                                <input type="text" name=""
                                                                    class="form-control" placeholder="Mô tả ảnh">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox slide-setting slide-normal">
                    <div class="ibox-title">
                        <h5>Cài đặt cơ bản</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Tên Slide
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="name" placeholder=""
                                        autocomplete="off" value="{{ old('name', $slide->name ?? '') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Từ Khóa
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="keyword" placeholder=""
                                        autocomplete="off" value="{{ old('name', $slide->keyword ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="slide-setting">
                                    <div class="setting-item">
                                        <div class="" style="display:flex;justify-content:space-between">
                                            <span class="setting-text">
                                                Chiều rộng
                                            </span>
                                            <div class="setting-value">
                                                <input type="text" name="" class="form-control">
                                                <span class="px">px</span>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="" style="display:flex;justify-content:space-between">
                                            <span class="setting-text">
                                                Hiệu ứng
                                            </span>
                                            <div class="setting-value">
                                                <select name="" id="" class="form-control">
                                                    <option value="">Fade</option>
                                                    <option value="">...</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="" style="display:flex;justify-content:space-between">
                                            <span class="setting-text">
                                                Mũi tên
                                            </span>
                                            <div class="setting-value">
                                                <input type="checkbox" class="form-control" name=""
                                                    value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="setting-item">
                                        <div class="">
                                            <label for="" class="setting-text">
                                                Thanh điều hướng
                                            </label>
                                            <div class="row">
                                                <div class="setting-value">
                                                    <label for="">Ẩn thanh điều hướng</label>
                                                    <input type="radio" class="form-control" name=""
                                                        value="">
                                                </div>
                                                <div class="setting-value">
                                                    <label for="">Hiển thị dấu chấm</label>
                                                    <input type="radio" class="form-control" name=""
                                                        value="">
                                                </div>
                                                <div class="setting-value">
                                                    <label for="">Hiển thị ảnh dạng Thumbnails</label>
                                                    <input type="radio" class="form-control" name=""
                                                        value="">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox slide-setting slide-advance">
                    <div class="ibox-title">
                        <h5>Cài đặt nâng cao</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="setting-item">
                            <div class="" style="display:flex;justify-content:space-between">
                                <span class="setting-text">
                                    Tự động chạy
                                </span>
                                <div class="setting-value">
                                    <input type="checkbox" class="form-control" name="" value="">
                                </div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="" style="display:flex;justify-content:space-between">
                                <span class="setting-text">
                                    Dừng khi di chuột
                                </span>
                                <div class="setting-value">
                                    <input type="checkbox" class="form-control" name="" value="">
                                </div>
                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="" style="display:flex;justify-content:space-between">
                                <span class="setting-text">
                                    Thời gian chuyển ảnh
                                </span>
                                <div class="setting-value">
                                    <input type="text" name="" class="form-control">
                                    <span class="px">ms</span>
                                </div>

                            </div>
                        </div>
                        <div class="setting-item">
                            <div class="" style="display:flex;justify-content:space-between">
                                <span class="setting-text">
                                    Tốc độ hiệu ứng
                                </span>
                                <div class="setting-value">
                                    <input type="text" name="" class="form-control">
                                    <span class="px">ms</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox short-code">
                    <div class="box-title">
                        <h5>Short Code</h5>
                    </div>
                    <div class="ibox-content">
                        <textarea name="" id="" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
