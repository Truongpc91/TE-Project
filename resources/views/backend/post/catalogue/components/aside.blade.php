<div class="ibox">
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Chọn danh mục cha
                        <span class="text-danger">(*)</span>
                    </label><br>
                    <span class="text-danger font-italic notice"> * Chọn Root nếu không có danh mục
                        cha</span>
                    <select name="parent_id" id="" class="form-control setupSelect2">
                        @foreach ($dropdown as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>    
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>Chọn ảnh đại diện</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row text-center">
                    {{-- <span class="image image-cover image-target">
                        <img src="backend/img/no-image.png" alt="" width="200">
                    </span>
                    <input type="hidden" name="image" value=""> --}}
                    <input type="file" name="image" id="" value="{{ old('image', $post_catalogue->image ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox">
    <div class="ibox-title">
        <h5>Cấu Hình Nâng Cao</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row text-center">
                    <div class="mb-1" style="margin-bottom: 20px">
                        <select name="publish" id=""
                            class="form-control setupSelect2 mb20 mb-1">
                            @foreach (config('apps.general.publish') as $key => $value)
                                <option
                                    {{ $key == old('publish', (isset($postCatalogue->publish)) ? $postCatalogue->publish : '') ? 'selected' : ''}}
                                value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" id="" class="form-control setupSelect2">
                        @foreach (config('apps.general.follow') as $key => $value)
                            <option
                            {{ $key == old('follow', (isset($postCatalogue->follow)) ? $postCatalogue->follow : '') ? 'selected' : ''}}
                            value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>