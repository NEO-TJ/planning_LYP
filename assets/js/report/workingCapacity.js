// ************************************************ Event **********************************************
// ----------------------------------------------- Doc Load --------------------------------------------
$(document).ready(function() {
	document.title += '-Working Capacity';
});


//************************************************ Method **********************************************
//------------------------------------------------ AJAX -----------------------------------------------
function getReport() {
	var arrayCustomerID = $('select#customerID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayLineID = $('select#lineID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arraySubAssemblyID = $('select#subAssemblyID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'customerID' : arrayCustomerID,
			'jobID' : arrayJobID,
			'lineID' : arrayLineID,
			'subAssemblyID' : arraySubAssemblyID,
	};

	// Get workingCapacity report by ajax.
	$.ajax({
		url: 'workingCapacityReport/ajaxGetWorkingCapacityReport',
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
		success: function(dsWorkingCapacity) {
			$('table#workingCapacityReport > tbody').html(genReport(dsWorkingCapacity));
		}
	});
}




//--------------------------------------------- Generate Html ------------------------------------------
function genData(row) {
	var htmlReport;
	
	htmlReport +='<tr>';

	htmlReport +='<td class="text-left">' + row['customerName'] + '</td>';
	htmlReport +='<td class="text-left">' + row['jobName'] + '</td>';
	htmlReport +='<td class="text-left">' + row['lineName'] + '</td>';
	htmlReport +='<td class="text-left">' + row['sub_assemblyName'] + '</td>';
	htmlReport +='<td class="text-right">' + row['Operation_Time'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';

	// Week 1
	htmlReport +='<td class="text-right bg-success">'
					+ row['planQtyOK1'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + ((row['hours1'] == '-') ? '-' 
					: parseFloat(row['hours1']).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")) + '</td>';
	// Week 2
	htmlReport +='<td class="text-right bg-success">'
					+ row['planQtyOK2'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + ((row['hours2'] == '-') ? '-' 
					: parseFloat(row['hours2']).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")) + '</td>';
	// Week 3
	htmlReport +='<td class="text-right bg-success">'
					+ row['planQtyOK3'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + ((row['hours3'] == '-') ? '-' 
					: parseFloat(row['hours3']).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")) + '</td>';
	// Week 4
	htmlReport +='<td class="text-right bg-success">'
					+ row['planQtyOK4'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + ((row['hours4'] == '-') ? '-' 
					: parseFloat(row['hours4']).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")) + '</td>';
	// Week 5
	htmlReport +='<td class="text-right bg-success">'
					+ row['planQtyOK5'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + ((row['hours5'] == '-') ? '-' 
					: parseFloat(row['hours5']).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")) + '</td>';
	// Week 6
	htmlReport +='<td class="text-right bg-success">'
					+ row['planQtyOK6'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + ((row['hours6'] == '-') ? '-' 
					: parseFloat(row['hours6']).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")) + '</td>';
	// Week 7
	htmlReport +='<td class="text-right bg-success">'
					+ row['planQtyOK7'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + ((row['hours7'] == '-') ? '-' 
					: parseFloat(row['hours7']).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")) + '</td>';

	htmlReport +='</tr>';
	
	return htmlReport;
}
function genReport(dsWorkingCapacity) {
	var htmlReport = "";
	
	for(var i=0; i<dsWorkingCapacity.length; i++)
	{
		//Data.
		htmlReport += genData(dsWorkingCapacity[i]);
	}
	
	$('#headerPage').prop('title', "Total Record : " + dsWorkingCapacity.length);
	return htmlReport;	
}