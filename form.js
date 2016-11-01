function MarkValidationErrors(errors) {
	if (!errors) {
		return;
	}
	for (var key in errors) {
		item = document.getElementsByName(key)[0];
		if (item) {
			item.className = "studenterror"
		}
	}
}
