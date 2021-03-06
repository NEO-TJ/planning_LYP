// ************************************************ Event **********************************************
//------------------------------------------------ Component -------------------------------------------
$('button#search').click(function() { getDsActRecoveryNG(0); });

//------------------------------------------------ delegation ------------------------------------------
function paginationChange(pageCode) {
	getDsActRecoveryNG(pageCode);
}


// ********************************************** Method ***********************************************
//------------------------------------------------ AJAX ------------------------------------------------
function getDsActRecoveryNG(pageCode) {
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
		url: baseUrl + 'activityRevoke/ajaxGetActivityRecoveryNG',
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
			let dsActRecoveryNG = rDataResult.dsActRecoveryNG;
			let strHtml = ((dsActRecoveryNG.length > 0) 
				? genActRecoveryNGTable(dsActRecoveryNG, (parseInt(pageCode) + 1)) 
				: '');
			$('table#actRecoveryNG > tbody').html(strHtml);
		}
	});
}

function genActRecoveryNGTable(dsActRecoveryNG, startRowNo) {
	let strHtml = '';
	let cn = dsActRecoveryNG.length;
	for(let i=0; i<cn; i++) {
		let row = dsActRecoveryNG[i];
		strHtml += '<tr>';
			strHtml += '<td class="text-center td-group">' + (startRowNo + i) + '</td>';
			strHtml += '<td class="text-text-left td-group">' + row['Datetime_Stamp'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['JobName'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['StepNumber-Desc'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['LineName'] + '</td>';
			strHtml += '<td class="text-left td-group">' + row['WorkerName'] + '</td>';
			strHtml += '<td class="text-right td-group">' + row['Qty_NG'] + '</td>';
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
