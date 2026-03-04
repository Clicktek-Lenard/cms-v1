<style>
/* #verifyCodeInput {
    position: absolute !important;
    left: -9999px !important;  */
    /* Or simply use display:none if you want, but sometimes that breaks focus */
    /* display: none; */
/* } */

.swal-btn-custom {
    background-color: #28a745 !important;
    color: white !important;
    font-size: 15px !important;
    padding: 10px 25px !important;
    border-radius: 8px !important;
}

.swal-btn-custom2 {
    background-color: #28a745 !important;
    color: white !important;
    padding: 6px 12px !important;
    font-size: 14px !important;
    border-radius: 4px !important;
}

.swal-btn-custom2.cancel {
    background-color: #6c757d !important;
    color: white !important;
}

.exclamationButtonCell {
    display: flex;
    justify-content: center;
    align-items: center;
}

.bordered-icon {
    display: inline-block;
    border: 1px solid transparent; 
    border-radius: 5px; 
    margin: 2px;
}

.exclamationButton {
    background-color: #f0f0f0; 
    border: 1px solid #ccc;    
    color: gray;              
    display: inline-flex;
    justify-content: center;
    align-items: center;
    padding: 4px 8px;
}

.exclamationButton:hover {
    background-color: #d6d6d6; 
}

.exclamationButton.disabled {
    pointer-events: none; 
    opacity: 0.5; 
}

.orange {
    background-color:rgb(255, 193, 7) !important; /* Orange color */
    color: white !important;              /* White text */
    border-color: rgb(255, 193, 7) !important;     /* Orange border */
}

.orange i {
    color: white !important;              /* Change exclamation icon to white */
}

.red {
    background-color: #dc3545 !important; 
    color: white !important;
    border-color: #dc3545 !important;
}

.red i {
    color: white !important;
}

.info {
    background-color: #17a2b8 !important; /* Bootstrap 'info' blue */
    color: white !important;
    border-color: #17a2b8 !important;
}

.info i {
    color: white !important;
}

.purple {
    background-color: #28a745  !important;
    color: white !important;
    border-color: #28a745  !important;
}

.purple i {
    color: white !important;
}



#scanFeedback {
    position: absolute;
    top: 50%; /* center vertically in scanSection */
    left: 50%; /* center horizontally in scanSection */
    transform: translate(-50%, -50%);
    font-size: 100px;
    font-weight: bold;
    color: green;
    background: rgba(255, 255, 255, 0.95);
    padding: 20px 50px;
    border-radius: 20px;
    display: none;
    z-index: 10;
    box-shadow: 0 0 20px rgba(0,0,0,0.4);
}

</style>

<script src="{{ asset('/js/sweetalert2.all.min.js?0') }}"></script>  

<form id="sendOut" class="form-horizontal" role="form" method="POST" action="" autocomplete="off">
    <input type="hidden" name="_selected" value="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <input type="hidden" id="exclamationState" name="exclamationState" value="0">
    <input type="hidden" id="rejectionReason" value="">

    <div id="hidden-fields-container"></div> <!-- Hidden fields will be appended here -->

    <!-- Wrap the two rows -->
    <div id="scanSection" style="position: relative;">
        <div class="row form-group row-md-flex-center">
            <div class="col-sm-2 col-md-2 pad-0-md text-right-md">
                <label class="bold">Queue No.:</label>
            </div>
            <div class="col-sm-10 col-md-4">
                <input type="text" name="QueueCode" class="form-control" value="{{ $queue->Code }}" readonly>
            </div>
            <div class="col-sm-2 col-md-2 pad-0-md text-right-md">
                <label class="bold">Queue Date:</label>
            </div>
            <div class="col-sm-10 col-md-4">
                <input type="text" class="form-control" value="{{ $queue->Date }}" readonly>
            </div>
        </div>

        <div class="row form-group row-md-flex-center">
            <div class="col-sm-2 col-md-2 pad-0-md text-right-md">
                <label class="bold">Full Name:</label>
            </div>
            <div class="col-sm-10 col-md-4">
                <input type="text" class="form-control" value="{{ $queue->QFullName }}" readonly>
            </div>
            <div class="col-sm-10 col-md-4">
                <!-- <input type="text" id="verifyCodeInput" readonly> -->
                <input type="text" id="verifyCodeInput" readonly style="position:absolute; left:-9999px; top:0; width:1px; height:1px;">
            </div>
        </div>

        <!-- This will be centered only in #scanSection -->
        <div id="scanFeedback">✔</div>
    </div>

    <div class="modal-cms-header">
        <div class="col-menu-15 table-items"></div>
    </div>
</form>



<script>
$(document).ready(function(e) {
    var mless = 400;
    if ($(window).width() < 767) mless = 130;
	$html = "<div class=\"table-responsive\"><table id=\"SendOutTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
    $html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\" ></th>";
	$html += "<th>Item</th>";
	$html += "<th>Description</th>";
    $html += "<th></th>";
	$html += "<th>Notes</th>";
	$html += "<th>Status</th>";
	$html += "</tr>";
	$html +="</thead><tbody>";

	var data = []; 
	var datas = {!! json_encode($items) !!};
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('.table-items').append($html);

	var isMobile = $(window).width() < 768;	
    var table = $('#SendOutTable').DataTable({
        data			: data,
        autoWidth		: false,
        deferRender		: true,
        createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-cislink', data.FileLink).attr('data-toggle-companyCode', data.CompanyCode); },
        columns			: [
        { "data": null },
        { "data": "Id", "orderDataType": "dom-checkbox" , "className": "text-center", "render": function(data,type,row,meta) { return '<input type="checkbox" class="row-checkbox" name="id[]" value="'+data+'">'; } },
        { "data": "ItemCode", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
        { "data": "ItemDescription", "render": function(data,type,row,meta) { var match = data.match(/\((.*?)\)/); if (match) { var shortDescription = match[1] } else {  var shortDescription = data; }  return '<div class="wrap-row">' + shortDescription + '</div>'; } },
        { "data": "", "render": function(data,type,row,meta) { return `<div class="exclamationButtonCell"><div class="exclamationButton wrap-row text-center bordered-icon disabled"><i class="fa fa-exclamation-triangle fa-6" aria-hidden="true"></i></div></div>`;}},		
        { "data": null, "render": function(data, type, row, meta) { return '<textarea name="notes[]" class="form-control" rows="1" placeholder="Enter notes here..." style="font-weight: normal;"></textarea>'; }},
        { "data": "StatusName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }
        ],
        responsive		: { details: { type: 'column' } },
        columnDefs		: [
            {className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
            { targets: 1, "width":"10px",className: 'data-row' },
            { targets: 2, "width":"80px",className: 'data-row' },
            { targets: 3, "width":"200px",className: 'data-name' },
            { targets: 4, "width":"10px" },
            { targets: 5, "width":"150px" },
            { targets: 6, "width":"50px" }
        ],
        order	 : [ 1, 'desc' ],
        dom:     "frtiS",
        scrollY: isMobile ? false : $(window).height() - $('.modal-cms-header').height() - mless,
    });
    $('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});

    const subgroup = $('.add-modal').data('subgroup');

    // $('#verifyCodeInput').focus();

    // $('#verifyCodeInput')
    //     .attr('inputmode', 'none') // No keyboard
    //     .attr('autocomplete', 'off')
    //     .attr('autocorrect', 'off')
    //     .attr('autocapitalize', 'off')
    //     .attr('spellcheck', 'false');

    let barcodeBuffer = '';
    let barcodeTimer;

    $('#verifyCodeInput').focus();

    // Listen globally for key events from the scanner
    $(document).on('keydown', function(e) {
        // Ignore special keys
        if ($('.swal2-container:visible').length > 0) {
            return;
        }
        
        if (e.key.length === 1) {
            barcodeBuffer += e.key.toUpperCase();
            
            // Reset timer
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(() => {
                // When scanner input ends (usually scanner sends all keys fast)
                $('#verifyCodeInput').val(barcodeBuffer.toUpperCase()).trigger('input');
                barcodeBuffer = '';
            }, 50); // 50ms after last character
        }
    });



    $('#verifyCodeInput').on('input', function () {
        if ($('.swal2-container:visible').length > 0) {
            $(this).val('');
            return;
        }

        if ($(document.activeElement).is('textarea')) {
            return; // Skip if the active element is a textarea
        }
        const scannedCode = $(this).val().trim();
        const queueCode = $('input[name="QueueCode"]').val().trim();

        const checkedCount = $('#SendOutTable tbody input[type="checkbox"]:checked').length;

        if (checkedCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Please select at least one item before scanning.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'swal-btn-custom' },
                didClose: () => $('#verifyCodeInput').focus()
            });
            $(this).val('');
            return;
        }

        if (scannedCode === queueCode) {
            $('#scanFeedback')
                .text('✔')
                .css('color', 'green')
                .fadeIn(100)
                .delay(1000)
                .fadeOut(1000);

            parent.$('#btnsave').trigger('click');
            $(this).val('');
        } else if (scannedCode.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Code does not match. Please try again.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'swal-btn-custom' },
                didClose: () => $('#verifyCodeInput').focus()
            });
            $(this).val('');
        }
    });

    let selectAllWasIndeterminate = false;

    $('#select_all').on('mousedown', function () {
        selectAllWasIndeterminate = this.indeterminate;
    });

    $('#select_all').on('click', function () {
        const $allCheckboxes = $('#SendOutTable tbody input[type="checkbox"]');

        if (selectAllWasIndeterminate) {
            // If indeterminate and clicked -> deselect all
            $('#select_all').prop('indeterminate', false).prop('checked', false);
            $allCheckboxes.prop('checked', false).trigger('change');
        } else {
            // Normal behavior
            let isChecked = $(this).prop('checked');
            $allCheckboxes.prop('checked', isChecked).trigger('change');
        }
    });

    // Update select_all state whenever a row checkbox changes
    $('#SendOutTable tbody').on('change', 'input[type="checkbox"]', function () {
        let $allCheckboxes = $('#SendOutTable tbody input[type="checkbox"]');
        let total = $allCheckboxes.length;
        let checked = $allCheckboxes.filter(':checked').length;

        if (checked === 0) {
            $('#select_all').prop('indeterminate', false).prop('checked', false);
        } else if (checked === total) {
            $('#select_all').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select_all').prop('indeterminate', true).prop('checked', false);
        }
    });



    function getCheckedRowsData() {
        let table = $('#SendOutTable').DataTable();
        let checkedData = [];

        $('#SendOutTable tbody tr').each(function () {
            let $row = $(this);
            let isChecked = $row.find('input[type="checkbox"]').prop('checked');

            if (isChecked) {
                let rowData = table.row($row).data();
                let notes = $row.find('textarea[name="notes[]"]').val() || '';
                let isWaived = $row.find('.exclamationButton').hasClass('info'); // Check if waived
                let isReject = $row.find('.exclamationButton').hasClass('red');    // Reject
                let isRefused = $row.find('.exclamationButton').hasClass('orange'); // Refuse
                let isDoneOutside = $row.find('.exclamationButton').hasClass('purple'); // Done Outside
                let rejectReason = $row.find('.exclamationButton').data('reason') || ''; // Reject Reason
                let tubesToSend;

                if (isWaived && subgroup === "HEMATOLOGY") {
                    tubesToSend = { purple: "0", yellow: "0", blue: "0", red: "0", gray: "0" };
                } else {
                    tubesToSend = rowData.selectedTubes ?? rowData.tubes ?? { purple: "0", yellow: "0", blue: "0", red: "0", gray: "0" };
                }

                let rowWithNotes = {
                    ...rowData,
                    Notes: notes,
                    Tubes: tubesToSend,
                    Waived: isWaived ? 1 : 0,
                    Reject: isReject ? 1 : 0,
                    Refused: isRefused ? 1 : 0,
                    DoneOutside: isDoneOutside ? 1 : 0,
                    RejectReason: rejectReason
                };

                console.log('Pushed row data:', rowWithNotes);
                checkedData.push(rowWithNotes);
            }
        });

        if (checkedData.length > 0) {
            parent.checkedRowsData = checkedData;

            if (parent.$('#btnSelectTubes').length) {
                parent.$('#btnSelectTubes').removeClass('hide'); // ✅ Show the button
            }
            if (parent.$('#btnImaging').length) {
                parent.$('#btnImaging').removeClass('hide'); // ✅ Show the button
            }
        } else {
            parent.checkedRowsData = [];

            if (parent.$('#btnSelectTubes').length) {
                parent.$('#btnSelectTubes').addClass('hide'); // ❌ Hide it if exists
            }
            if (parent.$('#btnImaging').length) {
                parent.$('#btnImaging').addClass('hide'); // ❌ Hide it if exists
            }
        }
    }


    $('#SendOutTable tbody').on('click', 'textarea', function(e) {
        e.stopPropagation();
    });


    $('#SendOutTable tbody').on('click', 'tr', function (e) {
        // Only trigger if clicking outside a checkbox
        if (!$(e.target).is('input[type="checkbox"]')) {
            let $checkbox = $(this).find('input[type="checkbox"]');

            if ($checkbox.prop('disabled')) return;

            let isChecked = $checkbox.prop('checked');

            $checkbox.prop('checked', !isChecked).trigger('change');

            if (!isChecked) {
                $('#verifyCodeInput').focus();
            }
        }
    });

    // Focus input when clicking anywhere inside the form except the input itself
    $('#sendOut').on('click', function(e) {
        if (!$(e.target).is('#verifyCodeInput')) {
            $('#verifyCodeInput').focus();
        }
    });

    $('#SendOutTable tbody').on('change', 'input[type="checkbox"]', function () {
        let $this = $(this);
        let $row = $this.closest('tr');

        if ($this.prop('checked')) {
            $row.addClass('selected');
            $row.find('textarea').prop('disabled', false);
            $row.find('.exclamationButton').removeClass('disabled');
        } else {
            $row.removeClass('selected');
            $row.find('textarea').prop('disabled', false);
            $row.find('.exclamationButton').removeClass('orange').addClass('disabled').find('i').css('color', 'gray');
            $row.find('.exclamationButton').removeClass('info').addClass('disabled').find('i').css('color', 'gray');
            $row.find('.exclamationButton').removeClass('red').addClass('disabled').find('i').css('color', 'gray');
            $row.find('.exclamationButton').removeClass('purple').addClass('disabled').find('i').css('color', 'gray');
        }

        getCheckedRowsData();
    });

    $('#SendOutTable tbody').on('input', 'textarea[name="notes[]"]', function () {
        let $row = $(this).closest('tr');
        if ($row.find('input[type="checkbox"]').prop('checked')) {
            getCheckedRowsData();
        }
    });

    // This runs inside modal script
    if (parent.$('#btnSelectTubes').length) {
        parent.$('#btnSelectTubes').off('click').on('click', function () {
            if (!window.checkedRowsData || window.checkedRowsData.length === 0) return;

            Swal.fire({
                title: '<span style="font-size: 16px; font-weight: bold;">Select Tubes Used</span>',
                html: `
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <!-- Purple (EDTA) -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="purple" style="font-size: 16px; width: 120px; text-align: right; color: purple;">Purple (EDTA)</label>
                            <select id="purple" class="swal2-select" 
                                    style="font-size: 16px; padding: 6px; border-radius: 8px; 
                                        font-family: Roboto, Helvetica, Arial, sans-serif; 
                                        margin-left: 10px; border: 2px solid purple;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Yellow -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="yellow" style="font-size: 16px; width: 120px; text-align: right; color: goldenrod;">Yellow</label>
                            <select id="yellow" class="swal2-select" 
                                    style="font-size: 16px; padding: 6px; border-radius: 8px; 
                                        font-family: Roboto, Helvetica, Arial, sans-serif; 
                                        margin-left: 10px; border: 2px solid goldenrod;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Blue -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="blue" style="font-size: 16px; width: 120px; text-align: right; color: blue;">Blue</label>
                            <select id="blue" class="swal2-select" 
                                    style="font-size: 16px; padding: 6px; border-radius: 8px; 
                                        font-family: Roboto, Helvetica, Arial, sans-serif; 
                                        margin-left: 10px; border: 2px solid blue;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Red -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="red" style="font-size: 16px; width: 120px; text-align: right; color: red;">Red</label>
                            <select id="red" class="swal2-select" 
                                    style="font-size: 16px; padding: 6px; border-radius: 8px; 
                                        font-family: Roboto, Helvetica, Arial, sans-serif; 
                                        margin-left: 10px; border: 2px solid red;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Gray (Others) -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="gray" style="font-size: 16px; width: 120px; text-align: right; color: gray;">Gray (Others)</label>
                            <select id="gray" class="swal2-select" 
                                    style="font-size: 16px; padding: 6px; border-radius: 8px; 
                                        font-family: Roboto, Helvetica, Arial, sans-serif; 
                                        margin-left: 10px; border: 2px solid gray;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>

                `,
                confirmButtonText: 'Save',
                showCancelButton: true,
                customClass: { confirmButton: 'swal-btn-custom2', cancelButton: 'swal-btn-custom2 cancel' },
                focusConfirm: false,
                allowOutsideClick: false,
                preConfirm: () => ({
                    purple: document.getElementById('purple').value,
                    yellow: document.getElementById('yellow').value,
                    blue: document.getElementById('blue').value,
                    red: document.getElementById('red').value,
                    gray: document.getElementById('gray').value
                })
            }).then((result) => {
                if (result.isConfirmed) {
                    let table = $('#SendOutTable').DataTable();

                    $('#SendOutTable tbody input[type="checkbox"]:checked').each(function () {
                        let $row = $(this).closest('tr');
                        let rowData = table.row($row).data();

                        // Apply tube data without invalidating/redrawing the row
                        rowData.selectedTubes = result.value;

                        // No need to call .data(rowData).invalidate()
                    });

                    getCheckedRowsData(); // this will read the new tube values and update parent

                    setTimeout(() => {
                        $('#verifyCodeInput').focus();
                    }, 300); 
                }
            });
        });
    }

    // $('#SendOutTable tbody').on('click', '.exclamationButton', function(e) {
    //     if ($(this).hasClass('disabled')) return; // do nothing if disabled
    //     e.stopPropagation();

    //     // const $row = (this).closest('tr');
    //     // const table = $('#SendOutTable').DataTable();
    //     // let rowData = table.row($row).data();

    //     $(this).toggleClass('orange');
    //     if ($(this).hasClass('orange')) {
    //         $(this).find('i').css('color', 'white');

    //         // rowData.tubes = { edta: "0", yellow: "0", other: "0" };
            
    //         $('#exclamationState').val('1');
    //     } else {
    //         $(this).find('i').css('color', 'gray');

    //         $('#exclamationState').val('0');
    //     }

    //     getCheckedRowsData();
    // });

    const rejectionReasons = @json($rejectiondata);

    $('#SendOutTable tbody').on('click', '.exclamationButton', function(e) {
        if ($(this).hasClass('disabled')) return;
        e.stopPropagation();

        const $button = $(this);
        const $icon = $button.find('i');

        // 1. Detect current status from button classes
        const isWaived = $button.hasClass('info');
        const isReject = $button.hasClass('red');
        const isRefuse = $button.hasClass('orange');
        const isDoneOutside = $button.hasClass('purple');

        Swal.fire({
            title: 'Set Item Status',
            html: `
                <div style="display: flex; flex-direction: column; align-items: center; margin-top: 15px; font-size: 16px;">
                    <div style="display: grid; grid-template-columns: repeat(2, max-content); gap: 10px 30px;">
                        <label style="display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" class="statusChk" id="chkWaived"> Waived
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" class="statusChk" id="chkReject"> Reject
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" class="statusChk" id="chkRefuse"> Refused
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; visibility: hidden;">
                            <input type="checkbox" class="statusChk" id="chkDoneOutside"> Done Outside
                        </label>
                    </div>
                    
                    <!-- Dropdown for Reject reasons -->
                    <div id="rejectReasonContainer" style="align-items: center; display: none;">
                        <select id="rejectReasonSelect" class="swal2-select" style="font-size: 16px; padding: 6px; border-radius: 8px; font-family: Roboto, Helvetica, Arial, sans-serif; width: 100%;">
                            <option value="" selected disabled>-- Select reason --</option>
                        </select>
                    </div>
                </div>
            `,
            confirmButtonText: 'Save',
            showCancelButton: true,
            customClass: { 
                confirmButton: 'swal-btn-custom2', 
                cancelButton: 'swal-btn-custom2 cancel' 
            },
            didOpen: () => {
                // Add all reasons dynamically
                rejectionReasons.forEach(reason => {
                    $('#rejectReasonSelect').append(
                        `<option value="${reason.Code}">${reason.Description}</option>`
                    );
                });

                // Set checkbox state
                $('#chkWaived').prop('checked', isWaived);
                $('#chkReject').prop('checked', isReject);
                $('#chkRefuse').prop('checked', isRefuse);
                $('#chkDoneOutside').prop('checked', isDoneOutside);

                // Disable other checkboxes if one is checked
                if (isWaived) {
                    $('#chkReject, #chkRefuse').prop('disabled', true);
                } else if (isReject) {
                    $('#chkWaived, #chkRefuse').prop('disabled', true);
                } else if (isRefuse) {
                    $('#chkWaived, #chkReject').prop('disabled', true);
                }

                // Restore previous reject reason (if any)
                const prevReasonCode = $button.data('reason') || '';
                if (isReject) {
                    $('#rejectReasonContainer').css('display', 'flex');
                    if (prevReasonCode) {
                        $('#rejectReasonSelect').val(prevReasonCode);
                    }
                }

                // Handle checkbox toggle
                $('.statusChk').on('change', function () {
                    if (this.checked) {
                        $('.statusChk').not(this).prop('disabled', true);
                    } else {
                        $('.statusChk').prop('disabled', false);
                    }

                    // Show/hide dropdown based on Reject
                    toggleRejectDropdown($('#chkReject').is(':checked'));

                    // 🔹 Clear validation if reject is unchecked
                    if (!$('#chkReject').is(':checked')) {
                        Swal.resetValidationMessage();
                    }
                });

                function toggleRejectDropdown(show) {
                    if (show) {
                        $('#rejectReasonContainer').css('display', 'flex');
                    } else {
                        $('#rejectReasonContainer').hide();
                        $('#rejectReasonSelect').val('');
                    }
                }
            },

            preConfirm: () => {
                const waived = document.getElementById('chkWaived').checked;
                const reject = document.getElementById('chkReject').checked;
                const refuse = document.getElementById('chkRefuse').checked;
                const doneOutside = document.getElementById('chkDoneOutside').checked;
                const reasonCode = document.getElementById('rejectReasonSelect').value;

                if (reject && !reasonCode) {
                    Swal.showValidationMessage('Please select a reason for rejection.');
                    return false; // prevents modal from closing
                }

                return { waived, reject, refuse, doneOutside, reasonCode };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { waived, reject, refuse, doneOutside, reasonCode } = result.value;

                $button.removeClass('orange red info purple'); // clear all color states
                $icon.css('color', 'gray');
                $('#exclamationState').val('0');
                $('#rejectionReason').val('');

                if (waived) {
                    $button.addClass('info');
                    $icon.css('color', 'white');
                    $('#exclamationState').val('1');
                    $button.data('reason', reasonCode); 
                } else if (reject) {
                    $button.addClass('red');
                    $icon.css('color', 'white');
                    $('#exclamationState').val('2');
                    $button.data('reason', reasonCode);
                    $('#rejectionReason').val(reasonCode); 
                } else if (refuse) {
                    $button.addClass('orange');
                    $icon.css('color', 'white');
                    $('#exclamationState').val('3');
                    $button.data('reason', reasonCode); 
                } else if (doneOutside) {
                    $button.addClass('purple');
                    $icon.css('color', 'white');
                    $('#exclamationState').val('4');
                }

                getCheckedRowsData();
            }
        });
    });

    // if (subgroup === "XRAY") {
    //     console.log('Auto-selecting all for XRAY');
    //     setTimeout(() => {
    //         const $allCheckboxes = $('#SendOutTable tbody input[type="checkbox"]');
    //         $allCheckboxes.prop('checked', true).trigger('change'); // check all rows
    //         $('#select_all').prop('checked', true).prop('indeterminate', false); // update header
    //     }, 300);
    // }

});

function updateDataTable(newData) {
    let table = $('#SendOutTable').DataTable();

    // Clear and add new data
    table.clear();
    table.rows.add(newData);
    table.draw();

    $('#select_all').prop('indeterminate', false).prop('checked', false);
    $('#SendOutTable tbody input[type="checkbox"]').prop('checked', false).trigger('change');
    $('#verifyCodeInput').val('').focus();

    // Clear parent checked data
    parent.checkedRowsData = [];

    // Ensure select_all correctly updates when table is redrawn
    table.on('draw', function () {
        const $allCheckboxes = $('#SendOutTable tbody input[type="checkbox"]');
        const total = $allCheckboxes.length;
        const checked = $allCheckboxes.filter(':checked').length;

        if (checked === 0) {
            $('#select_all').prop('indeterminate', false).prop('checked', false);
        } else if (checked === total) {
            $('#select_all').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select_all').prop('indeterminate', true).prop('checked', false);
        }
    });
}

    
</script>