<!--@extends('app')-->

<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">


@section('style')
<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}
.realistic-button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 6px;
    box-shadow: 0 5px #1c7430;
    cursor: pointer;
    transition: all 0.1s ease-in-out;
}

.realistic-button:active {
    box-shadow: 0 2px #1c7430;
    transform: translateY(3px);
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

.red {
    background-color: #dc3545 !important; 
    color: white !important;
    border-color: #dc3545 !important;
}

.red i {
    color: white !important;
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

.item-rejected {
    color: red;
    font-weight: bold;
}


</style>

@endsection
<script src="{{ asset('/js/sweetalert2.all.min.js?0') }}"></script>  

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ '/cms/laboratory-receiving' }}" class="waiting"> Laboratory Receiving <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="body-content row">
        <div class="col-menu-15">
            <!-- Info Panel (Left) -->
            <div class="col-md-3">
                <div class="panel panel-info" style="margin-top:20px;">
                    <div class="panel-heading" style="line-height:12px;">Search Batch</div>
                    <div class="panel-body">
                        <div class="input-group" style="max-width: 100%;">
                            <input type="text" id="batchCode" class="form-control" placeholder="Enter Batch Code" aria-label="Batch Code"/>
                            <span class="input-group-btn">
                                <button id="fetchBatch" class="btn btn-success">Fetch</button>
                            </span>
                        </div>
                        
                       <div class="form-group" style="margin-top: 15px; ">
                            <label for="company" class="bold control-label text-left">Company</label>
                            <input type="text" id="company" class="form-control" disabled placeholder="Company Name" />
                        </div>

                        <div class="form-group" style="margin-top: 15px; ">
                            <label for="company" class="bold control-label text-left">From</label>
                            <input type="text" id="from" class="form-control" disabled placeholder="From" />
                        </div>

                        <div class="form-group" style="margin-top: 15px; ">
                            <label for="company" class="bold control-label text-left">Departure Date Time</label>
                            <input type="text" id="departure" class="form-control" disabled placeholder="Departure Date Time" />
                        </div>

                        <div class="form-group" style="margin-top: 15px; ">
                            <label for="company" class="bold control-label text-left">Arrival Date Time</label>
                            <input type="text" id="arrival" class="form-control" disabled placeholder="Arrival Date Time" />
                        </div>

                        <div class="form-group" style="margin-top: 15px; ">
                            <label for="company" class="bold control-label text-left">Quantity</label>
                            <input type="text" id="quantity" class="form-control" disabled placeholder="Quantity" />
                        </div>

                        <div class="form-group" style="margin-top: 15px; ">
                            <label for="company" class="bold control-label text-left">Status</label>
                            <input type="text" id="status" class="form-control" disabled placeholder="Status" />
                        </div>
                    </div>
                </div>

                    <div style="margin-top: 120px; text-align: center;">
                        <button id="receivedBtn" class="realistic-button" style="display: none;">Receive Specimen/s</button>
                    </div>
            </div>

            <div class="col-md-9">
                <div class="panel panel-success" style="margin-top:20px;">
                    <div class="panel-heading" style="line-height:12px;">To Receive</div>

                    <div class="panel-body">
                        <div class="col-menu-6 table-queue">

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
	<!-- <div class="navbar-fixed-bottom" >
		<div class="col-menu">
			<div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
				<div class="col-xs-12 col-sm-10 col-sm-offset-2 col-md-8 col-md-offset-4 col-lg-6 col-lg-offset-6">
					<button class="summarybtn btn btn-warning col-xs-4 col-sm-4 col-md-4 col-lg-4"  style="border-radius:0px; line-height:29px; visibility:hidden;" type="button"> Summary </button>
				</div>
			</div>
		</div>
	</div> -->
</div>
@endsection 
@section('script')
<script>
$(document).ready(function(e) {
    var $html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
    $html += "<thead>";
    $html += "<tr>";
    $html += "<th></th>";
    $html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\" ></th>";
    $html += "<th>Queue No.</th>";
    $html += "<th>Name</th>";
    $html += "<th>Item Code</th>";
    $html += "<th></th>";
    $html += "<th id='table-tube-header'>Send Out By</th>";
    $html += "<th>Received Time</th>";
    $html += "</tr>";
    $html += "</thead><tbody>";

    // Simulate some mock data for testing
    var data = [];

    $html += "</tbody></table></div>";
    $('.table-queue').append($html);

    var table = $('#QueueListTable').DataTable({
        data: data,
        autoWidth: true,
        deferRender: true,
        createdRow: function(row, data, index) {
            $(row).attr('data-receiving-id', data.ReceivingId);
        },
        columns: [
            { "data": null },
            {
                "data": "Id",
                "orderDataType": "dom-checkbox",
                "className": "text-center",
                "render": function(data, type, row, meta) {
                    if (row.ItemSubGroup === "BLOOD") {
                        if (!row.Items || row.Items.length === 0) {
                            return ""; // no checkbox if no items
                        }
                    }
                    return '<input type="checkbox" name="id[]" value="'+data+'">';
                }
            },
            { "data": "QueueCode", "render": function(data, type, row, meta) { 
                return '<div class="wrap-row">'+data+'</div>'; 
            }},
            { "data": "QFullName", "render": function(data, type, row, meta) { 
                return '<div class="wrap-row">'+data+'</div>'; 
            }},
            { "data": "ItemCode", "render": function(data,type,row,meta) { 
                if (row.ItemSubGroup === "NonBlood") {
                    var match = row.ItemDescription.match(/\((.*?)\)/);
                    var shortDescription = match ? match[1] : (row.ItemDescription.split(' - ')[1] || row.ItemDescription);
                    return '<div class="wrap-row">'+row.ItemCode+ ' - ' + shortDescription + '</div>';
                } else if (row.ItemSubGroup === "BLOOD" && row.Items) {
                    let codes = row.Items.map(i => {
                        let rejected = (row.RejectedItems || []).includes(i.ItemCode);
                        return `<span class="${rejected ? 'item-rejected' : ''}">${i.ItemCode}</span>`;
                    }).join(", ");
                    return '<div class="wrap-row">'+codes+'</div>';
                }
                return '';
            }},
            { "data": "", "render": function(data,type,row,meta) { 
                return `<div class="exclamationButtonCell">
                            <div class="exclamationButton wrap-row text-center bordered-icon disabled">
                                <i class="fa fa-exclamation-triangle fa-6" aria-hidden="true"></i>
                            </div>
                        </div>`;
            }},
            { "data": null, "render": function(data, type, row, meta) { 
                if (row.ItemSubGroup === "NonBlood") {
                    return '<div class="wrap-row">'+(row.SendoutBy || '')+'</div>';
                } else if (row.ItemSubGroup === "BLOOD") {
                    // Convert BloodBatchCode into Tube(s) string
                    let tubeText = '';
                    if (row.BloodBatchCode && row.TubesSent) {
                        let map = { P: "purple", Y: "yellow", B: "blue", R: "red", G: "gray" };
                        let displayMap = { P: "Purple", Y: "Yellow", B: "Blue", R: "Red", G: "Gray" };
                        
                        let key = Object.keys(row.BloodBatchCode)[0]; // like "TST202509180002Y"
                        let tubeKey = key.slice(-1); // last letter (P, Y, etc.)
                        let tubeName = displayMap[tubeKey] || tubeKey; // for showing
                        let tubeProp = map[tubeKey] || tubeKey; // to find in TubesSent
                        
                        // Get count from TubesSent
                        let count = row.TubesSent[tubeProp] ?? 0;
                        
                        tubeText = tubeName + ": " + count;
                    }
                    return '<div class="wrap-row">'+tubeText+'</div>';
                }
                return '';
            }},
            { "data": null, "render": function(data, type, row, meta) { 
                if (row.ItemSubGroup === "NonBlood") {
                    return '<div class="wrap-row">'+(row.DateReceived || '')+'</div>';
                } else if (row.ItemSubGroup === "BLOOD") {
                    // For Blood → still use SendoutDate if exists
                    return '<div class="wrap-row">'+(row.DateReceived || '')+'</div>';
                }
                return '';
            }}
        ],
        responsive: { details: { type: 'column' } },
        columnDefs: [
            { className: 'control', orderable: false, targets: 0, "width": "15px", defaultContent: "" },
            { targets: 1, "width": "5px" },
            { targets: 2, "width": "15px", className: 'data-row' },
            { targets: 3, "width": "200px" },
            { targets: 4, "width": "250px" },
            { targets: 5, "width": "25px" },
            { targets: 6, "width": "120px" },
            { targets: 7, "width": "120px" },
            {
                targets: 8, 
                visible: false, 
                orderable: true, 
                render: function(data, type, row, meta) {
                    return '<div class="wrap-row text-center hidden">' + row.Code + '-' + row.ItemCode +'</div>';
                }
            }
        ],
        order: [1, 'asc'],
        dom: "frtiS",
        scrollY: $(document).height() - $('.navbar-fixed-top.crumb').height() - $('.navbar-fixed-bottom').height() - 280
    });
    
    const $searchInput = $('.dataTables_filter input');
    $searchInput.addClass('form-control search')
        .attr({
            inputmode: 'none',
            readonly: true,
            autocomplete: 'off',
            autocorrect: 'off',
            autocapitalize: 'off',
            spellcheck: 'false',
            placeholder: 'Search'
        });

    // $('.dataTables_filter').prepend('<button type="button" class="btn btn-success mr-2" id="clearSearch" style="margin-right:5px;">Clear</button>');

    // $('#clearSearch').on('click', function() {
    //     $searchInput.val('');
    //     table.search('').draw();
    //     $searchInput.focus();
    // });

    // Keep focus on search when clicking outside
    $(document).on('click', function(event) {
        // Do nothing if any modal is open (SweetAlert2 or Bootstrap modal)
        if ($('.modal:visible').length || $(event.target).closest('.swal2-container').length) return;

        if (!$(event.target).closest('.dataTables_filter input, #batchCode').length) {
            $searchInput.focus();
        }
    });

    let scannerBuffer = '';
    let scannerTimer;

    // Capture scanner keystrokes
    $(document).on('keydown', function(e) {
        if ($('.modal:visible').length > 0) return; // Ignore if modal is open
        if ($(document.activeElement).is('#batchCode')) return; // Ignore if typing batchCode manually

        if (e.key.length === 1) {
            scannerBuffer += e.key;

            clearTimeout(scannerTimer);
            scannerTimer = setTimeout(function() {
                processScan(scannerBuffer);
                scannerBuffer = '';
            }, 100);
        } else if (e.key === 'Enter') {
            processScan(scannerBuffer);
            scannerBuffer = '';
        }
    });

    function processScan(code) {
        if (!code) return;

        let found = false;
        table.rows().every(function() {
            let rowData = this.data();
            let hiddenColValue = rowData.Code + '-' + rowData.ItemCode;

            if (hiddenColValue === code) {
                let $row = $(this.node());

                // Check and highlight
                $row.find('input[type="checkbox"]').prop('checked', true);
                $row.addClass('selected');
                updateSelectAllState();

                getSelectedRows();

                // Scroll row into view at top
                let $container = $('#QueueListTable').parent();
                $container.animate({
                    scrollTop: $container.scrollTop() + $row.position().top
                }, 300);

                found = true;
                return false; // stop loop
            }
        });

        if (!found) {
            Swal.fire({
                icon: 'warning',
                title: 'No match found',
                timer: 1000,
                showConfirmButton: false
            });
        }
    }
    $('#QueueListTable tbody').on('click', 'tr', function (e) {
        // Only trigger if clicking outside a checkbox
        if (!$(e.target).is('input[type="checkbox"]')) {
            let $checkbox = $(this).find('input[type="checkbox"]');

            if ($checkbox.prop('disabled')) return;

            let isChecked = $checkbox.prop('checked');

            $checkbox.prop('checked', !isChecked).trigger('change');
        }
    });

    // --- Manual row checkbox handling ---
    $('#QueueListTable tbody').on('change', 'input[type="checkbox"]', function() {
        let $row = $(this).closest('tr');
        const rowData = table.row($row).data();
        console.log("Checked row full data:", rowData);
        if ($(this).prop('checked')) {
            $row.addClass('selected');
            $row.find('.exclamationButton').removeClass('disabled');
        } else {
            // Row unchecked: Reset button to white and disable, remove reasonCode, deselect the row
            $row.removeClass('selected');
            $row.find('.exclamationButton').removeClass('red').addClass('disabled');  // Set to white and disabled
            const rowData = table.row($row).data();
            rowData.RejectReasonCode = null;
            rowData.RejectReason = null;
            rowData.RejectedItems = [];   // <-- clear rejected ItemCodes
            rowData.TubeCount = null;
            rowData.TubeColor = null;
            table.row($row).data(rowData).invalidate();  // Update the table data
            
            // Make sure the checkbox is unchecked
            $row.find('input[type="checkbox"]').prop('checked', false);
        }

        updateSelectAllState();
        getSelectedRows();
    });

    function updateSelectAllState() {
        let $allCheckboxes = $('#QueueListTable tbody input[type="checkbox"]');
        let total = $allCheckboxes.length;
        let checked = $allCheckboxes.filter(':checked').length;

        let selectAll = $('#select_all').get(0);

        if (checked === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else if (checked === total) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    let selectAllWasIndeterminate = false;

    $('#select_all').on('mousedown', function () {
        selectAllWasIndeterminate = this.indeterminate;
    });

    $('#select_all').on('click', function () {
        const $allCheckboxes = $('#QueueListTable tbody input[type="checkbox"]');

        if (selectAllWasIndeterminate) {
            // was indeterminate → deselect all
        $allCheckboxes.prop('checked', false).closest('tr').removeClass('selected').find('.exclamationButton').addClass('disabled red').each(function() {
            // Reset reasonCode and button state when unchecking
            const row = $(this).closest('tr');
            const rowData = table.row(row).data();
            rowData.RejectReasonCode = null; // clear reasonCode
            table.row(row).data(rowData).invalidate();
        });
            this.checked = false;
            this.indeterminate = false;
        } else if (this.checked) {
            // checked → select all
            $allCheckboxes.prop('checked', true).closest('tr').addClass('selected').find('.exclamationButton').removeClass('disabled');
        } else {
            // unchecked → deselect all
        $allCheckboxes.prop('checked', false).closest('tr').removeClass('selected').find('.exclamationButton').addClass('disabled red').each(function() {
            // Reset reasonCode and button state when unchecking
            const row = $(this).closest('tr');
            const rowData = table.row(row).data();
            rowData.RejectReasonCode = null; // clear reasonCode
            table.row(row).data(rowData).invalidate();
        });
        }

        // reset the tracker
        selectAllWasIndeterminate = false;

        getSelectedRows();
    });

    function getSelectedRows() {
        const count = $('#QueueListTable tbody input[type="checkbox"]:checked').length;

        const $receiveBtn = $('#receivedBtn');

        if (count > 0) {
            const label = count === 1 ? "Receive Specimen" : "Receive Specimens";
            $receiveBtn.text(label).fadeIn(150);
        } else {
            $receiveBtn.fadeOut(150);
        }
    }


    $('#receivedBtn').on('click', function () {
        const selectedRows = $('#QueueListTable tbody input[type="checkbox"]:checked');
        const receivingData = [];

        selectedRows.each(function () {
            const rowData = table.row($(this).closest('tr')).data();

            if (rowData.ItemSubGroup === "BLOOD") {
                // Original total tube count
                const bloodKey = Object.keys(rowData.BloodBatchCode || {})[0]; // e.g. "TST202509180003P"
                const originalTubeCount = parseInt(rowData.BloodBatchCode[bloodKey] || 0);

                // Rejected items
                const rejectItems = rowData.RejectedItems || [];

                // Remaining items (all items minus rejected)
                const remainingItems = (rowData.Items || []).filter(i => !rejectItems.includes(i.ItemCode));

                // Tubes remaining
                const tubesRemaining = Object.values(rowData.TubeCount || {})[0] ?? originalTubeCount;

                receivingData.push({
                    ReceivingBatchCode: rowData.ReceivingBatchCode,
                    QueueCode: rowData.QueueCode,
                    BloodBatchCode: rowData.BloodBatchCode,  // original counts
                    RejectItems: rejectItems,
                    RemainingItems: remainingItems.map(i => i.ItemCode), // array of remaining ItemCodes
                    RejectReason: rowData.RejectReasonCode || null,
                    TubeReject: originalTubeCount - tubesRemaining,
                    TubeRemaining: tubesRemaining,
                    isRejected: rejectItems.length > 0,
                    ItemSubGroup: 'BLOOD',
                });
            } else {
                receivingData.push({
                    ReceivingId: rowData.ReceivingId,
                    IdQueue: rowData.IdQueue,
                    QueueCode: rowData.QueueCode,
                    ItemCode: rowData.ItemCode,
                    isRejected: !!rowData.RejectReasonCode,  // true if rejected
                    RejectReasonCode: rowData.RejectReasonCode || null,
                    ItemSubGroup: rowData.ItemSubGroup,
                });
            }
        });

        $.ajax({
            url: '{{ route("laboratory.receiving.receive") }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                receiving_data: receivingData,
                _token: '{{ csrf_token() }}'
            }),
            success: function (response) {
                if (response.success) {
                    selectedRows.each(function () {
                        table.row($(this).closest('tr')).remove().draw(false);
                    });

                    updateSelectAllState();

                    $('#receivedBtn').hide();

                    Swal.fire({
                        icon: 'success',
                        title: response.message || 'Specimens received',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.message || 'Receiving failed',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Server error',
                    text: 'Please try again later.',
                });
            }
        });
    });

    const rejectionReasons = @json($rejectiondata);
    // Bind click for exclamationButton inside QueueListTable
    $('#QueueListTable tbody').on('click', '.exclamationButton', function(e) {
        if ($(this).hasClass('disabled')) return;
        e.stopPropagation();

        const $button = $(this);
        const $row = $button.closest('tr');
        const rowData = table.row($row).data();

        if ($button.hasClass('red')) {
            rowData.RejectReasonCode = null;
            rowData.RejectReason = null;
            rowData.RejectedItems = [];
            rowData.TubeCount = null;
            rowData.TubeColor = null;

            table.row($row).data(rowData).invalidate();
            const $newButton = $row.find('.exclamationButton');
            $newButton.removeClass('red').removeClass('disabled'); // Remove both red and disabled classes
            $row.find('input[type="checkbox"]').prop('checked', true);
            $row.addClass('selected');

            // console.log("🔄 Reset rowData:", table.row($row).data());
            return;
        }

        // Check if BLOOD or NonBlood
        if (rowData.ItemSubGroup === 'BLOOD') {
            let itemsHtml = '';
            const items = Array.isArray(rowData.Items) ? rowData.Items : [];
            items.forEach((item, idx) => {
                const shortName = getShortDescription(item.ItemDescription);
                itemsHtml += `
                    <div style="display:flex; align-items:center; margin-bottom:3px;">
                        <input type="checkbox" class="blood-item-checkbox" id="bloodItem${idx}" value="${item.ItemCode}" style="margin-right:8px;">
                        <label for="bloodItem${idx}" style="margin-bottom:-5px; font-size:16px; margin-right:8px;">${shortName}&nbsp;&nbsp;</label>
                    </div>
                `;
            });

            let bloodKey = Object.keys(rowData.BloodBatchCode || {})[0];   // e.g. "TST202509170003P"
            let bloodCount = rowData.BloodBatchCode[bloodKey];             // e.g. "2"

            let tubeCode = bloodKey ? bloodKey.slice(-1) : null;

            let tubeColorMap = { P: "Purple", Y: "Yellow", B: "Blue", R: "Red", G: "Gray" };
            let tubeColor = tubeColorMap[tubeCode] || "Unknown";

            let tubesHtml = `
                <div style="display:flex; align-items:center; margin-bottom:3px;">
                    <label style="margin-right:5px;">${tubeColor}</label>
                    <input type="number" min="0" max="${bloodCount}" value="${bloodCount}" class="tube-count-input" style="width:40px; height: 40px; border-radius: 3px;  padding-left:10px; text-align:left; margin-right: 10px; border:1px solid #ccc;" onkeydown="return false;">
                </div>
            `;

            Swal.fire({
                title: '<span style="font-size:16px;font-weight:bold;">Reject Blood Specimen</span>',
                html: `
                    <div style="display:flex; flex-direction:column; gap:10px; font-size:16px;">
                        <div style="display:flex; align-items:center;">
                            <label style="width:120px; text-align:right; margin-right:10px;">Patient Name:</label>
                            <input type="text" class="swal2-input" 
                                value="${rowData.QFullName || ''}" readonly 
                                style="font-size:16px; background-color:#f1f1f1; width:100%;">
                        </div>
                        <div style="display:flex; align-items:flex-start;">
                            <label style="width:120px; margin-right:10px;">Items:</label>
                            <div style="flex:1; display:flex; flex-wrap:wrap; align-items:center; min-height:40px;">
                                ${itemsHtml}
                            </div>
                        </div>
                        <div style="display:flex; align-items:flex-start;">
                            <label style="width:120px; margin-right:10px;">Tube:</label>
                            <div style="flex:1; display:flex; flex-direction:column;">
                                ${tubesHtml}
                            </div>
                        </div>
                        <div style="display:flex; align-items:center;">
                            <label style="width:120px; text-align:right; margin-right:10px;">Reason:</label>
                            <select id="rejectionReason" class="swal2-select" 
                                style="font-size:16px; padding:6px; border-radius:8px; width:100%;">
                                <option value="" disabled selected>Select Reason</option>
                                ${rejectionReasons.map(r => `<option value="${r.Code}">${r.Description.toUpperCase()}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                `,
                width: 500,
                confirmButtonText: 'Save',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                allowOutsideClick: false,
                customClass: { 
                    confirmButton: 'swal-btn-custom2'
                },
                preConfirm: () => {
                    // ✅ Collect checked items
                    let selectedItems = [];
                    document.querySelectorAll('.blood-item-checkbox:checked').forEach(cb => {
                        selectedItems.push(cb.value);
                    });

                    if (selectedItems.length === 0) {
                        Swal.showValidationMessage('Please select at least one item');
                        return false;
                    }

                    // ✅ Tube info
                    let bloodKey = Object.keys(rowData.BloodBatchCode || {})[0];
                    let tubeCode = bloodKey ? bloodKey.slice(-1) : null;
                    let tubeColorMap = { P: "Purple", Y: "Yellow", B: "Blue", R: "Red", G: "Gray" };
                    let tubeColor = tubeColorMap[tubeCode] || "Unknown";

                    // ✅ Tube counts
                    let tubesChanged = false;
                    let tubeCounts = {};
                    document.querySelectorAll('.tube-count-input').forEach(input => {
                        let max = parseInt(input.getAttribute('max'), 10);
                        let val = parseInt(input.value, 10);
                        let color = input.previousElementSibling.textContent.trim();

                        tubeCounts[color] = val;
                        if (val < max) {
                            tubesChanged = true;
                        }
                    });

                    if (!tubesChanged) {
                        Swal.showValidationMessage('Please reduce at least one tube count');
                        return false;
                    }

                    // ✅ Rejection reason
                    let rejectionReason = document.getElementById('rejectionReason')?.value;
                    if (!rejectionReason) {
                        Swal.showValidationMessage('Please select a rejection reason');
                        return false;
                    }

                    // ✅ Return full data
                    return { receivingId: rowData.ReceivingId, items: selectedItems, tubeCounts: tubeCounts, rejectionReason: rejectionReason, tubeColor: tubeColor };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // ✅ Tie back to row (like nonBlood)
                    rowData.RejectReasonCode = result.value.rejectionReason;
                    rowData.RejectedItems = result.value.items;
                    rowData.TubeCount = result.value.tubeCounts;
                    rowData.TubeColor = result.value.tubeColor;

                    // update DataTable row visually
                    table.row($row).data(rowData).invalidate().draw(false);

                    const reasonCode = result.value;

                    // tie reason to the row
                    rowData.RejectReasonCode = reasonCode;
                    table.row($row).data(rowData).invalidate();

                    // mark the button red
                    const $newButton = $row.find('.exclamationButton');
                    $newButton.removeClass('disabled').addClass('red');

                    // tick the checkbox
                    $row.find('input[type="checkbox"]').prop('checked', true);
                    // console.log("📌 Updated rowData now:", table.row($row).data());
                }
            });
        } else {
            // 🟢 NON-BLOOD → existing flow
            Swal.fire({
                title: 'Reject Specimen',
                html: `
                    <div style="display: flex; flex-direction: column; gap: 5px; font-size: 16px;">
                        <div id="rejectReasonContainer" style="align-items: center; display: flex;">
                            <select id="rejectReasonSelect" class="swal2-select" style="font-size: 16px; padding: 6px; border-radius: 8px; font-family: Roboto, Helvetica, Arial, sans-serif; width: 100%;">
                                <option value="" selected disabled>-- Select reason --</option>
                            </select>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Save',
                allowOutsideClick: false,
                showCancelButton: true,
                customClass: { 
                    confirmButton: 'swal-btn-custom2', 
                    cancelButton: 'swal-btn-custom2 cancel' 
                },
                didOpen: () => {
                    rejectionReasons.forEach(reason => {
                        $('#rejectReasonSelect').append(
                            `<option value="${reason.Code}">${reason.Description}</option>`
                        );
                    });
                },
                preConfirm: () => {
                    const reason = $('#rejectReasonSelect').val();
                    if (!reason) {
                        Swal.showValidationMessage('Please select a reason');
                        return false;
                    }
                    return reason;
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const reasonCode = result.value;
                    rowData.RejectReasonCode = reasonCode;
                    table.row($row).data(rowData).invalidate();
                    const $newButton = $row.find('.exclamationButton');
                    $newButton.removeClass('disabled').addClass('red');
                    $row.find('input[type="checkbox"]').prop('checked', true);
                }
            });
        }
    });



    $('#fetchBatch').click(function(e) {
        e.preventDefault();

        let batchCode = $('#batchCode').val().trim();
        if (!batchCode) {
            Swal.fire({
                icon: 'warning',
                title: 'Please enter a batch code',
                timer: 1500,
                showConfirmButton: false,
                timerProgressBar: true,
            });
            return;
        }

        $.ajax({
            url: "{{ route('laboratory.receiving.fetch') }}",
            method: 'GET',
            data: { batch_code: batchCode },
            success: function(response) {
                if (response.success) {

                    let isBlood = false;
                    if (response.tableData.length > 0) {
                        if (response.tableData[0].ItemSubGroup === 'BLOOD') {
                            isBlood = true;
                        } else if (response.tableData[0].Type === 'NonBlood') {
                            isBlood = false;
                        }
                    }

                    // Update the table header dynamically
                    if (isBlood) {
                        $('#table-tube-header').text('Tube(s)');
                    } else {
                        $('#table-tube-header').text('Send Out By');
                    }

                    if (Array.isArray(response.data.company) || response.data.company === null) {
                        $('#company').closest('.form-group').hide();
                    } else {
                        $('#company').val(response.data.company);
                        $('#company').closest('.form-group').show();
                    }
                    $('#from').val(response.data.from);
                    $('#departure').val(response.data.departure_datetime);
                    $('#arrival').val(response.data.arrival_datetime);
                    $('#quantity').val(response.data.quantity);
                    $('#status').val(response.data.status);

                    table.clear().rows.add(response.tableData).draw();

                    const $selectAll = $('#select_all').get(0);
                    if ($selectAll) {
                        $selectAll.checked = false;
                        $selectAll.indeterminate = false;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Batch not found',
                        timer: 1500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                    return;
                }
            },
            error: function(xhr) {
                alert("Error fetching batch details");
            }
        });
    });
});

function getShortDescription(name) {
    if (!name) return '';
    const parenMatch = name.match(/\((.*?)\)/);
    if (parenMatch) {
        let inside = parenMatch[1].trim();
        if (inside.includes('/')) {
            inside = inside.split('/')[0].trim();
        }
        return inside;
    }
    return name.trim();
}
</script>
@endsection