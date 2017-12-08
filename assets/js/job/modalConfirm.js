// ************************************************ Event **********************************************
// ------------------------------------------------ Caller ---------------------------------------------
$(document).on('click', 'button#remove', function(e) {
	var tr = $(e.target).closest('tr');
	var jobID = tr.find('td:last-child button#remove').val();
	var jobName = tr.find('td:first-child').html();
	
	swal({
		title: "Are you sure to remove job :",
		text: '<div class="row panel panel-primary">' + jobName
				+ '<div class="col-md-12">'
					+ '<span class="label label-default pull-left" id="delayValidate"></span>'
				+ '</div>'
			+ '</div>'

			+ '<div class="row">'
				+ '<div class="col-md-3">'
				+ '</div>'
				+ '<div class="col-md-3">'
					+ '<button type="button" class="btn btn-primary btn-submit" id="submitRemove">Sure!</button>'
				+ '</div>'
				+ '<div class="col-md-3 pull-left">'
					+ '<button type="button" class="btn btn-cancel btn-reset pull-left" id="cancelRemove">Cancel</button>'
				+ '</div>'
				+ '<div class="col-md-3">'
					+ '<input type="text" class="hide" id="jobID" value="' + jobID + '" />'
					+ '<input type="text" class="hide" id="jobName" value="' + jobName + '" />'
				+ '</div>'
			+ '</div>',
		showConfirmButton: false,
		showCancelButton: false,
		allowOutsideClick: false,
		closeOnConfirm: false,
	});
});

//-------------------------------------------------- Modal ---------------------------------------------
$(document).on('click', "button#submitRemove", function() {
	var jobID = $('input#jobID').val();
	
	if(isEmpty(jobID) || (jobID < 1)) {
		$('span#jobValidate').text("Can't find job please refresh!");
	}
	else {
		RemoveFullJob(jobID);
		swal.close();
	}
});
$(document).on('click', "button#cancelRemove", function() {
	swal.closeModal();
});





//************************************************ Method **********************************************
//------------------------------------------------ AJAX ------------------------------------------------
//___________________________________________ Remove Full Job __________________________________________
function RemoveFullJob(jobID) {
	var jobID = $('input#jobID').val();
	var jobName = $('input#jobName').val();
	var data = { 'jobID'	: jobID };

	// Shift date by dalay with offset sun.
	$.ajax({
		url: 'jobRemove/ajaxRemoveJob',
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
					text: "Remove job : " + jobName,
					type: "success",
					showCancelButton: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					submitDisplay();
				});
			}
			else {
				swal({
					title: "Warning!",
					text: '<span class="text-info">Remove job : ' + jobName + '</span> Not complete...!<p>' 
							+ 'Please check<span class="text-info"> Database or job. </span>',
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}