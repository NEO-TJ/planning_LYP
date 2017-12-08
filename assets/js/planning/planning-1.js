// ********************************************** Variable *********************************************
var days = ["Sun", "Mon.", "Tue", "Wed.", "Thu.", "Fri.", "Sat."];
var months = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "June", "July", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."];

var uiChanged = false;
// ************************************************ Event **********************************************
// ------------------------------------------------ Load -----------------------------------------------
$(document).ready(function() {
    $('select#jobID').multiselect({
        header: true,
        noneSelectedText: 'Default selected all job',
        click: function(event, ui) { uiChanged = true; },
        close: function(event, ui) { changeJob(); }
    }).multiselectfilter();

    bindingMultiselect('jobTypeID', 'job type');
    bindingMultiselect('stepID', 'step');

    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

    $('table#planning').floatThead({
        position: 'fixed',
        autoReflow: 'true',
    });
});



//----------------------------------------------- Shift Date -------------------------------------------
$(document).on('click', 'button#next-date', function(e) {
    nextDate();
    document.getElementById("form-search").submit();
});
$(document).on('click', 'button#previous-date', function(e) {
    previousDate();
    document.getElementById("form-search").submit();
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




//------------------------------------------------- Mode ----------------------------------------------
//******************************************** Change job mode ****************************************
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