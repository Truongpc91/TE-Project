@if (isset($details))
    <div class="ibox w">
        <div class="ibox-title">
            <h5>{{ __('messages.product.information') }}</h5>
        </div>
        <div class="ibox-content">
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">{{ __('messages.product.code') }}</label>
                        <input type="text" name="code" value="{{ old('code', $product->code ?? time()) }}"
                            class="form-control">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">{{ __('messages.product.made_in') }}</label>
                        <input type="text" name="made_in" value="{{ old('made_in', $product->made_in ?? null) }}"
                            class="form-control ">
                    </div>
                </div>
            </div>
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">{{ __('messages.product.price') }}</label>
                        <input type="text" name="price"
                            value="{{ old('price', isset($product) ? number_format($product->price, 0, ',', '.') : '') }}"
                            class="form-control int">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('messages.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row text-center">
                    <input type="file" name="image" id=""
                        value="{{ old('image', $productCatalogue->image ?? '') }}">
                    @if (isset($productCatalogue))
                        <div class="mt-2" style="margin-top: 20px">
                            <img src="{{ \Storage::url($productCatalogue->image) }}" alt="" width="250">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@if (isset($isset))
    <div class="ibox">
        <div class="ibox-title" style="display: flex; justify-content: space-between;">
            <h4 class="">Gallery</h4>
            <button type="button" class="btn btn-primary" onclick="addImageGallery()">Thêm ảnh</button>
        </div>
        <div class="ibox-content">
            <div class="live-preview">
                <div class="row gy-4" id="gallery_list">
                    <div class="col-md-6" id="gallery_default_item">
                        <label for="gallery_default" class="form-label">Image</label>
                        <div class="d-flex">
                            <input type="file" class="form-control" name="product_galleries[]" id="gallery_default">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.advange') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <div class="mb15">
                        <select name="publish" class="form-control setupSelect2" id="">
                            @foreach (__('messages.publish') as $key => $val)
                                <option
                                    {{ $key == old('publish', isset($model->publish) ? $model->publish : '') ? 'selected' : '' }}
                                    value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <select name="follow" class="form-control setupSelect2" id="">
                        @foreach (__('messages.follow') as $key => $val)
                            <option
                                {{ $key == old('follow', isset($model->follow) ? $model->follow : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    CKEDITOR.replace('content');

    function addImageGallery() {
        let id = 'gen' + '_' + Math.random().toString(36).substring(2, 15).toLowerCase();
        let html = `
        <div class="col-md-6" id="${id}_item">
            <label for="${id}" class="form-label">Image</label>
            <div class="d-flex" style="display: flex;">
                <input type="file" class="form-control" name="product_galleries[]" id="${id}">
                <button type="button" class="btn btn-danger" onclick="removeImageGallery('${id}_item')">
                    <span><i class="fa fa-trash"></i></span>
                </button>
            </div>
        </div>
    `;
        $('#gallery_list').append(html);
    }

    function removeImageGallery(id) {
        if (confirm('Chắc chắn xóa không?')) {
            $('#' + id).remove();
        }
    }
</script>
