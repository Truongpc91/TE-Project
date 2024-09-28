(function ($) {
    "use strict";
    var HT = {};
    var _token = $('meta[name="csrf_token"]').attr("content");
    HT.createMenuCatalogue = () => {
        $(document).on("submit", ".create-menu-catalogue", function (e) {
            e.preventDefault();

            let _form = $(this);
            let option = {
                name: _form.find("input[name=name]").val(),
                keyword: _form.find("input[name=keyword]").val(),
                _token: _token,
            };

            $.ajax({
                url: "http://shopprojectt.test/admin/ajax/menu/createCatalogue",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                    if (res.code == 0) {
                        $(".form-error")
                            .removeClass("error")
                            .addClass("text-success")
                            .html(res.message)
                            .show();

                        const menuCatalogueSelect = $(
                            "select[name=menu_catalogue_id]"
                        );
                        menuCatalogueSelect.append(
                            '<option value="' +
                                res.data.id +
                                '">' +
                                res.data.name +
                                "</option>"
                        );
                    } else {
                        $(".form-error")
                            .removeClass("success")
                            .addClass("text-danger")
                            .html(res.message)
                            .show();
                    }
                },
                beforeSend: function () {
                    _form.find(".error").html("");
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == 422) {
                        let errors = jqXHR.responseJSON.errors;

                        for (const field in errors) {
                            let errorMessage = errors[field];
                            errorMessage.forEach(function (message) {
                                $("." + field).html(message);
                            });
                        }
                    }
                },
            });
        });
    };

    HT.createMenuRow = () => {
        $(document).on("click", ".add-menu", function (e) {
            e.preventDefault();
            let _this = $(this);

            $(".menu-wrapper")
                .append(HT.menuRowHtml())
                .find(".notification")
                .hide();
        });
    };

    HT.menuRowHtml = (option) => {
        let html;
        let row = $("<div>").addClass(
            "row mb10 menu-item " +
                (typeof option != "undefined" ? option.canonical : "") +
                ""
        );

        const columns = [
            {
                class: "col-lg-4",
                name: "menu[name][]",
                value: typeof option != "undefined" ? option.name : "",
            },
            {
                class: "col-lg-4",
                name: "menu[canonical][]",
                value: typeof option != "undefined" ? option.canonical : "",
            },
            { class: "col-lg-2", name: "menu[order][]", value: 0 },
        ];

        columns.forEach((column) => {
            let col = $("<div>").addClass(column.class);
            let input = $("<input>")
                .attr("type", "text")
                .attr("value", column.value)
                .addClass(
                    "form-control " +
                        (column.name == "menu[order][]" ? "int text-right" : "")
                )
                .attr("name", column.name);
            col.append(input);
            row.append(col);
        });

        let removeCol = $("<div>").addClass("col-lg-2");
        let removeRow = $("<div>").addClass("form-row text-center");
        let a = $("<a>").addClass("delete-menu");
        let icon = $("<i>").addClass("fa fa-trash");
        let input = $('<input>').addClass("hidden").attr("name", "menu[id][]").attr("value", 0);

        a.append(icon);
        removeRow.append(a);
        removeCol.append(removeRow);
        removeCol.append(input);
        row.append(removeCol);

        return row;
    };

    HT.deleteRow = () => {
        $(document).on("click", ".delete-menu", function () {
            let _this = $(this);
            _this.parents(".menu-item").remove();
            HT.checkMenuItemLength();
        });
    };

    HT.getMenu = () => {
        $(document).on("click", ".menu-module", function () {
            let _this = $(this);
            let options = {
                model: _this.attr("data-model"),
            };
            let target = _this.parents(".panel-default").find(".menu-list");
            let menuRowClass = HT.checkMenuRowExist();

            HT.sendAjaxToGetMenu(options, target, menuRowClass);
        });
    };

    HT.checkMenuRowExist = () => {
        let menuRowClass = $(".menu-item")
            .map(function () {
                let allClasses = $(this)
                    .attr("class")
                    .split(" ")
                    .slice(3)
                    .join(" ");

                return allClasses;
            })
            .get();

        return menuRowClass;
    };
    HT.sendAjaxToGetMenu = (options, target, menuRowClass) => {
        $.ajax({
            url: "http://shopprojectt.test/admin/ajax/dashboard/getMenu",
            type: "GET",
            data: options,
            dataType: "json",
            beforeSend: function () {
                $(".menu-list").html("");
            },
            success: function (res) {
                let html = "";
                for (let i = 0; i < res.data.length; i++) {
                    html += HT.renderModelMenu(res.data[i], menuRowClass);
                }
                html += HT.menuLinks(res.links).prop("outerHTML");
                target.html(html);
            },
            error: function (jqXHR, textStatus, errorThrown) {},
        });
    };

    HT.menuLinks = (links) => {
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

    HT.checkMenuItemLength = () => {
        if ($(".menu-item").length === 0) {
            $(".notification").show();
        }
    };

    HT.renderModelMenu = (object, menuRowClass) => {
        let html = "";

        html += '<div class="m-item" style="padding: 10px;">';
        html += '<div class="">';
        html +=
            '<input type="checkbox" ' +
            (menuRowClass.includes(object.canonical) ? "checked" : "") +
            '  class="m0 choose-menu" value="' +
            object.canonical +
            '" name="" id="' +
            object.canonical +
            '">';
        html +=
            '<label for="' +
            object.canonical +
            '" style="color: blue; cursor:pointer;" >' +
            object.name +
            "</label>";
        html += "</div>";
        html += " </div>";

        return html;
    };

    HT.chooseMenu = () => {
        $(document).on("click", ".choose-menu", function () {
            let _this = $(this);
            let canonical = _this.val();
            let name = _this.siblings("label").text();
            let row = HT.menuRowHtml({
                name: name,
                canonical: canonical,
            });
            let isChecked = _this.prop("checked");
            if (isChecked === true) {
                $(".menu-wrapper").append(row).find(".notification").hide();
            } else {
                $(".menu-wrapper")
                    .find("." + canonical)
                    .remove();
                HT.checkMenuItemLength();
            }
        });
    };

    HT.getPaginationMenu = () => {
        $(document).on("click", ".page-link", function (e) {
            e.preventDefault();
            let _this = $(this);
            let option = {
                model: _this.parents(".panel-collapse").attr("id"),
                page: _this.text(),
            };
            let target = _this.parents(".menu-list");
            let menuRowClass = HT.checkMenuRowExist();
            HT.sendAjaxToGetMenu(option, target, menuRowClass);
        });
    };

    HT.searMenu = () => {
        let typingTimer;
        let doneTypingInterval = 1000;
        $(document).on("keyup", ".search-menu", function () {
            let _this = $(this);
            let keyword = _this.val();
            let option = {
                model: _this.parents(".panel-collapse").attr("id"),
                keyword: keyword,
            };
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                let menuRowClass = HT.checkMenuRowExist();
                let target = _this.parents("").siblings(".menu-list");
                HT.sendAjaxToGetMenu(option, target, menuRowClass);
            }, doneTypingInterval);
        });
    };

    HT.setupNestableMenu = () => {
        if ($("#nestable2").length) {
            $("#nestable2")
                .nestable({
                    group: 1,
                })
                .on("change", HT.updateNestableOutput);
        }
    };

    HT.updateNestableOutput = (e) => {
        // var updateOutput = function (e) {
        //     var list = e.length ? e : $(e.target),
        //         output = list.data("output");
        //     if (window.JSON) {
        //         output.val(window.JSON.stringify(list.nestable("serialize"))); //, null, 2));
        //     } else {
        //         output.val("JSON browser support required for this demo.");
        //     }
        // };

        var list = $(e.currentTarget),
        output = $(list.data("output"));
        let json = window.JSON.stringify(list.nestable('serialize'))
       
        if(json.length){
            let option = {
                json: json,
                menu_catalogue_id: $('#dataCatalogue').attr('data-catalogueId'),
                _token: _token
            }

            $.ajax({
                url: "http://shopprojectt.test/admin/ajax/menu/drag",
                type: "POST",
                data: option,
                dataType: "json",
                success: function (res) {
                
                },
                error: function (jqXHR, textStatus, errorThrown) {},
            });
            
        }
    };

    // HT.runUpdateOutput = () => {
    //     updateOutput($("#nestable2").data("output", $("#nestable2-output")));
    // };

    HT.expandAndCollapse = () => {
        console.log(123);
        $("#nestable-menu").on("click", function (e) {
            var target = $(e.target),
                action = target.data("action");
            if (action === "expand-all") {
                $(".dd").nestable("expandAll");
            }
            if (action === "collapse-all") {
                $(".dd").nestable("collapseAll");
            }
        });
    };

    $(document).ready(function () {
        HT.createMenuCatalogue();
        HT.createMenuRow();
        HT.deleteRow();
        HT.getMenu();
        HT.chooseMenu();
        HT.getPaginationMenu();
        HT.searMenu();
        HT.setupNestableMenu();
        HT.updateNestableOutput();
        HT.expandAndCollapse();
    });
})(jQuery);
