jQuery(document).ready(function(a){"use strict";var b={items:[],current_index:0,btn_process_point:null,init:function(){b.btn_process_point=a("input.process-point"),a("input[name=point-csv]").customFile(),a(".upload-point").on("click",b.upload_point),b.btn_process_point.on("click",b.process_point)},is_blocked:function(a){return a.is(".processing")||a.parents(".processing").length},block:function(a){b.is_blocked(a)||a.addClass("processing disabled").block({message:null,overlayCSS:{background:"#fff",opacity:.6}})},unblock:function(a){a.removeClass("processing disabled").unblock()},add_message:function(c,d){a("p."+c,b.$message).length>0?(d=a("p."+c,b.$message).html()+d,a("p."+c,b.$message).html(d)):b.$message.append('<p class="'+c+'">'+d+"</p>")},make:function(){b.is_blocked()||(b.block(),b.$message.empty().css("display","block"),b.upload_point_init())},upload_point:function(){var c=a("#wpwrap");if(!b.is_blocked(c)){if(0===a("input[name=point-csv]")[0].files.length)return void alert("업로드할 파일을 선택해주세요.");b.block(c);var d=new FormData;d.append("file",a("input[name=point-csv]")[0].files[0]),d.append("action",msps_upload_point.action_upload_point),d.append("_wpnonce",msps_upload_point._wpnonce),a.ajax({url:ajaxurl,type:"POST",data:d,processData:!1,contentType:!1,success:function(b){b&&b.success?(a("div.msps-upload-data").empty().append(b.data),a("input.process-point.button").removeAttr("disabled")):alert(b.data)},complete:function(){b.unblock(c)}})}},process_point:function(){if("disabled"!==b.btn_process_point.attr("disabled")){if(b.btn_process_point.attr("disabled","disabled"),b.items=a("tr.upload-point-item").not(".success"),0===b.items.length)return void alert("처리할 데이터가 없습니다.");b.current_index=0,b.process_point_item()}},process_point_item:function(){var c=b.items[b.current_index];a("td.status",c).html("..."),a(c).removeClass("fail").addClass("processing"),a.ajax({url:ajaxurl,type:"POST",data:{action:msps_upload_point.action_process_point,_wpnonce:msps_upload_point._wpnonce,point_data:a(c).data("point_data")},success:function(d){a(c).removeClass("processing"),d&&d.success?(a("td.status",c).html("성공"),a(c).addClass("success"),b.current_index++,b.current_index<b.items.length?b.process_point_item():b.btn_process_point.removeAttr("disabled")):(a("td.status",c).html("실패"),a(c).addClass("fail"),alert(d.data),b.btn_process_point.removeAttr("disabled"))},complete:function(){}})}};b.init()});