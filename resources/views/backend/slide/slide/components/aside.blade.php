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
                    <input type="text" class="form-control" name="name" placeholder="" autocomplete="off"
                        value="{{ old('name', $slide->name ?? '') }}">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-row">
                    <label for="" class="control-label text-right">Từ Khóa
                        <span class="text-danger">(*)</span>
                    </label>
                    <input type="text" class="form-control" name="keyword" placeholder="" autocomplete="off"
                        value="{{ old('name', $slide->keyword ?? '') }}">
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
                                <input type="text" name="setting[width]" class="form-control int"
                                    value="{{ old('setting.width',  $slide->setting['width'] ?? 0) }}">
                                <span class="px">px</span>
                            </div>

                        </div>
                    </div>
                    <div class="setting-item">
                        <div class="" style="display:flex;justify-content:space-between">
                            <span class="setting-text">
                                Chiều cao
                            </span>
                            <div class="setting-value">
                                <input type="text" name="setting[height]" class="form-control int"
                                    value="{{ old('setting.height', $slide->setting['height'] ?? 0) }}">
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
                                <select name="setting[animation]" id="" class="form-control setupSelect2">
                                    @foreach (__('module.effects') as $key => $val)
                                        <option {{ $key == old('setting.animation', ($slide->setting['animation']) ?? null) ? 'selected' : '' }}
                                            value="{{ $key }}">{{ $val }}</option>
                                    @endforeach

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
                                <input type="checkbox" class="form-control" name="setting[arrow]" value="accept"
                                    @if (!old() || old('setting.animation', ($slide->setting['arrow']) ?? null) == 'accept') checked="checked" @endif>
                            </div>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div class="">
                            <label for="" class="setting-text">
                                Thanh điều hướng
                            </label>
                            <div class="row">
                                @foreach (__('module.navigate') as $key => $val)
                                    <div class="setting-value">
                                        <label for="navigate_{{ $key }}">{{ $val }}</label>
                                        <input type="radio" id="navigate_{{ $key }}" class="form-control"
                                            name="setting[navigate]" value="{{ $key }}"
                                            {{ old('setting.navigate', ($slide->setting['navigate']) ?? 'dots', (!old()) ? 'dots' : ($slide->setting['navigate']) ?? null) === $key ? 'checked' : '' }}>
                                    </div>
                                @endforeach
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
                    <input type="checkbox" class="form-control" name="setting[autoplay]" value="accept"
                    @if (!old() || old('setting.autoplay', ($slide->setting['autoplay']) ?? null) == 'accept') checked="checked" @endif>
                </div>
            </div>
        </div>
        <div class="setting-item">
            <div class="" style="display:flex;justify-content:space-between">
                <span class="setting-text">
                    Dừng khi di chuột
                </span>
                <div class="setting-value">
                    <input type="checkbox" class="form-control" name="setting[pauseHover]" value="accept"
                    @if (!old() || old('setting.pauseHover', ($slide->setting['pauseHover']) ?? null) == 'accept') checked="checked" @endif>
                </div>
            </div>
        </div>
        <div class="setting-item">
            <div class="" style="display:flex;justify-content:space-between">
                <span class="setting-text">
                    Thời gian chuyển ảnh
                </span>
                <div class="setting-value">
                    <input type="text" name="setting[animationDelay]" class="form-control int"
                        value="{{ old('setting.animationDelay', $slide->setting['animationDelay'] ?? 0) }}">
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
                    <input type="text" name="setting[animationSpeed]" class="form-control int"
                        value="{{ old('setting.animationSpeed', $slide->setting['animationSpeed'] ?? 0) }}">
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
        <textarea name="short_code" id="" class="form-control">{{ old('short_code', ($slide->short_code) ?? '') }}</textarea>
    </div>
</div>
