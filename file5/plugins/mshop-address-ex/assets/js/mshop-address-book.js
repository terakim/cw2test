jQuery(function(a){var b=function(a){return a.is(".processing")||a.parents(".processing").length},c=function(a){b(a)||a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}})},d=function(a){a.removeClass("processing").unblock()},e={fields:_msaddr.fields,$address_books:a("table.address-book"),$editor_wrapper:a("div.shipping_address_edit_wrapper"),init:function(){a("input[name=address_item]",this.$address_books).on("click",this.select_address_item),a("input[name=address_item] ~ label",this.$address_books).on("click",this.select_address_label),a("img.edit-address",this.$address_books).on("click",this.edit_address_item),a("img.delete-address",this.$address_books).on("click",this.delete_address_item),a("input[name=address_item]:first",this.$address_books).trigger("click"),a("tr",this.$address_books).on("click",this.select_address_item_line),a(document.body).on("init_checkout",function(){a("input[name=address_item]:first",e.$address_books).trigger("click")})},edit_address_item:function(b){b.stopPropagation();var c=a(this).closest("tr");a("tr.new input[name=address_item]",this.$address_books).attr("checked","checked"),e.update_address(c.data("address")),e.$editor_wrapper.css("display","block")},delete_address_item:function(){if(confirm(_msaddr.i18n.confirm_delete_address)){var b=this,e=a(this).closest("form"),f=a(this).closest("tr");c(e),a.ajax({type:"post",dataType:"json",url:_msaddr.ajaxurl,data:{action:_msaddr.slug+"_delete_address_item",key:f.data("key"),_ajax_nonce:_msaddr._ajax_nonce},success:function(c){c.success&&f.remove(),a("tr:first-child input[name=address_item]",b.$address_books).trigger("click"),d(e)}})}},select_address_label:function(){var b=a(this).closest("tr");a("input[name=address_item]",b).trigger("click")},select_address_item_line:function(b){b.stopPropagation(),a("input[name=address_item]",a(this)).trigger("click")},select_address_item:function(b){b.stopPropagation();var c=a(this).closest("tr");c.hasClass("history")?(e.update_address(c.data("address")),e.$editor_wrapper.css("display","none")):(a("input[name=address_item]").length>1&&e.update_address(_msaddr.defaults),e.$editor_wrapper.css("display","block"))},update_address:function(b){a.each(e.fields,function(c,d){var f=a("[name="+d+"]",e.$editor_wrapper);if(f.val(b[d]),f.hasClass("combination-field")){var g=f.data("fields").split(","),h=f.data("delimeter"),i=b[d]?b[d].split(h):[];a.each(g,function(b,c){a("[name="+d+"-"+c+"]",e.$editor_wrapper).val(i[b])})}}),a("select[name=shipping_country]",e.$editor_wrapper).trigger("change")}};e.init()});