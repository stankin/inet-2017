var columes =  ['surname', 'name', 'subgroup', 'team'];
var rcolumes = ['Фамилия', 'Имя', 'Подгруппа', 'Команда'];
var students;

function setup() {
  noCanvas();
  loadJSON('data/studentData.json', tableCreate);
}

function draw() {
}

function tableCreate(jsn) {
  students = jsn.data;
  var body = document.body,
    tbl  = document.createElement('table');
  tbl.style.width  = '100px';
  tbl.style.border = '1px solid black';
  var tr = tbl.insertRow();
  for (var j = 0; j < columes.length; ++j) {
    var td = tr.insertCell();
    td.appendChild(document.createTextNode( rcolumes[j] ));
    td.style.border = '1px solid black';
  }
  for (var i = 0; i < students.length; ++i) {
    tr = tbl.insertRow();
    for (var j = 0; j < columes.length; ++j) {
      var td = tr.insertCell();
      td.appendChild(document.createTextNode( students[i][columes[j]] ));
      td.style.border = '1px solid black';
    }
  }
  body.appendChild(tbl);
}

window.onload = function function_name() {
    setup();
    console.log("функцию сборки сделал");
}
