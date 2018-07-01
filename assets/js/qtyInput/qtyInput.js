// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error
// ************************************************ Event **********************************************
$(window).load(function() {
	resetQtyInputPage();
});
$(document).ready(function() {
	$('#dateTimeStamp').datetimepicker();
	$('input#dateTimeStamp').val(moment().format('DD-MMM-YYYY HH:mm'));
	
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
});

$('input#dateTimeStamp').focusout(function(e) {
	validateDateTimeStamp();
});

$(document).on('keydown', 'input[type="number"]', function(e) {
	numericFilter(e, this, true);
});
$('input[type="number"]').on('keydown', function(e) {
	numericFilter(e, this, true);
});
//************************************************ Method **********************************************
//************************************************* Tool ***********************************************
function showDialog($type){
	if($type == dltOK){
		
	}else if($type == dltValidate) {
		swal("Warning", "Please check your input key.","warning");
	}
}







//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Quantity Input %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//************************************************ Submit & Reset **************************************
$('form#formQtyInput').submit(function(e) {
	e.preventDefault();
	
	if(validateAll()) {
		saveAll();
	} else {
		showDialog(dltValidate);
	}
});
$('form#formQtyInput button#resetQtyInput').click(function(e) {
	resetQtyInputFill();
});


//************************************************ Method **********************************************
//------------------------------------------------- Save -----------------------------------------------
function saveAll(){
	let jobID = $('select#job :selected').val();
	let dateTimeStamp = $('input#dateTimeStamp').val();
	let stepID = $('select#step :selected').val();
	let workerID = $('select#worker :selected').val();
	let qtyOK = $('input#qtyOK').val();
	let totalQtyNG = $('input#totalQtyNG').val();

	let dsNG = new Array();
	$('table#ng.table-components tbody tr').each(function(i, row){
		if( ($('table#ng.table-components tbody tr').length == 1)
				&& (i == 0)
				&& ($(this).find('td:nth-child(4) input#qtyNG').val() == 0)) {
			return true; 
		}
		
		let dictNG = {
					'subAssemblyID':	$(this).find('td:nth-child(2) select#subAssemble :selected').val(),
					'defectID':		 	$(this).find('td:nth-child(3) select#defect :selected').val(),
					'qtyNG':	 		$(this).find('td:nth-child(4) input#qtyNG').val(),
				};
		dsNG.push(dictNG);
	});

	let dataQtyInput = {
				'jobID': 			jobID,
				'dateTimeStamp':	dateTimeStamp,
				'stepID': 			stepID,
				'workerID': 		workerID,
				'qtyOK': 			qtyOK,
				'totalQtyNG': 		totalQtyNG,
				'dsNG': 			dsNG,
				};
	
	// Get process table one row by ajax.
	$.ajax({
		url: 'qtyInput/ajaxSaveQtyInput',
		type: 'post',
		data: dataQtyInput,
		beforeSend: function(){
			swal({title:"Saving...", 
				text: '<span class="text-info"><i class="fa fa-refresh fa-spin"></i> Saving please wait...</span>', 
				showConfirmButton: false, 
			});
		},
		error: function(xhr, textStatus){
			swal("Error", textStatus + xhr.responseText, "error");
		},
		complete: function(){
		},
		success: function(result) {
			if(result == 0) {
				swal({
					title: "Success",
					text: "Save quantity input to database has success",
					type: "success",
					showCancelButton: false,
					allowOutsideClick: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					window.location.href="qtyInput"
				});
			}
			else if(result == 1) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Quantity input </span> Not complete...!<p>'
							+ 'Not enougth stock please check stock'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else if(result == 2) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Quantity input </span> Not complete...!<p>'
							+ 'Error at update stock'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else if(result == 3) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Quantity input </span> Not complete...!<p>'
							+ 'Error at insert activity'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Quantity input </span> Not complete...!<p>'
							+ 'Error'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}
//********************************************** Validation *******************************************
function validateAll(){
	let result = false;
	
	let resultJob = false;
	let resultDateTimeStamp = false;
	let resultStep = false;
	let resultWorker = false;
	let resultQtyOK = false;
	let resultTotalQtyNG = false;
	let resultQtyNotValue = false;
	let resultAllNG = false;
	
	let qtyOK = $('input#qtyOK').val();
	let totalQtyNG = $('input#totalQtyNG').val();
	
	// Check all require field.
	
	// Check job id selected?
	resultJob = validateFillSelectElement($('select#job'));
	// Check DateTimeStamp selected?
	resultDateTimeStamp = validateDateTimeStamp();
	// Check step id selected?
	resultStep = validateFillSelectElement($('select#step'));
	// Check worker id selected?
	resultWorker = validateFillSelectElement($('select#worker'));
	// Check qty ok has input?
	resultQtyOK = validateFillInputElement($('input#qtyOK'));
	// Check total qty ng require has input?
	resultTotalQtyNG = validateFillInputElement($('input#totalQtyNG'));
	// Check qty ok and total qty ng require has input more than 0?
	if( (qtyOK == 0) && (totalQtyNG == 0) ) {
		$('input#qtyOK').addClass('bg-error');
		$('input#totalQtyNG').addClass('bg-error');
	}
	else{
		$('input#qtyOK').removeClass('bg-error');
		$('input#totalQtyNG').removeClass('bg-error');
		resultQtyNotValue = true;
	}
	
	// Check all qty ng require has input?
	resultAllNG = validateAllNG();
	
	result = (resultJob && resultDateTimeStamp && resultStep && resultWorker
			&& resultQtyOK && resultTotalQtyNG && resultQtyNotValue && resultAllNG);
	return result;
}
function validateDateTimeStamp(){
    let result = false;

    if($('input#dateTimeStamp').length) {
        let dateTimeStamp = $('input#dateTimeStamp').val();
        if(isEmpty(dateTimeStamp)) {
        	swal("Warning", "Please check your 'DateTime Stamp'.","warning");
        }
        else {
            if(moment(dateTimeStamp, 'DD-MMM-YYYY HH:mm') > moment()) {
            	swal("Warning", "Can't choose future DateTime.\n Please check your 'DateTime Stamp'.","warning");
            	$('input#dateTimeStamp').val(moment().format('DD-MMM-YYYY HH:mm'))
            }
            else {
            	result = true;
            }
        }
    }
    else {result = true;}
	
	return result;
}




//************************************************** Tool ****************************************************
//******************************************** Reset input fill **********************************************
function resetQtyInputPage(jobID, stepID){
	if(isEmpty(jobID)) { jobID = 0; }
	if(isEmpty(stepID)) { stepID = 0; }

	$('select').val(0);
	
	$('select#job').val(jobID);
	$('select#job').trigger('change');

	if(stepID != 0) {
		$('select#step').val(stepID);
		$('select#step').trigger('change');
	}
}
