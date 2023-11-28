jQuery(document).ready(function(a){"use strict";var b={$dialog:null,$progressBar:null,$progressLabel:null,$templateSelector:null,totalCount:0,processedCount:0,postsPerPage:0,queryString:"",templateId:0,ajaxRequest:null,init:function(){this.$dialog=a("#msex_download"),this.$progressBar=a("#progressbar",this.$wrapper),this.$progressLabel=a(".progress-label",this.$dialog),this.$templateSelector=a(".template_selector",this.$dialog),this.$progressWrapper=a(".progress_wrapper",this.$dialog),this.initElement(),this.bindEvent()},initElement:function(){this.$dialog.dialog({autoOpen:!1,closeOnEscape:!1,resizable:!1,dialogClass:"no-close",open:function(){b.processedCount=0,b.totalCount=0,b.templateId=0,b.postsPerPage=0,b.$progressBar.progressbar("value",!1),b.$progressLabel.text("템플릿을 선택하신 후, 다운로드 버튼을 클릭해주세요."),b.$progressWrapper.css("display","none"),b.$templateSelector.css("display","flex"),b.$dialog.dialog("option","buttons",[{text:"다운로드 취소",click:b.closeDialog}])}}),this.$progressBar.progressbar({value:!1,change:function(){b.$progressLabel.text("처리 진행도 : "+b.processedCount+"/"+b.totalCount+"건")},complete:function(){b.$progressLabel.text("다운로드가 완료되었습니다."),b.$dialog.dialog("option","buttons",[{text:"닫기",click:b.closeDialog}])}})},bindEvent:function(){a(".bulkactions input:submit").on("click",b.showDialog),a("input.start-download",b.$templateSelector).on("click",b.startDownload)},showDialog:function(c){var d=a(c.target).parent().children("select");if(_msex.bulk_action===d.val())return c.stopPropagation(),c.preventDefault(),c.stopImmediatePropagation(),b.$dialog.dialog("open"),!1},closeDialog:function(){b.ajaxRequest&&(b.ajaxRequest.abort(),b.ajaxRequest=null),b.$dialog.dialog("close")},updateProgress:function(){var a=(100/b.totalCount*b.processedCount).toFixed(2);b.$progressBar.progressbar("value",Number(a))},startDownload:function(){b.$progressLabel.text("다운로드를 시작합니다."),b.$progressWrapper.css("display","block"),b.$templateSelector.css("display","none"),b.queryString=window.location.search.substring(1);var c=a("select[name=msex_template]",b.$templateSelector).val();b.templateId=c,b.postsPerPage=_msex.params[c].posts_per_page;var d=_.map(a("th.check-column input[type=checkbox]:checked"),function(b){return a(b).val()});d.length>0&&(b.postsPerPage=d.length),b.ajaxRequest=a.ajax({type:"POST",dataType:"json",url:_msex.ajaxurl,async:!0,data:{action:_msex.slug+"_get_total_count",params:b.queryString,template_id:b.templateId,ids:d},success:function(a){a.success&&(b.totalCount=a.data.total_count,b.totalPage=Math.floor((b.totalCount-1)/b.postsPerPage+1),b.processData(1,d))}})},processData:function(a,c){b.processDataFragment(a,c).then(function(){a!==b.totalPage&&b.totalCount>b.processedCount?b.processData(a+1,c):b.downloadFile()})},processDataFragment:function(c,d){var e=a.Deferred();return b.ajaxRequest=a.ajax({type:"POST",dataType:"json",url:_msex.ajaxurl,async:!0,data:{action:_msex.slug+"_generate_page_data",params:b.queryString,paged:c,posts_per_page:b.postsPerPage,total_page:b.totalPage,template_id:b.templateId,ids:d},success:function(a){b.$dialog.dialog("isOpen")&&(b.processedCount+=parseInt(a.data.length),b.updateProgress(),e.resolve())}}),e.promise()},downloadFile:function(){a.ajax({type:"POST",dataType:"json",url:_msex.ajaxurl,async:!0,data:{action:_msex.slug+"_download_file",params:b.queryString,template_id:b.templateId},success:function(a){var b=a.data.download_url,c=b.indexOf("?")<0?"?":"&";window.location=b+c+"_t="+(new Date).getTime()},error:function(a,b){var c="";c=404==a.status?"Requested page not found. [404]":"parsererror"===b?"Requested JSON parse failed.":"timeout"===b?"Time out error.":"Uncaught Error. \n"+a.responseText,console.log(c)}})}};b.init()});