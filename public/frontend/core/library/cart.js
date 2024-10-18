(function ($) {
    "use strict";
    var HT = {}; // Khai báo là 1 đối tượng
    var timer = null;
    var _token = $('meta[name="csrf_token"]').attr("content");

    var $window = $(window),
        $document = $(document);

    $.fn.elExists = function () {
        return this.length > 0;
    };

    HT.addCart = () => {
        $(document).on("click", ".addToCart", function (e) {
            e.preventDefault();
            let _this = $(this);
            let id = _this.attr("data-id");
            let quantity = $(".quantity-text").val();
            if (typeof quantity === "undefined") {
                quantity = 1;
            }

            let attribute_id = [];
            $(".attribute-value .choose-attribute").each(function () {
                let _this = $(this);
                if (_this.hasClass("active")) {
                    attribute_id.push(_this.attr("data-attributeid"));
                }
            });

            let option = {
                id: id,
                quantity: quantity,
                attribute_id: attribute_id,
                _token: _token,
            };

            $.ajax({
                url: "http://shopprojectt.test/ajax/cart/create",
                type: "POST",
                data: option,
                dataType: "json",
                beforeSend: function () {},
                success: function (res) {
                    toastr.clear();
                    if (res.code === 10) {
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

    $document.ready(function () {
        HT.addCart();
    });
})(jQuery);
