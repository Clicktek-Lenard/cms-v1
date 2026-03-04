<!--@extends('app')-->

<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">

@section('style')
<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}

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
.hidden1, .hidden2 {
    display: none;
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
                    <li class="active"><a href="{{ '/specimen-receiving/rejection' }}" class="waiting">Rejection <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="body-content row">
        <div class="col-menu-15 table-queue">
            <form id="formReportCreate" class="form-horizontal" role="form" method="POST" action="{{ '/cms/specimen-receiving' }}" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="rejectedSpecimen" id="rejectedSpecimenInput">

                <!-- Info Panel (Left) -->
                <div class="col-md-6">
                    <div class="panel panel-info" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Specimen Received</div>

                        <div class="panel-body">
                            <div class="col-menu-6 table-queue-nonblood"></div>
                            <div class="col-menu-6 table-queue-blood"></div>
                        </div>
                    </div>
                </div>

                <!-- Success Panel (Right) -->
                <div class="col-md-6">
                    <div class="panel panel-success" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">To Reject</div>

                        <div class="panel-body">
                            <div class="col-menu-6 table-queue2">

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="scanFeedback">✔</div>
    
    <input type="text" id="barcodeScannerInput">
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
let rejectionReasons = {!! json_encode($rejectiondata) !!};

$(document).ready(function(e)
{
    const nonBloodData = @json($nonBlood->values());
    const bloodDataRaw = @json($blood->values());

    const bloodData = bloodDataRaw.map(batch => {
        const tubeCounts = batch.TubeCounts || {};

        function capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
            }

        const tubeSummary = Object.entries(tubeCounts)
        .map(([color, count]) => `${capitalize(color)}: ${count}`)
        .join(', ');

        const itemCodes = (batch.Items || []).map(item => item.ItemCode).join(', ');
        const itemNames = (batch.Items || []).map(item => getShortDescription(item.ItemDescription)).join(', ');

        return {
            QueueCode: batch.QueueCode,
            Patient: batch.Patient,
            TubeColor: tubeSummary,
            ItemCodes: itemCodes,
            ItemNames: itemNames,
            ReceivingBatchCode: batch.ReceivingBatchCode,
        };
    });

    // Reusable DataTable rendering (same as before, already cleaned up)
    function renderQueueTable(container, tableId, data, isBloodTable = false) {
        let html = `
            <div class="table-responsive">
                <table id="${tableId}" class="table table-striped table-hover dt-responsive display" style="width:100%;" cellspacing="0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Queue No.</th>
                            <th>Name</th>
                            <th>${isBloodTable ? 'Tube Color' : 'Item'}</th>
                            <th>${isBloodTable ? 'Items' : 'Received By'}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>`;
        $(container).html(html);

        return $(`#${tableId}`).DataTable({
            data: data,
            autoWidth: false,
            deferRender: true,
            createdRow: function (row, rowData) {
                if (!isBloodTable) {
                    $(row)
                        .attr('data-toggle-queueId', rowData.Id)
                        .attr('data-toggle-queueIdQueue', rowData.IdQueueCMS);
                }
            },
            columns: [
                { data: null },
                {
                    data: isBloodTable ? 'QueueCode' : 'Code',
                    render: function (data, type, row) {
                        var cssClass = row.QueueStatus == "Adjusting Entry - For Approval" ? 'data-row-2' : 'wrap-row';
                        return `<div class="${cssClass}">${data}</div>`;
                    }
                },
                {
                    data: isBloodTable ? 'Patient' : 'QFullName',
                    render: data => `<div class="wrap-row">${data}</div>`
                },
                {
                    data: isBloodTable ? 'TubeColor' : 'ItemCode',
                    render: function (data, type, row) {
                        if (isBloodTable) {
                            return `<div class="wrap-row">${data}</div>`;
                        } else {
                            const description = row.ItemDescription || '';
                            const match = description.match(/\((.*?)\)/);
                            const shortDescription = match ? match[1] : (description.split(' - ')[1] || description);
                            return `<div class="wrap-row">${row.ItemCode} - ${shortDescription}</div>`;
                        }
                    }
                },
                ...(isBloodTable
                    ? [{
                        data: 'ItemCodes',
                        render: function (data) {
                            return `<div class="wrap-row">${data}</div>`;
                        }
                    }]
                    : [{
                        data: 'ReceivedBy',
                        render: function (data) {
                            return `<div class="wrap-row">${data || ''}</div>`;
                        }
                    }]
                ),
                { data: null, visible: false, render: () => '' }
            ],
            responsive: { details: { type: 'column' } },
            columnDefs: [
                { className: 'control', orderable: false, targets: 0, width: "15px", render: () => '' },
                { targets: 1, width: "70px", className: 'data-row' },
                { targets: 2, width: "200px" },
                { targets: 3, width: "150px" },
                { targets: 4, width: "120px" },
                { targets: 5, visible: false }
            ],
            order: [1, 'asc'],
            dom: "frtiS",
            scrollY: $(document).height() - $('.navbar-fixed-top.crumb').height() - $('.navbar-fixed-bottom').height() - 280,
        });
    }

    // Render both tables
    const tableNonBlood = renderQueueTable('.table-queue-nonblood', 'QueueListTableNonBlood', nonBloodData, false);
    const tableBlood = renderQueueTable('.table-queue-blood', 'QueueListTableBlood', bloodData, true);

    // Hide blood by default
    $('.table-queue-blood').hide();

    // Search input UI tweaks
    const $searchInput = $('.dataTables_filter input');
    $searchInput.addClass('form-control search')
        .attr({
            inputmode: 'none',
            autocomplete: 'off',
            autocorrect: 'off',
            autocapitalize: 'off',
            spellcheck: 'false',
            placeholder: 'Search'
        });

    if ($(window).width() <= 767) {
        $('.dataTables_filter label').contents().filter(function() {
            return this.nodeType === 3;
        }).remove();
    }

    $(window).resize(function() {
        if ($(window).width() <= 767) {
            $('.dataTables_filter label').contents().filter(function() {
                return this.nodeType === 3;
            }).remove();
        } else {
            $('.dataTables_filter label').text('Search:');
        }
    });

    // Subgroup filter dropdown
    let subgroupFilter = `
        <div class="dataTables_batch_wrapper" style="float:left;">
            <select class="form-control" id="batchSendDropdown">
                <option value="ALL" selected>ALL</option>
                <option value="BLOOD">BLOOD</option>
                <option value="URINE">URINE</option>
                <option value="FECA">FECAL</option>
                <option value="LA002">PAPS</option>
            </select>
        </div>
    `;

    $('#QueueListTableNonBlood_wrapper .dataTables_filter').before(subgroupFilter);

    // Filter map
    const filterMap = {
        "ALL": null,
        "BLOOD": ["HEMATOLOGY", "CHEMISTRY"],
        "URINE": ["URINALYSIS"],
        "FECA": ["FECALYSIS"],
        "LA002": ["LA002"]
    };

    // Handle dropdown changes
    $('#batchSendDropdown').on('change', function () {
        const value = $(this).val();
        const filterValues = filterMap[value];

        if (value === "BLOOD") {
            $('.table-queue-nonblood').hide();
            $('.table-queue-blood').show();
            $('#QueueListTableBlood_wrapper .dataTables_filter').before($('#batchSendDropdown').parent());
        } else {
            $('.table-queue-blood').hide();
            $('.table-queue-nonblood').show();
            $('#QueueListTableNonBlood_wrapper .dataTables_filter').before($('#batchSendDropdown').parent());

            if (!filterValues) {
                tableNonBlood.column(3).search('').draw();
            } else {
                const regex = filterValues.join('|');
                tableNonBlood.column(3).search(regex, true, false).draw();
            }
        }
    });
});



$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable2\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
    $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Queue No.</th>";
	$html += "<th>Name</th>";
	$html += "<th>Item</th>";
    $html += "<th id='table2-item-header'>Received By</th>";
	$html += "</tr>";
    $html +="</thead><tbody>";
	$html +="</tbody></table></div>";
	$('.table-queue2').append($html);

	var table = $('#QueueListTable2').DataTable({
		data			: [], // Empty initially
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) {
			$(row).attr('data-toggle-queueId', data.Id);
		},
		columns			: [
			{ "data": null },
			{ "data": "Code", "render": function(data, type, row, meta) {
				var cssClass = row.QueueStatus == "Adjusting Entry - For Approval" ? 'data-row-2' : 'wrap-row';
				return '<div class="' + cssClass + '">' + data + '</div>';
			}},
			{ "data": "QFullName", "render": function(data,type,row,meta) {
				return '<div class="wrap-row">'+data+'</div>';
			}},
            { 
                data: "ItemDescription",
                render: function(data, type, row, meta) {
                    if (row.ItemSubGroup === 'BLOOD') {
                        // BLOOD → show items rejected (LH002, LC013)
                        return `<div class="wrap-row">${data || ''}</div>`;
                    } else {
                        // non-BLOOD → show short description
                        var match = (data || '').match(/\((.*?)\)/);
                        var shortDescription = match 
                            ? match[1] 
                            : ((data || '').split(' - ')[1] || data);
                        return `<div class="wrap-row">${row.ItemCode} - ${shortDescription}</div>`;
                    }
                }
            },
            { 
                data: "ItemCode",
                render: function(data, type, row, meta) {
                    if (row.ItemSubGroup === 'BLOOD') {
                        // BLOOD → show reduced tube counts
                        return `<div class="wrap-row">${data || ''}</div>`;
                    } else {
                        // non-BLOOD → keep ReceivedBy column
                        return `<div class="wrap-row">${row.ReceivedBy || ''}</div>`;
                    }
                }
            }
        ],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ targets: 1, "width":"70px",className: 'data-row' },
            { targets: 2, "width":"200px" },
            { targets: 3, "width":"150px" },
			{ targets: 4, "width":"120px" },
            {
                targets: 5, 
                visible: false, 
                orderable: true, 
                render: function(data, type, row, meta) {
                    return '<div class="wrap-row text-center hidden">' + row.Code + '-' + row.ItemCode +'</div>';
                }
            }
		],
		order			: [ 1, 'asc' ],
		dom:            "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-280,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
    if ($(window).width() < 768) {
        $('#QueueListTable2_filter label').contents().filter(function() {
            return this.nodeType === 3; // text node
        }).remove();

        $('#QueueListTable2_filter input').css({
            'max-width': '150px'
        });
    }

	let rejectBtnHtml = `
		<div class="dataTables_batch_wrapper" style="float:left; margin-bottom:10px;">
			<button class="btn btn-warning btn" id="rejectBtn">Reject Specimen</button>
		</div>
	`;

	// Insert the button before the filter
	$('#QueueListTable2_wrapper .dataTables_filter').before(rejectBtnHtml);

    $('#QueueListTable2_wrapper').on('click', '#rejectBtn', function (e) {
        e.preventDefault();

        const table2 = $('#QueueListTable2').DataTable();
        const tableData = table2.rows().data().toArray();

        if (tableData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No specimen to reject!',
                timer: 1500,
                showConfirmButton: false,
                timerProgressBar: true,
            });
            return;
        }

        // Check if BLOOD is mixed with other specimen types
        const hasBlood = tableData.some(d => d.ItemSubGroup === 'BLOOD');
        const hasOther = tableData.some(d => d.ItemSubGroup !== 'BLOOD');

        if (hasBlood && hasOther) {
            // Highlight BLOOD rows in red temporarily
            table2.rows().every(function() {
                const rowData = this.data();
                if (rowData.ItemSubGroup === 'BLOOD') {
                    $(this.node()).css({
                        'background-color': '#f8d7da',
                        'color': '#721c24'
                    }).find('*').css({
                        'color': '#721c24',
                        'background-color': '#f8d7da'
                    });
                }
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Invalid Selection!',
                text: 'You cannot reject BLOOD items together with other specimen types.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal-btn-custom2'
                }
            });
            return; // Prevent form submission
        }

        // Show scan feedback first
        $('#scanFeedback')
            .text('✔')
            .css('color', 'green')
            .fadeIn(100)
            .delay(1500)
            .fadeOut(1500, function () {
                // After animation completes, fill hidden input
                $('#rejectedSpecimenInput').val(JSON.stringify(tableData));

                // Temporarily change form action to Word generator
                const form = $('#formReportCreate');
                const originalAction = form.attr('action');
                form.attr('action', '{{ route("reject.specimen") }}');

                // Submit form
                form.submit();

                // Optional: restore original action if needed
                // form.attr('action', originalAction);
            });
    });
});

$(document).ready(function () {
    var tableNonBlood = $('#QueueListTableNonBlood').DataTable();
    var tableBlood = $('#QueueListTableBlood').DataTable();
    var table2 = $('#QueueListTable2').DataTable(); // right side

    $('#QueueListTableNonBlood, #QueueListTableBlood').on('click', '.data-row', function (e) {
        const selectedType = $('#batchSendDropdown').val();
        if (selectedType === 'ALL') {
            Swal.fire({
                icon: 'warning',
                title: 'Please select a subgroup first!',
                timer: 1000,
                showConfirmButton: false,
                timerProgressBar: true,
            });
            return;
        }

        const $row = $(this).closest('tr');
        const sourceTableId = $row.closest('table').attr('id');
        const sourceTable = sourceTableId === 'QueueListTableNonBlood' ? tableNonBlood : tableBlood;
        const rowData = sourceTable.row($row).data();
        const isBlood = sourceTableId === 'QueueListTableBlood';

        let optionsHtml = `
            <option value="" disabled selected>Select Reason</option>
        ` + rejectionReasons.map(r => 
            `<option value="${r.Code}">${r.Description.toUpperCase()}</option>`
        ).join('');

        // 🟢 Show separate SweetAlert modal depending on type
        if (isBlood) {
            let itemsHtml = '';
            const codes = (rowData.ItemCodes || '').split(',').map(s => s.trim());
            const names = (rowData.ItemNames || '').split(',').map(s => s.trim());

            codes.forEach((code, idx) => {
                const name = names[idx] || '';
                itemsHtml += `
                    <div style="display:flex; align-items:center; margin-bottom:3px;">
                        <input type="checkbox" class="blood-item-checkbox" id="bloodItem${idx}" value="${code}" style="margin-right:8px;">
                        <label for="bloodItem${idx}" style="margin-bottom:-5px; margin-right:8px;">${name}</label>
                    </div>
                `;
            });

            let tubesHtml = '';
            const tubes = rowData.TubeColor.split(','); // e.g., "Purple:3, Yellow:1"
            tubes.forEach((tube, idx) => {
                const [color, count] = tube.split(':').map(s => s.trim());
                tubesHtml += `
                    <div style="display:flex; align-items:center; margin-bottom:3px;">
                        <label style="margin-right:5px;">${color}</label>
                        <button type="button" class="tube-dec" style="width:30px;height:30px;">-</button>
                        <input 
                            type="text" 
                            value="${count}" 
                            data-max="${count}" 
                            class="tube-count-input" 
                            readonly
                            style="width:40px; height:40px; text-align:center; margin:0 5px; border:1px solid #ccc;">
                        <button type="button" class="tube-inc" style="width:30px;height:30px;">+</button>
                    </div>
                `;
            });

            // Events for + / - buttons
            $(document).on('click', '.tube-dec', function () {
                let $input = $(this).siblings('.tube-count-input');
                let val = parseInt($input.val(), 10);
                if (val > 0) $input.val(val - 1);
            });

            $(document).on('click', '.tube-inc', function () {
                let $input = $(this).siblings('.tube-count-input');
                let val = parseInt($input.val(), 10);
                let max = parseInt($input.data('max'), 10);
                if (val < max) $input.val(val + 1);
            });


            Swal.fire({
                title: '<span style="font-size:16px;font-weight:bold;">Reject Blood Specimen</span>',
                html: `
                    <div style="display:flex; flex-direction:column; gap:10px; font-size:16px;">
                        <div style="display:flex; align-items:center;">
                            <label style="width:120px; text-align:right; margin-right:10px;">Patient Name:</label>
                            <input type="text" class="swal2-input" 
                                value="${rowData.Patient || ''}" readonly 
                                style="font-size:16px; background-color:#f1f1f1; width:100%;">
                        </div>
                        <div style="display:flex; align-items:flex-start;">
                            <label style="width:120px; margin-right:10px;">Items:</label>
                            <div style="flex:1; display:flex; flex-wrap:wrap; align-items:center; min-height:40px;">
                                ${itemsHtml || '<span>No items</span>'}
                            </div>
                        </div>
                        <div style="display:flex; align-items:flex-start;">
                            <label style="width:120px; margin-right:10px;">Tubes:</label>
                            <div style="flex:1; display:flex; flex-wrap:wrap;">
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
                width:500,
                showCancelButton:true,
                confirmButtonText:'Save',
                customClass:{
                    confirmButton:'swal-btn-custom2',
                    cancelButton:'swal-btn-custom2 cancel'
                },
                allowOutsideClick:false,
                preConfirm: () => {
                    // get selected items
                    const selectedItems = $('.blood-item-checkbox:checked').map(function(){ return $(this).val(); }).get();
                    if(selectedItems.length === 0){
                        Swal.showValidationMessage('Please select at least one item');
                        return false;
                    }

                    const reasonCode = $('#rejectionReason').val();
                    if (!reasonCode) {
                        Swal.showValidationMessage('Please select a reason');
                        return false;
                    }

                    // ✅ check if tubes counts have changed
                    let tubesChanged = false;
                    const tubeCounts = {};
                    $('.tube-count-input').each(function() {
                        const max = parseInt($(this).data('max'));   // use data-max
                        const val = parseInt($(this).val(), 10) || 0;
                        const color = $(this).closest('div').find('label').text().replace(':','').trim();
                        tubeCounts[color] = val;

                        if (val < max) {
                            tubesChanged = true;
                        }
                    });

                    if (!tubesChanged) {
                        Swal.showValidationMessage('Please reduce at least one tube count');
                        return false;
                    }

                    return { reasonCode, selectedItems, tubeCounts };
                }
            }).then((result) => {
                if(result.isConfirmed){
                    const { reasonCode, selectedItems, tubeCounts } = result.value;
                    moveRowToRight($row, sourceTable, rowData, isBlood, reasonCode, selectedItems, tubeCounts);
                }
            });
        } else {
            Swal.fire({
                title: '<span style="font-size: 16px; font-weight: bold;">Rejection Reason</span>',
                html: `
                    <div style="display: flex; flex-direction: column; gap: 5px; font-size: 16px;">
                        <div style="display: flex; align-items: center;">
                            <label for="patientName" style="width: 120px; text-align: right; margin-right: 10px;">Patient Name:</label>
                            <input type="text" id="patientName" class="swal2-input" value="${rowData.QFullName}" readonly
                                style="font-size: 16px; background-color: #f1f1f1; font-family: Roboto, Helvetica, Arial, sans-serif; width: 100%;">
                        </div>
                        <div style="display: flex; align-items: center;">
                            <label for="itemCode" style="width: 120px; text-align: right; margin-right: 10px;">Item:</label>
                            <input type="text" id="itemCode" class="swal2-input" value="${rowData.ItemCode} - ${rowData.ItemDescription}" readonly
                                style="font-size: 16px; background-color: #f1f1f1; font-family: Roboto, Helvetica, Arial, sans-serif; width: 100%;">
                        </div>
                        <div style="display: flex; align-items: center;">
                            <label for="rejectionReason" style="width: 120px; text-align: right; margin-right: 10px;">Reason:</label>
                            <select id="rejectionReason" class="swal2-select" 
                                    style="font-size: 16px; padding: 6px; border-radius: 8px; font-family: Roboto, Helvetica, Arial, sans-serif; width: 100%;">
                                ${optionsHtml}
                            </select>
                        </div>
                    </div>
                `,
                width: 500,
                showCancelButton: true,
                confirmButtonText: 'Save',
                customClass: {
                    confirmButton: 'swal-btn-custom2',
                    cancelButton: 'swal-btn-custom2 cancel'
                },
                allowOutsideClick: false,
                preConfirm: () => {
                    const reasonCode = $('#rejectionReason').val();
                    if (!reasonCode) {
                        Swal.showValidationMessage('Please select a reason');
                        return false;
                    }
                    return reasonCode; // 👉 will be available in result.value
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const reasonCode = result.value;
                    moveRowToRight($row, sourceTable, rowData, isBlood, reasonCode);
                }
            });
        }

        e.preventDefault();
    });

    // 🟢 Extracted logic for moving row ➜ right-hand table
    function moveRowToRight($row, sourceTable, rowData, isBlood, reasonCode = null, selectedItems = [], tubeCounts = {}) {
        let newRowData;

        if (isBlood) {
            // Parse original counts from rowData.TubeColor (e.g. "Purple: 3, Yellow: 1")
            const origCounts = {};
            (rowData.TubeColor || '').split(',').forEach(part => {
                const pieces = part.split(':').map(s => s.trim());
                if (pieces.length === 2) {
                    origCounts[pieces[0].toLowerCase()] = parseInt(pieces[1]) || 0;
                }
            });

            // tubeCounts currently contains the values from inputs (remaining counts)
            // compute reduced = original - remaining
            const reducedArr = [];
            Object.entries(tubeCounts).forEach(([colorLabel, remainingVal]) => {
                const key = (colorLabel || '').toLowerCase();
                const orig = origCounts[key] || 0;
                const remaining = parseInt(remainingVal) || 0;
                const reduced = orig - remaining;
                if (reduced > 0) {
                    // Keep label same as user sees in modal (capitalized), e.g. "Purple: 1"
                    reducedArr.push(`${colorLabel}: ${reduced}`);
                }
            });

            const reducedTubesStr = reducedArr.join(', ');
            
            newRowData = {
                Id: rowData.Id,
                ReceivingId: rowData.ReceivingId,
                Code: rowData.QueueCode,
                QFullName: rowData.Patient,
                ItemCode: reducedTubesStr,                // 👉 goes to "Reject Tubes" column
                ItemDescription: selectedItems.join(', '), // 👉 goes to "Item" column
                ItemSubGroup: 'BLOOD',
                ReceivingBatchCode: rowData.ReceivingBatchCode || null,
                ReceivedBy: '',
                OriginalTubeColor: rowData.TubeColor,
                OriginalItemDescription: rowData.ItemNames,
                OriginalItemCodes: rowData.ItemCodes,
                RejectionReason: reasonCode || null
            };
        } else {
            newRowData = {
                Id: rowData.Id,
                ReceivingId: rowData.ReceivingId,
                Code: rowData.Code,
                QFullName: rowData.QFullName,
                ItemCode: rowData.ItemCode,
                ItemDescription: rowData.ItemDescription,
                ItemSubGroup: rowData.ItemSubGroup || '',
                ReceivingBatchCode: rowData.ReceivingBatchCode || null,
                ReceivedBy: rowData.ReceivedBy || '',
                RejectionReason: reasonCode || null
            };
        }
        
        console.log("Row being sent to right table:", newRowData);

        sourceTable.row($row).remove().draw(false);
        table2.row.add(newRowData).draw(false);

        // Change header for blood
        $('#table2-item-header').text(isBlood ? 'Reject Tubes' : 'Received By');
    }



    // Return from right ➜ back to left
    $('#QueueListTable2').on('click', '.data-row', function (e) {
        const $row = $(this).closest('tr');
        const rowData = table2.row($row).data();
        const isBlood = rowData.ItemSubGroup === 'BLOOD';

        table2.row($row).remove().draw(false);

        if (isBlood) {
            // Use ORIGINAL values if available
            const tubeColor = rowData.OriginalTubeColor || rowData.ItemCode;
            const itemDesc = rowData.OriginalItemDescription || rowData.ItemDescription;
            const itemCodes = rowData.OriginalItemCodes || rowData.ItemDescription;

            tableBlood.row.add({
                QueueCode: rowData.Code,
                Patient: rowData.QFullName,
                TubeColor: tubeColor,               // reset to original
                ItemNames: itemDesc,                // reset to original
                ItemCodes: itemCodes,
                ReceivingBatchCode: rowData.ReceivingBatchCode,
                ReceivedBy: rowData.ReceivedBy || '',
            }).draw(false);
        } else {
            tableNonBlood.row.add({
                Code: rowData.Code,
                QFullName: rowData.QFullName,
                ItemCode: rowData.ItemCode,
                ItemDescription: rowData.ItemDescription,
                ItemSubGroup: rowData.ItemSubGroup,
                ReceivingBatchCode: rowData.ReceivingBatchCode,
                ReceivedBy: rowData.ReceivedBy || '',
            }).draw(false);
        }

        e.preventDefault();
    });

    // 📌 Barcode scanner handling with input event
    const $barcodeInput = $('#barcodeScannerInput');
    $barcodeInput.attr({
        inputmode: 'none',
        autocomplete: 'off',
        autocorrect: 'off',
        autocapitalize: 'off',
        spellcheck: 'false'
    }).css({
        position: 'absolute',
        left: '-9999px',   // push far left so no scroll
        top: '0',
        width: '1px',
        height: '1px',
        opacity: '0',      // make fully invisible
        border: 'none',
        padding: '0',
        margin: '0'
    });
    $barcodeInput.focus();

    // Always refocus if user clicks somewhere else
    $(document).on('keydown', function(e) {
        const activeTag = document.activeElement.tagName.toLowerCase();
        if (activeTag !== 'input' && activeTag !== 'textarea') {
            $barcodeInput.focus();
        }
    });

    // Handle scanner input
    $barcodeInput.on('input', function() {
        let scannedCode = $barcodeInput.val().trim();

        if (!scannedCode) return;

        // Wait briefly in case scanner sends slowly
        clearTimeout(window._scanTimer);
        window._scanTimer = setTimeout(function() {
            let code = scannedCode.toUpperCase(); // force uppercase
            $barcodeInput.val('');

            // Focus back for next scan
            $barcodeInput.focus();

            // Apply DataTables search first (show match)
            if ($('#batchSendDropdown').val() === 'BLOOD') {
                tableBlood.search(code).draw();
            } else {
                tableNonBlood.search(code).draw();
            }

            // Then, try to trigger a click if exact match found
            let found = false;

            if ($('#batchSendDropdown').val() === 'BLOOD') {
                $('#QueueListTableBlood tbody tr').each(function() {
                    let rowData = tableBlood.row(this).data();
                    if (rowData && rowData.QueueCode.toUpperCase() === code) {
                        $(this).find('.data-row').trigger('click');
                        tableBlood.search('').draw();
                        found = true;
                        scrollToLastRow('QueueListTable2');
                        return false;
                    }
                });
            } else {
                $('#QueueListTableNonBlood tbody tr').each(function() {
                    let rowData = tableNonBlood.row(this).data();
                    if (rowData && rowData.Code.toUpperCase() === code) {
                        $(this).find('.data-row').trigger('click');
                        tableNonBlood.search('').draw();
                        found = true;
                        scrollToLastRow('QueueListTable2');
                        return false;
                    }
                });
            }

            if (!found) {
                    if ($('#batchSendDropdown').val() === 'BLOOD') {
        tableBlood.search('').draw();
    } else {
        tableNonBlood.search('').draw();
    }
    
                Swal.fire({
                    icon: 'error',
                    title: 'No Code Matched!',
                    timer: 1000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }
        }, 200);
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