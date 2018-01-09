var columns = 10, rows = 20; //Классические размеры стакана
var board = []; //Стакан
var lose; //Конец игры
var lines = 0; //Убрано линий
var count = 0; //Счёт
var maxCount = 0; //Рекорд
var interval; //Скорость игры в мс
var current; //Текущая фигурка
var currentX, currentY; //Позиция текущей фигурки
var shapes = [ //Массив фигур
 [1,1,1,1], //I
 [1,1,1,0, //L
  1],
 [1,1,1,0, //J
  0,0,1],
 [1,1,0,0, //O
  1,1],
 [1,1,0,0, //Z
  0,1,1],
 [0,1,1,0, //S
  1,1],
 [0,1,0,0, //T
  1,1,1 ]
];
var colors = [ //Массив цветов
 'cyan', 'orange', 'blue', 'yellow', 'red', 'lime', 'purple'
];
var shaped = 0; //Есть ли следующая фигурка
var savedShape; //Следующая фигурка

function drawNewShape (current) { //Нарисовать следующую фигуру на отдельной канве
 var canvas = document.getElementById ('figurecanvas');
 var ctx = canvas.getContext ('2d');
 var width = canvas.width, height = canvas.height;
 var blockWidth = width / 4, blockHeight = height / 4;
 ctx.fillStyle = 'red';
 ctx.strokeStyle = 'black';
 ctx.clearRect (0,0,width,height);
 for (var y=0; y<4; y++) {
  for (var x=0; x<4; x++) {
   if (current[y][x]) {
    ctx.fillStyle = colors[current[y][x]-1];
    ctx.fillRect (blockWidth*x, blockHeight*y, blockWidth-1, blockHeight-1);
    ctx.strokeRect (blockWidth*x, blockHeight*y, blockWidth-1, blockHeight-1);
   }
  }
 }
}

function generateShape () { //Сгенерировать следующую фигуру
 var id = Math.floor (Math.random()*shapes.length);
 var shape = shapes[id];
 var current = [];
 for (var y=0; y<4; y++) {
  current[y] = [];
  for (var x=0; x<4; x++) {
   var i = 4*y+x;
   if (typeof(shape[i])!='undefined' && shape[i]) current[y][x] = id+1;
   else current[y][x]=0;
  }
 }
 if (shaped) drawNewShape(current);
 return current;
}

function newShape() { //Создать новую фигурку 4x4 в массиве current
 if (shaped) { //Есть сохранённая
  for (var i=0; i<savedShape.length; i++) current[i] = savedShape[i]; 
 }
 else { //Нет сохранённой
  current = generateShape();
  shaped = 1;
 }
 savedShape = generateShape();
 currentX = Math.floor((columns-4)/2); currentY = 0; //Начальная позиция новой фигурки
}

function init() { //Очистить стакан
 for (var y=0; y<rows; ++y) {
  board[y] = [];
  for (var x=0; x<columns; x++) board[y][x] = 0;
 }
}

function countPlus (lines0) { //Подсчёт очков
 lines += lines0; 
 var bonus = [0, 100, 300, 700, 1500];
 count += bonus[lines0];
 if (count > maxCount) maxCount = count;
 document.getElementById('tetriscount').innerHTML = 
  "Lines: "+lines+"<br>Count: "+count+"<br>Record: "+maxCount;
}

function freeze() { //Остановить фигурку и записать её положение в board
 for (var y=0; y<4; y++) {
  for (var x=0; x<4; x++) {
   if (current[y][x]) board[y+currentY][x+currentX] = current[y][x];
  }
 }
}

function rotate( current ) { //Вращение текущей фигурки current против часовой стрелки
 var newCurrent = [];
 for (var y=0; y<4; y++) {
  newCurrent[y] = [];
  for (var x=0; x<4; x++) newCurrent[y][x]=current[3-x][y];
 }
 return newCurrent;
}

function clearLines() { //Проверить, есть ли заполненные линии и очистить их
 var cleared = 0;
 for (var y=rows-1; y>-1; y--) {
  var rowFilled = true;
  for (var x=0; x<columns; x++) {
   if (board[y][x]==0) {
    rowFilled = false;
    break;
   }
  }
  if (rowFilled) { //Очистить линию
   cleared++;
   document.getElementById ('clearsound').play();
   for (var yy=y; yy>0; yy--) {
    for (var x=0; x<columns; x++) {
     board[yy][x]=board[yy-1][x];
    }
   }
   y++;
  }
 }
 return cleared;
}

function keyPress( key ) { //Обработчик нажатий клавиш
 switch ( key ) {
  case 'escape':    
   window.alert ('paused'); //В JS уже есть модальное окно :)
  break;
  case 'left':
   if (valid(-1)) --currentX;
  break;
  case 'right':
   if (valid(1)) ++currentX;
  break;
  case 'down':
   if (valid(0,1)) ++currentY;
  break;
  case 'rotate':
   var rotated = rotate(current);
   if (valid(0,0,rotated)) current = rotated;
  break;
 }
}

function valid (offsetX,offsetY,newCurrent) { //Проверка допустимости итоговой позиции фигуры current
 offsetX = offsetX || 0;
 offsetY = offsetY || 0;
 offsetX = currentX + offsetX;
 offsetY = currentY + offsetY;
 newCurrent = newCurrent || current;
 for (var y=0; y<4; y++) {
  for (var x=0; x<4; x++) {
   if (newCurrent[y][x]) {
    if (typeof(board[y+offsetY])=='undefined' || typeof(board[y+offsetY][x+offsetX])=='undefined'
     || board[y+offsetY][x+offsetX]
     || x+offsetX<0 || y+offsetY>=rows || x+offsetX>=columns) {
     if (offsetY==1) lose=true; //Конец игры, если текущая фигура - на верхней линии
     return false;
    }
   }
  }
 }
 return true;
}

function playGame() { //Контроль падения фигурки, создание новой и очистка линии
 if (valid(0,1)) currentY++;
 else {
  freeze();
  var cleared = clearLines();
  if (cleared) countPlus(cleared);
  if (lose) {
   newGame();
   return false;
  }
  newShape();
 }
}

function newGame() { //Новая игра
 clearInterval (interval);
 init ();
 shaped = 0; newShape ();
 lose = false; lines = 0; count = 0; countPlus (0); 
 interval = setInterval (playGame,300); //скорость игры, мс
}

newGame();