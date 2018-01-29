var cnv;
var index = 0;
var drawing = true;

function setup() {
  cnv = createCanvas(windowWidth,windowHeight);
  cnv.parent("#cnv");
  background(0,0);
}


function draw() {
  noStroke();
  fill((index==0)?255:0,(index==1)?255:0,(index==2)?255:0, 1);
  if(drawing && mouseX >0 &&mouseY>0) {
    ellipse(mouseX, mouseY, 90);
  }
}


function windowResized(){
  cnv.resize(windowWidth,windowHeight);
  background(0,0);
}

function mousePressed(){
  switch(mouseButton){
    case LEFT:
    ++index;
    if(index >=3) index%=3;
    break;
    case RIGHT:
    windowResized();
    drawing=!drawing;
  }
}
