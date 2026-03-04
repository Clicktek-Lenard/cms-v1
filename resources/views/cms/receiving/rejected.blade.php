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
                    <li class="active"><a href="{{ '/specimen-receiving/rejected' }}" class="waiting">Rejected Specimens <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="body-content row">
        <div class="col-menu-15 table-queue">
            <form id="formReportCreate" class="form-horizontal" role="form" method="POST" action="{{ '/cms/specimen-receiving' }}" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="receiveSpecimen" id="receiveSpecimenInput">

                <!-- Info Panel (Left) -->
                <div class="col-md-6">
                    <div class="panel panel-info" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Rejected Specimen</div>

                        <div class="panel-body">
                            <div class="col-menu-6 table-queue-nonblood"></div>
                            <div class="col-menu-6 table-queue-blood"></div>
                        </div>
                    </div>
                </div>

                <!-- Success Panel (Right) -->
                <div class="col-md-6">
                    <div class="panel panel-success" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">For Re-receiving</div>

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
$(document).ready(function () {
    // Map rejection reasons
    var reasons = {!! $rejectiondata !!};
    var reasonMap = {};
    reasons.forEach(function (r) {
        reasonMap[r.Code] = r.Description;
    });

    // Get data from controller
    var nonBloodData = Object.values(@json($nonBlood));
    var bloodData = {!! $blood !!};

    // Reusable function to render a DataTable
    function renderQueueTable(container, tableId, data, isBloodTable = false) {
        let html = `
            <div class="table-responsive">
                <table id="${tableId}" class="table table-striped table-hover dt-responsive display" style="width:100%;" cellspacing="0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Queue No.</th>
                            <th>Name</th>
                            <th>${isBloodTable ? 'Item(s)' : 'Item'}</th>
                            <th>Reason</th>
                            <th>Rejected By</th>
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
                { data: isBloodTable ? "QueueCode" : "Code", render: (data, type, row) => `<div class="wrap-row">${data}</div>` },
                { data: isBloodTable ? "Patient" : "QFullName", render: data => `<div class="wrap-row">${data}</div>` },
                {
                    data: isBloodTable ? "Items" : "ItemCode",
                    render: function (data, type, row) {
                        if (isBloodTable) {
                            // Show only ItemCode for blood table
                            let items = (row.Items || []).map(i => i.ItemCode).join(', ');
                            return `<div class="wrap-row">${items}</div>`;
                        } else {
                            return `<div class="wrap-row">${row.ItemCode} - ${row.ItemDescription}</div>`;
                        }
                    }
                },
                {
                    data: isBloodTable ? "RejectReason" : "Reason",
                    render: function (data) {
                        if (Array.isArray(data)) {
                            // Join multiple reasons with comma
                            return `<div class="wrap-row">${data.map(code => reasonMap[code] || code).join(', ')}</div>`;
                        } else {
                            let desc = reasonMap[data] || data || '';
                            return `<div class="wrap-row">${desc}</div>`;
                        }
                    }
                },

                { data: isBloodTable ? "RejectedBy" : "RejectBy", render: d => `<div class="wrap-row">${d || ''}</div>` },
                {
                    data: null,
                    visible: false,
                    render: function (row) {
                        if (isBloodTable) {
                            return `<span>${row.QueueCode}|BLOOD</span>`;
                        } else {
                            const hidden1 = row.Code + '-' + row.ItemCode;
                            const hidden2 = row.ItemSubGroup === 'MICROSCOPY' ? row.ItemDescription : row.ItemSubGroup;
                            return `<span>${hidden1}|${hidden2}</span>`;
                        }
                    }
                }
            ],
            responsive: { details: { type: 'column' } },
            columnDefs: [
                { className: 'control', orderable: false, targets: 0, width: "15px", render: () => '' },
                { targets: 1, width: "70px", className: 'data-row' },
                { targets: 2, width: "200px" },
                { targets: 3, width: "150px" },
                { targets: 4, width: "120px" },
                { targets: 5, width: "120px" },
                { targets: 6, visible: false }
            ],
            order: [1, 'asc'],
            dom: "frtiS",
            scrollY: $(document).height() - $('.navbar-fixed-top.crumb').height() - $('.navbar-fixed-bottom').height() - 280,
        });
    }

    // Render two tables
    var tableNonBlood = renderQueueTable('.table-queue-nonblood', 'QueueListTableNonBlood', nonBloodData, false);
    var tableBlood = renderQueueTable('.table-queue-blood', 'QueueListTableBlood', bloodData, true);

    // Hide blood table by default
    $('.table-queue-blood').hide();

    // Customize search input
    const $searchInput = $('.dataTables_filter input');
    $searchInput.addClass('form-control search').attr({
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

    // Create dropdown filter (NO tubeFilter)
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

    // Map of dropdown options to subgroup filters
    const filterMap = {
        "ALL": null,
        "BLOOD": ["HEMATOLOGY","CHEMISTRY"],
        "URINE": ["URINALYSIS"],
        "FECA": ["FECALYSIS"],
        "LA002": ["LA002"]
    };

    // Dropdown change event
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
                tableNonBlood.column(6).search('').draw();
            } else {
                const regex = filterValues.join('|');
                tableNonBlood.column(6).search(regex, true, false).draw();
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
    $html += "<th id='table2-item-header'>Item</th>";
	$html += "<th id='table2-reject-header'>Reject By</th>";
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
                "data": "ItemCode", 
                "render": function(data, type, row, meta) { 
                    let shortDescription = '';
                    if (row.ItemDescription) {
                        const match = row.ItemDescription.match(/\((.*?)\)/);
                        if (match) { 
                            shortDescription = match[1];
                        } else {
                            shortDescription = row.ItemDescription.split(' - ')[1] || row.ItemDescription;
                        }
                    }
                    return '<div class="wrap-row">' + data + (shortDescription ? ' - ' + shortDescription : '') + '</div>'; 
                } 
            },
            { 
                "data": "RejectBy", 
                "render": function(data, type, row, meta) {
                    if (row.Tubes) {
                        // Only show tubes with count > 0
                        const tubesHtml = Object.entries(row.Tubes)
                            .filter(([key, value]) => parseInt(value) > 0)
                            .map(([key, value]) => {
                                const name = key.charAt(0).toUpperCase() + key.slice(1); // Purple → Purple
                                return `${name}: ${value}`;
                            })
                            .join(', ');
                        return `<div class="wrap-row">${tubesHtml || '-'}</div>`;
                    } else {
                        return `<div class="wrap-row">${data || ''}</div>`;
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
            'max-width': '130px'
        });
    }

	let batchBtnHtml = `
		<div class="dataTables_batch_wrapper" style="float:left; margin-bottom:10px;">
			<button class="btn btn-success btn" id="batchSendBtn">Re-Receive Specimen</button>
		</div>
	`;

	// Insert the button before the filter
	$('#QueueListTable2_wrapper .dataTables_filter').before(batchBtnHtml);

    $('#QueueListTable2_wrapper').on('click', '#batchSendBtn', function (e) {
        e.preventDefault();

        const table2 = $('#QueueListTable2').DataTable();
        const tableData = table2.rows().data().toArray();

        if (tableData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No specimen to receive.',
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
                
                $('#receiveSpecimenInput').val(JSON.stringify(tableData));

                // Temporarily change form action to Word generator
                const form = $('#formReportCreate');
                const originalAction = form.attr('action');
                form.attr('action', '{{ route("receive.specimen") }}');

                // Submit form
                form.submit();

				// 🔄 Force reload after short delay (so file download works first)
				setTimeout(() => {
					location.reload();
				}, 1000);

                // Optional: restore original action if needed
                // form.attr('action', originalAction);
			});
	});
});

$(document).ready(function () {
    var tableNonBlood = $('#QueueListTableNonBlood').DataTable();
    var tableBlood = $('#QueueListTableBlood').DataTable();
    var table2 = $('#QueueListTable2').DataTable();

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

        if (isBlood) {
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

            Swal.fire({
                title: '<span style="font-size: 16px; font-weight: bold;">Select Tubes Used</span>',
                html: `
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="display:flex; align-items:center;">
                            <label style="width:120px; font-size: 16px; text-align:right; margin-right:10px;">Patient Name:</label>
                            <input type="text" class="swal2-input" value="${rowData.Patient}" readonly style="font-size:16px; background-color:#f1f1f1; width:100%;">
                        </div>

                        <div style="display:flex; align-items:center; min-height:60px;">
                            <label style="width:120px; font-size: 16px; text-align:center; margin-right:10px;">Items:</label>
                            <div style="flex:1; display:flex; flex-wrap:wrap; align-items:center; min-height:40px;">
                                ${itemsHtml}
                            </div>
                        </div>
                        <!-- Purple (EDTA) -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="purple" style="font-size: 16px; width: 120px; text-align: right; color: purple;">Purple (EDTA)</label>
                            <select id="purple" class="swal2-select" style="font-size: 16px; padding: 6px; border-radius: 8px; margin-left: 10px; border: 2px solid purple;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Yellow -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="yellow" style="font-size: 16px; width: 120px; text-align: right; color: goldenrod;">Yellow</label>
                            <select id="yellow" class="swal2-select" style="font-size: 16px; padding: 6px; border-radius: 8px; margin-left: 10px; border: 2px solid goldenrod;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Blue -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="blue" style="font-size: 16px; width: 120px; text-align: right; color: blue;">Blue</label>
                            <select id="blue" class="swal2-select" style="font-size: 16px; padding: 6px; border-radius: 8px; margin-left: 10px; border: 2px solid blue;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Red -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="red" style="font-size: 16px; width: 120px; text-align: right; color: red;">Red</label>
                            <select id="red" class="swal2-select" style="font-size: 16px; padding: 6px; border-radius: 8px; margin-left: 10px; border: 2px solid red;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <!-- Gray (Others) -->
                        <div style="display: flex; align-items: end; justify-content: flex-start;">
                            <label for="gray" style="font-size: 16px; width: 120px; text-align: right; color: gray;">Gray (Others)</label>
                            <select id="gray" class="swal2-select" style="font-size: 16px; padding: 6px; border-radius: 8px; margin-left: 10px; border: 2px solid gray;">
                                <option value="0" selected>0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>
                `,
                width: 500,
                showCancelButton: true,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal-btn-custom2',
                    cancelButton: 'swal-btn-custom2 cancel'
                },
                allowOutsideClick: false,
                preConfirm: () => {
                    // Get selected items
                    const selectedItems = [];
                    $('.blood-item-checkbox:checked').each(function() {
                        selectedItems.push($(this).val());
                    });

                    if(selectedItems.length === 0){
                        Swal.showValidationMessage('Please select at least one item');
                        return false;
                    }

                    // Get tube counts
                    const tubes = {
                        purple: parseInt($('#purple').val()) || 0,
                        yellow: parseInt($('#yellow').val()) || 0,
                        blue: parseInt($('#blue').val()) || 0,
                        red: parseInt($('#red').val()) || 0,
                        gray: parseInt($('#gray').val()) || 0
                    };

                    // Validate that at least one tube has a count > 0
                    const totalTubes = Object.values(tubes).reduce((sum, val) => sum + val, 0);
                    if (totalTubes === 0) {
                        Swal.showValidationMessage('Please select at least one tube');
                        return false;
                    }

                    return { selectedItems, tubes };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { selectedItems, tubes } = result.value;

                    // Prepare row data for right table
                    const newRowData = {
                        Id: rowData.Id || '',
                        Code: rowData.QueueCode || '',             // map QueueCode → Code
                        QFullName: rowData.QFullName || rowData.Patient || '', // map Patient → QFullName
                        ItemCode: (selectedItems && selectedItems.length > 0 ? selectedItems.join(', ') : (rowData.Items || []).map(i=>i.ItemCode).join(', ')),
                        ItemDescription: rowData.ItemDescription || '',
                        RejectBy: rowData.RejectedBy || rowData.RejectBy || '',
                        RejectReason: rowData.RejectReason || [],  
                        ItemSubGroup: 'BLOOD',
                        Tubes: tubes,

                        Items: rowData.Items || []
                    };

                    console.log("Moving Blood row ➜ right table:", newRowData);
                    showScanFeedback('✔', 'green');

                    $('#table2-item-header').text('Item(s)');
                    $('#table2-reject-header').text('Tubes Used');

                    // Remove from source and add to right table
                    tableBlood.row($row).remove().draw(false);
                    table2.row.add(newRowData).draw(false);
                }
            });
        } else {
            // 🟢 NonBlood -> move to right table
            const rowData = tableNonBlood.row($row).data();

            // Build new row for right table
            const newRowData = {
                Id: rowData.Id,
                Code: rowData.Code,
                QFullName: rowData.QFullName,
                ItemCode: rowData.ItemCode,
                ItemDescription: rowData.ItemDescription,
                ItemSubGroup: rowData.ItemSubGroup || '',
                Reason: rowData.Reason || [],  
                RejectBy: rowData.RejectBy || '',
                ReceivingId: rowData.ReceivingId
            };

            console.log("Moving NonBlood row ➜ right table:", newRowData);
            showScanFeedback('✔', 'green');
            tableNonBlood.row($row).remove().draw(false);
            table2.row.add(newRowData).draw(false);
        }

        e.preventDefault();
    });

    // Return from right ➜ back to left
    $('#QueueListTable2').on('click', '.data-row', function (e) {
        const $row = $(this).closest('tr');
        const rowData = table2.row($row).data(); // Get the current row data
        const isBlood = rowData.ItemSubGroup === 'BLOOD';

        // Remove from table2
        table2.row($row).remove().draw(false);

        // Restore to original table with original data
        if (isBlood) {
            // Reconstruct Items array from selectedItems or ItemCode string
            const originalItems = (rowData.selectedItems || (rowData.ItemCode?.split(',') || []))
                .map(i => ({ ItemCode: i.trim() }));

            tableBlood.row.add({
                Id: rowData.Id,
                QueueCode: rowData.Code,
                Patient: rowData.QFullName,
                Items: originalItems,
                RejectReason: rowData.RejectReason || [], // preserve RejectReason
                RejectedBy: rowData.RejectBy || '',
                ItemSubGroup: 'BLOOD',
                Items: rowData.Items || [],
            }).draw(false);
        } else {
            // NonBlood, restore all original fields including Reason
            tableNonBlood.row.add({
                Id: rowData.Id,
                Code: rowData.Code,
                QFullName: rowData.QFullName,
                ItemCode: rowData.ItemCode,
                ItemDescription: rowData.ItemDescription,
                Reason: rowData.Reason || [], // preserve Reason
                RejectBy: rowData.RejectBy || '',
                ItemSubGroup: rowData.ItemSubGroup || '',
                ReceivingId: rowData.ReceivingId
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

        // Most scanners send the full code quickly, so wait a little
        clearTimeout(window._scanTimer);
        window._scanTimer = setTimeout(function() {
            $barcodeInput.val(''); // clear after full scan

            let currentFilter = $('#batchSendDropdown').val(); // same dropdown as before?
            if (currentFilter === 'ALL') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select a subgroup first!',
                    timer: 1000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
                return; 
            }

            let found = false;

            if (currentFilter === 'BLOOD') {
                $('#QueueListTableBlood tbody tr').each(function() {
                    let rowData = tableBlood.row(this).data();
                    if (rowData && rowData.QueueCode === scannedCode) {
                        $(this).find('.data-row').trigger('click');
                        found = true;
                        return false; // break loop
                    }
                });
            } else {
                $('#QueueListTableNonBlood tbody tr').each(function() {
                    let rowData = tableNonBlood.row(this).data();
                    if (rowData && rowData.Code === scannedCode) {
                        $(this).find('.data-row').trigger('click');
                        found = true;
                        showScanFeedback('✔', 'green');
                        return false;
                    }
                });
            }

            if (!found) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Code Matched!',
                    timer: 1000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }
        }, 200); // wait 200ms after last char
    });

});

function showScanFeedback(symbol, color) {
    $('#scanFeedback')
        .text(symbol)
        .css('color', color)
        .fadeIn(100)
        .delay(800)
        .fadeOut(800);
}

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