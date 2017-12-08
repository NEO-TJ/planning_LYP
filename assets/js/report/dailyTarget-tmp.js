// ************************************************ Event **********************************************
// ----------------------------------------------- Doc Load --------------------------------------------
$(document).ready(function() {
	document.title += '-Daily Target';
	
	changeLine();

	$('#ckUseDataPlan').change(function() {
		alert('Test grobal');
		if(this.checked) {
			alert('test');
//			var returnVal = confirm("Are you sure?");	// ทางลัดทำ Dialog box
//			$(this).prop("checked", returnVal);

//			$('input#dateStart').prop("disabled", true);
//			$('input#dateEnd').prop("disabled", true);
		}
		else{
			alert('test 2');
		}
		$('input#dateStart').prop("disabled", !this.checked);
		$('input#dateEnd').prop("disabled", !this.checked);
	})
});
//--------------------------------------------- Event Method ------------------------------------------
function bindingMultiselectJob() {
	$('select#jobID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all job',
	}).multiselectfilter();
}




// ------------------------------------------------- Line -----------------------------------------------
$('select#lineID').change(changeLine);



//------------------------------------------------- Mode ----------------------------------------------
//******************************************* Change line mode ****************************************
function changeLine(){
	let lineID = $('select#lineID :selected').val();
	let data = {'lineID': lineID};
		
	// Get project table one row by ajax.
	$.ajax({
		url: 'dailyTargetReport/ajaxGetDsJobByLineID',
		type: 'post',
		data: data,
		dataType: 'json',
		beforeSend: function(){
		},
		error: function(xhr, textStatus){
			swal("Error", textStatus + xhr.responseText, "error");
		},
		complete: function(){
		},
		success: function(dsJob) {
			filterJob(dsJob);
		}
	});
}

function filterJob(dsJob) {
	var tableTagInputCaption = '<span class="input-group-btn">';
	tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Job : </button>';
	tableTagInputCaption += '</span>';
	
	var tableTagInputSelecter = '<select class="form-control multi-select" id="jobID" name="jobID[]" multiple="multiple">';
	for(var i=0; i<dsJob.length; i++) {
		tableTagInputSelecter += '<option value=' + dsJob[i]['id'] + '>' + dsJob[i]['Name'] + '</option>';
	}
	tableTagInputSelecter += '</select>'
	
	$('div#jobID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectJob();
}





//-------------------------------------------- Extra Validate ------------------------------------------
function validateLine(){
	let result = false;
	let lineID = $('select#lineID :selected').val();

	if(lineID > 0) { result = true; }
    else {
		swal("Warning", "Please select 'Line'.","warning");
	}
	
	return result;
}


//************************************************ Method **********************************************
// ------------------------------------------------ AJAX -----------------------------------------------
function getReport() {
	if(validateLine()) {
		let strDateStart = $('input#dateStart').val();
		let strDateEnd = $('input#dateEnd').val();
		let lineID = $('select#lineID :selected').val();
		let arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();

		let data = {
				'strDateStart'	: strDateStart,
				'strDateEnd'	: strDateEnd,
				'lineID'		: lineID,
				'jobID'			: arrayJobID,
		};

		// Get daily target report by ajax.
		$.ajax({
			url: 'dailyTargetReport/ajaxGetDailyTargetReport',
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function(){
			},
			error: function(xhr, textStatus){
				swal("Error", textStatus + xhr.responseText, "error");
			},
			complete: function(){
			},
			success: function(dsDailyTarget) {
				$('table#dailyTargetReport > tbody').html(genReport(dsDailyTarget));
				for(var i=0; i<dsDailyTarget.length; i++) {
					generateBarcode(dsDailyTarget[i]['jsBarcode'], i);
				}
			}
		});
	}
}



//-------------------------------------------- Generate Barcode ----------------------------------------
function generateBarcode(strSource, d){
	var value = strSource;
	var btype = "code39";
	var renderer = "bmp";
	
	var settings = {
		output:renderer,
		barWidth: "2",
		barHeight: "30",
	};
	
    $('td#bc' + d).barcode(value, btype, settings);
}

//--------------------------------------------- Generate Html ------------------------------------------
function genLineGroup(lineCurrent) {
	var htmlReport = '';
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-left" rowspan="1" colspan="8">';
	htmlReport +='<h5><strong><u>';
	htmlReport +=lineCurrent;
	htmlReport +='</us></strong></h5>';
	htmlReport +='</td>';	
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genSummary(totalQtyPlan) {
	var htmlReport = '';

	htmlReport +='<tr>';
	htmlReport +='<td class="text-left"></td>';
	htmlReport +='<td class="text-left"></td>';
	htmlReport +='<td class="text-left"></td>';
		
	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Qty Plan Summary">';
	htmlReport +=totalQtyPlan.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';
		
	htmlReport +='<td class="text-center"></td>';
	htmlReport +='<td class="text-left"></td>';
	htmlReport +='<td class="text-left"></td>';
	htmlReport +='<td class="text-center"></td>';
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genData(row, d) {
	var htmlReport = '';
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-left">' + row['Date_Stamp'] + '</td>';
	htmlReport +='<td class="text-left">' + row['Job Number'] + '</td>';
	htmlReport +='<td class="text-right">' + row['Number'] + '</td>';
	htmlReport +='<td class="text-left">  -   ' + row['DESC'] + '</td>';
	htmlReport +='<td class="text-right">' + row['Plan_Qty_OK'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-center"></td>';
	htmlReport +='<td class="text-right">' + emptyDefault(row['nextStepNumber']) + '</td>';
	htmlReport +='<td class="text-left"> - ' + emptyDefault(row['lineNext']) + '</td>';
	htmlReport +='<td class="text-center" id="bc' + d + '"></td>';
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genReport(dsDailyTarget) {
	var htmlReport = '';
	
	var r = 0;
	var d = 0;
	var totalQtyPlan = 0;
	var lineCurrent = " (TJ Start) ";
	
	var row;
	for(var i=0; i<dsDailyTarget.length; i++)
	{
		row = dsDailyTarget[i];
		
		if(lineCurrent != row['lineCurrent']) {
			if(lineCurrent != " (TJ Start) ") {
				//Summary.
				htmlReport += genSummary(totalQtyPlan);
				r++;
			}
			//Line Group.
			lineCurrent = row['lineCurrent'];
			if((r%2) == 0) {
				htmlReport +='<tr></tr> <tr></tr> <tr></tr>';
				r += 3;
			}
			htmlReport += genLineGroup(lineCurrent);
			totalQtyPlan = 0;
			r++;
		}
		//Data.
		htmlReport += genData(row, d++);
		totalQtyPlan = parseInt(totalQtyPlan) + parseInt(row['Plan_Qty_OK']);
		r++;
	}

	if(r > 0) {
		//Summary.
		htmlReport += genSummary(totalQtyPlan);
		r++;
	}
	
	
	$('#headerPage').prop('title', "Total Record : " + dsDailyTarget.length);
	return htmlReport;
}