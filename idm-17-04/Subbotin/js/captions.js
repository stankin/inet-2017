
$(document).ready(function(){
$('.boxgrid.slidedown').hover(function(){
$(".cover", this).stop().animate({top:'-240px'},{queue:false,duration:300});
}, function() {
$(".cover", this).stop().animate({top:'0px'},{queue:false,duration:300});
});
});
//Horizontal Sliding
				$('.boxgrid.slideright').hover(function(){
					$(".cover", this).stop().animate({left:'445px'},{queue:false,duration:300});
				}, function() {
					$(".cover", this).stop().animate({left:'0px'},{queue:false,duration:300});
				});