function confirmSubmit(event, element, idform) {
	if (window.confirm('Confirmer ? ' + element) === false) {
		event.preventDefault();
	} else {
		document.getElementById(idform).submit();
	}
}