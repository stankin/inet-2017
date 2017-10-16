function setup() {
  var cnv = createCanvas(400,400);
  cnv.parent("cnv");
  background(200);
}

function draw() {
  background(200, 5);
  noStroke();
  fill(255,0,0);
  ellipse(mouseX, mouseY, 10);
}
