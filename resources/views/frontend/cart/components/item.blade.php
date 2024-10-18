<div class="panel-body mb30">
    @if (count($carts) && !is_null($carts))
        <div class="cart-list">
          
            @foreach ($carts as $key => $cart)
                @php
                    $cartTotal += $cart->price * $cart->qty;
                @endphp
                <div class="cart-item">
                    <div class="uk-grid uk-grid-item">
                        <div class="uk-width-small-1-1 uk-width-medium-1-5">
                            <div class="cart-item-image">
                                <span class="image img-scaledown">
                                    <img src="{{ $cart->image ? \Storage::url($cart->image) : 'https://e7.pngegg.com/pngimages/892/744/png-clipart-new-product-development-pricing-strategies-marketing-marketing-company-label.png' }}"
                                        alt="">
                                </span>
                                <span class="cart-item-number">{{ $cart->qty }}</span>
                            </div>
                        </div>
                        <div class="uk-width-small-1-1 uk-width-medium-4-5">
                            <div class="cart-item-info">
                                <h3 class="title"><span>{{ $cart->name }}</span></h3>
                                <div class="cart-item-action uk-flex uk-flex-middle"
                                    style="display: flex; justify-content:space-between">
                                    <div class="cart-item-qty">
                                        <button type="button"
                                            class="btn-qty minus">-</button>
                                        <input type="text" class="input-qty"
                                            value="{{ $cart->qty }}">
                                        <button type="button"
                                            class="btn-qty plus">+</button>
                                        <input type="hidden" class="rowId"
                                            value="{{ $cart->rowId }}">
                                    </div>
                                    <div class="cart-item-price">
                                        {{ convertPrice(number_format($cart->price * $cart->qty)) }}
                                    </div>
                                    <div class="cart-item-remove" data-row-id="{{ $cart->rowId }}">
                                        <span style="color: red"><i class="fa fa-trash"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>