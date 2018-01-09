document.body.onkeydown = function (e) {
 var keys = { //Клавиши
  37: 'left',
  39: 'right', //Стрелки влево и вправо
  40: 'down',
  32: 'down', //Вниз - пробелом или стрелкой вниз
  38: 'rotate', //Вращение- стрелкой вверх
  27: 'escape' //Пауза по клавише Esc
 };
 if (typeof(keys[e.keyCode])!='undefined') { //Если код клавиши допустимый,
  keyPress (keys[e.keyCode]); //Передать его обработчику
  render(); //и перерисовать стакан
 }
};