!function(a){var b=a("form.checkout"),c={$forms:[],is_blocked:function(){return b.is(".processing")||b.parents(".processing").length},block:function(){c.is_blocked(b)||(b.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),c.$forms.push(b))},unblock:function(){_.each(c.$forms,function(a){a.removeClass("processing").unblock()}),c.$form=[]}},d=void 0!==a("<input/>")[0].multiple,e=/msie/i.test(navigator.userAgent);a.fn.customFile=function(){return this.each(function(){var b=a(this).addClass("custom-file-upload-hidden"),d=a('<div class="file-upload-wrapper">'),f=a('<input type="text" class="file-upload-input" />'),g=a('<button type="button" class="file-upload-button">파일 선택</button>'),h=a('<label class="file-upload-button" for="'+b[0].id+'">파일 선택</label>');""!==a(this).attr("value")&&f.val(a(this).attr("value")).attr("title",a(this).attr("value")),b.css({position:"absolute",left:"-9999px"}),d.insertAfter(b).append(b,f,e?h:g),b.attr("tabIndex",-1),g.attr("tabIndex",-1),g.click(function(){b.focus().click()}),b.change(function(){if(!c.is_blocked()){c.block();var d=a(this).attr("id"),e=new FormData;e.append("action",_msaddr_upload.slug+"_upload_file"),e.append("_wpnonce",_msaddr_upload._wpnonce),e.append("upload_key",a("input[name="+d+"]").val()),a.each(a(this)[0].files,function(a,b){e.append(d+"#"+a,b)}),a.ajax({url:_msaddr_upload.ajaxurl,processData:!1,contentType:!1,data:e,type:"POST",success:function(b){b&&b.success?a("input[name="+d+"]").val(b.data):alert(b.data),c.unblock()}});var g=b.val().split("\\").pop();f.val(g).attr("title",g).focus()}}),f.on({blur:function(){b.trigger("blur")},keydown:function(a){if(13===a.which)e||b.trigger("click");else{if(8!==a.which&&46!==a.which)return 9===a.which&&void 0;b.replaceWith(b=b.clone(!0)),b.trigger("change"),f.val("")}}})})},d||a(document).on("change","input.customfile",function(){var b=a(this),c="customfile_"+(new Date).getTime(),d=b.parent(),e=d.siblings().find(".file-upload-input").filter(function(){return!this.value}),f=a('<input type="file" id="'+c+'" name="'+b.attr("name")+'"/>');setTimeout(function(){b.val()?e.length||(d.after(f),f.customFile()):(e.parent().remove(),d.appendTo(d.parent()),d.find("input").focus())},1)})}(jQuery);