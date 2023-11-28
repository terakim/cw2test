jQuery(function(a){"use strict";var b={$forms:[],$wrapper:a(".pafw-checkout-wrap"),is_blocked:function(a){return this.$wrapper.length>0&&(a=this.$wrapper),a.is(".processing")||a.parents(".processing").length},block:function(c){this.$wrapper.length>0&&(c=this.$wrapper),b.is_blocked(c)||(c.addClass("processing").block({message:a('<div class="ajax-loader"/>'),overlayCSS:{background:"#fff",opacity:.6}}),b.$forms.push(c))},unblock:function(){_.each(b.$forms,function(a,b){a.removeClass("processing").unblock()}),b.$forms=[]}};const c=function(a){void 0===a.data("pafwBACS")&&(this.$element=a,this._init(),a.data("pafwBACS",this))};c.prototype._init=function(){this._bindEventHandler()},c.prototype._bindEventHandler=function(){a("input[name=pafw_change_bacs_receipt_info]",this.$element).on("click",this._changeReceiptInfo.bind(this)),a("input[name=pafw_bacs_receipt_issue]",this.$element).on("click",this._refresh.bind(this)),a("input[name=pafw_bacs_receipt_usage]",this.$element).on("click",this._refresh.bind(this)),a("select[name=pafw_bacs_receipt_issue_type]",this.$element).on("change",this._refresh.bind(this)),this._refresh()},c.prototype._changeReceiptInfo=function(){a(".pafw_bacs_default_info",this.$element).css("display","none"),a("input[name=pafw_bacs_receipt_use_default]",this.$element).val("no"),this._refresh()},c.prototype._refresh=function(){if("yes"!==a("input[name=pafw_bacs_receipt_use_default]",this.$element).val()){a(".pafw_bacs_receipt",this.$element).css("display","block");const b=a("input[name=pafw_bacs_receipt_issue]:checked",this.$element).val();a("div.receipt_issue",this.$element).css("display","none"),a("div.receipt_issue.receipt_issue_"+b,this.$element).css("display","block");const c=a("input[name=pafw_bacs_receipt_usage]:checked",this.$element).val();a("div.receipt_usage",this.$element).css("display","none"),a("div.receipt_usage.receipt_usage_"+c,this.$element).css("display","flex")}},a.fn.pafw_hook={hooks:[],add_filter:function(b,c){void 0===a.fn.pafw_hook.hooks[b]&&(a.fn.pafw_hook.hooks[b]=[]),a.fn.pafw_hook.hooks[b].push(c)},apply_filters:function(b,c){if(void 0!==a.fn.pafw_hook.hooks[b])for(var d=0;d<a.fn.pafw_hook.hooks[b].length;++d)c[0]=a.fn.pafw_hook.hooks[b][d](c);return c[0]}},a.fn.pafw=function(b){if("object"==typeof(b=b||{}))return this.each(function(){var c=a.extend({paymentMethods:_pafw.gateway,isOrderPay:_pafw.is_checkout_pay_page,isSimplePay:!1,ajaxUrl:_pafw.ajax_url,slug:_pafw.slug,gatewayDomain:_pafw.gateway_domain,forms:a(".pafw-checkout-block form")},b);new d(a(this),c)}),this;throw new Error("잘못된 호출입니다.: "+b)};var d=function(a,b){void 0===a.data("pafw")&&(this.$element=a,this.$paymentForm=b.forms,this.options=b||{},this.uuid=this._generateUUID(),this._registerHandler(),a.data("pafw",this))};d.prototype._ajaxUrl=function(){var a="";if("yes"===this.options.isSimplePay){var b=this.options.ajaxUrl.split("?");a=b.length>1?b[0]+"?action="+this.options.slug+"-pafw_simple_payment&"+b[1]:this.options.ajaxUrl+"?action="+this.options.slug+"-pafw_simple_payment"}else a=_pafw.wc_checkout_url;return a},d.prototype._generateUUID=function(){var a=(new Date).getTime();return"pafw_"+"xxxxxxxxxxxxxxxxxxxx".replace(/[x]/g,function(b){var c=(a+16*Math.random())%16|0;return a=Math.floor(a/16),("x"===b?c:3&c|8).toString(16)})},d.prototype._paymentComplete=function(a){window.location.href=a},d.prototype._paymentCancel=function(){a("#"+this.uuid).remove(),b.unblock()},d.prototype._paymentFail=function(c){var d=this;setTimeout(function(){alert(c),a("#"+d.uuid).remove(),b.unblock()})},d.prototype._registerHandler=function(){this.$element.on("click",this.processPayment.bind(this)),a(".pafw_bacs_receipt_wrapper",a(".pafw-checkout-block.pafw-payment-methods-block")).length>0&&_.each(a(".pafw_bacs_receipt_wrapper",a(".pafw-checkout-block.pafw-payment-methods-block")),function(b){new c(a(b))})},d.prototype._processPostMessage=function(b){b.origin===this.options.gatewayDomain&&("pafw_cancel_payment"===b.data.action||"pafw_payment_fail"===b.data.action?a.fn.payment_fail(b.data.message):"pafw_payment_complete"===b.data.action&&a.fn.payment_complete(b.data.redirect_url))},d.prototype._registerPaymentCallback=function(){a.fn.payment_complete=this._paymentComplete.bind(this),a.fn.payment_cancel=this._paymentCancel.bind(this),a.fn.payment_fail=this._paymentFail.bind(this),_.isUndefined(a.fn.pafwPostMessageHandler)||window.removeEventListener("message",a.fn.pafwPostMessageHandler,!0),a.fn.pafwPostMessageHandler=this._processPostMessage.bind(this),window.addEventListener("message",a.fn.pafwPostMessageHandler,!0)},d.prototype.openPaymentWindow=function(b,c){if(_.isUndefined(c.redirect_url)||_.isEmpty(c.redirect_url))if(_.isUndefined(c.redirect)||_.isEmpty(c.redirect)){document.getElementById(b)||a(document.body).append('<div id="'+b+'" style="width: 100%;height: 100%;position: fixed;top: 0;z-index: 99999;"></div>');var d=a("#"+b);d.empty().append(c.payment_form)}else window.location.href=c.redirect;else window.location.href=c.redirect_url},d.prototype.processPayment=function(){var c=this.uuid,d=this;if(this.$paymentForm=a(".pafw-checkout-block form:not(.no-submit)"),!1!==a("form.pafw-checkout.pafw-payment-methods").triggerHandler("pafw_process_payment_"+a('[name="payment_method"]').val(),this)){if(b.is_blocked(this.$paymentForm))return!1;b.block(a(".pafw-checkout-block form:not(.no-submit), .pafw-need-block")),this._registerPaymentCallback(),a.ajax({type:"POST",url:this._ajaxUrl(),data:this.$paymentForm.serialize(),success:function(e){var f="";try{if(e.indexOf("\x3c!--WC_START--\x3e")>=0&&(e=e.split("\x3c!--WC_START--\x3e")[1]),e.indexOf("\x3c!--WC_END--\x3e")>=0&&(e=e.split("\x3c!--WC_END--\x3e")[0]),f=a.parseJSON(e),"success"!==f.result)throw"Invalid response";d.openPaymentWindow(c,f)}catch(c){if(!0===f.reload||"true"===f.reload)return void window.location.reload();"true"===f.refresh&&a("body").trigger("update_checkout"),d.submitError(f.messages),"failure"===f.result&&f.messages&&a.fn.pafw_alert(f.messages,!1),b.unblock()}},dataType:"html"})}return!1},d.prototype.submitError=function(b){this.$paymentForm.removeClass("processing").unblock(),this.$paymentForm.find(".input-text, select, input:checkbox").trigger("validate").blur(),a(document.body).trigger("checkout_error",[b])},d.prototype.processOrderPay=function(){var c=this.uuid,d=this,e=a("input[name=payment_method]:checked",this.$paymentForm).val();return-1===_.flatten(_.values(this.options.paymentMethods)).indexOf(e)||!b.is_blocked(this.$paymentForm)&&(b.block(this.$paymentForm),this._registerPaymentCallback(),a.ajax({type:"POST",url:_pafw.ajax_url,data:{action:_pafw.slug+"-pafw_ajax_action",payment_method:e,payment_action:"process_order_pay",order_id:_pafw.order_id,order_key:_pafw.order_key,data:this.$paymentForm.serialize(),_wpnonce:_pafw._wpnonce},success:function(a){void 0!==a&&void 0!==a.success&&!0===a.success?d.openPaymentWindow(c,a.data):alert(a.data),b.unblock()}}),!1)},d.prototype.destroy=function(){},a("body").trigger("pafw_init_hook"),_.each(a("input.pafw-payment"),function(b,c){a(b).pafw()}),a(document).bind("fragment_updated",function(b,c){_.intersection(c,["payment-methods"]).length>0&&(_.each(a("input.pafw-payment"),function(b,c){a(b).pafw()}),a(".pafw-card-field-wrap").each(function(b,c){a(c).CardJs()}))})});