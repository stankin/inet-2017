function MakeArray( n){
    this.length = n;
    for (var i = 1; i <= n; i++) {
        this[i] = 0
    }
    return this
}


var d0 = new Date();
var r0 = d0.getSeconds();
function poor_rand(){
    d1 = new Date();
    r0 = (r0 * r0 + r0 + d1.getSeconds()) % 3721 ;
    return r0 % width_n;
}


var height_n = 8;
var width_n =5;


var enemy = new MakeArray( width_n);
var wtime;
var score;
var gameover;


document.write( "<FORM NAME='fm1'>");
document.write( "<INPUT TYPE='text' NAME='message' SIZE=20>");
document.write( "</FORM>");
document.write( "<FORM NAME='fm2'><TABLE>");
for( var i=0; i<height_n; i++){
    document.write( "<TR>");
    for( var j=0;j<width_n; j++){
        document.write( "<TD><CENTER><INPUT TYPE='radio'></CENTER></TD>");
    }
    document.write( "</TR>");
}
document.write( "<TR>");
for( var j=0;j<width_n; j++){
    document.write( "<TD><INPUT TYPE='button' VALUE='-x-' onClick='fire("
        + j + ")'></TD>");
}
document.write( "</TR>");
document.write( "</TABLE>");
document.write( "<INPUT TYPE='button' VALUE='СТАРТ' onClick='game_start()'>");
document.write( "</FORM>");

function come(){
    var n = poor_rand();
    document.fm2.elements[ width_n * enemy[n+1] + n].checked = true;
    enemy[n+1]++;
    if( enemy[n+1] < height_n){
        setTimeout("come()", wtime);
    }else{
        gameover = true;
        document.fm1.message.value =  "ИГРА ОКОНЧЕНА: " + score ;
    }
}

function fire( n){
    if( gameover ) return;
    for( var i=0; i<enemy[n+1]; i++){ // clear enemy
        document.fm2.elements[ width_n * i + n].checked = false;
    }
    score += enemy[ n+1];
    document.fm1.message.value = "ОЧКИ: " + score;
    enemy[ n+1] = 0;
    if( wtime > 100){ wtime -= 10};
}

function game_start(){
    for( var n=0; n<width_n; n++){
        for( var i=0; i<enemy[n+1]; i++){
            document.fm2.elements[ width_n * i + n].checked = false;
        }
        enemy[n+1] = 0;
    }
    wtime = 400;
    score = 0;
    gameover = false;
    document.fm1.message.value = "ОЧКИ: " + score;
    setTimeout("come()", wtime);
}
