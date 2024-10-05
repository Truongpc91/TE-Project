(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf_token"]').attr("content");
    let typingTimer;
    let doneTypingInterval = 100;
    HT.searchModel = () => {
        $(document).on("keyup", ".search-model", function (e) {
            e.preventDefault();
            let _this = $(this);
            if ($("input[type=radio]:checked").length === 0) {
                alert("Bạn chưa chọn Module");
                _this.val("");
                return false;
            }

            let keyword = _this.val();
            let option = {
                model: $("input[type=radio]:checked").val(),
                keyword: keyword,
                _token: _token,
            };

            if (keyword.length > 2) {
                HT.sendAjax(option);
            }
        });
    };

    HT.chooseModel = () => {
        $(document).on("change", ".input-radio", function () {
            let _this = $(this);
            let keyword = $(".search-model").val();
            let option = {
                model: _this.val(),
                keyword: keyword,
                _token: _token,
            };
            $(".search-model-result").html("");

            if (keyword.length > 2) {
                HT.sendAjax(option);
            }
        });
    };

    HT.sendAjax = (option) => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            $.ajax({
                url: "http://shopprojectt.test/admin/ajax/dashboard/findModelObject",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                    let html = HT.renderSearchResult(res);
                    if (html.length) {
                        $(".ajax-search-result").html(html).show();
                    } else {
                        $(".ajax-search-result").html().hide();
                    }
                },
                beforeSend: function () {
                    $(".ajax-search-result").html("").hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {},
            });
        }, doneTypingInterval);
    };

    HT.renderSearchResult = (data) => {
        let html = "";
        if (data.length) {
            for (let i = 0; i < data.length; i++) {
                let flag = $("#model-" + data[i].id).length > 0 ? 1 : 0;

                let setChecked = $("#model-" + data[i].id).length
                    ? HT.setChecked()
                    : "";

                html += `
                <button class="ajax-search-item" 
                    data-canonical="${data[i].languages[0].pivot.canonical}" 
                    data-flag="${flag}"  
                    data-image="${data[i].image}"  
                    data-name="${data[i].languages[0].pivot.name}"  
                    data-id="${data[i].id}
                ">
                    <div class="" style="display:flex;justify-content:space-between">
                        <span>${data[i].languages[0].pivot.name}</span>
                            <div class="auto-icon">
                                 ${setChecked}       
                            </div>
                    </div>
                </button>`;
            }
        }

        return html;
    };

    HT.setChecked = () => {
        return `<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20"
                    height="20" viewBox="0 0 50 50">
                        <path
                            d="M 25 2 C 12.309534 2 2 12.309534 2 25 C 2 37.690466 12.309534 48 25 48 C 37.690466 48 48 37.690466 48 25 C 48 12.309534 37.690466 2 25 2 z M 25 4 C 36.609534 4 46 13.390466 46 25 C 46 36.609534 36.609534 46 25 46 C 13.390466 46 4 36.609534 4 25 C 4 13.390466 13.390466 4 25 4 z M 34.988281 14.988281 A 1.0001 1.0001 0 0 0 34.171875 15.439453 L 23.970703 30.476562 L 16.679688 23.710938 A 1.0001 1.0001 0 1 0 15.320312 25.177734 L 24.316406 33.525391 L 35.828125 16.560547 A 1.0001 1.0001 0 0 0 34.988281 14.988281 z">
                        </path>
                </svg>`;
    };

    HT.unforcusSearchBox = () => {
        $(document).on("click", "html", function (e) {
            if (
                !$(e.target).hasClass("search-model-result") &&
                !$(e.target).hasClass("search-model")
            ) {
                $(".ajax-search-result").html("");
            }
        });

        $(document).on("click", ".ajax-search-result", function (e) {
            e.stopPropagation();
        });
    };

    HT.addModel = () => {
        $(document).on("click", ".ajax-search-item", function (e) {
            e.preventDefault();
            let _this = $(this);

            let data = _this.data();
            let html = HT.modelTemplates(data);

            if (data.flag == 0) {
                _this.find(".auto-icon").html(HT.setChecked());
                _this.attr("data-flag", 1);
                $(".search-model-result").append(html);
            } else {
                $("#model-" + data.id).remove();
                _this.find(".auto-icon").html("");
                _this.attr("data-flag", 0);
            }
        });
    };

    HT.modelTemplates = (data) => {
        let html = `
        <div class="search-result-item" id="model-${data.id}" data-model-id="${data.id}">
            <div class="" style="display:flex;justify-content:space-between">
                <div class="" style="display:flex;">
                    <span class="image img-cover">
                        <img src="http://shopprojectt.test//storage/${data.image}"
                             alt="">
                         </span>
                    <span class="name">${data.name}</span>
                    <div class="hidden">
                        <input type="text" name="modelItem[id][]" value="${data.id}">
                        <input type="text" name="modelItem[name][]" value="${data.name}">
                        <input type="text" name="modelItem[image][]" value="${data.image}">
                    </div>
                </div>
                <div class="deleted btn btn-danger">
                    <i class="fa fa-trash"></i>
                </div>
            </div>
        </div>`;

        return html;
    };

    HT.removeModel = () => {
        $(document).on("click", ".deleted", function () {
            let _this = $(this);
            _this.parents(".search-result-item").remove();
        });
    };

    $(document).ready(function () {
        HT.searchModel();
        HT.chooseModel();
        HT.unforcusSearchBox();
        HT.addModel();
        HT.removeModel();
    });
})(jQuery);
