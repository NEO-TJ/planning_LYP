// ********************************************** Variable *********************************************
var days = ["Sun", "Mon.", "Tue", "Wed.", "Thu.", "Fri.", "Sat."];
var months = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "June", "July", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."];

var uiChanged = false;
// ************************************************ Event **********************************************
// ------------------------------------------------ Load -----------------------------------------------
$(document).ready(function() {
    // Multiselect.
    $('select#jobID').multiselect({
        header: true,
        noneSelectedText: 'Default selected all job',
        click: function(event, ui) { uiChanged = true; },
        close: function(event, ui) { changeJob(); }
    }).multiselectfilter();

    bindingMultiselect('jobTypeID', 'job type');
    bindingMultiselect('stepID', 'step');

    // DateTimePicker.
    $('#dtsStart').datetimepicker({
        viewMode: 'days',
        format: 'DD-MMM-YYYY',
        useCurrent: true
    });
    $('#dtsStart').val(moment().format('DD-MMM-YYYY'));

    // UI Block.
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

});
//------------------------------------------------- Search ---------------------------------------------
$('button#search').click(function(e) {
    displayFullPlanningTable();
    uiChanged = false;
});

//-------------------------------------------- Change Start Date ---------------------------------------
$("#dtsStart").on("dp.change", function(e) {
    let curDate = new Date().getTime();
    let dtsStartDate = e.date;
    let dDiff = Math.round(((dtsStartDate) - curDate) / (1000 * 60 * 60 * 24));

    $('input#diffStartCurrentDate').val(dDiff)
});



//----------------------------------------------- Shift Date -------------------------------------------
$(document).on('click', 'button#next-date', function(e) {
    nextDate();
    submitDisplay();
});
$(document).on('click', 'button#previous-date', function(e) {
    previousDate();
    submitDisplay();
});

// --------------------------------------------- Plan input --------------------------------------------
$(document).on('keydown', 'input', function(e) {
    numericFilter(e, this, false);
});


$(document).on('change', 'input[name^="okQtySlot"]', function(e) {
    var index = $(this).prop('name').match(/\[(.*?)\]/)[1];
    saveOKQtyPlan($(this), (index - 1));
});
$(document).on('change', 'input[name^="workerQtySlot"]', function(e) {
    var index = $(this).prop('name').match(/\[(.*?)\]/)[1];
    saveWorkerQtyPlan($(this), (index - 1));
});



// ----------------------------------------------- Delay -----------------------------------------------
$(document).on('keydown', '#delayDayQty', function(e) {
    numericFilter(e, this, false);
});
$(document).on('click', "#submitDelay", function() {
    var stockID = $('input#stockID').val();
    var delayDayQty = $('input#delayDayQty').val();

    if (isEmpty(stockID) || (stockID < 1)) {
        $('span#delayValidate').text("Can't find stock id please refresh!");
    } else if (isEmpty(delayDayQty) || (delayDayQty < 1)) {
        $('span#delayValidate').text("Please input Day number!");
    } else {
        shiftDatePlanDelayWithOffsetSun(stockID, delayDayQty);
        swal.close();
    }
});
$(document).on('click', "#cancelDelay", function() {
    swal.closeModal();
});
$(document).on('click', 'button#delay', function(e) {
    var tr = $(e.target).closest('tr');
    var stockID = tr.find('td:last-child button#delay').val();


    swal({
        title: "Plan delay",
        text: '<div class="row panel panel-primary">' +
            '<div class="col-md-12 margin-input">' +
            '<div class="input-group">' +
            '<span class="input-group-btn">' +
            '<button class="btn btn-primary disabled" type="button">Day number : </button>' +
            '</span>' +
            '<input type="number" class="form-control text-right" autocomplete="off" id="delayDayQty"' +
            ' placeholder="Day number for delay..." value="" />'

            +
            '</div>' +
            '</div>' +
            '<div class="col-md-12">' +
            '<span class="label label-default pull-left" id="delayValidate"></span>' +
            '</div>' +
            '</div>'

            +
            '<div class="row">' +
            '<div class="col-md-3">' +
            '</div>' +
            '<div class="col-md-3">' +
            '<button type="button" class="btn btn-primary btn-submit" id="submitDelay">Submit</button>' +
            '</div>' +
            '<div class="col-md-3 pull-left">' +
            '<button type="button" class="btn btn-cancel btn-reset pull-left" id="cancelDelay">Cancel</button>' +
            '</div>' +
            '<div class="col-md-3">' +
            '<input type="text" class="hide" id="stockID" value="' + stockID + '" />' +
            '</div>' +
            '</div>',
        showConfirmButton: false,
        showCancelButton: false,
        allowOutsideClick: false,
        closeOnConfirm: false,
    });
});
//-----------------------------------------------------------------------------------------------------
//--------------------------------------------- Event Method ------------------------------------------
//-----------------------------------------------------------------------------------------------------

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Start freeze table header.
function freezeTableHeader() {
    $('table#planning').floatThead({
        position: 'fixed',
        autoReflow: 'true',
    });
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Binding multiselect element.
function bindingMultiselect(elementID, captionName) {
    $('select#' + elementID).multiselect({
        header: true,
        noneSelectedText: 'Default selected all ' + captionName,
        click: function(event, ui) { uiChanged = true; }
    }).multiselectfilter();
}
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------






//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//________________________________________________ Search ______________________________________________
function displayFullPlanningTable() {
    var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; }).get();
    var arrayJobTypeID = $('select#jobTypeID').multiselect("getChecked").map(function() { return this.value; }).get();
    var arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; }).get();
    var diffStartCurrentDate = parseInt($('input#diffStartCurrentDate').val(), 10);
    var totalSlotDate = parseInt($('select#dayOfPlan :selected').val());

    var data = {
        'jobID': arrayJobID,
        'jobTypeID': arrayJobTypeID,
        'stepID': arrayStepID,
        'diffStartCurrentDate': diffStartCurrentDate,
        'totalSlotDate': totalSlotDate,
    };

    // Get top reject report by ajax.
    $.ajax({
        url: 'planning/ajaxGetFullDsPlanning',
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            var dsFullPlanning = result['dsFullPlanning'];
            var diffStartCurrentDate = result['diffStartCurrentDate'];

            genTable(dsFullPlanning, diffStartCurrentDate, totalSlotDate);
            $('input#diffStartCurrentDate').val(diffStartCurrentDate);
            freezeTableHeader();
        }
    });
}



//____________________________________________ OK Qty Plan save ________________________________________
function saveOKQtyPlan(obj, offsetDiffDate) {
    var stockID = getStockIDAtRow(obj);
    var diffStartCurrentDate = getDiffCurrentDate(offsetDiffDate);
    var okQtyPlan = getValueAtRow(obj);

    var data = {
        'stockID': stockID,
        'diffStartCurrentDate': diffStartCurrentDate,
        'okQtyPlan': okQtyPlan,
    }

    // Get plan join table many row by ajax.
    $.ajax({
        url: 'planning/ajaxSaveOKQtyPlan',
        type: 'post',
        data: data,
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            if (result == 0) {} else {
                obj.val('');
            }
        }
    });
}
//___________________________________________Worker Qty Plan save_______________________________________
function saveWorkerQtyPlan(obj, offsetDiffDate) {
    var stockID = getStockIDAtRow(obj);
    var diffStartCurrentDate = getDiffCurrentDate(offsetDiffDate);
    var workerQtyPlan = getValueAtRow(obj);

    var data = {
        'stockID': stockID,
        'diffStartCurrentDate': diffStartCurrentDate,
        'workerQtyPlan': workerQtyPlan,
    }

    // Get plan join table many row by ajax.
    $.ajax({
        url: 'planning/ajaxSaveWorkerQtyPlan',
        type: 'post',
        data: data,
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            var machineTime = obj.closest('tr').find('td:eq(5)').text();
            var operationTime = obj.closest('td').next('td').text();
            if (result == 0) {
                obj.closest('td').next('td').text(machineTime * obj.val());
            } else {
                obj.val(operationTime / machineTime);
            }
        }
    });
}
//________________________________ Shift date plan delay with offset sunday ____________________________
function shiftDatePlanDelayWithOffsetSun(stockID, delayDayQty) {
    var data = {
        'stockID': stockID,
        'delayDayQty': delayDayQty,
    };
    // Shift date by dalay with offset sun.
    $.ajax({
        url: 'planning/ajaxShiftDatePlanDelayWithOffsetSun',
        type: 'post',
        data: data,
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            if (result > 0) {
                swal({
                    title: "Success",
                    text: "Shift plan day " + delayDayQty + " day\n" +
                        "Total shift " + result + " record.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Done",
                    confirmButtonClass: "btn btn-success",
                }).then(function() {
                    submitDisplay();
                });
            } else if (result == -1) {
                swal({
                    title: "Information",
                    text: "Not have plan to shift plan day",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonText: "OK",
                    confirmButtonClass: "btn btn-info",
                });
            } else {
                swal({
                    title: "Warning!",
                    text: '<span class="text-info">Shift plan day ' + delayDayQty + ' day</span> Not complete...!<p>' +
                        'Please check<span class="text-info"> Database or plan date. </span>',
                    type: "error",
                    confirmButtonColor: "#DD6B55"
                });
            }
        }
    });
}




//------------------------------------------------- Mode -----------------------------------------------
//******************************************** Change job mode *****************************************
function changeJob() {
    var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; }).get();

    var data = { 'jobID': arrayJobID };

    // Get project table one row by ajax.
    $.ajax({
        url: 'planning/ajaxGetDsStepByJobID',
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(dsStep) {
            filterStep(dsStep);
        }
    });
}

function filterStep(dsStep) {
    var tableTagInputCaption = '<span class="input-group-btn">';
    tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Step number : </button>';
    tableTagInputCaption += '</span>';

    var tableTagInputSelecter = '<select class="form-control multi-select" id="stepID" name="stepID[]" multiple="multiple">';
    for (var i = 0; i < dsStep.length; i++) {
        tableTagInputSelecter += '<option value=' + dsStep[i]['id'] + '>'
        tableTagInputSelecter += dsStep[i]['Number'] + ' - ' + dsStep[i]['DESC'] + '</option>';
    }

    $('div#stepID').html(tableTagInputCaption + tableTagInputSelecter);
    bindingMultiselect('stepID', 'step');
}









//************************************************ Method **********************************************
//------------------------------------------------ Normal ----------------------------------------------
function submitDisplay() {
    if (uiChanged) {
        uiChanged = false;
    }
    displayFullPlanningTable();
}
//_________________________________________________ Date _______________________________________________
function nextDate() {
    var diffStartCurrentDate = parseInt($('input#diffStartCurrentDate').val(), 10);
    $('input#diffStartCurrentDate').val(++diffStartCurrentDate);
}

function previousDate() {
    var diffStartCurrentDate = parseInt($('input#diffStartCurrentDate').val(), 10);
    $('input#diffStartCurrentDate').val(--diffStartCurrentDate);
}




//------------------------------------------------ Helper ----------------------------------------------
//___________________________________________ Save worker & plan _______________________________________
function getStockIDAtRow(obj) {
    return obj.closest('tr').find('td:last-child button#delay').val();
}

function getDiffCurrentDate(offsetDiffDate) {
    return (parseInt($('input#diffStartCurrentDate').val(), 10) + offsetDiffDate);
}

function getValueAtRow(obj) {
    return (obj.val());
}
//__________________________________________ Calculate Total Column ____________________________________
function getTotalColumn(diffStartCurrentDate, totalSlotDate) {
    var totalSpanSlotDate = ((diffStartCurrentDate > 0) ? 0 :
        (diffStartCurrentDate < ((totalSlotDate - 1) * (-1))) ? totalSlotDate :
        (1 - diffStartCurrentDate));

    return ((totalSpanSlotDate * 3) + totalSlotDate + 11);
}



//******************************************* Generate table HTML **************************************
function genTable(dsFullPlanning, diffStartCurrentDate, totalSlotDate) {
    let result = false;
    let tableHtml = [];

    tableHtml.push('<table class="table table-bordered table-components');
    tableHtml.push(' table-condensed table-hover table-striped table-responsive"');
    tableHtml.push(' id="planning" style="width: 100%;">');

    tableHtml.push(genHeader(diffStartCurrentDate, totalSlotDate));
    tableHtml.push(genBody(dsFullPlanning, diffStartCurrentDate, totalSlotDate));
    tableHtml.push('</table>');
    document.getElementById("divPlanning").innerHTML = tableHtml.join('');

    $('#headerPage').prop('title', "Total Record : " + dsFullPlanning.length);
    return true;
}


function genHeader(diffStartCurrentDate, totalSlotDate) {
    var htmlHeader = "";

    var totalCol = getTotalColumn(diffStartCurrentDate, totalSlotDate);
    // Header row 0
    htmlHeader += '<thead class="table-header">';
    htmlHeader += '<tr>';
    htmlHeader += '<th id="tableHeader" class="text-center" colspan="' + totalCol + '"><h4><b>Planning</b></h4></th>';
    htmlHeader += '</tr>';

    // Header row 1
    htmlHeader += '<tr>';
    htmlHeader += '<th class="text-center" colspan="10">';
    htmlHeader += '<button type="button" class="btn btn-warning text-left pull-right" id="previous-date">Previous</button>';
    htmlHeader += '</th>';
    for (var i = 0; i < totalSlotDate; i++) {
        htmlHeader += '<th id="slot-' + (i + 1) + '" class="text-center" colspan="' +
            (((parseInt(diffStartCurrentDate) + i) > 0) ? 1 : 4) + '">' + (i + 1) + '</th>';
    }
    htmlHeader += '<th class="text-right">';
    htmlHeader += '<button type="button" class="btn btn-warning pull-right" id="next-date">Next</button>';
    htmlHeader += '</th>';
    htmlHeader += '</tr>';

    // Header row 2
    htmlHeader += '<tr>';
    htmlHeader += '<th class="text-center" rowspan="2">Job</th>';
    htmlHeader += '<th class="text-center" rowspan="2">Next step</th>';
    htmlHeader += '<th class="text-center" rowspan="2">Step-Description</th>';
    htmlHeader += '<th class="text-center" rowspan="2">Line</th>';
    htmlHeader += '<th class="text-center" rowspan="2">Machine</th>';
    htmlHeader += '<th class="text-center" rowspan="2">(Sec)</th>';
    htmlHeader += '<th class="text-center" colspan="4">Total</th>';
    for (var i = 0; i < totalSlotDate; i++) {
        htmlHeader += '<th class="text-center" colspan="' + (((parseInt(diffStartCurrentDate) + i) > 0) ? 1 : 4) +
            '" id="date-slot-' + (i + 1) + '">';
        htmlHeader += genDateSlotCaption(parseInt(diffStartCurrentDate) + i);
        htmlHeader += '</th>';
    }
    htmlHeader += '<th class="text-center" rowspan="2">Delay</th>';
    htmlHeader += '</tr>';

    // Header row 3
    htmlHeader += '<tr>';
    htmlHeader += '<th class="text-center">Sub Assembly</th>';
    htmlHeader += '<th class="text-center">Stock</th>';
    htmlHeader += '<th class="text-center">OK</th>';
    htmlHeader += '<th class="text-center">NG</th>';
    var elementHidden = "";
    for (var i = 0; i < totalSlotDate; i++) {
        elementHidden = (((parseInt(diffStartCurrentDate) + i) > 0) ? ' hidden' : '');
        htmlHeader += '<th class="text-center">...Plan...</th>';
        htmlHeader += '<th class="text-center' + elementHidden + '" id="ngQtySlotH' + (i + 1) + '">NG</th>';
        htmlHeader += '<th class="text-center' + elementHidden + '" id="workerQtySlotH' + (i + 1) + '">Worker No.</th>';
        htmlHeader += '<th class="text-center' + elementHidden + '" id="totalTimeSlotH' + (i + 1) + '">Time</th>';
    }
    htmlHeader += '</tr>';

    htmlHeader += '</thead>';

    return htmlHeader;
}

function genBody(dsFullPlanning, diffStartCurrentDate, totalSlotDate) {
    var htmlBody = '<tbody>';

    var rowSpanSub = 0;
    var dupNo = 0;
    var row;
    var elementDisabled = "";
    var elementDisplayDisabled = "";
    var elementHidden = "";
    var elementStriped = "";
    var striped = false;

    var oDate = null;
    var bgColor = "";

    for (var i = 0; i < dsFullPlanning.length; i++) {
        row = dsFullPlanning[i];
        if (dupNo == rowSpanSub) {
            // Set start
            rowSpanSub = row['duplicatePStock'];
            dupNo = 0;
        }

        htmlBody += '<tr>';
        if (dupNo == 0) {
            htmlBody += '<td class="text-left" rowspan=' + rowSpanSub + '>' + row['JobName'] + '</td>';
            htmlBody += '<td class="text-left" rowspan=' + rowSpanSub + '>' + row['Next_Step_Number'] + '</td>';
            htmlBody += '<td class="text-left" rowspan=' + rowSpanSub + '>' + row['NumberAndDESC'] + '</td>';
            htmlBody += '<td class="text-left" rowspan=' + rowSpanSub + '>' + row['LineName'] + '</td>';
            htmlBody += '<td class="text-left" rowspan=' + rowSpanSub + '>' + row['MachineName'] + '</td>';
            htmlBody += '<td class="text-right" rowspan=' + rowSpanSub + '>' + (row['Operation_Time'] * 60) + '</td>';
        }

        htmlBody += '<td class="text-left bg-success">' + row['SubAssemblyName'] + '</td>';
        htmlBody += '<td class="text-right bg-success">' + row['stock'] + '</td>';

        if (dupNo == 0) {
            htmlBody += '<td class="text-right bg-success" rowspan=' + rowSpanSub + '>' + row['activity_Qty_OK'] + '</td>';
            htmlBody += '<td class="text-right bg-success" rowspan=' + rowSpanSub + '>' + row['Qty_NG'] + '</td>';

            //<!-- Date Slot -->
            oDate = getObjectDate(diffStartCurrentDate);
            striped = false;
            for (var d = 1; d < (totalSlotDate + 1); d++) {
                // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Set planning mode & striped $$$$$$$$$$$$$$$$$
                if ((parseInt(diffStartCurrentDate) + d) > 1) {
                    elementDisabled = '';
                    elementDisplayDisabled = '';
                    elementHidden = ' hidden';
                    elementStriped = (striped ? " warning" : "");
                    attrHidden = ' style="display: none !important; overflow: hidden;"';
                    striped = !striped;
                } else {
                    elementDisabled = ' readonly';
                    elementDisplayDisabled = ' bg-error';
                    elementHidden = '';
                    elementStriped = '';
                    attrHidden = '';
                }

                // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Set holiday on planning mode $$$$$$$$$$$$$$$$$
                if (oDate.getDay() == 0) {
                    bgColor = " bg-primary";
                    elementStriped = "";
                } else {
                    bgColor = "";
                }
                oDate.addDays(+1);

//                htmlBody += '<td class="text-right" rowspan=' + rowSpanSub + '>' + row['LineName'] + '</td>';

                htmlBody += '<td class="text-center' + elementDisplayDisabled + elementStriped + bgColor + '"';
                htmlBody += ' rowspan="' + rowSpanSub + '"';
                htmlBody += ' id="okQtySlot' + d + '" name="okQtySlot[' + d + ']";>';
//                htmlBody += '<input type="text" class="form-control text-right" autocomplete="off"';
//                htmlBody += ' id="okQtySlot' + d + '"';
//                htmlBody += ' name="okQtySlot[' + d + ']";';
//                htmlBody += ' style="font-size: 15px; font-family: monospace;"';
//                htmlBody += ' placeholder="Plan..." value="' + row['OKQtySlot' + d] + '"' + elementDisabled + ' />';
                htmlBody += row['OKQtySlot' + d];
                htmlBody += '</td>';

                htmlBody += '<td class="text-center' + elementDisplayDisabled + elementHidden + bgColor + '"';
                htmlBody += ' rowspan="' + rowSpanSub + '"';
                htmlBody += ' style="font-size: 15px; font-family: monospace;"';
                htmlBody += ' id="ngQtySlot' + d + '">';
                htmlBody += row['NGQtySlot' + d];
                htmlBody += '</td>';

                htmlBody += '<td class="text-center' + elementDisplayDisabled + elementHidden + bgColor + '"';
                htmlBody += ' rowspan="' + rowSpanSub + '"';
                htmlBody += ' id="workerQtySlot' + d + '" name="workerQtySlot[' + d + ']";>';
//                htmlBody += '<input type="text" class="form-control text-right" autocomplete="off"';
//                htmlBody += ' id="workerQtySlot' + d + '"';
//                htmlBody += ' name="workerQtySlot[' + d + ']";';
//                htmlBody += ' style="font-size: 15px; font-family: monospace;"';
//                htmlBody += ' placeholder="Machine..." value="' + row['WorkerQtySlot' + d] + '"' + elementDisabled + ' />';
                htmlBody += row['WorkerQtySlot' + d];
                htmlBody += '</td>';

                htmlBody += '<td class="text-center' + elementDisplayDisabled + elementHidden + bgColor + '"';
                htmlBody += ' rowspan="' + rowSpanSub + '"';
                htmlBody += ' style="font-size: 15px; font-family: monospace;"';
                htmlBody += ' id="totalTimeSlot' + d + '">';
                htmlBody += row['TotalTimeSlot' + d];
                htmlBody += '</td>';
            }

            //<!-- Delay button -->
            htmlBody += '<td class="text-center" rowspan="' + rowSpanSub + '">';
            htmlBody += '<button type="button" class="btn btn-danger" id="delay" value=' + row['StockID'] + '>';
            htmlBody += '<i class="fa fa-plus"></i>';
            htmlBody += '</button>';
            htmlBody += '</td>';
        }
        htmlBody += '</tr>';
        dupNo++;
    }
    htmlBody += '</tbody>';

    return htmlBody;
}
//_______________________________________________Date Caption __________________________________________
function genDateSlotCaption(diffStartCurrentDate) {
    var diffStartCurrentDate = parseInt(diffStartCurrentDate, 10);
    var startDate = new Date().addDays(diffStartCurrentDate);
    var d = new Date(startDate.getTime());
    var strDate = (diffStartCurrentDate == 0) ? 'Today' :
        (days[d.getDay()] + ", " + months[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear());;

    return strDate;
}
//______________________________________________ Object Date ___________________________________________
function getObjectDate(diffStartCurrentDate) {
    var diffStartCurrentDate = parseInt(diffStartCurrentDate, 10);
    var oDate = new Date().addDays(diffStartCurrentDate);

    return oDate;
}