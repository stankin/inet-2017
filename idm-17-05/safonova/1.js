// Scroll to the desired section on click
function scrollToSection(event) {
  event.preventDefault();
  
  var $section = $($(this).attr('href'));
  
  $('html, body').animate({
    scrollTop: $section.offset().top
  }, 500);
}

$('.scroll-to-section').on('click', scrollToSection);