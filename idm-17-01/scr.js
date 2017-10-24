var udata = "";
var req = new XMLHttpRequest();
req.open('GET','data.txt');
req.onreadystatechange = function(){
	udata = req.responseText;
}
req.send();
alert(udata);
var ulist = udata.split("/n");
for (var i = 0; i < ulist.length; i++){
	console.log(ulist[i]);
	alert(ulist[i]);
}
