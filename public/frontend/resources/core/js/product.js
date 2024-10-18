(function ($) {
    "use strict";
    var HT = {}; // Khai báo là 1 đối tượng
    var timer;

    HT.selectVariantProduct = () => {
        if ($(".choose-attribute").length) {
            $(document).off("click", ".choose-attribute");
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
                    .parents(".attribute-item")
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

        $(".attribute-value").each(function () {
            if ($(this).find(".active").length === 0) {
                flag = false;
                return false;
            }
        });

        if (flag) {
            $.ajax({
                url: "http://shopprojectt.test/ajax/product/loadVariant",
                type: "GET",
                data: {
                    attribute_id: attribute_id,
                    product_id: $("input[name=product_id]").val(),
                    language_id: $("input[name=language_id]").val(),
                },
                dataType: "json",
                beforeSend: function () {},
                success: function (res) {
                    if (res.variant != null) {
                        HT.setupVariantName(res);
                        HT.setupVariantPrice(res);
                        HT.setupVariantUrl(res, attribute_id);
                    }
                },
            });
        }
    };

    HT.setupVariantName = (res) => {
        let productName = $(".productName").val();
        let productVariantName =
            productName + " " + res.variant.languages[0].pivot.name;
        $(".product-main-title").html(productVariantName);
    };

    HT.setupVariantPrice = (res) => {
        $(".popup-product .price").html(res.variantPrice.html);
    };

    HT.loadProductVariant = () => {
        let attributeCatalogue = JSON.parse($('.attributeCatalogue').val())
        if(typeof attributeCatalogue != 'undefined' && attributeCatalogue.length) {
            HT.handleAttribute()
        }
    };

    HT.setupVariantUrl = (res, attribute_id) => {
        let queryString = '?attribute_id=' + attribute_id.join(',');
        let productCanonical = $('.productCanonical').val();
        productCanonical = productCanonical + queryString
        let stateOject = { attribute_id:attribute_id }
        history.pushState(stateOject, "Page title", productCanonical)      
    }

    $(document).ready(function () {
        HT.selectVariantProduct();
        HT.loadProductVariant();
    });
})(jQuery);



