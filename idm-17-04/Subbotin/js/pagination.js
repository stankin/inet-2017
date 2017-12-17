 
$(document).ready(function(){
	
	//how much items per page to show
	var show_per_page = 1; 
	//getting the amount of elements inside content div
	var number_of_items = $('#paginate-place').children().size();
	//calculate the number of pages we are going to have
	var number_of_pages = Math.ceil(number_of_items/show_per_page);
	
	//set the value of our hidden input fields
	$('#current_page').val(0);
	$('#show_per_page').val(show_per_page);
	
	//now when we got all we need for the navigation let's make it '
	
	/* 
	what are we going to have in the navigation?
		- link to previous page
		- links to specific pages
		- link to next page
	*/
	load_navigation();
	
	//add active_page class to the first page link
	$('#page_navigation .page_link:first').addClass('active_page');
	
	//hide all the elements inside content div
	$('#paginate-place').children().css('display', 'none');
	
	//and show the first n (show_per_page) elements
	$('#paginate-place').children().slice(0, show_per_page).css('display', 'block');
	
});
 
function load_navigation(page_num){
var number_of_items = $('#paginate-place').children().size();
var show_per_page = $('#show_per_page').val();
	//calculate the number of pages we are going to have
	var number_of_pages = Math.ceil(number_of_items/show_per_page);
	var navigation_html = '<a class="previous_link" href="javascript:previous();">Back</a>';
	
	var max_pages_to_show = 10;
	if(number_of_pages > max_pages_to_show){
			var middle_items = 5;
			var current_page = $('#current_page').val();
			var start_range = parseInt(current_page) - Math.floor(middle_items);
			var end_range = parseInt(current_page) + Math.floor(middle_items);
			if(start_range < 0){
			
				end_range += Math.abs(start_range);
				start_range=0;
			}
			if(end_range > number_of_pages){
				start_range -= end_range - number_of_pages;
				end_range = number_of_pages
			}
			
			
			
			var i = 0;
			while(i <= number_of_pages){
				
				
				
				if(start_range>2 && i == start_range){
					navigation_html += '...';
				}
				
				if(i == 1 || i == number_of_pages || (i >= start_range && i <= end_range)){
					navigation_html += '<a class="page_link" href="javascript:go_to_page(' + i +')" longdesc="' + i +'">'+ (i + 1) +'</a>';
				}				
				
				if(end_range < number_of_pages-1 && i == end_range){
					navigation_html += '...';
				}
				
				i++;
			}
			
	}else{
	
		var current_link = 0;
		while(number_of_pages > current_link){
			navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';
			current_link++;
		}
		
	}
		
	navigation_html += '<a class="next_link" href="javascript:next();">Next</a>';
	
	$('#page_navigation').html(navigation_html);
	
	$('.page_link[longdesc=' + page_num +']').addClass('active_page').siblings('.active_page').removeClass('active_page');
}
 
function previous(){
	
	new_page = parseInt($('#current_page').val()) - 1;
	//if there is an item before the current active link run the function
	if($('.active_page').prev('.page_link').length==true){
		go_to_page(new_page);
	}
	
}
 
function next(){
	new_page = parseInt($('#current_page').val()) + 1;
	//if there is an item after the current active link run the function
	if($('.active_page').next('.page_link').length==true){
		go_to_page(new_page);
	}
	
}
function go_to_page(page_num){
	//get the number of items shown per page
	var show_per_page = parseInt($('#show_per_page').val());
	
	//get the element number where to start the slice from
	start_from = page_num * show_per_page;
	
	//get the element number where to end the slice
	end_on = start_from + show_per_page;
	
	//hide all children elements of content div, get specific items and show them
	$('#paginate-place').children().css('display', 'none').slice(start_from, end_on).css('display', 'block');
	
	/*get the page link that has longdesc attribute of the current page and add active_page class to it
	and remove that class from previously active page link*/
	
	//update the current page input field
	$('#current_page').val(page_num);
	
	load_navigation(page_num);
}
