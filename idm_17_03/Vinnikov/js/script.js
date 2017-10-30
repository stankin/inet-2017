new WOW().init();

$(document).ready(function(){
  $('.reports__description_content').on('click', function(event){
    event.preventDefault();
    var $parent = $(this).parent();
    if($parent.hasClass('active')) {
      $parent.toggleClass('active');
    } else {
      $('.js-open').removeClass('active');
      $parent.toggleClass('active');
    }
  });

  $('.go_to').click( function(event){
    var scroll_el = $(this).attr('href'); // возьмем содержимое атрибута href, должен быть селектором, т.е. например начинаться с # или .
    if ($(scroll_el).length != 0) { // проверим существование элемента чтобы избежать ошибки
      $('html, body').animate({ scrollTop: $(scroll_el).offset().top }, 500); // анимируем скроолинг к элементу scroll_el
    }
  });

  $('.content__header_button').on('click', function(event){
    event.preventDefault();
    $('.invis').removeClass('none');
  });
  scrolled();
  $(function() {
    $(".owl-carousel").owlCarousel();
  });
});

$(document).scroll(function scrolled(){
  if ($(this).scrollTop() > 0) {
    $('.scroll').addClass('scrolled');
  } else {
    $('.scroll').removeClass('scrolled');
  }
});
