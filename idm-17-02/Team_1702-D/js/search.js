$(function(){
    $.getJSON('https://cejow.github.io/Git_Project/toursinfo.json', function(data) {
            for(var i=0;i<data.countries.length;i++){
                $('#countries').append('<option value="' + data.countries[i].country + '">' + data.countries[i].country + 
                '</option>');
            }
    });
});

$(function(){
    $.getJSON('https://cejow.github.io/Git_Project/toursinfo.json', function(data) {
            for(var i=0;i<data.nights.length;i++){
                $('#nights').append('<option value="' + data.nights[i].night + '">' + data.nights[i].night + 
                '</option>');
            }
    });
});


function Search() {
for (var i = document.getElementById('result').getElementsByTagName('tr').length -1; i; i--) {
	document.getElementById('result').deleteRow(i);}
	$.getJSON('https://cejow.github.io/Git_Project/toursinfo.json', function(data) {
            for(var i=0;i<data.tours.length;i++){
			if(data.tours[i].country == $("#countries").val() && data.tours[i].nights == $("#nights").val()){
                $('#result').append('<tr><td>' + data.tours[i].arrival + '</td><td>' + data.tours[i].tour + 
                '</td><td>' + data.tours[i].nights + '</td><td>'+ data.tours[i].hotel + '</td><td>'+ data.tours[i].food + '</td><td>'
				+ data.tours[i].accommodation + '</td><td>>' + data.tours[i].price +'</td><td onclick="action()" >'+ 'Заказать' +'</td></tr>');
			}
			} 
    });	
    };
	
function action() {
	with(event.target || event.srcElement) {
	var numRow = parentNode.rowIndex;}
	$(".popup_bg").click(function(){	 
		$(".popup").fadeOut(800);	
	});
	$(".popup").fadeIn(800); 
    var numCell = 0;
	var row = $("table tr").eq(numRow);
	outtxt1.value = $("td", row).eq(numCell).text();
	outtxt2.value = $("td", row).eq(numCell+1).text();
	outtxt3.value = $("td", row).eq(numCell+6).text();
}


var img_dir = "pics/"; 
var sort_case_sensitive = false; 

function _sort(a, b) {
    var a = a[0];
    var b = b[0];
    var _a = (a + '').replace(/,/, '.');
    var _b = (b + '').replace(/,/, '.');
    if (parseFloat(_a) && parseFloat(_b)) return sort_numbers(parseFloat(_a), parseFloat(_b));
    else if (!sort_case_sensitive) return sort_insensitive(a, b);
    else return sort_sensitive(a, b);
}


function sort_numbers(a, b) {
    return a - b;
}

function sort_insensitive(a, b) {
    var anew = a.toLowerCase();
    var bnew = b.toLowerCase();
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sort_sensitive(a, b) {
    if (a < b) return -1;
    if (a > b) return 1;
    return 0;
}

function getConcatenedTextContent(node) {
    var _result = "";
    if (node == null) {
        return _result;
    }
    var childrens = node.childNodes;
    var i = 0;
    while (i < childrens.length) {
        var child = childrens.item(i);
        switch (child.nodeType) {
            case 1: // ELEMENT_NODE
            case 5: // ENTITY_REFERENCE_NODE
                _result += getConcatenedTextContent(child);
                break;
            case 3: // TEXT_NODE
            case 2: // ATTRIBUTE_NODE
            case 4: // CDATA_SECTION_NODE
                _result += child.nodeValue;
                break;
            case 6: // ENTITY_NODE
            case 7: // PROCESSING_INSTRUCTION_NODE
            case 8: // COMMENT_NODE
            case 9: // DOCUMENT_NODE
            case 10: // DOCUMENT_TYPE_NODE
            case 11: // DOCUMENT_FRAGMENT_NODE
            case 12: // NOTATION_NODE
            // skip
            break;
        }
        i++;
    }
    return _result;
}

function sort(e) {
    var el = window.event ? window.event.srcElement : e.currentTarget;
    while (el.tagName.toLowerCase() != "td") el = el.parentNode;
    var a = new Array();
    var name = el.lastChild.nodeValue;
    var dad = el.parentNode;
    var table = dad.parentNode.parentNode;
    var up = table.up;
    var node, arrow, curcol;
    for (var i = 0; (node = dad.getElementsByTagName("td").item(i)); i++) {
        if (node.lastChild.nodeValue == name){
            curcol = i;
            if (node.className == "curcol"){
                arrow = node.firstChild;
                table.up = Number(!up);
            }else{
                node.className = "curcol";
                arrow = node.insertBefore(document.createElement("img"),node.firstChild);
                table.up = 0;
            }
            arrow.src = img_dir + table.up + ".gif";
            arrow.alt = "";
        }else{
            if (node.className == "curcol"){
                node.className = "";
                if (node.firstChild) node.removeChild(node.firstChild);
            }
        }
    }
    var tbody = table.getElementsByTagName("tbody").item(0);
    for (var i = 0; (node = tbody.getElementsByTagName("tr").item(i)); i++) {
        a[i] = new Array();
        a[i][0] = getConcatenedTextContent(node.getElementsByTagName("td").item(curcol));
        a[i][1] = getConcatenedTextContent(node.getElementsByTagName("td").item(1));
        a[i][2] = getConcatenedTextContent(node.getElementsByTagName("td").item(0));
        a[i][3] = node;
    }
    a.sort(_sort);
    if (table.up) a.reverse();
    for (var i = 0; i < a.length; i++) {
        tbody.appendChild(a[i][3]);
    }
}

function init(e) {
    if (!document.getElementsByTagName) return;

    for (var j = 0; (thead = document.getElementsByTagName("thead").item(j)); j++) {
        var node;
        for (var i = 0; (node = thead.getElementsByTagName("td").item(i)); i++) {
            if (node.addEventListener) node.addEventListener("click", sort, false);
            else if (node.attachEvent) node.attachEvent("onclick", sort);
            node.title = "Нажмите на заголовок, чтобы отсортировать колонку";
        }
        thead.parentNode.up = 0;
        
        if (typeof(initial_sort_id) != "undefined"){
            td_for_event = thead.getElementsByTagName("td").item(initial_sort_id);
            if (document.createEvent){
                var evt = document.createEvent("MouseEvents");
                evt.initMouseEvent("click", false, false, window, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, td_for_event);
                td_for_event.dispatchEvent(evt);
            } else if (td_for_event.fireEvent) td_for_event.fireEvent("onclick");
            if (typeof(initial_sort_up) != "undefined" && initial_sort_up){
                if (td_for_event.dispatchEvent) td_for_event.dispatchEvent(evt);
                else if (td_for_event.fireEvent) td_for_event.fireEvent("onclick");
            }
        }
    }
}

var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
if (root){
    if (root.addEventListener) root.addEventListener("load", init, false);
    else if (root.attachEvent) root.attachEvent("onload", init);
}
