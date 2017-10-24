var udata = "";
var req = new XMLHttpRequest();
req.open('GET','data.txt',true);
req.onreadystatechange = function(){
	if(req.readyState != 4) return;
	if(req.status == 200){
		udata = req.responseText;
	}
	else{
		return;
	}
	var ulist = udata.split("\n");
	console.log("ulist length: "+ulist.length);
	var udiv = document.createElement('div');
	udiv.className = 'idm_user';
	var stra = ""
	stra += "<table border = '1'>";
	stra += "<tr><th>Студент</th>"+
							"<th>Модуль 1</th>"+
							"<th>Модуль 2</th>"+
							"<th>Страница</th>"+
							"<th>Команда</th>"+
							"<th>Роль</th></tr>";
	
	for (var i = 0; i < ulist.length; i++){
		var sudata = ulist[i].split("!!!");
		if(sudata.length < 6 || sudata[0]=="#"){
			continue;
		}
		
		stra += "<tr>";
		
		stra += "<td>"+sudata[0]+"</td>";
		stra += "<td>"+sudata[1]+"</td>";
		stra += "<td>"+sudata[2]+"</td>";
		stra += "<td> <a href=idm-17-01/"+sudata[3]+">Page link</a>"+"</td>";
		stra += "<td>"+sudata[4]+"</td>";
		stra += "<td>"+sudata[5]+"</td>";
		
		stra += "</tr>";
		
	}
	stra += "</table>";
	udiv.innerHTML = stra;
	var bdy = document.body;
	bdy.appendChild(udiv);
}
req.send();