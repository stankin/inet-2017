var responseData = "";
var req = new XMLHttpRequest();
req.open('GET','data.json',true);
req.onreadystatechange = function(){
	if(req.readyState != 4) return;
	if(req.status == 200){
		responseData = req.responseText;
	}
	else{
		return;
	}
	var studentList = JSON.parse(responseData);
	var udiv = document.createElement('div');
	var tbl = ""
	tbl += "<table border = '1'>";
	tbl += "<tr><th>Студент</th>"+
							"<th>Модуль 1</th>"+
							"<th>Модуль 2</th>"+
							"<th>Страница</th>"+
							"<th>Команда</th>"+
							"<th>Роль</th></tr>";
	
	for (var i = 0; i < studentList.students.length; i++){
		var sudata = studentList.students[i];
		
		tbl += "<tr>";
		
		tbl += "<td>"+sudata.name + "</td>";
		tbl += "<td>"+sudata.module1 + "</td>";
		tbl += "<td>"+sudata.module2 + "</td>";
		tbl += "<td> <a href='"+sudata.project + "'>Project</a>" + "</td>";
		tbl += "<td>"+sudata.team + "</td>";
		tbl += "<td>"+sudata.role + "</td>";
		
		tbl += "</tr>";
		
	}
	tbl += "</table>";
	udiv.innerHTML = tbl;
	var body = document.body;
	body.appendChild(udiv);
}
req.send();