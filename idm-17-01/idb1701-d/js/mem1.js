for (r = 0; r <= 5; r++) {
		document.write('<tr>');
		for (c = 0; c <= 5; c++) {
		document.write('<td align="center">');
		document.write('<a href="javascript:showimage('+((6*r)+c)+')" onClick="document.f.b.focus()">');
		document.write('<img src="./images/0.gif" name="img'+((6*r)+c)+'" border="0">');
		document.write('</a></td>');}
		document.write('</tr>');}