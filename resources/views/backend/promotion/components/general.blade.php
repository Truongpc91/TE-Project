<div class="ibox">
    <div class="ibox-title">
        <h5>Thông tin chung</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            @if (!isset($offTitle))
                <div class="col-lg-6">
                    <div class="form-row">
                        <label for="" class="control-label text-left">Tên chương trình <span
                                class="text-danger">(*)</span></label>
                        <input type="text" name="name" value="{{ old('name', $model->name ?? '') }}"
                            class="form-control" placeholder="Nhập vào tên khuyến mãi" autocomplete=""
                            {{ isset($dissable) ? 'disable' : '' }}>
                    </div>
                </div>
            @endif
            <div class="col-lg-6">
                <div class="form-row">
                    <label for="" class="control-label text-left">Mã khuyến mãi 
                        @if (isset($offTitle))
                        <span class='text-danger'>(*)</span>
                        @endif
                    </label>
                    <input type="text" name="code" value="{{ old('code', $model->code ?? '') }}"
                        class="form-control" placeholder="Nếu không có mã khuyến mãi hệ thống sẽ tự động tạo"
                        autocomplete="" {{ isset($dissable) ? 'disable' : '' }}>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Mô tả khuyến mãi</label>
                    <textarea name="description" cols="15" rows="10" class="form-control form-textarea">{{ old('description', $model->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
