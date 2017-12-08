// ************************************************ Event **********************************************
// ------------------------------------------------ Load -----------------------------------------------
$(document).on('click', 'td#stockQty', function(e) {
	var tr = $(e.target).closest('tr');
	var allID = tr.find('td:first-child input#allID').val();

	var strStockQty = tr.find('td:last-child').html();
	var stockQty = parseFloat(strStockQty.replace(',','').replace(' ',''));

	swal({
		title: "Adjust Stock",
		text: '<div class="row panel panel-primary">'
				+ '<div class="col-md-12 margin-input">'
					+ '<div class="input-group">'
						+ '<span class="input-group-btn">'
							+ '<button class="btn btn-primary disabled" type="button">Stock Qty : </button>'
						+ '</span>'
						+ '<input type="number" class="form-control text-right" autocomplete="off" id="stockQty"'
							+ ' placeholder="Stock Quantity..." value=' + stockQty + ' />'
						
					+ '</div>'
				+ '</div>'
				+ '<div class="col-md-12">'
					+ '<span class="label label-default pull-left" id="stockValidate"></span>'
				+ '</div>'
			+ '</div>'

			+ '<div class="row">'
				+ '<div class="col-md-3">'
				+ '</div>'
				+ '<div class="col-md-3">'
					+ '<button type="button" class="btn btn-primary btn-submit" id="submitAdjustStock">Submit</button>'
				+ '</div>'
				+ '<div class="col-md-3 pull-left">'
					+ '<button type="button" class="btn btn-cancel btn-reset pull-left" id="cancelAdjustStock">Cancel</button>'
				+ '</div>'
				+ '<div class="col-md-3">'
					+ '<input type="text" class="hide" id="singleAllID" value="' + allID + '" />'
				+ '</div>'
			+ '</div>',
		showConfirmButton: false,
		showCancelButton: false,
		allowOutsideClick: false,
		closeOnConfirm: false,
	});
});

$(document).on('keydown', 'input#stockQty', function(e) {
	numericFilter(e, this, true);
});
$(document).on('click', "button#submitAdjustStock", function() {
	var allID = $('input#singleAllID').val();
	var stockQty = $('input#stockQty').val();
	
	var arrayID = allID.split(',');
	var jobID = arrayID[0];
	var stepID = arrayID[1];
	var firstStepFlag = arrayID[2];

	if( (isEmpty(jobID) || (jobID < 1)) || (isEmpty(stepID) || (stepID < 1)) || (isEmpty(firstStepFlag)) ) {
		$('span#stockValidate').text("Can't find Stock please refresh!");
	}
	else if(isEmpty(stockQty) || (stockQty < 0)) {
		$('span#stockValidate').text("Please input Stock Quantity!");
	}
	else {
		adjustStockQty(jobID, stepID, firstStepFlag, stockQty);
		swal.close();
	}
});
$(document).on('click', "button#cancelAdjustStock", function() {
	swal.closeModal();
});


//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//__________________________________________ Adjust Stock Quantity _____________________________________
function adjustStockQty(jobID, stepID, firstStepFlag, stockQty) {
	var data = {
				'jobID'			: jobID,
				'stepID'		: stepID,
				'firstStepFlag'	: firstStepFlag,
				'stockQty'		: stockQty,
	};
	// Shift date by dalay with offset sun.
	$.ajax({
		url: 'stockAdjust/ajaxAdjustStock',
		type: 'post',
		data: data,
		beforeSend: function(){
		},
		error: function(xhr, textStatus){
			swal("Error", textStatus + xhr.responseText, "error");
		},
		complete: function(){
		},
		success: function(result) {
			if(result) {
				swal({
					title: "Success",
					text: "Adjust stock quantity to : " + stockQty,
					type: "success",
					showCancelButton: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					displayFullStockTable();
				});
			}
			else {
				swal({
					title: "Warning!",
					text: '<span class="text-info">Adjust stock</span> Not complete...!<p>' 
							+ 'Please check<span class="text-info"> Database or stock. </span>',
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}