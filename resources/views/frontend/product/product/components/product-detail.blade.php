@if (isset($product))
    @php
        $name = $product->name;
        // $canonical = write_url($product->canonical);
        $image = $product->image;
        $price = getPrice($product);
        // $catName = $productCatalogue->name;
        $review = getReview($product);
        $description = $product->description;
        $attributeCatalogue = $product->attributeCatalogue;
        $galleries = $product->galleries;
    @endphp
    <div class="panel-body">
        <?php
        $colorImage = ['https://wp.alithemes.com/html/ecom/demo/assets/imgs/page/product/img-gallery-2.jpg', 'https://wp.alithemes.com/html/ecom/demo/assets/imgs/page/product/img-gallery-1.jpg', 'https://wp.alithemes.com/html/ecom/demo/assets/imgs/page/product/img-gallery-3.jpg', 'https://wp.alithemes.com/html/ecom/demo/assets/imgs/page/product/img-gallery-4.jpg', 'https://wp.alithemes.com/html/ecom/demo/assets/imgs/page/product/img-gallery-5.jpg', 'https://wp.alithemes.com/html/ecom/demo/assets/imgs/page/product/img-gallery-6.jpg', 'https://wp.alithemes.com/html/ecom/demo/assets/imgs/page/product/img-gallery-7.jpg'];
        ?>
        <div class="uk-grid uk-grid-medium">
            <div class="uk-width-large-3-4" style="display:flex; justify-content:space-between">
                <div class="uk-width-large-2-4">
                    <div class="popup-gallery">
                        <div class="swiper-container">
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-wrapper big-pic">
                                <?php foreach($colorImage as $key => $val){  ?>
                                <div class="swiper-slide" data-swiper-autoplay="2000">
                                    <a href="<?php echo $val; ?>" class="image img-cover"><img
                                            src="{{ \Storage::url($image) }}" alt="<?php echo $val; ?>"></a>
                                </div>
                                <?php }  ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                        @if (!empty($galleries))
                            <div class="swiper-container-thumbs">
                                <div class="swiper-wrapper pic-list">
                                    @foreach ($galleries as $key => $val)
                                        @php
                                            $imageGallery = $val->image;
                                        @endphp
                                        <div class="swiper-slide">
                                            <span class="image img-cover"><img src="{{ \Storage::url($imageGallery) }}"
                                                    alt="<?php echo $val; ?>"></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="uk-width-large-2-4" style="padding-left:20px">
                    <div class="popup-product">
                        <h2 class="title product-main-title"><span>{{ $name }}</span>
                        </h2>
                        <div class="rating">
                            <div class="uk-flex uk-flex-middle">
                                <div class="author">By Tuan Nguyen</div>
                                <div class="star">
                                    <?php for($i = 0; $i<=4; $i++){ ?>
                                    <i class="fa fa-star"></i>
                                    <?php }  ?>
                                </div>
                                <div class="rate-number">(65 reviews)</div>
                            </div>
                        </div>
                        {!! $price['html'] !!}
                        <div class="description">
                            {!! $description !!}
                        </div>
                        @include('frontend.product.product.components.variant')
                        <div class="quantity">
                            <div class="text">Quantity</div>
                            <div class="uk-flex uk-flex-middle">
                                <div class="quantitybox uk-flex uk-flex-middle">
                                    <div class="minus quantity-button"><img src="../frontend/resources/img/minus.svg"
                                            alt="">
                                    </div>
                                    <input type="text" name="" value="1" class="quantity-text">
                                    <div class="plus quantity-button"><img src="../frontend/resources/img/plus.svg"
                                            alt="">
                                    </div>
                                </div>
                                <div class="btn-group uk-flex uk-flex-middle">
                                    <div class="btn-item btn-1 addToCart" data-id="{{ $product->id }}"><a
                                            href="" title="">Thêm vào giỏ hàng</a></div>
                                    {{-- <div class="btn-item btn-2"><a href="" title="">Buy Now</a></div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-width-large-1-4">
                <div class="aside">
                    @if (!is_null($category))
                        @foreach ($category as $key => $val)
                            @php
                                $name = $val['item']->languages->first()->pivot->name;
                            @endphp
                            <div class="aside-panel aside-category">
                                <div class="aside-heading">{{ $name }}</div>
                                @if (!is_null($val['children']) && count($val['children']))
                                    <div class="aside-body">
                                        <ul class="uk-list uk-clearfix">
                                            @foreach ($val['children'] as $item)
                                                @php
                                                    $itemName = $item['item']->languages->first()->pivot->name;
                                                    $itemImage = $item['item']->image;
                                                    $itemCanonical = write_url(
                                                        $item['item']->languages->first()->pivot->canonical,
                                                    );
                                                    $productCount = $item['item']->products_count;

                                                @endphp
                                                <li class="mb20">
                                                    <div class="categories-item-1">
                                                        <a href="{{ $itemCanonical }}"
                                                            style="display: flex;justify-content:space-between"
                                                            title="{{ $itemName }}">
                                                            <div class="uk-flex uk-flex-middle">
                                                                <img src="{{ \Storage::url($itemImage) }}"
                                                                    alt="">
                                                                <span class="title">{{ $itemName }}</span>
                                                            </div>
                                                            <span class="total">{{ $productCount }}</span>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @include('frontend.product.product.components.review', ['model' => $product, 'reviewable' => 'App\Models\Product'])
    </div>
@endif

<input type="hidden" value="{{ $product->name }}" class="productName">
<input type="hidden" value="{{ json_encode($attributeCatalogue) }}" class="attributeCatalogue">
<input type="hidden" value="{{ write_url($product->canonical) }}" class="productCanonical">
