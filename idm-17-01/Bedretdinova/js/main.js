
$(document).ready(function(){
	$("ul").hide();
	$("ul li:odd").css("background-color", "#efefef");
	$("h3 span").click(function(){
		$(this).parent().next().slideToggle();
	});
});
