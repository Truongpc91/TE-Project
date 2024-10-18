(function ($) {
    "use strict";
    var HT = {}; // Khai báo là 1 đối tượng
    var timer;
    var filter = $(".filtering");

    HT.showFilter = () => {
        $(document).on("click", ".btn-filter", function (e) {
            $(".filter-content").show();
        });
    };

    HT.hideFilter = () => {
        $(document).on("click", ".filter-close", function (e) {
            $(".filter-content").hide();
        });
    };

    HT.priceRange = () => {
        let isInitialized = false;

        var priceRange = document.getElementById("priceRange");

        noUiSlider.create(priceRange, {
            start: [0, 100000000], // Giá trị ban đầu: [giá trị tối thiểu, giá trị tối đa]
            connect: true, // Hiển thị thanh kết nối giữa hai giá trị
            range: {
                min: 0, // Giá trị tối thiểu
                max: 30000000, // Giá trị tối đa
            },
            step: 50000, // Bước nhảy mỗi lần kéo
        });

        // Cập nhật giá trị khi người dùng thay đổi thanh kéo
        priceRange.noUiSlider.on("update", function (values) {
            $(".min-value").val(addCommas(Math.round(values[0])));
            $(".max-value").val(addCommas(Math.round(values[1])));
        });

        // Tạo giá trị cho input khi slider được tạo
        priceRange.noUiSlider.on("set", function () {
            if (isInitialized) {
                // HT.sendDataToFilter(); // Gọi hàm gửi dữ liệu nếu đã khởi tạo
            }
        });

        // Đặt giá trị ban đầu cho input
        $(".min-value").val(
            addCommas(Math.round(priceRange.noUiSlider.get()[0]))
        );
        $(".max-value").val(
            addCommas(Math.round(priceRange.noUiSlider.get()[1]))
        );

        // Hàm khởi tạo cho slider
        priceRange.noUiSlider.on("create", function () {
            isInitialized = true; // Đánh dấu rằng slider đã được khởi tạo
        });
    };

    HT.filter = () => {
        $(document).on("change", ".filtering", function () {
            HT.sendDataToFilter();
        });
    };

    HT.sendDataToFilter = () => {
        let option = HT.filterOption();
        console.log(option);

        $.ajax({
            url: "http://shopprojectt.test/ajax/product/filter",
            type: "GET",
            data: option,
            dataType: "json",
            beforeSend: function () {},
            success: function (res) {
                let html = res.data;

                $(".product-catalogue .product-list").html(html);
            },
        });
    };

    HT.filterOption = () => {
        var filterOption = {
            perpage: $("select[name=perpage]").val(),
            sort: $("select[name=sort]").val(),
            rate: $('input[name="rate[]"]:checked')
                .map(function () {
                    return this.value;
                })
                .get(),
            price: {
                price_min: $(".min-value").val(),
                price_max: $(".max-value").val(),
            },
            productCatalogueId: $(".product_catalogue_id").val(),
            attributes: {},
        };

        $(".filterAttribute:checked").each(function () {
            let attributeId = $(this).val();
            let attributeGroup = $(this).attr("data-group");

            if (!filterOption.attributes.hasOwnProperty(attributeGroup)) {
                filterOption.attributes[attributeGroup] = [];
            }

            filterOption.attributes[attributeGroup].push(attributeId);
        });

        return filterOption;
    };

    $(document).ready(function () {
        HT.hideFilter();
        HT.showFilter();
        HT.priceRange();
        HT.filter();
    });
})(jQuery);
