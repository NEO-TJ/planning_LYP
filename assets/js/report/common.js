// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
	
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// DateTime picker.
    $('input#dateStart').datepicker({ dateFormat: "yy-mm-dd" }).val()
    $('input#dateEnd').datepicker({ dateFormat: "yy-mm-dd" }).val()

    $('input#dateEnd').on("change", function(e) {
    	validateDateRange();
    });
	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
});

//------------------------------------------------ Component -------------------------------------------
$('button#refresh').click(search);
$('button#search').click(search);






//************************************************ Method **********************************************
//------------------------------------------------ Search ----------------------------------------------
function search() {
	if(validateDateRange()) {
		getReport();
	}
}
//---------------------------------------------- Validation -------------------------------------------
function validateDateRange(){
    var result = false;

    if($('input#dateStart').length && $('input#dateEnd').length) {
        var dateStart = $('input#dateStart').val();
        var dateEnd = $('input#dateEnd').val();
        if(isEmpty(dateStart) || isEmpty(dateEnd)) {
        	swal("Warning", "Please check your 'Date Range'.","warning");
        }
        else {
            if(dateEnd < dateStart) {
            	swal("Warning", "'End Date' more than 'Start Date'\n Please check your 'Date Range'.","warning");
            }
            else {
            	result = true;
            }
        }
    }
    else {result = true;}
	
	return result;
}






//------------------------------------------------- Helper ---------------------------------------------
function emptyDefault(obj) {
	var str = "";

	if(!isEmpty(obj)) {
		if(obj != 0) {
			str = obj
		}
	}
	
	return str;
}
