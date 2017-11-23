$(document).ready(function(){
	 $("#owl-demo").owlCarousel({
		 
		autoPlay: 3000,
		 
		items : 3,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [979,3],
		navigation: true,
		navigationText: ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"]
		 
	});
	
	 $("#slide-testimonial").owlCarousel({
		 
		autoPlay: 3000,
		 
		singleItem:true,
		pagination: true,
		 
	});
	

});

$(document).ready(function(){
    $(".counter").on("click","a", function (event) {
        event.preventDefault();
        var id  = $(this).attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 1000);
    });
});

