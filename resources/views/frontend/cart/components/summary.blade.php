<div class="panel-foot">
    <div class="cart-summary">
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summary-title">Giảm giá</span>
                <div class="summary-value discount-value">- {{ convertPrice(number_format($cartPromotion['discount'])) }} đ</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summary-title">Phí giao hàng</span>
                <div class="summary-value">Miễn phí</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summary-title">Giảm giá</span>
                <div class="summary-value cart-total">
                    {{ (count($carts) && !is_null($carts)) ?  convertPrice(number_format($cartTotal - $cartPromotion['discount'] )) : 0 }} đ</div>
            </div>
        </div>
    </div>
</div>
</div>