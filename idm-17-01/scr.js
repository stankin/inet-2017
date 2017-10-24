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
	udiv.innerHTML += "<table border = '1'>";
	udiv.innerHTML += "<tr><th>Студент</th>"+
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
		
		udiv.innerHTML += "<tr>";
		
		udiv.innerHTML += "<td>"+sudata[0]+"</td>";
		udiv.innerHTML += "<td>"+sudata[1]+"</td>";
		udiv.innerHTML += "<td>"+sudata[2]+"</td>";
		udiv.innerHTML += "<td> <a href="+sudata[3]+">Page link</a>"+"</td>";
		udiv.innerHTML += "<td>"+sudata[4]+"</td>";
		udiv.innerHTML += "<td>"+sudata[5]+"</td>";
		
		udiv.innerHTML += "</tr>";
		
	}
	udiv.innerHTML += "</table>";
	var bdy = document.body;
	bdy.appendChild(udiv);
}
req.send();