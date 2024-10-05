<div class="ibox slide-setting slide-normal">
    <div class="ibox-title">
        <h5>Cài đặt cơ bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-right">Tên Widget
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="text" class="form-control" name="name" placeholder="" autocomplete="off"
                        value="{{ old('name', $widget->name ?? '') }}">
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-right">Từ Khóa Widget
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="text" class="form-control" name="keyword" placeholder="" autocomplete="off"
                        value="{{ old('keyword', $widget->keyword ?? '') }}">
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
        <textarea name="short_code" id="" class="form-control">{{ old('short_code', $widget->short_code ?? '') }}</textarea>
    </div>
</div>
