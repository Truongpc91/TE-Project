<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<div class="filter-content" style="display:none">
    <div class="filter-overlay">
        <div class="filter-close">
            <i class="fi fi-rs-cross"></i>
        </div>
        <div class="filter-content-container">
            @if (!is_null($filters))
                @foreach ($filters as $key => $val)
                    @php
                        $catName = $val->languages->first()->pivot->name;
                        if (is_null($val->attributes) || count($val->attributes) == 0) {
                            continue;
                        }
                    @endphp
                    <div class="filter-item">
                        <div class="filter-heading">{{ $catName }}</div>
                        <div class="filter-body">
                            @foreach ($val->attributes as $index => $item)
                                @php
                                    $attrName = $item->languages->first()->pivot->name;
                                    $id = $item->id;
                                @endphp
                                <div class="filter-choose">
                                    <input type="checkbox" id="attribute-{{ $id }}"
                                        class="input-checkbox filtering filterAttribute" value="{{ $id }}"
                                        data-group="{{ $val->id }}">
                                    <label for="attribute-{{ $id }}">{{ $attrName }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            @endif

            <div class="filter-item filter-price slider-box">
                <div class="filter-heading" for="priceRange">Lọc theo giá : </div>
                <div class="filter-price-content">
                    <div id="priceRange"></div>
                    {{-- <span id="minPriceValue">0</span> VND - <span id="maxPriceValue">1000</span> VND --}}
                </div>
                <div class="filter-input-value mt5" style="margin-top: 5px">
                    <div class="" style="display:flex;justify-content:space-between">
                        <input type="text" name="rate" class="min-value input-value" value="0 d">
                        <input type="text" name="rate" class="max-value input-value" value="10.000.000 d">
                    </div>
                </div>
            </div>
            <div class="filter-item filter-category">
                <div class="filter-heading">Tình trạng sản phẩm</div>
                <div class="filter-body" style="margin-top: 5px">
                    <div class="filter-choose">
                        <input type="checkbox" name="stock[]" id="input-available" value="1"
                            class="input-checkbox filtering">
                        <label for="input-available">Còn hàng</label>
                    </div>
                </div>
                <div class="filter-body" style="margin-top: 5px">
                    <div class="filter-choose">
                        <input type="checkbox" name="stock[]" id="input-outstock" value="0"
                            class="input-checkbox filtering">
                        <label for="input-outstock">Hết hàng</label>
                    </div>
                </div>
            </div>
            <div class="filter-review">
                <div class="filter-heading">Lọc theo đánh giá</div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" name="rate[]" value="5" id="input-rate-5"
                        class="input-checkbox filtering">
                    <label for="input-rate-5 uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(5)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" name="rate[]" value="4" id="input-rate-5"
                        class="input-checkbox filtering">
                    <label for="input-rate-5 uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(4)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" name="rate[]" value="3" id="input-rate-5"
                        class="input-checkbox filtering">
                    <label for="input-rate-5 uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(3)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" name="rate[]" value="2" id="input-rate-5"
                        class="input-checkbox filtering">
                    <label for="input-rate-5 uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(2)</span>
                </div>
                <div class="filter-choose uk-flex uk-flex-middle">
                    <input type="checkbox" name="rate[]" value="1" id="input-rate-5"
                        class="input-checkbox filtering">
                    <label for="input-rate-5 uk-flex uk-flex-middle">
                        <div class="filter-star">
                            <i class="fi-rs-star"></i>
                        </div>
                    </label>
                    <span class="totalProduct ml5 mb5">(1)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="product_catalogue_id" value="{{ $productCatalogue->id }}">
