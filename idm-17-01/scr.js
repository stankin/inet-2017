var udata = "";
var req = new XMLHttpRequest();
req.open('GET','data.txt',true);
req.onreadystatechange = function(){
	if(req.readyState != 4) return;
	if(req.status == 200){
		udata = req.responseText;
		alert("Ok!");
	}
	else{
		alert("KEK");
		return;
	}
	var ulist = udata.split("/n");
	for (var i = 0; i < ulist.length; i++){
		console.log(ulist[i]);
	}
}
req.send();