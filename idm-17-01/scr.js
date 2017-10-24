var req = new XMLHttpRequest();
req.open('GET','data.txt');
req.onreadystatechange = function(){
	console.log(req.responseText);
}
req.send();