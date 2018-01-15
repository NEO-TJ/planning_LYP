//************************************************ Helper **********************************************
//------------------------------------------ Normal numeric input --------------------------------------
function numericFilter(e, obj, isDecimal) {
	if ((([e.keyCode||e.which] >= 48) && ([e.keyCode||e.which] <= 57))			// Allow: 0-9
			|| (([e.keyCode||e.which] >= 96) && ([e.keyCode||e.which] <= 105))	// Allow: 0-9 Of Numpad
			|| ($.inArray(e.keyCode, [9, 13, 27, 116]) !== -1)					// Allow: tab, enter(carriageReturn), escape, F5
			|| (e.keyCode >= 33 && e.keyCode <= 40)								// Allow: 4arrow, home, end, Pg Up, Pg Dn
			|| ($.inArray(e.which, [8, 46]) !== -1)								// Allow: backspace, delete
			|| ($.inArray(e.keyCode, [8, 46]) !== -1)) {
		return;
	}
	else if ([e.keyCode||e.which] == 190) {										//this is to allow decimal point
		if(isDecimal == true) {
			let val = obj.value;
			
			if(val.indexOf(".") > -1) {
				e.preventDefault();
			}
			return;
		}
		e.preventDefault();
	}
	else {
		e.preventDefault();
		return;
	}
}

//-------------------------------------------- Check variable ------------------------------------------
function isEmpty(str) {
	return typeof str == 'string' && !str.trim() || typeof str == 'undefined' || str === null;
}
//------------------------------------- Check input element variable -----------------------------------
function validateFillInputElement(obj, allowZero){
	let result = false;
	let val = obj.val();
	
	if(isEmpty(val)) {
		obj.addClass('bg-error');
	}
	else{
		obj.removeClass('bg-error');
		result = true;
	}
	
	return result;
}
function validateFillSelectElement(obj){
	let result = false;
	let val = obj.find(':selected').val();
	
	if(val > 0) {
		obj.removeClass('bg-error');
		result = true;
	}
	else{
		obj.addClass('bg-error');
	}
	
	return result;
}
function validateFillMultiSelectElement(obj){
	let result = false;
	let objId = obj.prop('id');
	let val = obj.find(':selected').val();
	
	if(val > 0) {
		$("div#" + objId).removeClass('bg-error');
		result = true;
	}
	else{
		$("div#" + objId).addClass('bg-error');
	}
	
	return result;
}