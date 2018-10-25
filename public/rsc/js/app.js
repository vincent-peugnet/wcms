function confirmSubmit(event, element, idform) {
	if (window.confirm('Confirm ? ' + element) === false) {
		event.preventDefault();
	} else {
		document.getElementById(idform).submit();
	}
}