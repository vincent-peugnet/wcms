function confirmSubmit(event, element) {
	if (window.confirm('Confirmer ? ' + element) === false) {
		event.preventDefault();
	}
}