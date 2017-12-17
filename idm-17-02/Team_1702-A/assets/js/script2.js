$(function () {

// Чекбоксы из JSON
    // country.json
    $.getJSON("country.json", function(data) {
        var output = "";
        var type = "checkbox";
        var name = "cuisine";
        for (var i in data.countries) {
            output+="<label><input type=" + type + " name=" + name + " value=" + data.countries[i].flag + ">" + data.countries[i].name + "</label>";
        }
        output+= "";
        document.getElementById("placeholder1").innerHTML=output;
    });
    
    // ingrid.json
    $.getJSON("ingrid.json", function(data) {
        var output = "";
        for (var i in data.ingridients) {
            output+="<label><input type='checkbox' name='ingredients' value=" + data.ingridients[i].flag + ">" + data.ingridients[i].name + "</label>";
        }
        output+= "</div>";
        document.getElementById("placeholder2").innerHTML=output;
    });
    
    // type.json
    $.getJSON("type.json", function(data) {
        var output = "";
        for (var i in data.types) {
            output+="<label><input type='checkbox' name='type' value=" + data.types[i].flag + ">" + data.types[i].name + "</label>";
        }
        output+= "";
        document.getElementById("placeholder3").innerHTML=output;
    });
    
    // forwhat.json
    $.getJSON("forwhat.json", function(data) {
        var output = "";
        for (var i in data.forwhats) {
            output+="<label><input type='checkbox' name='sphere' value=" + data.forwhats[i].flag + ">" + data.forwhats[i].name + "</label>";
        }
        output+= "";
        document.getElementById("placeholder4").innerHTML=output;
    });
	
	});