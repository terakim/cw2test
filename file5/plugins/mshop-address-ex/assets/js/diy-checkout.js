jQuery(document).ready(function(a){var b={$wrapper:a("body"),is_blocked:function(a){return a=_.isUndefined(a)?b.$wrapper:a,a.is(".processing")},block:function(a){a=_.isUndefined(a)?b.$wrapper:a,b.is_blocked()||a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}})},unblock:function(a){a=_.isUndefined(a)?b.$wrapper:a,a.removeClass("processing").unblock()}},c={$tabs:null,$newShippingTab:null,countrySelector:"",xhr:null,init:function(){a(document).on("fragment_updated",function(a,b){-1!==b.indexOf(_msaddr_diy_checkout.address_type+"-fields")&&this.bindEvent()}.bind(this)),this.countrySelector="[name="+_msaddr_diy_checkout.address_type+"_country]",this.refreshButtons(1,_msaddr_diy_checkout.last_page),this.bindEvent()},bindEvent:function(){this.$tabs=a("#pafw-dc-address"),this.$defaultShippingTab=a("#default-shipping"),this.$newShippingTab=a("#new-destination"),this.$destinationsTab=a("#destinations"),this.defaultKey=this.$tabs.data("default_key"),this.defaultDestination=this.$tabs.data("default_destination"),this.$destinationWrap=a(".destinations-wrap",this.$destinationsTab),this.$destinationNav=a(".destinations-nav",this.$destinationsTab),this.$searchKeyword=a("input[name=msaddr-search-key]",this.$destinationsTab),a("li.new-destination-tab").on("click",this.resetDestination.bind(this)),this.$destinationKey=a("input[name=msaddr_shipping_destination_key]",this.$newShippingTab),_.isUndefined(this.defaultKey)||""===this.defaultKey?(this.$tabs.tabs({active:1,disabled:[0,2]}),this.resetDestination(),a("body").trigger("update_checkout")):(this.$tabs.tabs({active:0}),c.setAddress(this.defaultKey,this.defaultDestination.address),a(this.countrySelector,c.$newShippingTab).trigger("change"),a("li.default-destination-tab",this.$tabs).on("click",this.setDefaultDestination.bind(this)),a("li.destinations-tab",this.$tabs).on("click",function(){a(".history:first-child input[name=address_item]").trigger("click")})),a(".msaddr-page",this.$destinationNav).on("click",this.loadDestinations),this.$searchKeyword.on("keyup",this.searchDestinations),this.bindHistoryItemEvent()},bindHistoryItemEvent:function(){a(".edit-default-shipping",this.$defaultShippingTab).on("click",this.editDefaultDestination.bind(this)),a(".edit-destination",this.$destinationWrap).on("click",this.editDestination.bind(this)),a(".delete-destination",this.$destinationWrap).on("click",this.deleteDestination),a(".history input[name=address_item]",this.$destinationWrap).on("click",this.setDestination.bind(this)),a(".history",this.$tabs).on("click",function(b){b.stopPropagation(),b.preventDefault(),a("input[name=address_item]",a(this)).trigger("click")}),a(".pafw-icon.set-default",this.$tabs).on("click",this.makeDefaultDestination),a("button.button-primary",this.$destinationNav).on("click",this.loadDestinations)},setDefaultDestination:function(){c.setAddress(this.defaultKey,this.defaultDestination.address),a(this.countrySelector,c.$newShippingTab).trigger("change")},setDestination:function(b){b.stopPropagation(),b.preventDefault();var d=a(b.currentTarget).closest(".history").data("key"),e=a(b.currentTarget).closest(".history").data("address");c.setAddress(d,e),a(this.countrySelector,c.$newShippingTab).trigger("change"),a(".history").removeClass("selected"),a(b.currentTarget).closest(".history").addClass("selected")},setAddress:function(b,d){c.$destinationKey.val()!==(void 0!==b?b:"new")&&(void 0===d&&(d=[]),a.each(_msaddr_diy_checkout.fields,function(b,e){var f=a("[name="+e+"]",c.$newShippingTab);if(f.val(d[e]),f.hasClass("combination-field")){var g=f.data("fields").split(","),h=f.data("delimeter"),i=d[e]?d[e].split(h):[];a.each(g,function(b,d){a("[name="+e+"-"+d+"]",c.$newShippingTab).val(i[b])})}}),c.$destinationKey.val(void 0!==b?b:"new"),a("body").trigger("update_checkout"))},editDefaultDestination:function(){c.setAddress(this.defaultKey,this.defaultDestination.address),a(this.countrySelector,c.$newShippingTab).trigger("change"),c.$tabs.tabs({active:1})},editDestination:function(){var b=a(this).closest("tr").data("key"),d=a(this).closest("tr").data("address");c.setAddress(b,d),a(this.countrySelector,c.$newShippingTab).trigger("change"),c.$tabs.tabs({active:1})},resetDestination:function(){c.setAddress(),a(this.countrySelector,c.$newShippingTab).val("KR").trigger("change")},makeDefaultDestination:function(c){c.stopPropagation(),c.preventDefault(),b.block(),a.ajax({type:"POST",url:_msaddr_diy_checkout.ajax_url,data:{action:_msaddr_diy_checkout.slug+"_set_default_address",address_type:_msaddr_diy_checkout.address_type,key:a(this).closest(".history").data("key"),_wpnonce:_msaddr_diy_checkout._wpnonce},success:function(d){a(".set-default").removeClass("default"),a(c.currentTarget).addClass("default"),a("input[name=address_item]",a(c.currentTarget).closest(".history")).trigger("click"),b.unblock()}})},deleteDestination:function(c){confirm("선택하신 배송지를 삭제하시겠습니까?")&&(c.stopPropagation(),c.preventDefault(),b.block(),a.ajax({type:"POST",url:_msaddr_diy_checkout.ajax_url,data:{action:_msaddr_diy_checkout.slug+"_delete_destination",key:a(this).closest(".history").data("key"),_wpnonce:_msaddr_diy_checkout._wpnonce},success:function(a){window.location.reload(),b.unblock()}}))},loadDestinations:function(d){d.stopPropagation(),d.preventDefault(),b.block(),a.ajax({type:"POST",url:_msaddr_diy_checkout.ajax_url,data:{action:_msaddr_diy_checkout.slug+"_load_destinations",page:a(this).data("page"),keyword:c.$searchKeyword.val(),address_type:_msaddr_diy_checkout.address_type,template:a(this).closest(".pafw-checkout-block").data("template"),_wpnonce:_msaddr_diy_checkout._wpnonce},success:function(a){c.$destinationWrap.html(a.data.fragment),c.refreshButtons(a.data.page,a.data.total),c.bindHistoryItemEvent(),b.unblock()}})},searchDestinations:function(){this.xhr&&this.xhr.abort(),b.is_blocked(c.$destinationWrap)||b.block(c.$destinationWrap),this.xhr=a.ajax({type:"POST",url:_msaddr_diy_checkout.ajax_url,data:{action:_msaddr_diy_checkout.slug+"_search_destinations",page:1,keyword:c.$searchKeyword.val(),address_type:_msaddr_diy_checkout.address_type,template:a(this).closest(".pafw-checkout-block").data("template"),_wpnonce:_msaddr_diy_checkout._wpnonce},success:function(a){c.$destinationWrap.html(a.data.fragment),c.refreshButtons(a.data.page,a.data.total),c.bindHistoryItemEvent(),b.unblock()}})},refreshButtons:function(b,c){if(a(".destinations-nav","#destinations").html(""),1<c){var d=a(".destinations-nav","#destinations"),e="",f=5,g=parseInt((b-1)/f)*f+1,h=g+f-1,i=h+1;c<h&&(h=c),e+=b>f&&b>1?"<button class='button button-primary button-prev' data-page='1'><i class='fa-solid fa-angle-double-left'></i></button>":"",e+=b>f?"<button class='button button-primary button-prev' data-page='"+(1===g?1:g-1)+"'><i class='fa-solid fa-angle-left'></i></button>":"";for(var j=g;j<=h;j++)e+="<button class='button button-primary page-number' data-page='"+j+"'>"+j+"</button>";e+=i<=c?"<button class='button button-primary button-next' data-page='"+(i<c?i:c)+"'><i class='fa-solid fa-angle-right'></i></button>":"",e+=b<c&&i<c?"<button class='button button-primary button-next' data-page='"+c+"'><i class='fa-solid fa-angle-double-right'></i></button>":"",d.html(e);a('button.page-number[data-page="'+b+'"]',d).attr("disabled",!0)}}};c.init()});