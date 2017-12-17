function show(Element){
    var display =document.getElementById(Element).style.display;

    if(display==="block"){
        document.getElementById(Element).style.display='none';
    }else{
        document.getElementById(Element).style.display='block';
    }

};

