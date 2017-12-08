// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error
const dltValidateEnoughStock = 2;	// Validate Enough Stock error
// ************************************************ Event **********************************************
$(window).load(function() {
	resetRecoveryNGPage();
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
//************************************************ Method **********************************************
//************************************************* Tool ***********************************************
function showDialog($type){
	if($type == dltOK){
		
	}else if($type == dltValidate) {
		swal("Warning", "Please check your input key.","warning");
	}else if($type == dltValidateEnoughStock) {
		swal("Warning", "Please check your 'Total NG stock' and 'Qty NG to send'\n"
				+ "'Total NG stock' much more 'Qty NG to send' or 'Qty NG to send' much more zero."
				,"warning");
	}
}








//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Quantity Input %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//************************************************ Submit & Reset **************************************
$('form#formRecoveryNG').submit(function(e) {
	e.preventDefault();
	
	if(validateRequireFill()) {
		if(validateEnoughStock()) {
			saveAll();			
		} else {
			showDialog(dltValidateEnoughStock);
		}
	} else {
		showDialog(dltValidate);
	}
});
$('form#formRecoveryNG button#resetAllStep').click(function(e) {
	fullResetAllStep();
});


//************************************************ Method **********************************************
//------------------------------------------------- Save -----------------------------------------------
function saveAll(){
	var jobID = $('select#job :selected').val();
	var dateTimeStamp = $('input#dateTimeStamp').val();
	var workerID = $('select#worker :selected').val();
	var sourceStepID = $('select#sourceStep :selected').val();
	var destinationStepID = $('select#destinationStep :selected').val();
	var qtyNGSend = $('input#qtyNGSend').val();

	var data = {
				'jobID': 				jobID,
				'dateTimeStamp':		dateTimeStamp,
				'workerID': 			workerID,
				'sourceStepID': 		sourceStepID,
				'destinationStepID': 	destinationStepID,
				'qtyNGSend': 			qtyNGSend,
				};

	// Get process table one row by ajax.
	$.ajax({
		url: 'recoveryNG/ajaxSaveRecoveryNG',
		type: 'post',
		data: data,
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
					text: "Save Recovery NG to database has success",
					type: "success",
					showCancelButton: false,
					allowOutsideClick: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					window.location.href="recoveryNG"
				});
			}
			else if(result == 1) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
							+ 'Not enougth stock please check stock'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else if(result == 2) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
							+ 'Error at update stock'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else if(result == 3) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
							+ 'Error at insert activity'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
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
function validateRequireFill(){
	var result = false;
	
	var resultJobID = false;
	var resultDateTimeStamp = false;
	var resultWorker = false;
	var resultSourceQtyNG = false;
	var resultDestinationQtyOK = false;
	var resultQtyNGSend = false;
	
	var qtyNGSend = $('input#qtyNGSend').val();
	
	// Check job id selected?
	resultJobID = validateFillSelectElement($('select#job'));
	// Check DateTimeStamp selected?
	resultDateTimeStamp = validateDateTimeStamp();
	// Check worker id selected?
	resultWorker = validateFillSelectElement($('select#worker'));
	// Check Source Step id selected?
	resultSourceQtyNG = validateFillSelectElement($('select#sourceStep'));
	// Check Destination Step id selected?
	resultDestinationQtyOK = validateFillSelectElement($('select#destinationStep'));
	// Check Quantity NG for send require has input?
	resultQtyNGSend = validateFillInputElement($('input#qtyNGSend'));
	
	
	result = (resultJobID && resultDateTimeStamp && resultWorker && resultSourceQtyNG
				&& resultDestinationQtyOK && resultQtyNGSend);
	return result;
}
function validateEnoughStock() {
	var sourceQtyNG = parseInt($('input#sourceQtyNG').val());
	var qtyNGSend = parseInt($('input#qtyNGSend').val());
	
	return ( ((sourceQtyNG < qtyNGSend) || (qtyNGSend < 1)) ? false : true);
}
function validateDateTimeStamp(){
    var result = false;

    if($('input#dateTimeStamp').length) {
        var dateTimeStamp = $('input#dateTimeStamp').val();
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
function resetRecoveryNGPage(jobID, sourceStepID, destinationStepID){
	if(isEmpty(jobID)) { jobID = 0; }
	if(isEmpty(sourceStepID)) { sourceStepID = 0; }
	if(isEmpty(destinationStepID)) { destinationStepID = 0; }

	$('select').val(0);
	
	$('select#job').val(jobID);
	$('select#job').trigger('change');

	if(sourceStepID != 0) {
		$('select#sourceStep').val(sourceStepID);
		$('select#sourceStep').trigger('change');
	}
	if(destinationStepID != 0) {
		$('select#destinationStep').val(destinationStepID);
		$('select#destinationStep').trigger('change');
	}
}
