// ************************************************ Event **********************************************
//------------------------------------------------ Component -------------------------------------------
$('button#search').click(function() { getDsActQtyInput(0); });

//------------------------------------------------ Delegation ------------------------------------------
function paginationChange(pageCode) {
	getDsActQtyInput(pageCode);
}


// ********************************************** Method ***********************************************
//------------------------------------------------ AJAX ------------------------------------------------
function getDsActQtyInput(pageCode) {
	let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
	let arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	let arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();
	let arrayLineID = $('select#lineID').multiselect("getChecked").map(function() { return this.value; } ).get();
	pageCode = ( (pageCode) ? pageCode : 0);

	let data = {
		'strDateStart'  : strDateStart,
		'strDateEnd'    : strDateEnd,
		'jobID' 				: arrayJobID,
		'stepID' 				: arrayStepID,
		'lineID' 				: arrayLineID,
		"pageCode" 			: pageCode
	};

	// Get percent of NG report by ajax.
	$.ajax({
		url: baseUrl + 'activityRevoke/ajaxGetActivityQtyInput',
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
		success: function(rDataResult) {
			// pagination.
			$('div#paginationLinks').html(rDataResult.paginationLinks);

			// datatable.
			let dsActQtyInput = rDataResult.dsActQtyInput;
			let strHtml = ((dsActQtyInput.length > 0) 
				? genActQtyInputTable(dsActQtyInput, (parseInt(pageCode) + 1)) 
				: '');
			$('table#actQtyInput > tbody').html(strHtml);
		}
	});
}

function genActQtyInputTable(dsActQtyInput, startRowNo) {
	let strHtml = '';
	let cn = dsActQtyInput.length;
	for(let i=0; i<cn; i++) {
		let row = dsActQtyInput[i];
		strHtml += '<tr>';
			strHtml += '<td class="text-center td-group">' + (startRowNo + i) + '</td>';
			strHtml += '<td class="text-text-left td-group">' + row['Datetime_Stamp'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['JobName'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['StepNumber-Desc'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['LineName'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['WorkerName'] + '</td>';
			strHtml += '<td class="text-right td-group">' + row['Qty_OK'] + '</td>';
			strHtml += '<td class="text-right td-group">' + row['Qty_NG'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['DefectName'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['UserName'] + '</td>';
			
			strHtml += '<td class="text-center">';
				strHtml += '<button type="button" class="btn btn-danger delete-elements"';
				strHtml += ' id="activityID" value="' + row['activityID'] + '">';
					strHtml += '<i class="fa fa-minus"></i>';
				strHtml += '</button>';
				strHtml += '<input type="hidden" id="stockID" value="' + row['stockID'] + '">';
			strHtml += '</td>';
		strHtml += '</tr>';
	}

	return strHtml;
}
