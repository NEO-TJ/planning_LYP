// Global Variable.
let gbPageCode = 0;

// Event.
$(document).on("click", '.pagination a', function (e) {
	e.preventDefault();

	let link = $(this).get(0).href; // get the link from the DOM object
	let segments = link.split('/');
	let pageCode = segments[segments.length - 1];
	if( (pageCode !== "#") && ($.isNumeric(pageCode)) ) {
		gbPageCode = pageCode;
		paginationChange(pageCode);
	} else {
		document.getElementById('docTopBody').scrollIntoView(true);
	}
});