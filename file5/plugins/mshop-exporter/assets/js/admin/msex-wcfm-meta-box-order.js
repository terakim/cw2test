jQuery(document).ready(function(a){"use strict";var b={init:function(){b.$wrapper=a("div#msex-sheet-info"),a(".msex_action_button.delete",b.$wrapper).on("click",b.delete_sheet_info),a(".msex_action_button.update",b.$wrapper).on("click",b.update_sheet_info),a(".msex-show-edit-sheet").on("click",b.toggle_edit_sheet),a(".msex-edit-sheet-info input.delete-sheet").on("click",this.delete_sheet_by_order_item),a(".msex-edit-sheet-info input.update-sheet").on("click",this.update_sheet_by_order_item),a(".msex-edit-sheet-info input.view-sheet").on("click",this.view_sheet_by_order_item)},is_blocked:function(a){return _.isUndefined(a)&&(a=b.$wrapper),a.is(".processing")||a.parents(".processing").length},block:function(a){_.isUndefined(a)&&(a=b.$wrapper),b.is_blocked()||(a.addClass("processing disabled"),a.css("background","#fff"),a.css("opacity","0.6"))},unblock:function(a){_.isUndefined(a)&&(a=b.$wrapper),a.removeClass("processing disabled").unblock()},toggle_edit_sheet:function(){a(this).closest("div.msex-sheet-info").find("div.msex-edit-sheet-info-wrapper").toggleClass("show-edit-sheet")},delete_sheet_by_order_item:function(){var c=a(this).closest("div.msex-sheet-info");c.data("item_id");!b.is_blocked(c)&&confirm("송장정보를 삭제하시겠습니까?")&&(b.block(c),a.ajax({url:_msex_meta_box_order.ajax_url,type:"POST",data:{action:_msex_meta_box_order.slug+"_wcfm_delete_sheet_by_order_item",_wpnonce:_msex_meta_box_order._wpnonce,item_id:c.data("item_id")},success:function(a){a&&a.success?window.location.reload():(alert(a.data),b.unblock(c))},complete:function(){}}))},update_sheet_by_order_item:function(){var c=a(this).closest("div.msex-sheet-info"),d=c.data("item_id"),e=c.find("select[name='dlv_code["+d+"]']").val(),f=c.find("input[name='sheet_no["+d+"]']").val();return _.isEmpty(e.trim())?void alert("택배사를 선택해주세요."):_.isEmpty(f.trim())?void alert("송장번호를 입력해주세요."):void(!b.is_blocked(c)&&confirm("송장정보를 등록하시겠습니까?")&&(b.block(c),a.ajax({url:_msex_meta_box_order.ajax_url,type:"POST",data:{action:_msex_meta_box_order.slug+"_wcfm_update_sheet_by_order_item",_wpnonce:_msex_meta_box_order._wpnonce,item_id:d,dlv_code:e,sheet_no:f},success:function(a){a&&a.success?window.location.reload():(alert(a.data),b.unblock(c))},complete:function(){}})))},view_sheet_by_order_item:function(){window.open(a(this).data("url"),"_blank")},delete_sheet_info:function(){!b.is_blocked()&&confirm("송장정보를 삭제하시겠습니까?")&&(b.block(),a.ajax({url:_msex_meta_box_order.ajax_url,type:"POST",data:{action:_msex_meta_box_order.action_delete,_wpnonce:_msex_meta_box_order._wpnonce,order_id:_msex_meta_box_order.order_id},success:function(a){a&&a.success?window.location.reload():(alert(a.data),b.unblock())},complete:function(){}}))},update_sheet_info:function(){if(!b.is_blocked()&&confirm("송장정보를 업데이트 하시겠습니까?")){var c=a("select[name=msex_dlv_code]").val(),d=a("input[name=msex_sheet_no]").val();if(""===c)return void alert("택배사를 선택하세요.");if(""===c)return void alert("송장번호를 입력해주세요.");b.block(),a.ajax({url:_msex_meta_box_order.ajax_url,type:"POST",data:{action:_msex_meta_box_order.action_update,_wpnonce:_msex_meta_box_order._wpnonce,order_id:_msex_meta_box_order.order_id,dlv_code:c,sheet_no:d},success:function(a){a&&a.success?window.location.reload():(alert(a.data),b.unblock())},complete:function(){}})}}};b.init()});