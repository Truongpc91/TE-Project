<div class="ibox slide-setting slide-normal">
    <div class="ibox-title">
        <h5>Cài đặt cơ bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-right">Tên Nguồn khách hàng
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="text" class="form-control" name="name" placeholder="" autocomplete="off"
                        value="{{ old('name', $source->name ?? '') }}">
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-right">Từ Khóa Nguồn
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="text" class="form-control" name="keyword" placeholder="" autocomplete="off"
                        value="{{ old('keyword', $source->keyword ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>

