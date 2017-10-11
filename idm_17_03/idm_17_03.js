var columes =  ['surname', 'name', 'team', 'role'];
var rcolumes = ['Фамилия', 'Имя', 'Команда', 'РП', 'СП', 'ВН', 'НИ', 'БА', 'АД', 'КО', 'ПП'];
var students;

function setup() {
  noCanvas();
  loadJSON('/data/studentData.json', tableCreate, function errorCallback(arg){console.log('failed to load json'); console.log(arg)});
}

function draw() {
}

var td;
function tableCreate(jsn) {
  
  students = jsn.data;
  var body = document.body, tbl  = document.createElement('table');
  tbl.style.width  = '100px';
  tbl.style.border = '1px solid black';
  var tr = tbl.insertRow();
  for (var j = 0; j < rcolumes.length; ++j) {
    td = tr.insertCell();
    td.appendChild(document.createTextNode( rcolumes[j] ));
    td.style.border = '1px solid black';
  }
  for (var i = 0; i < students.length; ++i) {
    tr = tbl.insertRow();
    for (var j = 0; j < columes.length; ++j) {
      if (columes[j]!='role') {
        td = tr.insertCell();
        td.appendChild(document.createTextNode( students[i][columes[j]] ));
        td.style.border = '1px solid black';
      } else {
        var st = students[i].role;
        //console.log(st);
        for (var k=0; k<st.length; ++k) {
          td = tr.insertCell();
          td.appendChild(document.createTextNode( st.charAt(k) ));
          td.style.border = '1px solid black';
        }
      }
    }
  }
  body.appendChild(tbl);
}