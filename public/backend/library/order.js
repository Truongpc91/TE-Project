(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf_token"]').attr("content");

    HT.select2 = () => {
        if ($(".setupSelect2").length) {
            $(".setupSelect2").select2();
        }
    };

    HT.loadCity = (province_id) => {
        if (province_id != "") {
            $(".provinces").val(province_id).trigger("change");
        }
    };

    HT.editOrder = () => {
        $(document).on("click", ".edit-order", function () {
            let _this = $(this);
            let target = _this.attr("data-target");

            let html = "";
            let originalHtml = _this
                .parents(".ibox")
                .find(".ibox-content")
                .html();
            if (target == "description") {
                html = HT.renderDescriptionOrder(_this);
            } else if (target == "customerInfo") {
                html = HT.renderCustomerOrderInformation();
                setTimeout(() => {
                    HT.select2();
                }, 0);
            }

            _this.parents(".ibox").find(".ibox-content").html(html);
            HT.changeEditToCancel(_this, originalHtml);
        });
    };

    HT.changeEditToCancel = (_this, originalHtml) => {
        let encodeHtml = btoa(encodeURIComponent(originalHtml.trim()));

        _this
            .html("Hủy bỏ")
            .removeClass("edit-order")
            .addClass("cancel-edit")
            .attr("data-html", encodeHtml);
    };

    HT.cancelEdit = () => {
        $(document).on("click", ".cancel-edit", function () {
            let _this = $(this);
            let originalHtml = decodeURIComponent(
                atob(_this.attr("data-html"))
            );
            _this.html("Sửa").removeClass("cancel-edit").addClass("edit-order");
            _this.parents(".ibox").find(".ibox-content").html(originalHtml);
        });
    };

    HT.renderCustomerOrderInformation = () => {
        let data = {
            fullName: $(".fullName").text(),
            email: $(".email").text(),
            phone: $(".phone").text(),
            address: $(".address").text(),
            ward_id: $(".ward_id").val(),
            district_id: $(".district_id").val(),
            province_id: $(".province_id").val(),
        };

        let html = `
            <div class="row mb15 ">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Họ tên</label>
                        <input type="text" name="fullName" value="${
                            data.fullName
                        }" class="form-control">
                    </div>
                </div>
            </div>
             <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Email</label>
                        <input type="text" name="email" value="${
                            data.email
                        }" class="form-control">
                    </div>
                </div>
            </div>
             <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Số điện thoại</label>
                        <input type="text" name="phone" value="${
                            data.phone
                        }" class="form-control">
                    </div>
                </div>
            </div>
             <div class="row mb15 ">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Địa chỉ</label>
                        <input type="text" name="address" value="${
                            data.address
                        }" class="form-control">
                    </div>
                </div>
            </div>
             <div class="row mb15 order-aside">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Thành phố</label>
                        <select name="province_id"  class="setupSelect2 provinces location" data-target="districts">
                            <option>[Chọn Thành phố]</option>
                            ${HT.provincesList(data.province_id)}
                        </select>
                    </div>
                </div>
            </div>
             <div class="row mb15 order-aside">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Quận/Huyện</label>
                        <select name="district_id" class="setupSelect2 districts location" data-target="wards">
                            <option>[Chọn Quận/Huyện]</option>
                        </select>
                    </div>
                </div>
            </div>
             <div class="row mb15 order-aside">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label for="">Phường/Xã</label>
                        <select name="ward_id" class="setupSelect2 wards">
                            <option>[Chọn Phường/Xã]</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mb15 order-aside">
                <div class="col-lg-12">
                    <div class="form-row">
                       <button class="btn btn-primary saveCustomer">Lưu lại</button>
                    </div>
                </div>
            </div>
        `;
        setTimeout(() => {
            HT.loadCity(data.province_id);
        }, 0);
        return html;
    };

    HT.provincesList = (province_id) => {
        let html = "";
        for (let i = 0; i < provinces.length; i++) {
            html +=
                '<option value="' +
                provinces[i].id +
                '">' +
                provinces[i].name +
                "</option>";
        }
        return html;
    };

    HT.renderDescriptionOrder = (_this) => {
        let inputValue = _this
            .parents(".ibox")
            .find(".ibox-content")
            .text()
            .trim();

        return (
            '<input class="form-control ajax-edit" name="description" data-field="description" value="' +
            inputValue +
            '">'
        );
    };

    HT.updateDescription = () => {
        $(document).on("change", ".ajax-edit", function () {
            let _this = $(this);
            let field = _this.attr("data-field");
            let value = _this.val();
            let option = {
                id: $(".orderId").val(),
                payload: {
                    [field]: value,
                },
                _token: _token,
            };

            HT.ajaxUpdateOrderInfo(option, _this);
        });
    };

    HT.getLocation = () => {
        $(document).on("change", ".location", function () {
            let _this = $(this);
            let option = {
                data: {
                    location_id: _this.val(),
                },
                target: _this.attr("data-target"),
            };

            HT.sendDataTogetLocation(option);
        });
    };

    HT.sendDataTogetLocation = (option) => {
        let district_id = $(".district_id").val();
        let ward_id = $(".ward_id").val();

        $.ajax({
            url: "http://shopprojectt.test/admin/ajax/location/getlocation",
            type: "GET",
            data: option,
            dataType: "json",
            success: function (res) {
                $("." + option.target).html(res.html);

                if (district_id != "" && option.target == "districts") {
                    $(".districts").val(district_id).trigger("change");
                }

                if (ward_id != "" && option.target == "wards") {
                    $(".wards").val(ward_id).trigger("change");
                }
            },
            // error: function (jqXHR, textStatus, errorThrown) {
            //     console.log("lỗi" + textStatus + " " + errorThrown);
            // },
        });
    };

    HT.ajaxUpdateOrderInfo = (option, _this) => {
        $.ajax({
            url: "http://shopprojectt.test/admin/ajax/order/update",
            type: "POST",
            data: option,
            dataType: "json",
            success: function (res) {
                if (res.error == 10) {
                    if (
                        _this
                            .parents(".ibox")
                            .find(".cancel-edit")
                            .attr("data-target") == "description"
                    ) {
                        HT.renderDescriptionHtml(
                            option.payload,
                            _this.parents(".ibox")
                        );
                    } else if (
                        _this
                            .parents(".ibox")
                            .find(".cancel-edit")
                            .attr("data-target") == "customerInfo"
                    ) {
                        HT.renderCustomerOrderInfoHtml(res);
                    }
                }
            },
        });
    };

    HT.renderCustomerOrderInfoHtml = (res) => {
        let html = `
            <div class="custom-line">
                <strong>N:</strong>
                <span class="fullName">${res.order.fullName}</span>
            </div>
            <div class="custom-line">
                <strong>E:</strong>
                <span class="email">${res.order.email}</span>
            </div>
            <div class="custom-line">
                <strong>P:</strong>
                <span class="phone">${res.order.phone}</span>
            </div>
            <div class="custom-line">
                <strong>A:</strong>
            <span class="address">${res.order.address}</span>
            </div>
            <div class="custom-line">
                <strong>P:</strong>
                ${res.order.ward_name}
            </div>
            <div class="custom-line">
                <strong>Q:</strong>
                ${res.order.district_name}
            </div>
            <div class="custom-line">
                <strong>T:</strong>
                ${res.order.province_name}
            </div>
        `;

        $(".order-customer-information").html(html);
        $(".ward_id").val(res.order.ward_id);
        $(".district_id").val(res.order.district_id);
        $(".province_id").val(res.order.province_id);

        $(".order-customer-information")
            .parents(".ibox")
            .find(".cancel-edit")
            .removeClass("cancel-edit")
            .addClass("edit-order")
            .attr("data-html", "")
            .html("Sửa");
    };

    HT.renderDescriptionHtml = (payload, target) => {
        target.find(".ibox-content").html(payload.description);
        target
            .find(".cancel-edit")
            .removeClass("cancel-edit")
            .addClass("edit-order")
            .attr("data-html", "")
            .html("Sửa");
    };

    HT.saveCustomer = () => {
        $(document).on("click", ".saveCustomer", function () {
            let _this = $(this);
            let option = {
                id: $(".orderId").val(),
                payload: {
                    fullName: $('input[name="fullName"]').val(),
                    email: $('input[name="email"]').val(),
                    phone: $('input[name="phone"]').val(),
                    address: $('input[name="address"]').val(),
                    ward_id: $(".wards").val(),
                    district_id: $(".districts").val(),
                    province_id: $(".provinces").val(),
                },
                _token: _token,
            };
            HT.ajaxUpdateOrderInfo(option, _this);
        });
    };

    HT.updateField = () => {
        $(document).on("click", ".updateField", function () {
            let _this = $(this);
            let option = {
                payload: {
                    [_this.attr("data-field")]: _this.attr("data-value"),
                },
                id: $(".orderId").val(),
                _token: _token,
            };
            $.ajax({
                url: "http://shopprojectt.test/admin/ajax/order/update",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                    if (res.error == 10) {
                        HT.createOrderConfirmSelection(_this);
                    }
                },
            });
        });
    };

    HT.updateBadge = () => {
        $(document).on("change", ".updateBadge", function () {
            let _this = $(this);
            let option = {
                payload: {
                    [_this.attr("data-field")]: _this.val(),
                },
                id: _this.parents("tr").find(".checkBoxItem").val(),
                _token: _token,
            };
            let confirmStatus = _this.parents("tr").find(".confirm").val();

            if (confirmStatus != "pending") {
                $.ajax({
                    url: "http://shopprojectt.test/admin/ajax/order/update",
                    type: "POST",
                    data: option,
                    dataType: "json",
                    success: function (res) {
                        if (res.error == 10) {
                            toastr.success(
                                "Cập nhật trạng thái thành công",
                                "Hệ thống thông báo !"
                            );
                        }
                    },
                });
            } else {
                toastr.error(
                    "Bạn phải xác nhận đơn hàng trước khi cập nhật trạng thái",
                    "Hệ thống thông báo !"
                );
                return false;
            }
        });
    };

    HT.createOrderConfirmSelection = (_this) => {
        console.log(_this);
        let button =
            ' <button class="btn btn-danger updateField" data-field="confirm" data-value="cancel" data-title="ĐÃ HỦY THANH TOÁN ĐƠN HÀNG">Hủy đơn</button>';

        let correctButton = '<i class="fa fa-check text-success"></i>';

        $(".confirm-box").find(".icon").html(correctButton);
        $(".isConfirm").html(_this.attr("data-title"));

        if (_this.attr("data-field") == "confirm") {
            $(".confirm-block").html("Đã xác nhận");
            $(".cancel-block").html(button);
        }

        if (_this.attr("data-field") == "cancel") {
            _this.parent().html("Đơn hàng đã được hủy");
        }
    };

    $(document).ready(function () {
        HT.select2();
        HT.editOrder();
        HT.updateDescription();
        HT.cancelEdit();
        HT.getLocation();
        HT.saveCustomer();
        HT.updateField();
        HT.updateBadge();
    });
})(jQuery);
