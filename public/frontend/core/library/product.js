(function ($) {
    "use strict";
    var HT = {}; // Khai báo là 1 đối tượng
    var timer;
    var _token = $('meta[name="csrf_token"]').attr("content");

    HT.popupSwiperSlide = () => {
        document.querySelectorAll(".popup-gallery").forEach((popup) => {
            var swiper = new Swiper(popup.querySelector(".swiper-container"), {
                loop: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                thumbs: {
                    swiper: {
                        el: popup.querySelector(".swiper-container-thumbs"),
                        slidesPerView: 4,
                        spaceBetween: 10,
                        slideToClickedSlide: true,
                    },
                },
            });
        });
    };

    HT.changeQuantity = () => {
        $(document).on("click", ".btn-qty", function () {
            let _this = $(this);
            let qtyElement = _this.siblings(".input-qty");
            let qty = qtyElement.val();
            let newQty = _this.hasClass("minus")
                ? parseInt(qty) - 1
                : parseInt(qty) + 1;
            newQty = newQty < 1 ? 1 : newQty;
            qtyElement.val(newQty);

            let option = {
                qty: newQty,
                rowId: _this.siblings(".rowId").val(),
                _token: _token,
            };

            HT.handleUpdateCart(_this, option);
        });
    };

    HT.changQuantityInput = () => {
        $(document).on("change keyup", ".input-qty", function () {
            let _this = $(this);
            let option = {
                qty: parseInt(_this.val()) == 0 ? 1 : parseInt(_this.val()),
                rowId: _this.siblings(".rowId").val(),
                _token: _token,
            };
            toastr.clear();

            if (isNaN(option.qty)) {
                toastr.error(
                    "Số lượng nhập vào không hợp lệ",
                    "Thông báo từ hệ thống"
                );
                return false;
            }
            HT.handleUpdateCart(_this, option);
        });
    };

    HT.handleUpdateCart = (_this, option) => {
        $.ajax({
            url: "http://shopprojectt.test/ajax/cart/update",
            type: "POST",
            data: option,
            dataType: "json",
            beforeSend: function () {},
            success: function (res) {
                toastr.clear();
                if (res.code === 10) {
                        console.log(res);

                    HT.changMinyCartQuantity(res);
                    HT.changeMinyQuantityItem(_this, option);
                    HT.changCartItemSubTotal(_this, res);
                    HT.changeCartTotal(res);
                    toastr.success(res.message, "Thông báo từ hệ thống");
                } else {
                    toastr.error(
                        "Có vấn đề xảy ra ! Xin hãy thử lại",
                        "Thông báo từ hệ thống"
                    );
                }
            },
        });
    };

    HT.changeCartTotal = (res) => {
        $(".cart-total").html(addCommas(res.response.cartTotal) + " đ");
        $(".discount-value").html('-' + addCommas(res.response.cartDiscount) + " đ");
    };

    HT.changeMinyQuantityItem = (item, option) => {
        item.parents(".cart-item").find(".cart-item-number").html(option.qty);
    };

    HT.selectVariantProduct = () => {
        if ($(".choose-attribute").length) {
            $(document).on("click", ".choose-attribute", function (e) {
                e.preventDefault();
                let _this = $(this);
                let attribute_id = _this.attr("data-attributeid");
                let attribute_name = _this.text();
                _this
                    .parents(".attribute-item")
                    .find("span")
                    .html(attribute_name);
                _this
                    .parents(".attribute-value")
                    .find(".choose-attribute")
                    .removeClass("active");
                _this.addClass("active");
                HT.handleAttribute();
            });
        }
    };

    HT.handleAttribute = () => {
        let attribute_id = [];
        let flag = true;
        $(".attribute-value .choose-attribute").each(function () {
            let _this = $(this);
            if (_this.hasClass("active")) {
                attribute_id.push(_this.attr("data-attributeid"));
            }
        });

        $(".attribute").each(function () {
            if ($(this).find(".choose-attribute.active").length === 0) {
                flag = false;
                return false;
            }
        });

        if (flag) {
            $.ajax({
                url: "ajax/product/loadVariant",
                type: "GET",
                data: {
                    attribute_id: attribute_id,
                    product_id: $("input[name=product_id]").val(),
                    language_id: $("input[name=language_id]").val(),
                },
                dataType: "json",
                beforeSend: function () {},
                success: function (res) {
                    HT.setUpVariantPrice(res);
                    // HT.setupVariantGallery(res)
                    HT.setupVariantName(res);
                    HT.setupVariantUrl(res, attribute_id);
                },
            });
        }
    };

    HT.setupVariantUrl = (res, attribute_id) => {
        let queryString = "?attribute_id=" + attribute_id.join(",");
        let productCanonical = $(".productCanonical").val();
        productCanonical = productCanonical + queryString;
        let stateObject = { attribute_id: attribute_id };
        history.pushState(stateObject, "Page Title", productCanonical);
    };

    HT.setUpVariantPrice = (res) => {
        $(".popup-product .price").html(res.variantPrice.html);
    };

    HT.setupVariantName = (res) => {
        let productName = $(".productName").val();
        let productVariantName =
            productName + " " + res.variant.languages[0].pivot.name;
        $(".product-main-title span").html(productVariantName);
    };

    HT.setupVariantGallery = (gallery) => {
        let album = gallery.variant.album.split(",");

        let html = `<div class="swiper-container">
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-wrapper big-pic">`;
        album.forEach((val) => {
            html += ` <div class="swiper-slide" data-swiper-autoplay="2000">
					<a href="${val}" class="image img-cover"><img src="${val}" alt="${val}"></a>
				</div>`;
        });

        html += `</div>
			<div class="swiper-pagination"></div>
		</div>
		<div class="swiper-container-thumbs">
			<div class="swiper-wrapper pic-list">`;

        album.forEach((val) => {
            html += ` <div class="swiper-slide">
				<span class="image img-cover"><img src="${val}" alt="${val}"></span>
			</div>`;
        });

        html += `</div>
		</div>`;

        $(".popup-gallery").html(html);
        HT.popupSwiperSlide();
    };

    // HT.loadProductVariant = () => {
    // 	let attributeCatalogue = JSON.parse($('.attributeCatalogue').val())
    // 	if(typeof attributeCatalogue != 'undefined' && attributeCatalogue.length){
    // 		HT.handleAttribute()
    // 	}
    // }

    HT.chooseReviewStar = () => {
        $(document).on("click", ".popup-rating label", function () {
            let _this = $(this);
            let title = _this.attr("title");
            $(".rate-text").removeClass("uk-hidden").html(title);
        });
    };

    HT.changMinyCartQuantity = (res) => {
        console.log(res);
        $("#cartTotalItem").html(res.response.cartTotalItems);
    };

    HT.changCartItemSubTotal = (item, res) => {
        item.parents(".cart-item-info")
            .find(".cart-item-price")
            .html(addCommas(res.response.cartItemSubTotal));
    };

    HT.removeCartItem = () => {
        $(document).on("click", ".cart-item-remove", function (e) {
            // e.preventDefault()
            let _this = $(this);

            let option = {
                rowId: _this.attr("data-row-id"),
                _token: _token,
            };
            console.log(option);
            

            $.ajax({
                url: "http://shopprojectt.test/ajax/cart/delete",
                type: "POST",
                data: option,
                dataType: "json",
                beforeSend: function () {},
                success: function (res) {
                    toastr.clear();
                    if (res.code === 10) {
                        
                        HT.changMinyCartQuantity(res);
                        HT.changeCartTotal(res);
                        HT.removeCartItemRow(_this)
                        toastr.success(res.message, "Thông báo từ hệ thống");
                    } else {
                        toastr.error(
                            "Có vấn đề xảy ra ! Xin hãy thử lại",
                            "Thông báo từ hệ thống"
                        );
                    }
                },
            });
        });
    };

    HT.removeCartItemRow = (_this) => {
        _this.parents(".cart-item").remove();
    };

    $(document).ready(function () {
        /* CORE JS */
        HT.changeQuantity();
        // HT.popupSwiperSlide()
        HT.selectVariantProduct();
        // HT.loadProductVariant()
        HT.chooseReviewStar();
        HT.changQuantityInput();
        HT.removeCartItem() 
    });
})(jQuery);
