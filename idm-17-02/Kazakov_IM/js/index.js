
$(function() {
      setInterval( function() {
            /*Получаем секнуды на текущий момент времени из объекта Date */
      var seconds = new Date().getSeconds();
      /*Вычисляем на сколько градусов нужно повернуть секнудную стрелку*/
      var sdegree = seconds * 6;
      /*Записываем в переменную свойство CSS rotate()*/
      var srotate = "rotate(" + sdegree + "deg)";
          /*Используя jQuery записываем в свойствах css в id #sec знаечние полученное в перемнной srotate*/
      $("#sec").css({ "transform": srotate });
          /*Пример полученного значения CSS transform:rotate(60 deg)*/
      }, 0 );

      setInterval( function() {
      var hours = new Date().getHours();
      var mins = new Date().getMinutes();
      var hdegree = hours * 30 + (mins / 2);
      var hrotate = "rotate(" + hdegree + "deg)";

      $("#hour").css({ "transform": hrotate});

      }, 0 );

      setInterval( function() {
      var mins = new Date().getMinutes();
      var mdegree = mins * 6;
      var mrotate = "rotate(" + mdegree + "deg)";

      $("#min").css({ "transform" : mrotate });

      },0 );

});

function show(Element){
    var display =document.getElementById(Element).style.display;

    if(display==="block"){
        document.getElementById(Element).style.display='none';
    }else{
        document.getElementById(Element).style.display='block';
    }

};

