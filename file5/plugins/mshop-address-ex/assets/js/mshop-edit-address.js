jQuery(document).ready(function(a){"use strict";function b(){for(var a=["iPad Simulator","iPhone Simulator","iPod Simulator","iPad","iPhone","iPod"];a.length;)if(navigator.platform===a.pop())return!0;return!1}var c=b();window.edit_address_popup=function(){var b;this.form=null,this.order_id=0,this.init=function(d,e){this.order_id=d,this.edit_address=e,this.edit_button=a(".button.edit_address_"+d),this.form=a("form.edit_address_popup.popup_"+d),this.popup=this.form.closest(".msaddr_edit_address_popup"),this.requiredFields=a("p.validate-required",this.form),this.edit_button.on("click",function(){c&&(b=a("body").scrollTop()),this.popup.removeClass("hide"),c&&a("html,body").attr({style:"height: 100% !important; -webkit-overflow-scrolling : touch !important; overflow: auto !important;"})}.bind(this)),a(".msaddr_close",this.popup).on("click",function(){c&&(a("html,body").attr({style:""}),a("body").scrollTop(b)),this.popup.addClass("hide")}.bind(this)),a(".msaddr_update",this.popup).on("click",function(){this.update_address()}.bind(this)),a(".country_to_state.country_select").on("change",function(){a(document).trigger("country_to_state_changing",[a(this).val()])}),a(document).on("country_to_state_changing",function(a,b,c){this.update_address_fields(b)}.bind(this)),this.update_address_fields("KR"),this.init_address_search()},this.update_address_fields=function(b){"KR"===b?(this.form.find("p.mshop-enable-kr").css("display","block"),this.form.find("div.mshop-enable-kr").css("display","block"),this.form.find("p:not(.mshop-enable-kr)").css("display","none"),this.form.find("p:not(.mshop-enable-kr)").filter(function(b,c){return-1!==a.inArray(a(c).attr("id"),["billing_country_chosen","billing_country_field","shipping_country_chosen","shipping_country_field","ship-to-different-address","order_comments_field"])}).css("display","block"),this.form.find("p input[type=submit]").parent().hasClass("form-row")||this.form.find("p input[type=submit]").parent().css("display","block"),this.requiredFields.filter(function(b,c){return!a(c).hasClass("mshop-enable-kr")}).removeClass("validate-required"),this.requiredFields.filter(function(b,c){return a(c).hasClass("mshop-enable-kr")}).addClass("validate-required")):(this.form.find("p.mshop-enable-kr").css("display","none"),this.form.find("div.mshop-enable-kr").css("display","none"),this.form.find("p:not(.mshop-enable-kr)").css("display","block"),this.form.find("p.mshop-always-kr").css("display","block"),this.form.find("p:not(.mshop-enable-kr)").removeClass("validate-required"),this.requiredFields.filter(function(b,c){return!a(c).hasClass("mshop-enable-kr")}).addClass("validate-required"),this.requiredFields.filter(function(b,c){return a(c).hasClass("mshop-enable-kr")}).removeClass("validate-required")),this.form.find("p.form-row.create-account").css("display","none"),this.form.find("div.create-account p").css("display","none")},this.init_address_search=function(){a(".ms_addr_1").magnificPopup({type:"inline",midClick:!0,closeOnBgClick:!1,closeBtnInside:!1,showCloseBtn:!1,fixedContentPos:"true",callbacks:{open:function(){this.content.find(".search_result_postnum").val(),this.content.find(".search_result_addr").val()},close:function(){""!==this.content.find(".search_result_postnum").val()&&(this.currItem.el.closest("div").find(".postnum").val(this.content.find(".search_result_postnum").val()),this.currItem.el.closest("div").find(".addr1").val(this.content.find(".search_result_addr").val()),this.currItem.el.closest("div").find(".addr2").val(""))}}})},this.update_address=function(){a(".edit_address_popup_wrapper").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),a.ajax({dataType:"json",url:_msaddr_edit_address.ajaxurl,data:{action:_msaddr_edit_address.action,order_id:this.order_id,edit_address:this.edit_address,params:a(this.form).serialize(),_ajax_nonce:_msaddr_edit_address._ajax_nonce},async:!0,type:"POST",success:function(b){b&&b.success?window.location.reload():(alert(b.data),a(".edit_address_popup_wrapper").unblock())}.bind(this),error:function(b,c,d){alert(d),a(".edit_address_popup_wrapper").unblock()}.bind(this)})}},a(document).trigger("edit_address_popup_init")});