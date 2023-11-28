/*!
 * Font Awesome Free 5.10.2 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 */
(function ($) {
  // var sidebarWidth = $(".rtwwwap-open-dropdown").width();
  // console.log(sidebarWidth);

  // $(".rtwwwap-open-dropdown").hover(function() {
  //     $(this).next("ul").css("left", sidebarWidth).slideToggle("slow");
  // }, function() {
  //     $(this).find("ul").hide().css("left", 0);
  // });

  "use strict";

  ////main js

  $(document).ready(function () {
    $(".rtwwwap_toggle_notification_btn").on("click", function () {
      $(".rtwwwap_notification_dropdown_list").toggle();
    });
    $(".rtwwwap-modal-close").on("click", function () {
      $(".rtwwwap_notification_modal").hide();
    });
    $(".rtwwwap_sidebar_icon").click(function () {
      $(".rtwwwap_sidebar_wrapper").addClass("rtwwwap_sidebar_open");
    });
    $(".rtwwwap-close").click(function () {
      $(".rtwwwap_sidebar_wrapper").removeClass("rtwwwap_sidebar_open");
    });
    $(".rtwwwap_tab").on("click", function () {
      // if($(".rtwwwap_tab").hasClass("rtwwwap-navbar-active")){
      $(".rtwwwap_sidebar_wrapper").removeClass("rtwwwap_sidebar_open");
      // }
    });

    $(document).find(".rtwwwap_search_product").select2({ width: "50%" });
    $(document).find(".rtwwwap_select_cat").select2({ width: "50%" });
    $(document).find(".rtwwwap_payment_method").select2({ width: "100%" });

    $(document)
      .find("#rtwwwap-refferral-table ")
      .DataTable({
        select: true,
        responsive: true,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search",
        },
        drawCallback: function () {
          $(".dataTables_filter input").addClass("rtwwwap-input-search-field");
          $(".dataTables_length select").addClass("rtwwwap-select-box");
          $(".dataTables_paginate > .pagination .mdl-button--raised").addClass(
            "rtwwwap-pagination-btn-radius"
          );
          $("<span class='rtwwwap-focus-border'></span>").insertAfter(
            ".dataTables_filter input"
          );
        },
      });

    $(document)
      .find("#rtwwwap_coupon_table")
      .DataTable({
        select: true,
        responsive: true,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],

        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search",
        },
        drawCallback: function () {
          $(".dataTables_filter input").addClass("rtwwwap-input-search-field");
          $(".dataTables_length select").addClass("rtwwwap-select-box");
          $(".dataTables_paginate > .pagination .mdl-button--raised").addClass(
            "rtwwwap-pagination-btn-radius"
          );
          $("<span class='rtwwwap-focus-border'></span>").insertAfter(
            ".dataTables_filter input"
          );
        },
      });

    $(document)
      .find("#rtwwwap_mlm_table")
      .DataTable({
        select: true,
        responsive: true,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],

        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search",
        },
        drawCallback: function () {
          $(".dataTables_filter input").addClass("rtwwwap-input-search-field");
          $(".dataTables_length select").addClass("rtwwwap-select-box");
          $(".dataTables_paginate > .pagination .mdl-button--raised").addClass(
            "rtwwwap-pagination-btn-radius"
          );
          $("<span class='rtwwwap-focus-border'></span>").insertAfter(
            ".dataTables_filter input"
          );
        },
      });

    $(document)
      .find("#rtwwwap_test")
      .DataTable({
        select: true,
        responsive: true,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],

        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search",
        },
        drawCallback: function () {
          $(".dataTables_filter input").addClass("rtwwwap-input-search-field");
          $(".dataTables_length select").addClass("rtwwwap-select-box");
          $(".dataTables_paginate > .pagination .mdl-button--raised").addClass(
            "rtwwwap-pagination-btn-radius"
          );
          $("<span class='rtwwwap-focus-border'></span>").insertAfter(
            ".dataTables_filter input"
          );
        },
      });

    $(document)
      .find("#rtwwwap_report_date_wise")
      .DataTable({
        select: true,
        responsive: true,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],

        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search",
        },
        drawCallback: function () {
          $(".dataTables_filter input").addClass("rtwwwap-input-search-field");
          $(".dataTables_length select").addClass("rtwwwap-select-box");
          $(".dataTables_paginate > .pagination .mdl-button--raised").addClass(
            "rtwwwap-pagination-btn-radius"
          );
          $("<span class='rtwwwap-focus-border'></span>").insertAfter(
            ".dataTables_filter input"
          );
        },
      });

    $(document)
      .find("#rtwwwap_report_table")
      .DataTable({
        select: true,
        responsive: true,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],

        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search",
        },
        drawCallback: function () {
          $(".dataTables_filter input").addClass("rtwwwap-input-search-field");
          $(".dataTables_length select").addClass("rtwwwap-select-box");
          $(".dataTables_paginate > .pagination .mdl-button--raised").addClass(
            "rtwwwap-pagination-btn-radius"
          );
          $("<span class='rtwwwap-focus-border'></span>").insertAfter(
            ".dataTables_filter input"
          );
        },
      });

    $(document)
      .find("#rtwwwap_report_sec_table")
      .DataTable({
        select: true,
        responsive: true,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],

        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search",
        },
        drawCallback: function () {
          $(".dataTables_filter input").addClass("rtwwwap-input-search-field");
          $(".dataTables_length select").addClass("rtwwwap-select-box");
          $(".dataTables_paginate > .pagination .mdl-button--raised").addClass(
            "rtwwwap-pagination-btn-radius"
          );
          $("<span class='rtwwwap-focus-border'></span>").insertAfter(
            ".dataTables_filter input"
          );
        },
      });
    // $(document).find( '.rtwwwap_select_cat' ).select2({ width: '50%' });

    //   $(document).find( '.rtwwwap_search_product' ).select2({ width: '50%'});

    $(document)
      .find(".rtwwwap-toggle")
      .click(function () {
        $(".rtwwwap_sidebar_wrapper").addClass("rtwwwap-show-sidebar");
      });
    $(document)
      .find(".rtwwwap-close")
      .click(function () {
        $(".rtwwwap_sidebar_wrapper").removeClass("rtwwwap-show-sidebar");
      });
    $(".rtwwwap-submanu-open-btn").click(function () {
      $(".rtwwwap-banner-submenu").slideDown();
      $(".rtwwwap-submanu-open-btn").hide();
      $(".rtwwwap-submanu-close-btn").show();
    });

    $(".rtwwwap-submanu-close-btn").click(function () {
      $(".rtwwwap-banner-submenu").slideUp();
      $(".rtwwwap-submanu-open-btn").show();
      $(".rtwwwap-submanu-close-btn").hide();
    });

    $(document).on("click", ".rtwwwap-open-dropdown", function () {
      if ($(this).hasClass("rtwwwap_submenu_active")) {
        $(this).next().slideToggle();
        if (
          $(this).find(".rtwwwap_arrow_down").hasClass("rtwwwap-arrow-hide")
        ) {
          $(this).find(".rtwwwap_arrow_down").removeClass("rtwwwap-arrow-hide");
          $(this).find(".rtwwwap_arrow_up").addClass("rtwwwap-arrow-hide");
        } else {
          $(this).find(".rtwwwap_arrow_down").addClass("rtwwwap-arrow-hide");
          $(this).find(".rtwwwap_arrow_up").removeClass("rtwwwap-arrow-hide");
        }
      } else {
        $(".rtwwwap-banner-submenu").slideUp();
        $(".rtwwwap_arrow_up").addClass("rtwwwap-arrow-hide");
        $(".rtwwwap_arrow_down").removeClass("rtwwwap-arrow-hide");
        $(this).next().slideToggle();
        $(this).children(".rtwwwap_arrow_down").addClass("rtwwwap-arrow-hide");
        $(this).children(".rtwwwap_arrow_up").removeClass("rtwwwap-arrow-hide");
      }

      $(".rtwwwap-open-dropdown").removeClass("rtwwwap_submenu_active");
      $(this).addClass("rtwwwap_submenu_active");
    });

    $(document).on("click", ".rtwwwap_custom_banner_copy_html", function () {
      $(this)
        .parent(".rtwwwap_custom_banner_product")
        .find(".rtwwwap_banner_copy_text")
        .fadeIn(800)
        .delay(500)
        .fadeOut(800);
      var rtwwwap_html = "";
      var rtwwwap_image_url = $(this).data("image_id");
      var rtwwwap_target_link = $(this).data("target_link");
      var rtwwwap_image_width = $(this).data("image_width");
      var rtwwwap_image_height = $(this).data("image_height");
      rtwwwap_html +=
        '<a href="' +
        rtwwwap_target_link +
        '" style="width:' +
        rtwwwap_image_width +
        ";height:" +
        rtwwwap_image_height +
        '">';
      rtwwwap_html +=
        '<img src="' + rtwwwap_image_url + '" style="height:100%; width:100%">';
      rtwwwap_html += "</a>";
      var $rtwwwap_temp = $("<input>");
      $("body").append($rtwwwap_temp);
      $rtwwwap_temp.val(rtwwwap_html).select();
      document.execCommand("copy");
      $rtwwwap_temp.remove();
    });

    $("#rtwwwap-btn-2").click(function () {
      $(".rtwwwap-paypal-deatil").show();
      $(".rtwwwap-bank-deatil").hide();
      $(".rtwwwap-stripe-deatil").hide();
      $(".rtwwwap-paystack-deatil").hide();
    });
    $("#rtwwwap-btn-1").click(function () {
      $(".rtwwwap-paypal-deatil").hide();
      $(".rtwwwap-stripe-deatil").hide();
      $(".rtwwwap-paystack-deatil").hide();
      $(".rtwwwap-bank-deatil").show();
    });
    $("#rtwwwap-btn-3").click(function () {
      $(".rtwwwap-paypal-deatil").hide();
      $(".rtwwwap-bank-deatil").hide();
      $(".rtwwwap-paystack-deatil").hide();
      $(".rtwwwap-stripe-deatil").show();
    });
    $("#rtwwwap-btn-4").click(function () {
      $(".rtwwwap-paypal-deatil").hide();
      $(".rtwwwap-bank-deatil").hide();
      $(".rtwwwap-stripe-deatil").hide();
      $(".rtwwwap-paystack-deatil").show();
    });

    $(document)
      .find(".rtwwwap_tab")
      .on("click", function () {
        $(".rtwwwap_hide").css("display", "none");
        var rtwwwap_current_tab = $(this).data("tab");
        $(".rtwwwap_tab").removeClass("rtwwwap-navbar-active");
        $(this).addClass("rtwwwap-navbar-active");
        var rtwwwap_current_tab = "#" + rtwwwap_current_tab;
        $(rtwwwap_current_tab).css("display", "block");

        $("html, body").animate({
          scrollTop: $(".rtwwwap_template_body").offset().top,
        });
      });

    // on reload the page.
    $(".rtwwwap_hide").css("display", "none");
    var rtwwwap_current_URL = window.location.href;
    var rtwwwap_tab = rtwwwap_current_URL.split("#");
    if (rtwwwap_tab.length > 1 && rtwwwap_tab[1] != "") {
      var rtwwwap_current_tab = "#" + rtwwwap_tab[1];
      $(rtwwwap_current_tab).css("display", "block");
      $(".rtwwwap_tab").removeClass("rtwwwap-navbar-active");
      $(rtwwwap_current_tab).addClass("rtwwwap-navbar-active");
    } else {
      $("#rtwwwap-overview-wrapper").css("display", "block");
    }

    var prod_title = rtwwwap_global_params.rtwwwap_chart.title;

    var prod_sales = rtwwwap_global_params.rtwwwap_chart.data;
    var coloR = [];

    var dynamicColors = function () {
      var r = Math.floor(Math.random() * 255);
      var g = Math.floor(Math.random() * 255);
      var b = Math.floor(Math.random() * 255);
      return "rgb(" + r + "," + g + "," + b + ")";
    };

    for (var i in prod_title) {
      coloR.push(dynamicColors());
    }

    var myChart = new Chart("rtwwwap_status", {
      type: "bar",
      data: {
        labels: prod_title,
        datasets: [
          {
            backgroundColor: coloR,
            data: prod_sales,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: "Overall Commission",
        },
      },
    });

    var options = {
      series: [44, 55, 67, 83],
      chart: {
        height: 320,
        type: "radialBar",
      },
      plotOptions: {
        radialBar: {
          dataLabels: {
            name: {
              fontSize: "22px",
            },
            value: {
              fontSize: "16px",
            },
            total: {
              show: true,
              label: "Total",
              formatter: function (w) {
                // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                return 9000;
              },
            },
          },
        },
      },
      labels: prod_title,
    };

    var report_title = rtwwwap_global_params.rtwwwap_report_chart_device.title;
    var report_sales = rtwwwap_global_params.rtwwwap_report_chart_device.data;

    var myChart = new Chart("dashboard_report_device", {
      type: "doughnut",
      data: {
        labels: report_title,
        datasets: [
          {
            backgroundColor: Array("#ff788e", "#20deff"),
            data: report_sales,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: "Device Purchased",
        },
        cutoutPercentage: 70,
      },
    });

    //Report chart section
    var report_title = rtwwwap_global_params.rtwwwap_report_chart.title;
    var report_sales = rtwwwap_global_params.rtwwwap_report_chart.data;

    var myChart = new Chart("rtwwwap_report", {
      type: "doughnut",
      data: {
        labels: report_title,
        datasets: [
          {
            backgroundColor: Array("#15607a", "#1d81a2", "#09bb9f"),
            data: report_sales,
          },
        ],
      },
      options: {
        cutoutPercentage: 70,
      },
    });

    var report_title = rtwwwap_global_params.rtwwwap_report_chart_device.title;
    var report_sales = rtwwwap_global_params.rtwwwap_report_chart_device.data;

    var myChart = new Chart("rtwwwap_reports", {
      type: "doughnut",
      data: {
        labels: report_title,
        datasets: [
          {
            backgroundColor: Array("#20deff", "#ff6384"),
            data: report_sales,
          },
        ],
      },
      options: {
        cutoutPercentage: 70,
      },
    });

    $(document).on("click", "#rtwwwap_create_coupon", function (e) {
      var rtwwwap_amount = parseFloat(
        $(document).find("#rtwwwap_coupon_amount").val()
      );
      var rtwwwap_amount_min = parseFloat(
        $(document).find("#rtwwwap_coupon_amount").attr("min")
      );
      var rtwwwap_amount_max = parseFloat(
        $(document).find("#rtwwwap_coupon_amount").attr("max")
      );

      if (!rtwwwap_amount) {
        alert(" Please input amount for coupon generation");
      } else if (rtwwwap_amount < rtwwwap_amount_min) {
        alert(
          rtwwwap_global_params.rtwwwap_valid_coupon_less_msg +
            " " +
            rtwwwap_amount_min
        );
        return false;
      } else if (rtwwwap_amount > rtwwwap_amount_max) {
        alert(
          rtwwwap_global_params.rtwwwap_valid_coupon_more_msg +
            " " +
            rtwwwap_amount_max
        );
        return false;
      } else {
        var rtwwwap_data = {
          action: "rtwwwap_create_coupon",
          rtwwwap_amount: rtwwwap_amount,
          rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
        };

        $.blockUI({ message: "" });
        $.ajax({
          url: rtwwwap_global_params.rtwwwap_ajaxurl,
          type: "POST",
          data: rtwwwap_data,
          dataType: "json",
          success: function (response) {
            window.location.reload();

            $.unblockUI();
          },
        });
      }
    });

    $(document).on("click", ".rtwwwap_order_details", function () {
      var product_name = $(this).data("product_name");
      var product_price = $(this).data("product_pprice");
      var product_commi = $(this).data("product_commission");
      var order_status = $(this).data("order_status");
      var payment_method = $(this).data("payment_method");

      $("#rtwwwap_product_name").text(product_name);
      $("#rtwwwap_product_price").text(product_price);
      $("#rtwwwap_commission_received").text(product_commi);
      $("#rtwwwap_order_status").text(order_status);

      $("#rtwwwap_payment_method").text(payment_method);

      $(".rtwwwap_member_modal").css("display", "block");
    });

    $(document).on("click", ".rtwwwap_member_close", function () {
      $(".rtwwwap_member_modal").slideUp("100");
    });
    $(document).on("click", ".rtwwwap_close_button", function () {
      $(".rtwwwap_member_modal").slideUp("100");
    });

    $(document).on("click", "#rtwwwap_show_mlm_chain", function () {
      var rtwwwap_user_id = $(this).data("user_id");
      var rtwwwap_active = $(document)
        .find("#rtwwwap_show_active_only")
        .prop("checked");

      var data = {
        action: "rtwwwap_public_get_mlm_chain",
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
        rtwwwap_user_id: rtwwwap_user_id,
        rtwwwap_active: rtwwwap_active,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: data,
        dataType: "json",
        success: function (response) {
          $(document).find("#rtwwwap_mlm_show").html("");
          $(document)
            .find("#rtwwwap_mlm_chain_struct")
            .html(response.rtwwwap_tree_html);

          $(document)
            .find("#rtwwwap_mlm_show")
            .orgchart({
              data: $(document).find("#rtwwwap_mlm_data"),
              className: "top-level",
              createNode: function ($node, data) {
                if (data.class == "rtwwwap_noedit_disabled") {
                  var secondMenuIcon = $("<i>", {
                    class: "fa fa-info-circle rtwwwap-second-menu-icon",
                  });
                  var secondMenu =
                    '<div class="rtwwwap-second-menu">' +
                    rtwwwap_global_params.rtwwwap_disabled +
                    "</div>";

                  $node.append(secondMenuIcon).append(secondMenu);
                } else if (data.class == "rtwwwap_noedit") {
                  var secondMenuIcon = $("<i>", {
                    class: "fa fa-info-circle rtwwwap-second-menu-icon",
                  });
                  var secondMenu =
                    '<div class="rtwwwap-second-menu">' +
                    rtwwwap_global_params.rtwwwap_enabled +
                    "</div>";

                  $node.append(secondMenuIcon).append(secondMenu);
                } else if (data.class == "rtwwwap_disabled") {
                  var secondMenuIcon = $("<i>", {
                    class:
                      "fa fa-check-circle rtwwwap_active rtwwwap-second-menu-icon",
                  });
                  var secondMenu =
                    '<div class="rtwwwap-second-menu">' +
                    rtwwwap_global_params.rtwwwap_mlm_user_activate +
                    "</div>";

                  $node.append(secondMenuIcon).append(secondMenu);
                } else if (data.class == "rtwwwap_enabled") {
                  var secondMenuIcon = $("<i>", {
                    class:
                      "fa fa-times-circle rtwwwap_deactive rtwwwap-second-menu-icon",
                  });
                  var secondMenu =
                    '<div class="rtwwwap-second-menu">' +
                    rtwwwap_global_params.rtwwwap_mlm_user_deactivate +
                    "</div>";

                  $node.append(secondMenuIcon).append(secondMenu);
                } else {
                  var secondMenuIcon = $("<i>", {
                    class: "fa fa-info-circle rtwwwap-second-menu-icon",
                  });
                  var secondMenu =
                    '<div class="rtwwwap-second-menu">' +
                    rtwwwap_global_params.rtwwwap_parent +
                    "</div>";

                  $node.append(secondMenuIcon).append(secondMenu);
                }
              },
            });
          $(document).find("#rtwwwap_show_active_only").removeAttr("disabled");

          if (
            response.rtwwwap_improper_chain &&
            response.rtwwwap_mlm_user_status_checked
          ) {
            $(document).find(".rtwwwap_mlm_chain_not").show();
          }
          $.unblockUI();
        },
      });
    });

    $(document).on("click", "#rtwwwap_show_active_only", function () {
      $(document).find("#rtwwwap_show_mlm_chain").trigger("click");
    });

    $(document).on("click", "#rtwwwap_generate_button", function () {
      $(".rtwwwap_span_copied").css({ display: "inline-block" });
      $("#rtwwwap_generate_qr").css({
        display: "inline-block",
        padding: "6.5px",
      });

      var rtwwwap_url = $(document).find("#rtwwwap_aff_link_input").val();

      if (
        rtwwwap_url != "" &&
        rtwwwap_url.startsWith(rtwwwap_global_params.rtwwwap_home_url)
      ) {
        var rtwwwap_aff_id = $(this).data("rtwwwap_aff_id");
        var rtwwwap_aff_name = $(this).data("rtwwwap_aff_name");
        var rtwwwap_aff_slug = $(this).data("rtwwwap_aff_slug");

        if (rtwwwap_aff_slug != " ") {
          rtwwwap_aff_slug = rtwwwap_aff_slug;
        } else {
          rtwwwap_aff_slug = "rtwwwap_aff";
        }

        var rtwwwap_generated_url = "";
        var rtwwwap_generated_url_share = "";

        if (rtwwwap_url.indexOf('?') > 0) {
          rtwwwap_generated_url = rtwwwap_url + '&' + rtwwwap_aff_slug + '=' + rtwwwap_aff_name;
          rtwwwap_generated_url_share = rtwwwap_url + '&' + rtwwwap_aff_slug + '=' + rtwwwap_aff_name + '&action=share';
        }
        else {
            rtwwwap_generated_url = rtwwwap_url + '?' + rtwwwap_aff_slug + '=' + rtwwwap_aff_name;
            rtwwwap_generated_url_share = rtwwwap_url + '?' + rtwwwap_aff_slug + '=' + rtwwwap_aff_name + '&action=share';
        }

        $(document)
          .find("#rtwwwap_generated_link")
          .text(rtwwwap_generated_url)
          .css({ visibility: "visible" });
        // $(document).find( '#rtwwwap_copy_to_clip, #rtwwwap_generate_qr, .rtwwwap_download_qr' ).css({ 'visibility' : 'visible' });

        $(document)
          .find(".rtwwwap_twitter")
          .attr(
            "href",
            rtwwwap_global_params.rtwwwap_twitter_url +
              rtwwwap_generated_url_share
          );
        $(document).find(".rtwwwap_twitter").attr("target", "_blank");

        $(document)
          .find(".rtwwwap_fb_share")
          .attr(
            "href",
            rtwwwap_global_params.rtwwwap_fb_url + rtwwwap_generated_url_share
          );
        $(document).find(".rtwwwap_fb_share").attr("target", "_blank");

        $(document)
          .find(".rtwwwap_whatsapp_share")
          .attr(
            "href",
            rtwwwap_global_params.rtwwwap_whatsapp_url +
              encodeURIComponent(rtwwwap_generated_url_share)
          );
        $(document).find(".rtwwwap_whatsapp_share").attr("target", "_blank");

        $(document)
          .find(".rtwwwap_mail_button")
          .attr(
            "href",
            rtwwwap_global_params.rtwwwap_mail_url + encodeURIComponent(rtwwwap_generated_url_share)
          );

        $(document).find(".rtwwwap_social_share").css("display", "flex");

        // $(document).find( '#rtwwwap_qrcode_main' ).hide();
      } else {
        alert(rtwwwap_global_params.rtwwwap_enter_valid_url);
      }
    });

    $(document).on("click", ".rtwwwap_show_rank", function () {
      $(".rtwwwap_rank_requirement_model").css({ display: "block" });
    });
    $(document).on("click", ".rtwwwap_close_model_icon", function () {
      $(".rtwwwap_rank_requirement_model").css({ display: "none" });
    });

    $(document).on("click", "#rtwwwap_copy_to_clip", function () {
      var $rtwwwap_temp = $("<input>");
      $("body").append($rtwwwap_temp);
      $rtwwwap_temp.val($("#rtwwwap_generated_link").text()).select();
      document.execCommand("copy");
      $rtwwwap_temp.remove();

      $(document)
        .find("#rtwwwap_copy_tooltip_link")
        .css({ visibility: "visible", opacity: 1 });
      setTimeout(function () {
        $(document)
          .find("#rtwwwap_copy_tooltip_link")
          .css({ visibility: "hidden", opacity: 0 });
      }, 2000);
    });

    //generate qr code
    $(document).on("click", "#rtwwwap_generate_qr", function () {
      $(document).find("#rtwwwap_qrcode").html("");
      var rtwwwap_qrcode = new QRCode("rtwwwap_qrcode");
      var rtwwwap_Text = $(document).find("#rtwwwap_generated_link").text();

      rtwwwap_qrcode.makeCode(rtwwwap_Text);

      setTimeout(function () {
        var rtwwwap_link = $(document)
          .find("#rtwwwap_qrcode")
          .find("img")
          .attr("src");
        $(document).find("#rtwwwap_qrcode").attr("href", rtwwwap_link);
        $(document).find("#rtwwwap_download_qr").attr("href", rtwwwap_link);
        $(document).find("#rtwwwap_qrcode_main").show();
      }, 300);
    });

    $(document).on("click", ".rtwwwap_download_qr", function () {
      $(document).find("#rtwwwap_qrcode").trigger("download");
    });

    // generate csv

    $(document).on("click", "#rtwwwap_generate_csv", function () {
      var rtwwwap_cat_id = $(document).find(".rtwwwap_select_cat").val();

      var rtwwwap_data = {
        action: "rtwwwap_generate_csv",
        rtwwwap_cat_id: rtwwwap_cat_id,
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
      };
      if(rtwwwap_cat_id){
        $.blockUI({ message: "" });
        $.ajax({
          url: rtwwwap_global_params.rtwwwap_ajaxurl,
          type: "POST",
          data: rtwwwap_data,
          dataType: 'json',
          success: function (response) {
             
              if(response.status){
                  var url = window.location.href; 
                  url = url.replace(/#/, "?");
                  if (url.indexOf('?') > -1){
                     url += '&affiliate_csv_download='+response.filename;
                  }else{
                     url += '&affiliate_csv_download='+response.filename;
                  }
                  window.location.href = url;
                  // window.location.reload();
              }
              $.unblockUI();
          }
        });
      }
      else{
        alert("Please select the category");
      }

      
    });

    $(document).on("click", "#rtwwwap_rqst_mail", function () {
      var rtwwwap_msg = $(document).find(".rtwwwap_request_msg").val();
      if (rtwwwap_msg != "") {
        if (confirm(rtwwwap_global_params.rtwwwap_rqst_sure)) {
          var data = {
            action: "rtwwwap_send_rqst",
            rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
            rtwwwap_msg: rtwwwap_msg,
          };

          $.blockUI({ message: "" });
          $.ajax({
            url: rtwwwap_global_params.rtwwwap_ajaxurl,
            type: "POST",
            data: data,
            dataType: "json",
            success: function (response) {
              if (response.rtwwwap_status) {
                $(document).find(".rtwwwap_rqst_mail_sent").show();
                $(document).find(".rtwwwap_request_msg").val("");
                setTimeout(function () {
                  $(document).find(".rtwwwap_rqst_mail_sent").hide();
                }, 20000);
              } else {
                alert(response.rtwwwap_message);
              }
              $.unblockUI();
            },
          });
        }
      } else {
        alert(rtwwwap_global_params.rtwwwap_add_rqst_msg);
      }
    });

    $(document).on("click", "#rtwwwap-search-icon", function () {
      var rtwwwap_prod_name = $(document)
        .find("#rtwwwap_banner_prod_search")
        .val();
      var rtwwwap_cat_id = $(document).find(".rtwwwap_search_product").val();

      // console.log(rtwwwap_cat_id);
      // console.log(rtwwwap_prod_name);

      var rtwwwap_data = {
        action: "rtwwwap_search_product_temp3",
        rtwwwap_prod_name: rtwwwap_prod_name,
        rtwwwap_cat_id: rtwwwap_cat_id,
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: rtwwwap_data,
        dataType: "json",
        success: function (response) {
          if (response.rtwwwap_products == "") {
            alert(response.rtwwwap_message);
          } else {
            $(document).find(".rtwwwap-prdct-row").html("");
            $(document)
              .find(".rtwwwap-prdct-row")
              .append(response.rtwwwap_products);
            $(document)
              .find(".rtwwwap-search-prdct-section")
              .css("display", "block");
          }
          $.unblockUI();
        },
      });
    });

    $(document).on("click", "#rtwwwap_banner_link_button", function () {
      var rtwwwap_url = $(this).data("rtwwwap_home_url");

      if (
        rtwwwap_url != "" &&
        rtwwwap_url.startsWith(rtwwwap_global_params.rtwwwap_home_url)
      ) {
        var rtwwwap_aff_id = $(this).data("rtwwwap_aff_id");
        var rtwwwap_aff_name = $(this).data("rtwwwap_aff_name");
        var rtwwwap_affiliate_slug = $(this).data("rtwwwap_slug");

        var rtwwwap_prod_url = $(this).closest("button").data("rtwwwap_url");
        $(document)
          .find("#rtwwwap_banner_link")
          .text(rtwwwap_prod_url)
          .css({ visibility: "visible" });
        var rtwwwap_generated_url = "";

        rtwwwap_generated_url =
          rtwwwap_prod_url +
          "?" +
          rtwwwap_affiliate_slug +
          "=" +
          rtwwwap_aff_name +
          "_" +
          rtwwwap_aff_id;
        $(document)
          .find("#rtwwwap_banner_link")
          .text(rtwwwap_generated_url)
          .css({ visibility: "visible" });
        $(document)
          .find("#rtwwwap_copy_banner_link")
          .css({ visibility: "visible" });
      } else {
        alert(rtwwwap_global_params.rtwwwap_enter_valid_url);
      }
      $("html, body").animate(
        {
          scrollTop: $("#rtwwwap_create_banner_tab").offset().top,
        },
        200
      );
    });

    $(document).on("click", "#rtwwwap_copy_banner_link", function () {
      var $rtwwwap_temp = $("<input>");
      $("body").append($rtwwwap_temp);
      $rtwwwap_temp.val($("#rtwwwap_banner_link").text()).select();
      document.execCommand("copy");
      $rtwwwap_temp.remove();

      $(document)
        .find("#rtwwwap_copy_link_tooltip")
        .css({ visibility: "visible", opacity: 1 });
      setTimeout(function () {
        $(document)
          .find("#rtwwwap_copy_link_tooltip")
          .css({ visibility: "hidden", opacity: 0 });
      }, 2000);
    });

    $(document).on("click", "#rtwwwap_referral_email", function () {
      var rtwwwap_referral_email = $("#rtwwwap_referral_email").prop("checked");

      var rtwwwap_data = {
        action: "rtwwwap_payout_referral_email",
        rtwwwap_referral_email: rtwwwap_referral_email,
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: rtwwwap_data,
        dataType: "json",
        success: function (response) {
          $(".notifyjs-wrapper").remove();
          $.notify(response.rtwwwap_message, {
            className: "notification",
            position: "right bottom",
          });
          $.unblockUI();
        },
      });
    });

    $(document).on("change", ".rtwwwap_payment_method", function () {
      var rtwwwap_payment_method = $(".rtwwwap_payment_method").val();

      var rtwwwap_data = {
        action: "rtwwwap_payment_method",
        rtwwwap_payment_method: rtwwwap_payment_method,
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: rtwwwap_data,
        dataType: "json",
        success: function (response) {
          $(".notifyjs-wrapper").remove();
          $.notify(response.rtwwwap_message, {
            className: "notification",
            position: "right bottom",
          });
          $.unblockUI();
        },
      });
    });

    $(document).on("click", "#rtwwwap_payout_save", function () {
      var rtwwwap_direct_bank = $("#rtwwwap_direct").val();
      var rtwwwap_paypal_id = $("#rtwwwap_paypal_email").val();
      var rtwwwap_stripe_id = $("#rtwwwap_stripe_email").val();
      var rtwwwap_paystack_id = $("#rtwwwap_paystack_email").val();
      var rtwwwap_payment_method = $(".rtwwwap_payment_method").val();
      var rtwwwap_swift_code = $(".rtwwwap_swift_code").val();

      var rtwwwap_data = {
        action: "rtwwwap_payout_save",
        rtwwwap_direct_bank: rtwwwap_direct_bank,
        rtwwwap_paypal_id: rtwwwap_paypal_id,
        rtwwwap_stripe_id: rtwwwap_stripe_id,
        rtwwwap_paystack_id: rtwwwap_paystack_id,
        rtwwwap_payment_method: rtwwwap_payment_method,
        rtwwwap_swift_code: rtwwwap_swift_code,
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: rtwwwap_data,
        dataType: "json",
        success: function (response) {
          $(".notifyjs-wrapper").remove();
          $.notify(response.rtwwwap_message, {
            className: "notification",
            position: "right bottom",
          });
          $.unblockUI();
        },
      });
    });

    $(document).on("click", "#rtwwwap_rqst_mail", function () {
      var rtwwwap_msg = $(document).find(".rtwwwap_request_msg").val();
      if (rtwwwap_msg != "") {
        if (confirm(rtwwwap_global_params.rtwwwap_rqst_sure)) {
          var data = {
            action: "rtwwwap_send_rqst",
            rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
            rtwwwap_msg: rtwwwap_msg,
          };

          $.blockUI({ message: "" });
          $.ajax({
            url: rtwwwap_global_params.rtwwwap_ajaxurl,
            type: "POST",
            data: data,
            dataType: "json",
            success: function (response) {
              if (response.rtwwwap_status) {
                $(document).find(".rtwwwap_rqst_mail_sent").show();
                $(document).find(".rtwwwap_request_msg").val("");
                setTimeout(function () {
                  $(document).find(".rtwwwap_rqst_mail_sent").hide();
                }, 20000);
              } else {
                alert(response.rtwwwap_message);
              }
              $.unblockUI();
            },
          });
        }
      } else {
        alert(rtwwwap_global_params.rtwwwap_add_rqst_msg);
      }
    });

    $(document).on("click", "#rtwwwap_profile_save", function () {
      var rtwwwap_field = $(".rtwwwap_custom_fields");
      if (rtwwwap_field.length > 0) {
        var extra_data = {};

        $.each(rtwwwap_field, function (index, event) {
          extra_data[event.name] = event.value;
        });
      }

      var data = {
        action: "rtwwwap_save_profile",
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
        extra_data: extra_data,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: data,
        dataType: "json",
        success: function (response) {
          $(".notifyjs-wrapper").remove();
          if (response.rtwwwap_status) {
            $.notify(response.rtwwwap_message, {
              className: "notification",
              position: "right bottom",
            });
          } else {
            $.notify(response.rtwwwap_message, {
              className: "notification",
              position: "right bottom",
            });
          }

          $.unblockUI();
        },
      });
    });

    $(document).on("click", ".rtwwwap_date_wise", function () {
      $(".rtwwwap_report_tab").css("display", "none");
      $(".rtwwwap_report_tab_date").css("display", "block");
    });

    $(document).on("click", ".rtwwwap_link_table", function () {
      $(".rtwwwap_report_tab_date").css("display", "none");
      $(".rtwwwap_report_tab").css("display", "block");
    });

    $(document).on("click", ".rtwwwap_theme_toggle", function () {
      if ($(this).prop("checked")) {
        var rtwwwap_theme = "dark";
      } else {
        var rtwwwap_theme = "lite";
      }

      var data = {
        action: "rtwwwap_theme_change",
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
        rtwwwap_theme: rtwwwap_theme,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: data,
        dataType: "json",
        success: function (response) {
          window.location.reload();

          $.unblockUI();
        },
      });
    });

    $(document).on("click", "body", function (e) {
      if (
        !$(e.target).is(
          "#rtwwwap_txtPicker, #rtwwwap_linkPicker, #rtwwwap_bgPicker, .iris-picker, .iris-picker-inner, .iris-palette-container"
        )
      ) {
        if (
          $(document)
            .find("#rtwwwap_txtPicker")
            .siblings(".iris-picker")
            .css("display") == "block" ||
          $(document)
            .find("#rtwwwap_linkPicker")
            .siblings(".iris-picker")
            .css("display") == "block" ||
          $(document)
            .find(" #rtwwwap_bgPicker")
            .siblings(".iris-picker")
            .css("display") == "block"
        ) {
          $("#rtwwwap_txtPicker, #rtwwwap_linkPicker, #rtwwwap_bgPicker").iris(
            "hide"
          );
          return false;
        }
      }
    });

    $(document).on(
      "click",
      "#rtwwwap_txtPicker, #rtwwwap_linkPicker, #rtwwwap_bgPicker",
      function (event) {
        $(this).iris("hide");
        $(this).iris("show");
        return false;
      }
    );

    $(document).on("change", "#rtwwwap_price_check", function () {
      if ($(this).prop("checked")) {
        $(document)
          .find("#rtwwwap_iframe")
          .contents()
          .find("body")
          .find("#rtwwwap_banner_price")
          .show();
      } else {
        $(document)
          .find("#rtwwwap_iframe")
          .contents()
          .find("body")
          .find("#rtwwwap_banner_price")
          .hide();
      }
    });

    $(document).on("change", "#rtwwwap_border_check", function () {
      if ($(this).prop("checked")) {
        $(document)
          .find("#rtwwwap_iframe")
          .contents()
          .find("body")
          .find("#l_b_preview")
          .css("border", "1px solid #000");
      } else {
        $(document)
          .find("#rtwwwap_iframe")
          .contents()
          .find("body")
          .find("#l_b_preview")
          .css("border", "");
      }
    });

    $(document).on("click", "#rtwwwap_create_banner", function () {
      var rtwwwap_template = $(this).data("rtwwwap_template");

      var rtwwwap_prod_id = $(this).closest("p").data("rtwwwap_id");
      var rtwwwap_prod_url = $(this).closest("p").data("rtwwwap_url");
      var rtwwwap_prod_name = $(this).closest("p").data("rtwwwap_title");
      var rtwwwap_prod_img = $(this).closest("p").data("rtwwwap_image");
      var rtwwwap_prod_display_price = $(this)
        .closest("p")
        .data("rtwwwap_displayprice");
      var rtwwwap_affiliate_slug = $(this).closest("p").data("rtwwwap_slug");

      var rtwwwap_text_lang = rtwwwap_global_params.rtwwwap_text_color;
      var rtwwwap_link_lang = rtwwwap_global_params.rtwwwap_link_color;
      var rtwwwap_bg_lang = rtwwwap_global_params.rtwwwap_background_color;
      var rtwwwap_price_lang = rtwwwap_global_params.rtwwwap_show_price;
      var rtwwwap_border_lang = rtwwwap_global_params.rtwwwap_border_color;

      if (rtwwwap_prod_url != "") {
        var rtwwwap_aff_id = $(document)
          .find("#rtwwwap_generate_button")
          .data("rtwwwap_aff_id");
        var rtwwwap_aff_name = $(document)
          .find("#rtwwwap_generate_button")
          .data("rtwwwap_aff_name");

        var rtwwwap_generated_url = "";

        if (rtwwwap_prod_url.indexOf("?") > 0) {
          rtwwwap_generated_url =
            rtwwwap_prod_url +
            "&" +
            rtwwwap_affiliate_slug +
            "=" +
            rtwwwap_aff_name +
            "_" +
            rtwwwap_aff_id;
        } else {
          rtwwwap_generated_url =
            rtwwwap_prod_url +
            "?" +
            rtwwwap_affiliate_slug +
            "=" +
            rtwwwap_aff_name +
            "_" +
            rtwwwap_aff_id;
        }
      }

      var rtwwwap_html = "";

      rtwwwap_html +=
        '<div id="rtwwwap_banner_setting" style="  background: linear-gradient(45deg, teal, transparent);">';
      rtwwwap_html += '<div class="rtwwwap_text_color">';
      rtwwwap_html +=
        '<label for="rtwwwap_txtPicker">' + rtwwwap_text_lang + "</label>";
      rtwwwap_html +=
        '<input type="text" id="rtwwwap_txtPicker" data-type="text_color" class="rtwwwap_text_color_field"/>';
      rtwwwap_html += "</div>";
      rtwwwap_html += '<div class="rtwwwap_link_color">';
      rtwwwap_html +=
        '<label for="rtwwwap_linkPicker">' + rtwwwap_link_lang + "</label>";
      rtwwwap_html +=
        '<input type="text" id="rtwwwap_linkPicker" data-type="link_color" class="rtwwwap_text_color_field" />';
      rtwwwap_html += "</div>";
      rtwwwap_html += '<div class="rtwwwap_bg_color">';
      rtwwwap_html +=
        '<label for="rtwwwap_bgPicker">' + rtwwwap_bg_lang + "</label>";
      rtwwwap_html +=
        '<input type="text" id="rtwwwap_bgPicker" data-type="bg_color" class="rtwwwap_text_color_field" />';
      rtwwwap_html += "</div>";
      rtwwwap_html += '<div class="rtwwwap_price">';
      rtwwwap_html +=
        '<label for="rtwwwap_price_check">' + rtwwwap_price_lang + "</label>";
      rtwwwap_html +=
        '<input type="checkbox" id="rtwwwap_price_check" checked/checked/>';
      rtwwwap_html += "</div>";
      rtwwwap_html += '<div class="rtwwwap_border">';
      rtwwwap_html +=
        '<label for="rtwwwap_border_check">' + rtwwwap_border_lang + "</label>";
      rtwwwap_html += '<input type="checkbox" id="rtwwwap_border_check"/>';
      rtwwwap_html += "</div>";
      rtwwwap_html += "</div>";
      rtwwwap_html += '<div id="rtwwwap_banner_preview">';
      // rtwwwap_html += 	'<p>'+rtwwwap_global_params.rtwwwap_preview+'</p>';

      var rtwwwap_html2 = "";
      rtwwwap_html2 += '<div id="rtwwwap_preview">';
      rtwwwap_html2 +=
        '<div class="l_b_preview rtwwwap_border_show_hide" id="l_b_preview" style="width: 150px; border-radius: 3px;">';
      rtwwwap_html2 +=
        '<div class="rtwwwap_banner" style="padding: 10px; text-align: center;">';
      rtwwwap_html2 += "<div>";
      rtwwwap_html2 +=
        '<a href="' + rtwwwap_generated_url + '" title="" target="_blank">';
      rtwwwap_html2 +=
        '<img src="' +
        rtwwwap_prod_img +
        '" id="rtwwwap_prod_img" alt="" title="" style="border-radius: 3px; height: 175px; max-width: 100%; display: block;">';
      rtwwwap_html2 += "</a>";
      rtwwwap_html2 += "</div>";
      rtwwwap_html2 += '<div id="rtwwwap_banner_link">';
      rtwwwap_html2 +=
        '<a style="text-decoration: none;margin: 7px 0;display: inline-block;" href="' +
        rtwwwap_generated_url +
        '" title="" target="_blank">';
      rtwwwap_html2 += rtwwwap_prod_name;
      rtwwwap_html2 += "</a>";
      rtwwwap_html2 += "</div>";
      rtwwwap_html2 += '<div id="rtwwwap_banner_price">';
      rtwwwap_html2 += rtwwwap_prod_display_price;
      rtwwwap_html2 += "</div>";
      rtwwwap_html2 += "<div>";
      rtwwwap_html2 +=
        '<a style="text-decoration: none;display: inline-block;padding: 5px 13px;background: linear-gradient(to right,#e7e740,#cccc34);margin-top: 10px;font-size: 14px;color: #000000;border-radius: 4px;border: 1px solid #ddd;" href="' +
        rtwwwap_generated_url +
        '" title="" target="_blank" value="' +
        rtwwwap_global_params.rtwwwap_buy_now +
        '">';
      rtwwwap_html2 += rtwwwap_global_params.rtwwwap_buy_now;
      rtwwwap_html2 += "</a>";
      rtwwwap_html2 += "</div>";
      rtwwwap_html2 += "</div>";
      rtwwwap_html2 += "</div>";
      rtwwwap_html2 += "</div>";
      //
      rtwwwap_html +=
        '<iframe id="rtwwwap_iframe" frameborder="0px" src="" style="height:315px;position:relative;right:10px; width:175px;box-shadow: 0px 0px 8px;border-radius: 5px; margin-top: -43%; margin-left: 66%;" >';
      rtwwwap_html += "</iframe>";
      rtwwwap_html +=
        '<div class="rtwwwap_span_copied width-100 rtwwwap_get_script_button" >';
      rtwwwap_html +=
        '<input type="button" id="rtwwwap_get_script" value="' +
        rtwwwap_global_params.rtwwwap_copy_script +
        '" data-prod_id=' +
        rtwwwap_prod_id +
        " />";
      rtwwwap_html += '<span id="rtwwwap_copy_tooltip_script">';
      rtwwwap_html += rtwwwap_global_params.rtwwwap_copied;
      rtwwwap_html += "</span>";
      rtwwwap_html += "</div>";
      rtwwwap_html +=
        '<div class="rtwwwap_span_copied width-100 rtwwwap_get_html_button">';
      rtwwwap_html +=
        '<input type="button" id="rtwwwap_get_html" value="' +
        rtwwwap_global_params.rtwwwap_copy_html +
        '" data-prod_id=' +
        rtwwwap_prod_id +
        " />";
      rtwwwap_html += '<span id="rtwwwap_copy_tooltip_html">';
      rtwwwap_html += rtwwwap_global_params.rtwwwap_copied;
      rtwwwap_html += "</span>";
      rtwwwap_html += "</div>";
      rtwwwap_html += "</div>";

      $(document).find(".rtwwwap-search-prdct-section").html("");
      $(document).find(".rtwwwap-search-prdct-section").append(rtwwwap_html);

      rtwwwap_updateIframe(rtwwwap_html2);
      $(document)
        .find("#rtwwwap_txtPicker")
        .iris({
          defaultColor: true,
          clear: function () {},
          hide: true,
          palettes: true,
          width: 400,
          change: function (event, ui) {
            $(document)
              .find("#rtwwwap_txtPicker")
              .css("background", ui.color.toString());
            $(document)
              .find("#rtwwwap_txtPicker")
              .css("color", ui.color.toString());
            $(document)
              .find("#rtwwwap_iframe")
              .contents()
              .find("#rtwwwap_banner_price")
              .css("color", ui.color.toString());
          },
        });
      $(document).find("#rtwwwap_txtPicker").iris("color", "#222222");

      $(document)
        .find("#rtwwwap_linkPicker")
        .iris({
          defaultColor: true,
          clear: function () {},
          hide: true,
          palettes: true,
          width: 400,
          change: function (event, ui) {
            $(document)
              .find("#rtwwwap_linkPicker")
              .css("background", ui.color.toString());
            $(document)
              .find("#rtwwwap_linkPicker")
              .css("color", ui.color.toString());
            $(document)
              .find("#rtwwwap_iframe")
              .contents()
              .find("#rtwwwap_banner_link > a")
              .css("color", ui.color.toString());
          },
        });
      $(document).find("#rtwwwap_linkPicker").iris("color", "#1a8688");

      $(document)
        .find("#rtwwwap_bgPicker")
        .iris({
          defaultColor: true,
          clear: function () {},
          hide: true,
          palettes: true,
          width: 400,
          change: function (event, ui) {
            $(document)
              .find("#rtwwwap_bgPicker")
              .css("background", ui.color.toString());
            $(document)
              .find("#rtwwwap_bgPicker")
              .css("color", ui.color.toString());
            $(document)
              .find("#rtwwwap_iframe")
              .contents()
              .find("#l_b_preview")
              .css("background", ui.color.toString());
          },
        });
      $(document).find("#rtwwwap_bgPicker").iris("color", "#f6cfcf");
    });

    $(document).on("click", "#rtwwwap_get_script", function () {
      var rtwwwap_prod_id = $(this).data("prod_id");
      var rtwwwap_html = $(document)
        .find("#rtwwwap_iframe")
        .contents()
        .find("body")
        .html();
      var rtwwwap_script_html = "";

      rtwwwap_script_html += "'";
      rtwwwap_script_html += rtwwwap_html;
      rtwwwap_script_html += "'";

      var rtwwwap_script = '<script type="text/javascript">';
      rtwwwap_script += "$(document).ready( function(){";
      rtwwwap_script +=
        'var target = $(document).find( "#rtwwwap_iframe_' +
        rtwwwap_prod_id +
        '" ).contents()[0];';
      rtwwwap_script += "target.open();";
      rtwwwap_script +=
        'target.write( "<!doctype html><html><head></head><body></body></html>" );';
      rtwwwap_script += "target.close();";
      rtwwwap_script +=
        '$(document).find( "#rtwwwap_iframe_' +
        rtwwwap_prod_id +
        '" ).contents().find("body").html( ' +
        rtwwwap_script_html +
        " );";
      rtwwwap_script += "});";
      rtwwwap_script += "</script>";

      var $rtwwwap_temp = $("<input>");
      $("body").append($rtwwwap_temp);
      $rtwwwap_temp.val(rtwwwap_script).select();
      document.execCommand("copy");
      $rtwwwap_temp.remove();

      $(document)
        .find("#rtwwwap_copy_tooltip_script")
        .css({ visibility: "visible", opacity: 1 });
      setTimeout(function () {
        $(document)
          .find("#rtwwwap_copy_tooltip_script")
          .css({ visibility: "hidden", opacity: 0 });
      }, 2000);
    });

    $(document).on("click", "#rtwwwap_get_html", function () {
      var rtwwwap_html = "";
      var rtwwwap_prod_id = $(this).data("prod_id");
      // rtwwwap_html += '<iframe id="rtwwwap_iframe_'+rtwwwap_prod_id+'" frameborder="0" src="" style="height: 256px; width: 170px;">';
      // 	rtwwwap_html += "<!doctype html><html>";
      rtwwwap_html += $("#rtwwwap_iframe").contents().find("body").html();

      // rtwwwap_html += '</html>';

      var $rtwwwap_temp = $("<input>");
      $("body").append($rtwwwap_temp);
      $rtwwwap_temp.val(rtwwwap_html).select();
      document.execCommand("copy");
      $rtwwwap_temp.remove();

      $(document)
        .find("#rtwwwap_copy_tooltip_html")
        .css({ visibility: "visible", opacity: 1 });
      setTimeout(function () {
        $(document)
          .find("#rtwwwap_copy_tooltip_html")
          .css({ visibility: "hidden", opacity: 0 });
      }, 2000);
    });

    function rtwwwap_updateIframe(rtwwwap_html2) {
      var rtwwwap_target = $(document).find("#rtwwwap_iframe").contents()[0];
      rtwwwap_target.open();
      rtwwwap_target.write(
        "<!doctype html><html><head></head><body></body></html>"
      );
      rtwwwap_target.close();
      $(document)
        .find("#rtwwwap_iframe")
        .contents()
        .find("body")
        .html(rtwwwap_html2);
    }

    $(document).on("click", "#rtwwwap_affiliate_activate", function () {
      var rtwwwap_user_id = $(this).data("rtwwwap_num");

      // console.log(rtwwwap_user_id);

      var rtwwwap_data = {
        action: "rtwwwap_become_affiliate",
        rtwwwap_user_id: rtwwwap_user_id,
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
      };

      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: rtwwwap_data,
        dataType: "json",
        success: function (response) {
          if (response.rtwwwap_status) {
            alert(response.rtwwwap_message);
            window.location.reload();
          } 
          else if(response.rtwwwap_redirect == 1){
            alert(response.rtwwwap_message);
          }
          else {
            alert(response.rtwwwap_message);
            window.location.reload();
          }
          $.unblockUI();
        },
      });
    });

    $(document).on("click", ".rtwwwap_request_text_wrapper", function () {
      $(this).next().show();
      $("body").css("overflow", "hidden");
    });

    $(document).on("click", ".rtwwwap_transaction_text_wrapper", function () {
      $(this).next().show();
      $("body").css("overflow", "hidden");
    });
    $(document).on(
      "click",
      ".rtwwwap_close_model_icon, .rtwwwap_cancel_btn_with",
      function () {
        $(".rtwwwap_with_amount").val("");
        $(".rtwwwap_wallet_model").hide();
        $(".rtwwwap_wallet_model_transaction").hide();

        $("body").css("overflow", "scroll");
      }
    );

    $(document).on("click", "#rtwwwap_request_widh", function () {
      var rtwwwap_wallet_amount = $(this).data("wallet_amount");
      var rtwwwap_payment_method = $(this).data("payment_method");
      var rtwwwap_swift_code = $('.rtwwwap_swift_code').data("swift_code");
      var rtwwwap_with_amount = $(".rtwwwap_with_amount").val();
      var rtwwwap_bank_account = $('.rtwwwap_privacy_policy_content').data("bank_account");

      // console.log(rtwwwap_payment_method);

      if (rtwwwap_payment_method == "") {
        alert(rtwwwap_global_params.rtwwwap_error_set_payment_method);
        $(".rtwwwap_with_amount").val("");
        $(".rtwwwap_wallet_model").hide();
        $("body").css("overflow", "scroll");
      } else if (
        rtwwwap_with_amount > rtwwwap_wallet_amount ||
        rtwwwap_with_amount <= 0 ||
        rtwwwap_with_amount == ""
      ) {
        alert(rtwwwap_global_params.rtwwwap_error_with_amount);
        $(".rtwwwap_with_amount").val("");
      } else if (rtwwwap_with_amount <= rtwwwap_wallet_amount) {
        var data = {
          action: "rtwwwap_withdrawal_request",
          rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
          rtwwwap_with_amount: rtwwwap_with_amount,
          rtwwwap_swift_code: rtwwwap_swift_code,
          rtwwwap_payment_method: rtwwwap_payment_method,
          rtwwwap_bank_account: rtwwwap_bank_account
        };

        $.blockUI({ message: "" });
        $.ajax({
          url: rtwwwap_global_params.rtwwwap_ajaxurl,
          type: "POST",
          data: data,
          dataType: "json",
          success: function (response) {
            if (response.rtwwwap_status) {
              alert("Request send Successfully");
              $(".rtwwwap_with_amount").val("");
              window.location.reload();
            } else {
              alert(response.rtwwwap_message);
            }
            $.unblockUI();
          },
        });
      }
    });
    $(document)
      .find("#rtwwwap_coupons_table, #rtwwwap_referrals_table")
      .DataTable({
        pageLength: 5,
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 100, "All"],
        ],
        searching: false,
      });
    $(".rtwwwap_modal_close").on("click", function () {
      $(this).closest(".rtwwwap_reason_modal_").css("display", "none");
    });
    $(".rtwwwap_view_reject").on("click", function () {
      $(this).parent().next(".rtwwwap_reason_modal_").css("display", "block");
    });

    $(document).on("click", ".rtwwwap_login_button", function () {
      var user_id_email = $(".rtwwwap_user_name_email").val();
      var user_pass = $(".rtwwwap_user_password").val();
      var rtwwwap_current_URL = window.location.href;

      if (user_id_email == "" || user_pass == "") {
        $("#login_error")
          .text(rtwwwap_global_params.rtwwwap_login_field_missing_msg)
          .fadeIn(800)
          .delay(500);
      }

      if (user_id_email && user_pass) {
        var email_valid = rtwwwap_email_validation(user_id_email);
        var data = {
          action: "rtwwwap_login_request",
          rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
          user_id_email: user_id_email,
          user_pass: user_pass,
          email_valid: email_valid,
        };

        $.blockUI({ message: "" });
        $.ajax({
          url: rtwwwap_global_params.rtwwwap_ajaxurl,
          type: "POST",
          data: data,
          dataType: "json",
          success: function (response) {
            if (response.rtwwwap_status) {
              window.location.href = response.rtwwwap_redirect;
            } else {
              if (rtwwwap_current_URL == response.rtwwwap_redirect) {
                $("#login_error")
                  .text(response.rtwwwap_message)
                  .fadeIn(800)
                  .delay(500);
              } else {
                window.location.href = response.rtwwwap_redirect;
              }
            }
            $.unblockUI();
          },
        });
      }
    });

    $(document).on("click", ".rtwwwap_register", function () {
      $("#login").css("display", "none");
      var user_name = $(document).find(".rtwwap_reg_name").val();
      var user_email = $(document).find(".rtwwap_reg_email").val();
      var user_phone = $(document).find(".rtwwwap_reg_phone").val();
      var user_pass = $(document).find(".rtwwwap_passsword").val();
      var user_conf_pass = $(document).find(".rtwwwap_confirm_passsword").val();
      var user_referral_code = $(document)
        .find("#rtwwwap_referral_code_field")
        .val();
      var rtwwwap_extra_field = $(document).find(".rtwwwap_extra_field");
      var extra_fields = {};
      var rtwwwap_valid_email;
      var rtwwwap_password = false;

      if (rtwwwap_extra_field != "") {
        $(".rtwwwap_extra_field").each(function () {
          extra_fields[$(this).attr("name")] = $(this).val();
        });
      }

      if (user_pass != "" && user_pass.length > 6) {
        var reg_ex =
          /^(?=.*[0-9])(?=.*[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~\?"])[a-zA-Z0-9!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~\?"]{6,100}/g;
        var password_val = user_pass.match(reg_ex);
        if (password_val != "" && password_val != null) {
          rtwwwap_password = true;
        }
      }
      if (user_email != "") {
        rtwwwap_valid_email = rtwwwap_email_validation(user_email);
      }

      if (
        user_name == "" ||
        user_email == "" ||
        user_phone == "" ||
        user_conf_pass == "" ||
        user_pass == ""
      ) {
        $("#login")
          .text(rtwwwap_global_params.rtwwwap_register_error_msg)
          .fadeIn(800)
          .delay(500);
      } else if (user_pass != user_conf_pass) {
        $("#login")
          .text(rtwwwap_global_params.rtwwwap_confirm_password)
          .fadeIn(800)
          .delay(500);
      } else if (rtwwwap_valid_email == false) {
        $("#login")
          .text(rtwwwap_global_params.rtwwwap_email_validation)
          .fadeIn(800)
          .delay(500);
      } else if (user_pass.length < 6) {
        $("#login")
          .text(rtwwwap_global_params.rtwwwap_password_length)
          .fadeIn(800)
          .delay(500);
      } else if (rtwwwap_password == false) {
        $("#login")
          .text(rtwwwap_global_params.rtwwwap_strong_password_err)
          .fadeIn(800)
          .delay(500);
      } else {
        var data = {
          action: "rtwwwap_register_request",
          rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
          user_name: user_name,
          user_email: user_email,
          user_phone: user_phone,
          user_pass: user_pass,
          user_conf_pass: user_conf_pass,
          user_referral_code: user_referral_code,
          extra_fields: extra_fields,
        };
        $.blockUI({ message: "" });
        $.ajax({
          url: rtwwwap_global_params.rtwwwap_ajaxurl,
          type: "POST",
          data: data,
          dataType: "json",
          success: function (response) {
            $.unblockUI();
            if (response.rtwwwap_status) {
              window.location.href = response.redirect_link;
            } else {
              $("#login").text(response.error).fadeIn(800).delay(500);
            }
          },
        });
      }
    });

    $(document).on("click", ".rtwwwap_toggle_notification", function () {
      $(document)
        .find(".rtwwwap_notification_dropdown_wrapper")
        .fadeIn(300)
        .delay(50);
    });
    $(document).mouseup(function (e) {
      var container = $(".rtwwwap_notification_dropdown_wrapper");
      if (!container.is(e.target) && container.has(e.target).length === 0) {
        $(document).find(".rtwwwap_notification_dropdown_wrapper").fadeOut();
      }
    });

    $(document).on("click", ".rtwwwap_close_cont_modal", function () {
      $(document)
        .find(".rtwwwap_notification_modal_wrapper")
        .css("display", "none");
    });

    $(document).on("click", ".rtwwwap_noti_li_parent", function () {
      var THIS = $(this);
      var rtwwwap_content = $(this).data("content");
      var rtwwwap_title = $(this).data("title");
      var rtwwwap_noti_ID = $(this).data("noti_id");

      $(document)
        .find(".rtwwwap_notification_modal_wrapper")
        .css("display", "block");
      $(document).find(".rtwwwwap_noti_content").text(rtwwwap_content);
      $(document).find(".rtwwwap_modal_title").text(rtwwwap_title);

      var data = {
        action: "rtwwwap_noti_id",
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
        rtwwwap_noti_ID: rtwwwap_noti_ID,
      };
      $.blockUI({ message: "" });
      $.ajax({
        url: rtwwwap_global_params.rtwwwap_ajaxurl,
        type: "POST",
        data: data,
        dataType: "json",
        success: function (response) {
          if (response.rtwwwap_status) {
            if (response.rtwwwap_noti_unseen_count > 0) {
              $(document)
                .find(".rtwwwap_message_count")
                .text(response.rtwwwap_noti_unseen_count);
            } else {
              $(document).find(".rtwwwap_message_count").css("display", "none");
            }
            if (THIS.hasClass("rtwwwap_notification_list")) {
              THIS.removeClass("rtwwwap_notification_list");
              THIS.addClass("rtwwwap_notification_list_present");
            }
          }
          $.unblockUI();
        },
      });
    });

    var chart = new ApexCharts(
      document.querySelector("#rtwwwap_radial_bar_chart"),
      options
    );
    // chart.render();
    var prod_dates =
      rtwwwap_global_params.rtwwwap_dashboard_report_line_chart.dates;
    var prod_commission =
      rtwwwap_global_params.rtwwwap_dashboard_report_line_chart.commission;
    var prod_price =
      rtwwwap_global_params.rtwwwap_dashboard_report_line_chart.product_price;
    var prod_order =
      rtwwwap_global_params.rtwwwap_dashboard_report_line_chart.orders;

    new Chart(document.getElementById("rtwwwap_line_chart_report"), {
      type: "line",
      data: {
        labels: prod_dates,
        datasets: [
          {
            data: prod_commission,
            label: "Commission Earned",
            borderColor: "#3e95cd",
            fill: false,
          },
          {
            data: prod_price,
            label: "Sales Amount",
            borderColor: "#8e5ea2",
            fill: false,
          },
          {
            data: prod_order,
            label: "Orders",
            borderColor: "#3cba9f",
            fill: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: "Report Chart",
        },
        xAxes: [
          {
            ticks: {
              beginAtZero: true,
            },
          },
        ],
      },
    });

    // update password code
    $(document).on('click', '.rtwwwap_profile_change_psw', function(){
      $(".rtwwwap_change_password_model").show();
  });

  $(document).on('click', '.rtwwwap_modal_close', function(){
      $(".rtwwwap_change_password_model").hide();
  });

  $(document).on('click', '.rtwwwap_cancel_btn_with', function(){
      $(".rtwwwap_change_password_model").hide();
  });

  $(document).on('click', '.rtwwwap_save_password', function(){
      var oldPassword = $('.rtwwwap_old_password').val();
      var password = $('.rtwwwap_password').val();
      var confirmPassword = $('.rtwwwap_confirm_password').val();
      var rtwwwap_password = false;

      var data = {
        action: "rtwwwap_verify_old_psw",
        rtwwwap_security_check: rtwwwap_global_params.rtwwwap_nonce,
        oldPassword: oldPassword,
        password: password,
        confirmPassword: confirmPassword
      };

      if(password != "" && password.length > 6)
      {
          var reg_ex = /^(?=.*[0-9])(?=.*[!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~\?"])[a-zA-Z0-9!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~\?"]{6,100}/g;
          var password_val = password.match(reg_ex);
          if(password_val !="" && password_val != null )
          {
              rtwwwap_password = true; 
          }
      }
      if(rtwwwap_password == false){
          alert("Password should be more than 6 characters and have special character");
      }
      else{
          $.ajax({
          url: rtwwwap_global_params.rtwwwap_ajaxurl,
          type: "POST",
          data: data,
          dataType: "json",
          success: function (response) {
              if(response.rtwwwap_status){
                  alert(response.rtwwwap_success_msg);
                    window.location.reload();
                  $(".rtwwwap_change_password_model").hide();
              }
              else{
                  alert(response.rtwwwap_error_msg);
              }
          }
          });
      }
  });

  });
})(jQuery);
function rtwwwap_email_validation(user_email) {
  var rtwwwap_email_regex =
    /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if (!rtwwwap_email_regex.test(user_email)) {
    return false;
  } else {
    return true;
  }
}
