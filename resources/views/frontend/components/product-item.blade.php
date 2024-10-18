@php
    // dd($product);
    $name = $product->languages->first()->pivot->name;
    $canonical = write_url($product->languages->first()->pivot->canonical);
    $image = $product->image;
    $price = getPrice($product);
    foreach ($product->product_catalogues as $catalogue) {
        if ($catalogue->languages->isNotEmpty()) {
            $catName = $catalogue->languages->first()->pivot->name;
        }
    }
    $review = getReview($product);
@endphp

<div class="product-item product">
    @if ($price['percent'] > 0)
        <div class="badge badge-bg<?php echo rand(1, 3); ?>">-{{ $price['percent'] }}%</div>
    @endif
    <a href="{{ $canonical }}" class="image img-cover"><img src="{{ \Storage::url($image) }}" alt=""></a>
    <div class="info">
        <div class="category-title"><a href="" title="">{{ $catName }}</a></div>
        <h3 class="title"><a href="{{ $canonical }}" title="">{{ $name }}</a></h3>
        <div class="rating">
            <div class="uk-flex uk-flex-middle">
                <div class="star">
                    @for ($i = 0; $i < $review['stars']; $i++)
                        <i class="fa fa-star"></i>
                    @endfor
                </div>
                <span class="rate-number">({{ $review['count'] }})</span>
            </div>
        </div>
        <div class="product-group">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                {!! $price['html'] !!}
                <div class="addcart">
                    {!! renderQuickBuy($product, $canonical, $name) !!}
                </div>
            </div>
        </div>

    </div>
    <div class="tools">
        <a href="" title=""><img src="../frontend/resources/img/trend.svg" alt=""></a>
        <a href="" title=""><img src="../frontend/resources/img/wishlist.svg" alt=""></a>
        <a href="" title=""><img src="../frontend/resources/img/compare.svg" alt=""></a>
        <a href="#popup" data-uk-modal title=""><img src="../frontend/resources/img/view.svg" alt=""></a>
    </div>
</div>
