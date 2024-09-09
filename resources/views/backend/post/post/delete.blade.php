@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['delete']['title']])

<form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="box" enctype="multipart/form-data">
    @csrf
    @method('DELETE')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Xóa ngôn ngữ : <span class="text-danger">{{ $post->name }}</span></p>
                        <p>Lưu ý:</span> Xóa không thể khôi phục nhóm thành viên sau khi xóa. Hãy chắc chắn bạn muốn thực hiện chức năng này !</p>
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
                                        autocomplete="off" value="{{ old('name', $post->name ?? '') }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-right">Canotical
                                        <span class="text-danger">(*)</span>
                                    </label>
                                    <input type="text" class="form-control" name="canonical" placeholder=""
                                        autocomplete="off" value="{{ old('description', $post->canonical ?? '') }}" readonly>
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
