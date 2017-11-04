function setColor(e){
     
    if(e.type==="mouseover")
        e.target.style.backgroundColor = "red";
    else if(e.type==="mouseout")
        e.target.style.backgroundColor = "blue";
}

function setColor23(e)
{
	if(e.type==="mouseout")
        e.target.style.backgroundColor = "#a30000";
    else if(e.type==="mouseover")
        e.target.style.backgroundColor = "#8fbaf3";
}

function setColor14(e)
{
	if(e.type==="mouseout")
        e.target.style.backgroundColor = "#E70000";
    else if(e.type==="mouseover")
        e.target.style.backgroundColor = "#0245A3";
}

var lab1=document.getElementById("lab1");
lab1.addEventListener("mouseover", setColor14);
lab1.addEventListener("mouseout", setColor14);

var lab2=document.getElementById("lab2");
lab2.addEventListener("mouseover", setColor23);
lab2.addEventListener("mouseout", setColor23);

var lab3=document.getElementById("lab3");
lab3.addEventListener("mouseover", setColor23);
lab3.addEventListener("mouseout", setColor23);

var lab4=document.getElementById("lab4");
lab4.addEventListener("mouseover", setColor14);
lab4.addEventListener("mouseout", setColor14);

function createCookie()
{
	
}