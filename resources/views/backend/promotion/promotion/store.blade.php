@include('backend.dashboard.components.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.components.formError')

@php
    $url =
        $config['method'] == 'create'
            ? route('admin.promotion.store')
            : route('admin.promotion.udpate', $promotion->id);
@endphp

<form action="{{ $url }}" method="POST" class="box" enctype="multipart/form-data">
    @csrf
    @if ($config['method'] == 'edit')
        @method('PUT')
    @endif
    <div class="wrapper wrapper-content animated fadeInRight promotion-wrapper">
        <div class="row">
            <div class="col-lg-8">
                @include('backend.promotion.components.general', ['model' => $promotion ?? null])

                @include('backend.promotion.promotion.components.detail')
                
            </div>
            @include('backend.promotion.components.aside', ['model' => $promotion ?? null])
        </div>
        <div class="text-right mb15">
            <button type="submit" class="btn btn-primary" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

@include('backend.promotion.promotion.components.popup')

<input type="hidden" class="preload_promotionMethod" value="{{ old('method', $promotion->method ?? null) }}">
<input type="hidden" class="preload_select-product-and-quantity"
    value="{{ old('module_type', ($promotion->discountInformation['info']['model']) ?? null) }}">
<input type="hidden" class="preload_promotion_order_amount_range"
    value="{{ json_encode(old('promotion_order_amount_range', ($promotion->discountInformation['info']) ?? null)) }}">

<input type="hidden" class="input_product_and_quantity" value="{{ json_encode(old('product_and_quantity', ($promotion->discountInformation['info']) ?? null)) }}">
<input type="hidden" class="input_object" value="{{ json_encode(old('object', ($promotion->discountInformation['info']['object']) ?? null)) }}">
