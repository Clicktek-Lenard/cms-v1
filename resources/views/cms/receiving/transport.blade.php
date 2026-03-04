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
                    <li class="active"><a href="{{ '/specimen-receiving/transport' }}" class="waiting">Transport <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="body-content row">
        <div class="col-menu-15 table-queue">
            <form id="formReportCreate" class="form-horizontal" role="form" method="POST" action="{{ '/cms/specimen-receiving' }}" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="transportData" id="transportDataInput">

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
                        <div class="panel-heading" style="line-height:12px;">For Transport</div>

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
    // Get data from controller
    var nonBloodData = Object.values(@json($datas));
    var bloodData = {!! $blood !!};
    // console.log("nonBloodData", nonBloodData);
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
                            <th>${isBloodTable ? 'Tube Color' : 'Item'}</th>
                            <th>Received By</th>
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
                // Attach IDs
                if (!isBloodTable) {
                    $(row)
                        .attr('data-toggle-queueId', rowData.Id)
                        .attr('data-toggle-queueIdQueue', rowData.IdQueueCMS);
                }
            },
            columns: [
                { "data": null },
                { "data": isBloodTable ? "QueueCode" : "Code", "render": function (data, type, row) { var cssClass = row.QueueStatus == "Adjusting Entry - For Approval" ? 'data-row-2' : 'wrap-row'; return `<div class="${cssClass}">${data}</div>`; }},
                { "data": isBloodTable ? "Patient" : "QFullName", "render": data => `<div class="wrap-row">${data}</div>` },
                {
                    "data": isBloodTable ? "TubeColor" : "ItemCode",
                    "render": function (data, type, row) {
                        if (isBloodTable) {
                            const tubeColorMap = { "purple": "EDTA", "yellow": "Yellow", "blue": "Blue", "red": "Red", "gray": "Gray" };

                            let tubeLabel = tubeColorMap[data.toLowerCase()] || data;

                        return `<div class="wrap-row">${tubeLabel}</div>`;
                        } else {
                            const description = row.ItemDescription || '';
                            const match = description.match(/\((.*?)\)/);
                            const shortDescription = match ? match[1] : (description.split(' - ')[1] || description);
                            return `<div class="wrap-row">${row.ItemCode} - ${shortDescription}</div>`;
                        }
                    }
                },
                { "data": isBloodTable ? "ReceivedBy" : "ReceivedBy", "render": function (data) { return `<div class="wrap-row">${data || ''}</div>`; }},
                { "data": null, "visible": false, "render": function (row) { if (isBloodTable) { return `<span style="display:none">${row.QueueCode}|BLOOD</span>`; } else { const hidden1 = row.Code + '-' + row.ItemCode; const hidden2 = row.ItemSubGroup === 'MICROSCOPY' ? row.ItemDescription : row.ItemSubGroup; return `<span style="display:none">${hidden1}|${hidden2}</span>`; }}}
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

    // Create dropdown filter
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

    let tubeFilter = `
    <div class="dataTables_batch_wrapper" style="float:left; margin-left: 5px;">
        <select class="form-control" id="tubeFilter">
            <option value="ALL" selected>ALL</option>
            <option value="EDTA">EDTA</option>
            <option value="yellow">Yellow</option>
            <option value="blue">Blue</option>
            <option value="red">Red</option>
            <option value="gray">Gray</option>
        </select>
    </div>
    `;

    // Insert dropdown before the NonBlood filter
    $('#QueueListTableNonBlood_wrapper .dataTables_filter').before(subgroupFilter);
    $('#QueueListTableBlood_wrapper .dataTables_filter').before(tubeFilter);
    $('#tubeFilter').parent().hide(); // Hide initially

    $('#tubeFilter').on('change', function () {
        const selectedColor = $(this).val();

        if (selectedColor === 'ALL') {
            // Clear any tube filter
            tableBlood.column(3).search('').draw();
        } else {
            // Filter using tube color (case-insensitive match)
            tableBlood.column(3).search(selectedColor, true, false).draw();
        }
    });

    // Map of dropdown options to subgroup filters
    const filterMap = {
        "ALL": null,
        "BLOOD": ["HEMATOLOGY","CHEMISTRY"],
        "URINE": ["URINALYSIS"],
        "FECA": ["FECALYSIS"],
        "LA002": ["LA002"]
    };

    // Dropdown change event
    $('#batchSendDropdown').on('change', function() {
        const value = $(this).val();
        const filterValues = filterMap[value];

        if (value === "BLOOD") {
            // Show BLOOD table, hide NonBlood
            $('.table-queue-nonblood').hide();
            $('.table-queue-blood').show();
            if ($(window).width() <= 767) {
                $searchInput.css('width', '100px');
            }
            // Move the batch dropdown and tube filter to BLOOD table wrapper
            $('#QueueListTableBlood_wrapper .dataTables_filter').before($('#batchSendDropdown').parent());
            $('#QueueListTableBlood_wrapper .dataTables_filter').before($('#tubeFilter').parent());
            $('#tubeFilter').parent().show(); // Show tube filter

        } else {
            // Show NonBlood table, hide BLOOD
            $('.table-queue-blood').hide();
            $('.table-queue-nonblood').show();
            $searchInput.css('width', '');

            // Move the batch dropdown back to NonBlood table wrapper
            $('#QueueListTableNonBlood_wrapper .dataTables_filter').before($('#batchSendDropdown').parent());

            // Hide tube filter
            $('#tubeFilter').parent().hide();

            // Apply filter only for NonBlood
            if (!filterValues) {
                tableNonBlood.column(5).search('').draw();
            } else {
                const regex = filterValues.join('|');
                tableNonBlood.column(5).search(regex, true, false).draw();
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
	$html += "<th>Received By</th>";
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
                    if (row.ItemSubGroup === 'BLOOD') {
                        const tubeColorMap = { "purple": "EDTA", "yellow": "Yellow", "blue": "Blue", "red": "Red", "gray": "Gray" };

                        const label = tubeColorMap[(data || '').toLowerCase()] || data || '';
                        return `<div class="wrap-row">${label}</div>`;
                    } else {
                        var match = (row.ItemDescription || '').match(/\((.*?)\)/);
                        var shortDescription = match ? match[1] : ((row.ItemDescription || '').split(' - ')[1] || row.ItemDescription);
                        return `<div class="wrap-row">${data} - ${shortDescription}</div>`;
                    }
                }
            },
			{ "data": "ReceivedBy", "render": function(data,type,row,meta) {
				return '<div class="wrap-row">'+data+'</div>';
			}}
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
		ordering    : false,
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
			<button class="btn btn-success btn" id="batchSendBtn">Batch Send</button>
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
                title: 'No data to generate Word document.',
                timer: 1000,
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
                text: 'You cannot send BLOOD items together with other specimen types.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal-btn-custom2'
                }
            });
            return; // Prevent form submission
        }
        
        Swal.fire({
            title: 'Generating Word Document...',
            text: 'Please wait while your file is being prepared.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '{{ route("transport.generate.word") }}',
            method: 'POST',
            async: false,
            data: {
                _token: '{{ csrf_token() }}',
                data: tableData
            },
            success: function(response) {
                Swal.close();

                if (response.status === 'success') {
                    // Redirect browser to start the download
                    window.location.href = response.file;

                    // Optional reload after download starts
                    setTimeout(() => location.reload(), 4000);
                } else {
                    Swal.fire('Error', 'Failed to generate Word document.', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Something went wrong.', 'error');
            }
        });
	});
});

$(document).ready(function () {
    var tableNonBlood = $('#QueueListTableNonBlood').DataTable();
    var tableBlood = $('#QueueListTableBlood').DataTable();
    var table2 = $('#QueueListTable2').DataTable();  // For Transport (right)

    // Shared handler for both left tables
    $('#QueueListTableNonBlood, #QueueListTableBlood').on('click', '.data-row', function (e) {
        if ($('#batchSendDropdown').val() === 'ALL') {
            Swal.fire({
                icon: 'warning',
                title: 'Please select a subgroup first!',
                timer: 1000,
                showConfirmButton: false,
                timerProgressBar: true,
            });
            return;
        }

        if ($('#batchSendDropdown').val() === 'BLOOD' && $('#tubeFilter').val() === 'ALL') {
            Swal.fire({
                icon: 'warning',
                title: 'Please select a Tube Color first!',
                timer: 1000,
                showConfirmButton: false,
                timerProgressBar: true,
            });
            return;
        }

        let $row = $(this).closest('tr');
        let sourceTable = $row.closest('table').attr('id') === 'QueueListTableNonBlood' ? tableNonBlood : tableBlood;
        let rowData = sourceTable.row($row).data();

        let isBlood = !!rowData.TubeColor;

        let newRowData = {
            'Id': rowData.Id,
            'Code': rowData.Code || rowData.QueueCode,
            'QFullName': rowData.QFullName || rowData.Patient,
            'ItemCode': isBlood ? (rowData.TubeColor || '') : (rowData.ItemCode || ''),
            'ItemDescription': isBlood ? '' : (rowData.ItemDescription || ''),
            'ReceivedBy': rowData.ReceivedBy,
            'ReceivingId': rowData.ReceivingId,
            'QueueEnItemCode': (rowData.Code || rowData.QueueCode) + '-' + (rowData.ItemCode || rowData.TubeColor),
            'DateReceived': rowData.DateReceived,
            'ItemSubGroup': rowData.ItemSubGroup || (isBlood ? 'BLOOD' : ''),
            'Tubes': rowData.Tubes || rowData.TubeColor,
            'ReceivingBatchCode': rowData.ReceivingBatchCode,
            'LabId': rowData.LabId,
        };

        // 🛑 Check for tube color conflicts FIRST
        if (newRowData.ItemSubGroup === 'BLOOD') {
            let existingTubeColors = table2.rows().data().toArray()
                .filter(d => d.ItemSubGroup === 'BLOOD')
                .map(d => (d.Tubes || '').toLowerCase());

            let currentTubeColor = (newRowData.Tubes || '').toLowerCase();

            let hasDifferentColor = existingTubeColors.length > 0 && existingTubeColors.some(color => color !== currentTubeColor);

            if (hasDifferentColor) {
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot mix tube colors!',
                    text: `You cannot mix "${currentTubeColor.toUpperCase()}" with "${existingTubeColors[0].toUpperCase()}".`,
                    confirmButtonText: 'OK',
                    timer: 1000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });

                return;
            }
        }

        // ✅ Passed the check — now remove and add
        sourceTable.row($row).remove().draw(false);
        table2.row.add(newRowData).draw(false);
        
        scrollToLastRow('QueueListTable2');
        // console.log("Row being sent to right table:", newRowData);

        // 🔄 change header if blood or not
        if (rowData.TubeColor) {
            $('#table2-item-header').text('Tube Color');
        } else {
            $('#table2-item-header').text('Item');
        }

        e.preventDefault();

    });

    // Move from right → left
    $('#QueueListTable2').on('click', '.data-row', function (e) {
        let $row = $(this).closest('tr');
        let rowData = table2.row($row).data();

        // remove from right
        table2.row($row).remove().draw(false);

        if (rowData.ItemSubGroup === 'BLOOD') {
            // Return to BLOOD table
            tableBlood.row.add({
                Id: rowData.Id,
                QueueCode: rowData.Code,
                Patient: rowData.QFullName,
                TubeColor: rowData.ItemCode,
                ReceivedBy: rowData.ReceivedBy,
                ReceivingId: rowData.ReceivingId,
                DateReceived: rowData.DateReceived,
                ItemSubGroup: rowData.ItemSubGroup,
                Tubes: rowData.Tubes,
                ReceivingBatchCode: rowData.ReceivingBatchCode,
                LabId: rowData.LabId,
            }).draw(false);
        } else {
            // Return to NON-BLOOD table
            tableNonBlood.row.add({
                Id: rowData.Id,
                Code: rowData.Code,
                QFullName: rowData.QFullName,
                ItemCode: rowData.ItemCode,
                ItemDescription: rowData.ItemDescription,
                ReceivedBy: rowData.ReceivedBy,
                ReceivingId: rowData.ReceivingId,
                QueueEnItemCode: rowData.QueueEnItemCode,
                DateReceived: rowData.DateReceived,
                ItemSubGroup: rowData.ItemSubGroup,
                Tubes: rowData.Tubes,
                ReceivingBatchCode: rowData.ReceivingBatchCode,
                LabId: rowData.LabId,
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
            // console.log("Refocusing hidden barcode input");
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

function showScanFeedback(symbol, color) {
    $('#scanFeedback')
        .text(symbol)
        .css('color', color)
        .fadeIn(100)
        .delay(800)
        .fadeOut(800);
}

function scrollToLastRow(tableId) {
    let $scrollBody = $('#' + tableId).closest('.dataTables_scrollBody');
    $scrollBody.scrollTop($scrollBody[0].scrollHeight);
}


</script>
@endsection