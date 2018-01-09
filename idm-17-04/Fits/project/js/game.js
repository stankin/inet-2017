var columns = 10, rows = 20; 
var board = []; 
var lose; 
var lines = 0; 
var count = 0;
var maxCount = 0;
var interval; 
var current; 
var currentX, currentY; 
var shapes = [
 [1,1,1,1], 
 [1,1,1,0, 
  1],
 [1,1,1,0, 
  0,0,1],
 [1,1,0,0, 
  1,1],
 [1,1,0,0, 
  0,1,1],
 [0,1,1,0, 
  1,1],
 [0,1,0,0, 
  1,1,1 ]
];
var colors = [ 
 'cyan', 'orange', 'blue', 'yellow', 'red', 'lime', 'purple'
];
var shaped = 0; 
var savedShape; 

function drawNewShape (current) { 
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

function generateShape () { 
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

function newShape() { 
 if (shaped) { 
  for (var i=0; i<savedShape.length; i++) current[i] = savedShape[i]; 
 }
 else { 
  current = generateShape();
  shaped = 1;
 }
 savedShape = generateShape();
 currentX = Math.floor((columns-4)/2); currentY = 0; 
}

function init() { 
 for (var y=0; y<rows; ++y) {
  board[y] = [];
  for (var x=0; x<columns; x++) board[y][x] = 0;
 }
}

function countPlus (lines0) { 
 lines += lines0; 
 var bonus = [0, 100, 300, 700, 1500];
 count += bonus[lines0];
 if (count > maxCount) maxCount = count;
 document.getElementById('tetriscount').innerHTML = 
  "Lines: "+lines+"<br>Count: "+count+"<br>Record: "+maxCount;
}

function freeze() { 
 for (var y=0; y<4; y++) {
  for (var x=0; x<4; x++) {
   if (current[y][x]) board[y+currentY][x+currentX] = current[y][x];
  }
 }
}

function rotate( current ) {
 var newCurrent = [];
 for (var y=0; y<4; y++) {
  newCurrent[y] = [];
  for (var x=0; x<4; x++) newCurrent[y][x]=current[3-x][y];
 }
 return newCurrent;
}

function clearLines() { 
 var cleared = 0;
 for (var y=rows-1; y>-1; y--) {
  var rowFilled = true;
  for (var x=0; x<columns; x++) {
   if (board[y][x]==0) {
    rowFilled = false;
    break;
   }
  }
  if (rowFilled) { 
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

function keyPress( key ) { 
 switch ( key ) {
  case 'escape':    
   window.alert ('paused'); 
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

function valid (offsetX,offsetY,newCurrent) { 
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
     if (offsetY==1) lose=true; 
     return false;
    }
   }
  }
 }
 return true;
}

function playGame() { 
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

function newGame() { 
 clearInterval (interval);
 init ();
 shaped = 0; newShape ();
 lose = false; lines = 0; count = 0; countPlus (0); 
 interval = setInterval (playGame,300); 
}

newGame();
