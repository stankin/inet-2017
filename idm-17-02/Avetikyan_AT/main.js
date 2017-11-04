function toggleVisability(e, hash) {
	e.preventDefault();
	var id = hash.slice(1),
		element = document.getElementById(id);

	if (element.className === 'active') {
		element.className = '';
	} else {
		element.className = 'active';
	}
}
