!function(a){"use strict";a.fn.msm_password_strength_meter=function(){this.strength_meter=null,this.msg={0:msm_strength_meter.warning_level.step_0,1:msm_strength_meter.warning_level.step_1,2:msm_strength_meter.warning_level.step_2,3:msm_strength_meter.warning_level.step_3,4:""},this.color={0:"red",1:"red",2:"red",3:"orange",4:"green"},this.init=function(b){var c=this;c.strength_meter=a("div.strength-meter",b),c.strength_meter.progress({value:0,autoSuccess:!1,showActivity:!1}),a("input[type=password]",b).on("keyup change",function(){var b=wp.passwordStrength.meter(a(this).val(),wp.passwordStrength.userInputDisallowedList());c.strength_meter.progress({value:b>0?b:0,autoSuccess:!1,showActivity:!1});var d=b>=0?c.msg[b]:c.msg[0];b<4&&(d+=msm_strength_meter.guide_message),a("div.label",c.strength_meter).html(d),c.strength_meter.removeClass("active red orange green"),c.strength_meter.addClass(c.color[b])})}}}(jQuery);