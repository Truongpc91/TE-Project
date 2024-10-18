(function ($) {
    "use strict";
    var HT = {};

    var _token = $('meta[name="csrf_token"]').attr("content");
    let typingTimer;
    let doneTypingInterval = 500;

    $.fn.elExist = function () {
        return this.length > 0;
    };

    HT.promotionNeverEnd = () => {
        $(document).on("change", "#neverEnd", function () {
            let _this = $(this);
            let isChecked = _this.prop("checked");
            if (isChecked) {
                $("input[name=endDate]").val("").attr("disabled", true);
            } else {
                let endDate = $("input[name=startDate]").val();
                $("input[name=endDate]").val(endDate).attr("disabled", false);
            }
        });
    };

    HT.promotionSource = () => {
        $(document).on("change", ".chooseSource", function () {
            let _this = $(this);
            let flag = _this.attr("id") == "allSource" ? true : false;
            if (flag) {
                _this.parents(".ibox-content").find(".source-wrapper").remove();
            } else {
                $.ajax({
                    url: "http://shopprojectt.test/admin/ajax/source/getAllSource",
                    type: "GET",
                    dataType: "json",
                    success: function (res) {
                        console.log(res.data);

                        let sourceData = res.data;

                        if (!$(".source-wrapper").length) {
                            let sourceHtml =
                                HT.renderPromotionSource(sourceData).prop(
                                    "outerHTML"
                                );
                            _this.parents(".ibox-content").append(sourceHtml);
                            HT.promotionMultipleSelect2();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {},
                });
            }
        });
    };

    HT.renderPromotionSource = (sourceData) => {
        let wrapper = $("<div>").addClass("source-wrapper");
        let select = $("<select>")
            .addClass("multipleSelect2")
            .attr("name", "sourceValue[]")
            .attr("multiple", true);
        if (sourceData.length) {
            for (let i = 0; i < sourceData.length; i++) {
                let option = $("<option>")
                    .attr("value", sourceData[i].id)
                    .text(sourceData[i].name);
                select.append(option);
            }
        }
        wrapper.append(select);

        return wrapper;
    };

    HT.chooseCustomerCondition = () => {
        $(document).on("change", ".chooseApply", function () {
            let _this = $(this);
            let id = _this.attr("id");

            if (id === "allApply") {
                _this.parents(".ibox-content").find(".apply-wrapper").remove();
            } else {
                let applyHtml = HT.renderApplyCondition().prop("outerHTML");
                _this.parents(".ibox-content").append(applyHtml);
                HT.promotionMultipleSelect2();
            }
        });
    };

    HT.renderApplyCondition = () => {
        let applyConditionData = JSON.parse($(".applyStatusList").val());

        let wrapper = $("<div>").addClass("apply-wrapper");
        let wrapperConditionItem = $("<div>").addClass("wrapper-condition");
        let select = $("<select>")
            .addClass("multipleSelect2 conditionItem")
            .attr("name", "sourceValue[]")
            .attr("multiple", true);
        if (applyConditionData.length) {
            for (let i = 0; i < applyConditionData.length; i++) {
                let option = $("<option>")
                    .attr("value", applyConditionData[i].id)
                    .text(applyConditionData[i].name);
                select.append(option);
            }
        }
        wrapper.append(select);
        wrapper.append(wrapperConditionItem);

        return wrapper;
    };

    HT.chooseApplyItem = () => {
        $(document).on("change", ".conditionItem", function () {
            let _this = $(this);

            let condition = {
                value: _this.val(),
                label: _this.select2("data"),
            };

            $(".wrapperConditionItem").each(function () {
                let _item = $(this);
                let itemClass = _item.attr("class").split(" ")[1];

                if (condition.value.includes(itemClass) == false) {
                    _item.remove();
                }
            });

            for (let i = 0; i < condition.value.length; i++) {
                let value = condition.value[i];
                let html = HT.renderConditionItem(
                    value,
                    condition.label[i].text
                );
            }
        });
    };

    HT.createConditionLabel = (label, value) => {
        let iconButton = $("<i>").addClass("fa fa-trash");

        let deleteButton = $("<div>")
            .addClass("delete btn btn-danger")
            .attr("data-condition-item", value);

        deleteButton.append(iconButton);

        let conditionLabel = $("<div>")
            .addClass("conditionLabel mt10")
            .css("display", "flex")
            .text(label);

        conditionLabel.append(deleteButton);

        let flex = $("<div>");

        flex.append(conditionLabel);

        let wrapperBox = $("<div>").addClass("mb10");

        wrapperBox.append(flex);

        return wrapperBox.prop("outerHTML");
    };

    HT.checkConditionItemSet = () => {
        let checkedValue = $(".conditionItemSelected").val();
        console.log(checkedValue);

        if (checkedValue.length && $(".conditionItem").length) {
            checkedValue = JSON.parse(checkedValue);
            $(".conditionItem").val(checkedValue).trigger("change");
        }
    };

    HT.renderConditionItem = (value, label) => {
        if (
            !$(".wrapper-condition")
                .find("." + value)
                .elExist()
        ) {
            $.ajax({
                url: "http://shopprojectt.test/admin/ajax/dashboard/getPromotionConditionValue",
                type: "GET",
                data: {
                    value: value,
                },
                dataType: "json",
                success: function (res) {
                    let optionData = res.data;
                    let conditionItem = $("<div>").addClass(
                        "wrapperConditionItem " + value
                    );
                    let conditionHiddenInput = $(".condition_input_" + value);

                    let conditionHiddenInputValue = [];
                    if (conditionHiddenInput.length) {
                        conditionHiddenInputValue = JSON.parse(
                            conditionHiddenInput.val()
                        );
                    }
                    console.log(conditionHiddenInputValue);
                    console.log(value);

                    let select = $("<select>")
                        .addClass("multipleSelect2 mt10 objectItem")
                        .attr("name", value + "[]")
                        .attr("multiple", true);

                    for (let i = 0; i < optionData.length; i++) {
                        let option = $("<option>")
                            .attr("value", optionData[i].id)
                            .text(optionData[i].text);

                        select.append(option);
                    }
                    select.val(conditionHiddenInputValue).trigger("change");

                    const conditionLabel = HT.createConditionLabel(
                        label,
                        value
                    );

                    conditionItem.append(conditionLabel);

                    conditionItem.append(select);

                    $(".wrapper-condition").append(conditionItem);
                    HT.promotionMultipleSelect2();
                },
                error: function (jqXHR, textStatus, errorThrown) {},
            });
        }
    };

    HT.checkConditionItemExist = (conditionClass) => {
        return $(".wrapper-condition").find("." + conditionClass).length > 0;
    };

    HT.deleteCondition = () => {
        $(document).on("click", ".wrapperConditionItem .delete", function () {
            let _this = $(this);
            let unSelectedValue = _this.attr("data-condition-item");
            // $(".conditionItem").val(unSelectedValue).trigger("change");
        });
    };

    HT.promotionMultipleSelect2 = () => {
        $(".multipleSelect2").select2({
            // minimumInputLength: 2,
            placeholder: "Click vào ô để lựa chọn",
            // ajax: {
            //     url: 'http://shopprojectt.test/admin/ajax/attribute/getAttribute',
            //     type: 'GET',
            //     dataType: 'json',
            //     deley: 250,
            //     data: function (params){
            //         return {
            //             search: params.term,
            //             option: option,
            //         }
            //     },
            //     processResults: function(data){
            //         return {
            //             results: data.items
            //         }
            //     },
            //     cache: true
            //   }
        });
    };
    // var ranges = [];

    // HT.checkbtnJs100ConflictRange = (newFrom, newTo) => {
    //     for (let i = 0; i < ranges.length; i++) {
    //         let existRange = ranges[i];
    //         if (
    //             (newFrom >= existRange.from && newFrom <= existRange.to) ||
    //             (newFrom >= existRange.from && newTo <= existRange.to) ||
    //             (newFrom <= existRange.from && newTo >= existRange.from) ||
    //             (newFrom <= existRange.to   && newTo >= existRange.to)
    //         ) {
    //             return true;
    //         }
    //     }
    //     return false;
    // };

    // HT.isValidRange = (newFrom, newTo) => {
    //     if (newTo <= newFrom) {
    //         return false;
    //     }

    //     return true;
    // };

    HT.btnJS100 = () => {
        $(document).on("click", ".btn-js-100", function () {
            let _button = $(this);

            let newFrom = parseInt(
                $(".order_amount_range")
                    .find("tbody tr:last-child")
                    .find(".order_amount_range_from input")
                    .val()
            );
            let newTo = parseInt(
                $(".order_amount_range")
                    .find("tbody tr:last-child")
                    .find(".order_amount_range_to input")
                    .val()
            );

            // if (!HT.isValidRange(newFrom, newTo)) {
            //     alert(
            //         "Khoảng điều kiện không hợp lệ, giá trị tới phải lớn hơn giá trị từ"
            //     );
            //     return false;
            // }

            // console.log(HT.checkbtnJs100ConflictRange(newFrom, newTo));

            // if (HT.checkbtnJs100ConflictRange(newFrom, newTo)) {
            //     $(".order_amount_range")
            //         .find("tbody tr:last-child")
            //         .addClass("errorLine");
            //     alert(
            //         "Có xung đột giữa các khoảng điều kiện, hãy kiểm tra lại !"
            //     );
            //     return;
            // }

            // ranges.push({ form: newFrom, to: newTo });

            let $tr = $("<tr>");
            let tdList = [
                {
                    class: "order_amount_range_from td-range",
                    name: "promotion_order_amount_range[amountFrom][]",
                    value: newTo + 1,
                },
                {
                    class: "order_amount_range_to td-range",
                    name: "promotion_order_amount_range[amountTo][]",
                    value: 0,
                },
            ];

            for (let i = 0; i < tdList.length; i++) {
                let $td = $("<td>").addClass(tdList[i].class);
                let $input = $("<input>")
                    .addClass("form-control int")
                    .attr("name", tdList[i].name)
                    .attr("value", tdList[i].value);

                $td.append($input);
                $tr.append($td);
            }

            let discountTd = $("<td>").addClass("discountType");
            discountTd.append(
                $("<div>", { class: "promotion" })
                    .append(
                        $("<input>", {
                            type: "text",
                            name: "promotion_order_amount_range[amountValue][]",
                            class: "form-control",
                            placeholder: "",
                            value: 0,
                        })
                    )
                    .append(
                        $("<select>", {
                            class: "form-control multipleSelect2",
                            name: "promotion_order_amount_range[amountType][]",
                        })
                            .append(
                                $("<option>", { value: "cash", text: "(đ)" })
                            )
                            .append(
                                $("<option>", { value: "percent", text: "(%)" })
                            )
                    )
            );

            $tr.append(discountTd);

            let deleteButton = $("<td>").append(
                $("<div>", {
                    class: "delete-some-item delete-order-amount-range-condition btn btn-danger",
                }).append(
                    $("<i>", {
                        class: "fa fa-trash",
                    })
                )
            );
            $tr.append(deleteButton);

            $(".order_amount_range table tbody").append($tr);
            HT.promotionMultipleSelect2();
        });
    };

    HT.deleteAmountRangeCondition = () => {
        $(document).on(
            "click",
            ".delete-order-amount-range-condition",
            function () {
                let _this = $(this);

                _this.parents("tr").remove();
            }
        );
    };

    HT.renderOrderRangeConditionContainer = () => {
        $(document).on("change", ".promotionMethod", function () {
            let _this = $(this);
            let option = _this.val();

            switch (option) {
                case "order_amount_range":
                    HT.orderAmountRange();
                    break;
                case "product_and_quantity":
                    HT.renderProductAndQuantity();
                    break;
                case "product_quantity_range":
                    break;
                case "goods_discount_by_quantity":
                    break;
                default:
                    HT.removePromotionContainer();
                    break;
            }
        });

        let method = $(".preload_promotionMethod").val();
        if (method.length && typeof method !== "underfine") {
            $(".promotionMethod").val(method).trigger("change");
        }
    };

    HT.removePromotionContainer = () => {
        $(".promotion-container").html("");
    };

    HT.orderAmountRange = () => {
        let $tr = "";
        let order_amount_range = JSON.parse(
            $(".preload_promotion_order_amount_range").val()
        ) || {
            amountFrom: ["0"],
            amountTo: ["0"],
            amountValue: ["0"],
            amountType: ["cash"],
        };

        for (let i = 0; i < order_amount_range.amountFrom.length; i++) {
            let $amountFrom = order_amount_range.amountFrom[i];
            let $amountTo = order_amount_range.amountTo[i];
            let $amountType = order_amount_range.amountType[i];
            let $amountValue = order_amount_range.amountValue[i];

            $tr += `
                <tr>
                    <td class="order_amount_range_from">
                        <input type="text" name="promotion_order_amount_range[amountFrom][]" class="form-control int td-range"
                            placeholder="0" value="${$amountFrom}">
                    </td>
                        <td class="order_amount_range_to">
                            <input type="text" name="promotion_order_amount_range[amountTo][]" class="form-control int td-range"
                                placeholder="0" value="${$amountTo}">
                        </td>
                        <td class="discountType">
                            <div class="promotion" style="display:flex;justify-content:space-between">
                                <input type="text" name="promotion_order_amount_range[amountValue][]" class="form-control"
                                    placeholder="0" value="${$amountValue}">
                                <select name="promotion_order_amount_range[amountType][]" class="form-control setupSelect2" id="">
                                    <option value="">Chiết khấu</option>
                                    <option ${
                                        $amountType == "percent"
                                            ? "selected"
                                            : ""
                                    } value="percent">(%)</option>
                                    <option ${
                                        $amountType == "cash" ? "selected" : ""
                                    } value="cash">(đ)</option>
                                </select>
                            </div>
                        </td>        
                    </tr>
            `;
        }

        let html = `
        <div class="order_amount_range">
           <table class="table table-striped">
               <thead>
                   <tr>
                       <th class="text-right">Giá trị từ</th>
                       <th class="text-right">Giá trị đến</th>
                       <th class="text-right">Chiết khấu</th>
                       <th class="text-right"></th>
                   </tr>
               </thead>
                   <tbody>
                       ${$tr}
                   </tbody>
               </table>
           <div class="button-promotion">
               <button type="button" class="btn btn-info btn-custom btn-js-100">Thêm điều kiện</button>
           </div> 
        </div>`;
        console.log(html);
        $(".promotion-container").html(html);
    };

    HT.renderProductAndQuantity = () => {
        let selectData = JSON.parse($(".input-product-and-quantity").val());
        let selectHtml = "";
        let moduleType = $(".preload_select-product-and-quantity").val();

        for (let key in selectData) {
            selectHtml +=
                "<option " +
                (moduleType.length &&
                typeof moduleType !== "undefined" &&
                moduleType == key
                    ? "selected"
                    : "") +
                ' value="' +
                key +
                '">' +
                selectData[key] +
                "</option>";
        }

        let preloadData = JSON.parse(
            $(".input_product_and_quantity").val()
        ) || {
            quantity: ["1"],
            maxDiscountValue: ["0"],
            discountValue: ["0"],
            discountType: ["cash"],
        };

        console.log();

        let html = `
         <div class="product-quantity-variant">
            <div class="choose-module">
                <div class="fix-label">
                    <h5>Sản phẩm áp dụng</h5>
                </div>
                <select name="module_type" id=""
                    class="multipleSelect2 select-product-and-quantity">
                    ${selectHtml}
                </select>
            </div>
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-right" style="width:400px">Sản phẩm mua</th>
                        <th class="text-right" style="width:60px">SL tối hiểu</th>
                        <th class="text-right">Giới hạn khuyến mãi</th>
                        <th class="text-right">Chiết khấu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="chooseProductPromotionTd">
                            <div data-toggle="modal" data-target="#findProduct"
                                class="product-quantity">
                                <div class=""
                                    style="display:flex;justify-content:space-between">
                                    <div class="boxWrapper">
                                        <div class="boxSearchIcon">
                                            <i class="fa fa-search"></i>
                                        </div>
                                        <div class="boxSearchInput fixGrid6">
                                            <p>Tìm theo tên, mã sản phẩm</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="order_amount_range_to td-range">
                            <input type="text" name="product_and_quantity[quantity]"
                                class="form-control int td-range" value="${
                                    preloadData.quantity
                                }">
                        </td>
                        <td class="order_amount_range_from">
                            <input type="text" name="product_and_quantity[maxDiscountValue]"
                                class="form-control int td-range" value="${
                                    preloadData.maxDiscountValue
                                }">
                        </td>
                        <td class="discountType">
                            <div class="promotion"
                                style="display:flex;justify-content:space-between">
                                <input type="text" name="product_and_quantity[discountValue]" class="form-control"
                                    placeholder="0" value="${
                                        preloadData.discountValue
                                    }">
                                <select name="product_and_quantity[discountType]" class="form-control setupSelect2"
                                    id="">
                                    <option value="">Chiết khấu</option>
                                    <option ${
                                        preloadData.discountType == "percent"
                                            ? "selected"
                                            : ""
                                    } value="percent">(%)</option>
                                    <option ${
                                        preloadData.discountType == "cash"
                                            ? "selected"
                                            : ""
                                    } value="cash">(đ)</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        `;
        $(".promotion-container").html(html);
        HT.promotionMultipleSelect2();
    };

    HT.setupAjaxSearch = () => {
        $(".ajaxSearch").each(function () {
            let _this = $(this);
            let option = {
                model: _this.attr("data-model"),
            };

            _this.select2({
                minimumInputLength: 2,
                placeholder: "Nhập vào 2 từ đề tìm kiếm",
                ajax: {
                    url: "http://shopprojectt.test/admin/ajax/dashboard/findPromotionObject",
                    type: "GET",
                    dataType: "json",
                    deley: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            option: option,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.items,
                        };
                    },
                    cache: true,
                },
            });
        });
    };

    HT.loadProduct = (option) => {
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf_token"]').attr("content"),
            },
            url: "http://shopprojectt.test/admin/ajax/product/loadProductPromotion",
            type: "GET",
            data: option,
            dataType: "json",
            success: function (res) {
                console.log(res);
                
                HT.fillToObjectList(res);
            },
        });
    };

    HT.productQuantityListProduct = () => {
        $(document).on("click", ".product-quantity", function (e) {
            e.preventDefault();
            let option = {
                _token: _token,
                model: $(".select-product-and-quantity").val(),
            };
            HT.loadProduct(option);
        });
    };

    HT.fillToObjectList = (data) => {
        console.log(data.objects.data);  
        
        switch (data.model) {
            case "Product":
                HT.fillProductToList(data.objects);
                break;
            case "ProductCatalogue":
                HT.fillProductCatalogueToList(data.objects);
                break;
        }
    };

    HT.fillProductCatalogueToList = (object) => {
       
        
        let html = "";
        if (object.data.length) {
            let model = $(".select-product-and-quantity").val();
            for (let i = 0; i < object.data.length; i++) {
                let name = object.data[i].name;
                let id = object.data[i].id;
                let inventory =
                    typeof object.data.inventory != "undefined" ? inventory : 0;
                let couldSell =
                    typeof object.data.couldSell != "undefined" ? couldSell : 0;
                let classBox = model + "_" + id;
                let isChecked = $(".boxWrapper ." + classBox + "").length
                    ? true
                    : false;
                // let uuid = object.data[i].uuid;
                html += `
                <div class="search-object-item" 
                        data-productid="${id}" 
                        data-name="${name}" 
                    >
                    <div class="" style="display:flex; justify-content:space-between">
                        <div class="object-info">
                            <div class="" style="display:flex; justify-content:space-between">
                                <input 
                                    type="checkbox" 
                                    name="" 
                                    value="${id}" 
                                    class="input-checkbox"
                                    ${isChecked ? "checked" : ""}
                                >
                                <div class="object-name">
                                    <div class="name">${name}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }
        }
        html += HT.paginationLinks(object.links).prop("outerHTML");
        $(".search-list").html(html);
    };

    HT.changePromotionMethod = () => {
        $(document).on("change", ".select-product-and-quantity", function () {
            $(".fixGrid6").remove();
            objectChoose = [];
        });
    };

    HT.fillProductToList = (object) => {
        console.log(object.data);
        let html = "";
        if (object.data.length) {
            let model = $(".select-product-and-quantity").val();
            for (let i = 0; i < object.data.length; i++) {
                let image = object.data[i].image;
                let name = object.data[i].name;
                let price = object.data[i].price;
                let sku = object.data[i].sku;
                let product_variant_id = object.data[i].product_variant_id;
                let product_id = object.data[i].id;
                let inventory =
                    typeof object.data.inventory != "undefined" ? inventory : 0;
                let couldSell =
                    typeof object.data.couldSell != "undefined" ? couldSell : 0;
                let classBox =
                    model + "_" + product_id + "_" + product_variant_id;
                let isChecked = $(".boxWrapper ." + classBox + "").length
                    ? true
                    : false;
                let uuid = object.data[i].uuid;
                html += `<div class="search-object-item" 
                                data-productid="${product_id}" 
                                data-variant-id="${product_variant_id}"
                                data-name="${name}"
                                data-uuid="${uuid}"
                            >
                            <div class="" style="display:flex; justify-content:space-between">
                                <div class="object-info">
                                    <div class="" style="display:flex; justify-content:space-between">
                                        <input 
                                            type="checkbox" 
                                            name="" 
                                            value="${
                                                product_id +
                                                "_" +
                                                product_variant_id
                                            }" 
                                            class="input-checkbox"
                                            ${isChecked ? "checked" : ""}
                                        >
                                        <span class="image image-scaledown">
                                            <img src="http://shopprojectt.test//storage/${image}"
                                                alt="">
                                        </span>
                                        <div class="object-name">
                                            <div class="name">${name}</div>
                                            <div class="jscode">
                                                Mã SP: ${sku}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="object-extra-info">
                                    <div class="price">${price}</div>
                                    <div class="object-inventory">
                                        <div class="" style="display:flex; justify-content:space-between">
                                            <span class="text-1">Tồn kho:</span>
                                            <span class="text-value">${inventory}</span>
                                            <span class="text-1 slash">|</span>
                                            <span class="text-value">Có thể bán : ${couldSell}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
            }
        }
        html += HT.paginationLinks(object.links).prop("outerHTML");
        $(".search-list").html(html);
    };

    HT.paginationLinks = (links) => {
        let nav = $("<nav>");
        if (links.length > 3) {
            let paginationUl = $("<ul>").addClass("pagination");
            $.each(links, function (index, link) {
                let liClass = "page-item";
                if (link.active) {
                    liClass += " active";
                } else if (!link.url) {
                    liClass += " disabled";
                }

                let li = $("<li>").addClass(liClass);
                if (link.label == "pagination.previous") {
                    let span = $("<span>")
                        .addClass("page-link")
                        .attr("aria-hidden", true)
                        .html("<");
                    li.append(span);
                } else if (link.label == "pagination.next") {
                    let span = $("<a>")
                        .addClass("page-link")
                        .attr("aria-hidden", true)
                        .html(">");
                    li.append(span);
                } else if (link.url) {
                    let a = $("<a>")
                        .addClass("page-link")
                        .text(link.label)
                        .attr("href", link.url);
                    li.append(a);
                }
                paginationUl.append(li);
                nav.append(paginationUl);
            });
        }

        return nav;
    };

    HT.getPaginationMenu = () => {
        $(document).on("click", ".page-link", function (e) {
            e.preventDefault();
            let _this = $(this);
            let option = {
                model: $(".select-product-and-quantity").val(),
                page: _this.text(),
                keyword: $(".search-model").val(),
            };
            HT.loadProduct(option);
        });
    };

    HT.searchObject = () => {
        $(document).on("keyup", ".search-model", function (e) {
            let _this = $(this);
            let keyword = _this.val();
            let option = {
                model: $(".select-product-and-quantity").val(),
                keyword: keyword,
            };
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                HT.loadProduct(option);
                HT.sendAjaxToGetMenu(option, target, menuRowClass);
            }, doneTypingInterval);
        });
    };

    var objectChoose = [];

    HT.chooseProductPromotion = () => {
        $(document).on("click", ".search-object-item", function (e) {
            e.preventDefault();
            let _this = $(this);
            let isChecked = _this
                .find('input[type="checkbox"]')
                .prop("checked");

            let objectItem = {
                product_id: _this.attr("data-productid"),
                product_variant_id: _this.attr("data-variant-id"),
                name: _this.attr("data-name"),
                uuid: _this.attr("data-uuid"),
            };

            if (isChecked) {
                objectChoose = objectChoose.filter(
                    (item) => item.product_id !== objectItem.product_id
                );
                _this.find('input[type="checkbox"]').prop("checked", false);
            } else {
                objectChoose.push(objectItem);
                _this.find('input[type="checkbox"]').prop("checked", true);
            }
        });
    };

    HT.confirmProductPromotion = () => {
        console.log(123);
        
        let preloadData = JSON.parse($(".input_object").val()) || {
            id: [],
            product_variant_id: [],
            name: [],
            variant_uuid: [],
        };

        let objectArray = preloadData.id.map((id, index) => ({
            product_id: id,
            product_variant_id: preloadData.product_variant_id[index] || "null",
            name: preloadData.name[index],
            uuid: preloadData.variant_uuid[index] || "null",
        }));

        console.log(objectArray);
        

        if (objectArray.length && typeof objectArray !== "undefined") {
            let preloadHtml = HT.renderBoxWrapper(objectArray);
            HT.checkFixGrid(preloadHtml);
        }

        $(document).on("click", ".confirm-product-promotion", function (e) {
            e.preventDefault();
            let html = HT.renderBoxWrapper(objectChoose);
            HT.checkFixGrid(html);
            $("#findProduct").modal("hide");
        });
    };
    HT.renderBoxWrapper = (objectData) => {
        console.log(objectData);
        
        let html = "";
        let model = $(".select-product-and-quantity").val();
        for (let i = 0; i < objectData.length; i++) {
            let { product_id, product_variant_id, name, uuid } = objectData[i];
            let classBox = `${model}_${product_id}_${product_variant_id}`;
            if (!$(`.boxWrapper .${classBox}`).length) {
                html += `
                <div class="fixGrid6 ${classBox}">
                    <div class="goods-item">
                        <span class="goods-item-name">
                            ${name}
                        </span>
                        <button type="button" class="delete-goods-item">
                            <i class="fa fa-trash"></i>
                        </button>
                        <div class="hidden">
                            <input type="text" name="object[id][]" value="${product_id}"> 
                            <input type="text" name="object[product_variant_id][]" value="${product_variant_id}">
                            <input type="text" name="object[variant_uuid][]" value="${uuid}">
                             <input type="text" name="object[name][]" value="${name}">
                        </div>
                    </div>
                </div>`;
            }
        }

        return html;
    };

    HT.checkFixGrid = (html) => {
        if ($(".fixGrid6 goods-item").elExist) {
            $(".boxSearchIcon").remove();
            // $(".boxSearchInput").remove();
            $(".boxWrapper").prepend(html);
        } else {
            $(".fixGrid6").remove();
            $(".boxWrapper").prepend(HT.boxSearchIcon());
        }
    };

    HT.boxSearchIcon = () => {
        return `
         <div class="boxSearchIcon">
            <i class="fa fa-search"></i>
        </div>`;
    };

    HT.deleteGoodsItem = () => {
        $(document).on("click", ".delete-goods-item", function (e) {
            console.log(123);

            e.stopPropagation();
            let _button = $(this);
            _button.parents(".fixGrid6").remove();
        });
    };

    $(document).ready(function () {
        HT.promotionNeverEnd();
        HT.promotionSource();
        HT.promotionMultipleSelect2();
        HT.chooseCustomerCondition();
        HT.chooseApplyItem();
        HT.deleteCondition();
        HT.btnJS100();
        HT.deleteAmountRangeCondition();
        HT.renderOrderRangeConditionContainer();
        HT.setupAjaxSearch();
        HT.productQuantityListProduct();
        HT.getPaginationMenu();
        HT.searchObject();
        HT.chooseProductPromotion();
        HT.confirmProductPromotion();
        HT.deleteGoodsItem();
        HT.changePromotionMethod();
        HT.checkConditionItemSet();
        // HT.rangeOnChange()
    });
})(jQuery);
