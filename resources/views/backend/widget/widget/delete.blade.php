@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['delete']['title']])

<form action="{{ route('admin.widget.destroy', $widget->id) }}" method="POST" class="box" enctype="multipart/form-data">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Bạn đang muốn xóa Widget là : <strong>{{ $widget->name }}</strong></p>
                        <p><span class="text-danger">Lưu ý:</span> Xóa không thể khôi phục thành viên sau khi xóa. Hãy chắc chắn bạn muốn thực hiện chức năng này !</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Tên Widget
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="email" placeholder=""
                                        autocomplete="off" value="{{ old('email', $widget->name ?? '') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button type="submit" class="btn btn-danger" name="send" value="send">Xóa dữ liệu</button>
        </div>
    </div>
</form>