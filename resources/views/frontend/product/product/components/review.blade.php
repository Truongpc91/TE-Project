@php
    $totalReviews = $product->reviews()->count();
    $totalRate = (float)substr($product->reviews()->avg('score'), 0, 3);
    $starPercent = ($totalReviews == 0) ? 0 : ($totalRate/5)*100;
    // dd($totalRate);
@endphp
<div class="review-container">
    <div class="panel-head">
        <h2 class="review-heading">Đánh giá sản phẩm</h2>
        <div class="review-statistic">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-3">
                    <div class="review-averate">
                        <div class="title">Đánh giá trung bình</div>
                        <div class="score">{{ $totalRate }}/5</div>
                       <div class="star-rating">
                        <div class="stars" style="--star-width: {{ $starPercent }}%"></div>
                       </div>
                        <div class="total-rate">{{ $totalReviews }} đánh giá</div>
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="progress-block">
                        @for ($i = 5; $i >= 1; $i--)
                        @php
                            $countStar = $product->reviews()->where('score', $i)->count();
                            $starPercent = ($countStar > 0) ? $countStar / $totalReviews*100 : 0;
                        @endphp
                            <div class="progress-item">
                                <div class="uk-flex uk-flex-middle">
                                    <span class="text">{{ $i }}</span>
                                    <i class="fa fa-star"></i>
                                    <div class="uk-progress">
                                        <div class="uk-progress-bar" style="width: {{ $starPercent }}%"></div>
                                    </div>
                                    <span class="text">{{ $countStar }}</span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="review-action">
                        <div class="text">Bạn đã dùng sản phẩm này ?</div>
                        <button class="btn btn-review" href="#review" data-uk-toggle title="">Gửi đánh
                            giá</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="review-filter">
            <div class="uk-flex uk-flex-middle">
                <span class="filter-text">Lọc xem theo: </span>
                <div class="filter-item">
                    <span>Đã mua hàng</span>
                    <span>5 sao</span>
                    <span>4 sao</span>
                    <span>3 sao</span>
                    <span>2 sao</span>
                    <span>1 sao</span>
                </div>
            </div>
        </div>
        <div class="review-wrapper">
            @if (!is_null($reviews))
                @foreach ($product->reviews as $key => $val)
                    @php
                        $avatar = getReviewName($val->fullName);
                        $name = $val->fullName;
                        $description = $val->description;
                        $star = generateStar($val->score);
                        $created_at = convertDateTime($val->created_at);

                    @endphp
                    <div class="review-block-item">
                        <div class="review-general uk-clearfix">
                            <div class="review-avatar">
                                <span class="shae">{{ $avatar }}</span>
                            </div>
                            <div class="review-content-block">
                                <div class="review-content">
                                    <div class="name" style="display:flex;">
                                        <span>{{ $name }}</span>
                                        <span class="review-buy">
                                            <i class="fa fa-check-circle"></i>
                                            Đã mua hàng tại TE +
                                        </span>
                                    </div>
                                    <div class="review-star">
                                       {!! $star !!}
                                    </div>
                                    <div class="description">{{ $description }}</div>
                                    <div class="reivew-toolbox">
                                        <div class="uk-flex uk-flex-middle">
                                            <div class="created_at">Ngày {{ $created_at }}</div>
                                            {{-- <div class="review-reply"  href="#review" data-uk-toggle title=""> Trả lời</div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="review-block-item uk-clearfix reply-block">
                            <div class="review-avatar">
                                <span class="shae">TPC</span>
                            </div>
                            <div class="review-content-block">
                                <div class="review-content">
                                    <div class="name" style="display:flex;">
                                        <span>Phí Công Trường</span>
                                        <span class="review-buy">
                                            <i class="fa fa-check-circle"></i>
                                            Đã mua hàng tại TE +
                                        </span>
                                    </div>
                                    <div class="review-star">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <div class="description">Khá ổn</div>
                                    <div class="reivew-toolbox">
                                        <div class="uk-flex uk-flex-middle">
                                            <div class="created_at">Ngày 16/10/2024</div>
                                            <div class="review-reply"> Trả lời</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>


<div id="review" class="uk-modal" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" uk-close></button>
        <div class="review-popup-wrapper">
            <div class="panel-head">Đánh giá sản phẩm</div>
            <div class="panel-body">
                <div class="product-review uk-text-center">
                    <span class="image img-scaledown"><img src="{{ \Storage::url($product->image) }}"
                            alt="{{ $product->name }}"></span>
                    <div class="product-title">{{ $product->name }}</div>
                    <div class="popup-rating uk-clearfix">
                        <div class="rate uk-clearfix uk-flex uk-flex-middle">
                            <input type="radio" id="star5" name="rate" value="5" class="rate" />
                            <label for="star5" title="Tuyệt vời">5 stars</label>
                            <input type="radio" id="star4" name="rate" value="4" class="rate" />
                            <label for="star4" title="Hài lòng">4 stars</label>
                            <input type="radio" id="star3" name="rate" value="3" class="rate" />
                            <label for="star3" title="Bình thường">3 stars</label>
                            <input type="radio" id="star2" name="rate" value="2" class="rate" />
                            <label for="star2" title="Tạm được">2 stars</label>
                            <input type="radio" id="star1" name="rate" value="1" class="rate" />
                            <label for="star1" title="Không thích">1 star</label>
                        </div>
                        <div class="rate-text">
                            Không thích
                        </div>
                    </div>
                    <div class="review-form">
                        <div class="uk-form form">
                            <div class="form-row">
                                <textarea name="" id="" class="review-textarea"
                                    placeholder="Hãy chia sẻ cảm nhận của bạn sau khi mua sản phẩm"></textarea>
                            </div>
                            <div class="form-row">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" class="gender" value="Nam"
                                            id="male">
                                        <label for="">Nam</label>
                                    </div>
                                    <div class="gender-item uk-flex uk-flex-middle">
                                        <input type="radio" name="gender" class="gender" value="Nữ"
                                            id="female">
                                        <label for="">Nữ</label>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-grid uk-grid-medium">
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="fullName" value="" class="review-text"
                                            placeholder="Nhập họ và tên">
                                    </div>
                                </div>
                                <div class="uk-width-large-1-2">
                                    <div class="form-row">
                                        <input type="text" name="phone" value="" class="review-text"
                                            placeholder="Nhập số điện thoại">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <input type="text" name="email" value="" class="review-text"
                                    placeholder="Nhập số Email">
                            </div>
                            <div class="uk-text-center">
                                <button value="send" name="create" class="btn-send-review">Hoàn tất</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<input type="hidden" class="reviewable_type" value="{{ $reviewable }}">
<input type="hidden" class="reviewable_id" value="{{ $product->id }}">
<input type="hidden" class="review_parent_id" value="{{ $product->id }}">
