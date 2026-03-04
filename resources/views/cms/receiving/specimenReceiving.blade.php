<!--@extends('app')-->

<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">


@section('style')
<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}

</style>

@endsection
<script src="{{ asset('/js/sweetalert2.all.min.js?0') }}"></script>

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ '/specimen-receiving/specimen' }}" class="waiting">Specimen Receiving <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="body-content row">
        <div class="col-menu-15 table-queue">
            <form id="formReportCreate" class="form-horizontal" role="form" method="POST" action="{{ '/cms/specimen-receiving' }}" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_repType" value="">

                <!-- Info Panel (Left) -->
                <div class="col-md-6">
                    <div class="panel panel-info" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Patient Received</div>

                        <div class="panel-body">
                            <div class="col-menu-6 table-queue" id="patientReceivedTableContainer"></div>

                            
                        </div>
                    </div>
                </div>

                <!-- Success Panel (Right) -->
                <div class="col-md-6">
                    <div class="panel panel-success" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Specimen Received</div>

                        <div class="panel-body">
                            <div class="col-menu-6 table-queue2">

                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Queue No.</th>";
	$html += "<th>Name</th>";
	$html += "<th>Status</th>";
	$html += "<th>Input By</th>";
	$html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $queue !!}; 
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('#patientReceivedTableContainer').append($html);

		
		var table = $('#QueueListTable').DataTable({
			data			: data,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-queueIdQueue', data.IdQueueCMS); },
			columns			: [
			{ "data": null },
			{ "data": "Code", "render": function(data, type, row, meta) { var cssClass = row.QueueStatus == "Adjusting Entry - For Approval" ? 'data-row-2' : 'wrap-row';return '<div class="' + cssClass + '">' + data + '</div>';} },
			{ "data": "FullName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "QueueStatus", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "InputBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }
            ],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
				{ targets: 1, "width":"70px",className: 'data-row' },
				{ targets: 2, "width":"300px" },
				{ targets: 3, "width":"120px" },
				{ targets: 4, "width":"120px" },
                {
                    targets: 5, 
                    visible: false, 
                    orderable: true, 
                    render: function(data, type, row, meta) {
                        return '<div class="wrap-row text-center hidden">' + row.PatientCode + '</div>';
                }
            }
			],
			order			: [ 1, 'asc' ],
			dom:            "frtiS",
			scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-280,
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

    $(window).resize(function() {
        var width = $(window).width();
        
        if (width < 768) {  // Mobile screen
            $('.dataTables_filter input').css('width', '50px');
        } else {  // Larger screens
            
        }
    }).resize();  // Trigger resize to set initial value

    // $('.dataTables_filter').prepend('<button type="button" class="btn btn-success mr-2" id="clearSearch" style="margin-right:5px;">Clear</button>');

    // $('#clearSearch').on('click', function() {
    //     $searchInput.val('');
    //     table.search('').draw();
    //     $searchInput.focus();
    // });

    // Keep focus on search when clicking outside
    $(document).on('click', function(event) {
        if($('.modal:visible').length) return;
        if(!$(event.target).closest('.dataTables_filter input').length) { $searchInput.focus(); }
    });

    // ---- Scanner input handler ----
    let scannerBuffer = '';
    let scannerTimer;
    $(document).on('keydown', function(e){
        if ($('.modal:visible').length > 0) return;  // Prevent scan handling while modal is open
        if (e.key.length === 1) { // printable
            scannerBuffer += e.key.toUpperCase();
            clearTimeout(scannerTimer);
            scannerTimer = setTimeout(() => {
                $searchInput.val(scannerBuffer.toUpperCase()).trigger('input');
                scannerBuffer = '';
            }, 50);
        }
    });

    $searchInput.on('input', function() {
        var input = $(this).val().trim().toUpperCase();
        var matchFound = false;

        if ($('.modal:visible').length > 0) return; // Prevent multiple modals

        table.rows({ search: 'applied' }).every(function() {
            var rowData = this.data();
            if (rowData.PatientCode === input && !matchFound) {
                matchFound = true;
                var $row = $(this.node());
                setTimeout(() => { 
                    if (!$('.modal:visible').length) { // Prevent multiple modals
                        $row.find('.data-row').trigger('click');
                    }
                }, 100);
                return false; 
            }
        });
        
        if (!matchFound && input !== '') {
            Swal.fire({
                icon: 'error',
                title: 'No Code Matched!',
                timer: 1000,
                showConfirmButton: false,
                timerProgressBar: true,
            }).then(() => {
                // clear search after alert
                $searchInput.val('');
                table.search('').draw();
                $searchInput.focus();
            });
        }
    });

		
    $('#QueueListTable').on('click','.data-row',function(e){
        var id = $(this).closest('tr').data('toggle-queueidqueue'); 
        sendout.setTitle('Edit Queue');
        sendout.setType(BootstrapDialog.TYPE_INFO);
        sendout.setData('pageToLoad', '/specimen-receiving/specimen/' + id + '/edit?subgroup=MICROSCOPY,MICROBIOLOGY');
        sendout.setData('queueId', id); // Store queueId in dialog data

        sendout.realize();
        sendout.open();
        e.preventDefault();
    });

    var sendout = new BootstrapDialog({
        message: function(dialog) {
            var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
            var pageToLoad = dialog.getData('pageToLoad');
            $message.load(pageToLoad);
            return $message;
        },
        size: BootstrapDialog.SIZE_WIDE,
        type: BootstrapDialog.TYPE_INFO,
        data: {
            pageToLoad: ''
        },
        animate: false,
        closable: false,
            onshown: function(dialogRef){
            $('#viewbtn').focus();
             },
            onhidden: function(dialogRef) {
                $('#QueueListTable_filter input').val('').trigger('input');
                $('#QueueListTable_filter input').focus();
            },
        buttons: [{
            cssClass: 'btn-default modal-closebtn',
            label: 'Close',
            action: function (modalRef) {
                modalRef.close();
            }
        },
        {
            id: 'btnsave',
            cssClass: 'btn-success hide',
            label: 'Send Out',
            action: function (modalRef) {
                const $btn = $('#btnsave');

                if ($btn.prop('disabled')) return;
                $btn.prop('disabled', true);

                let queueId = sendout.getData('queueId'); // from dialog
                let branch = $('select[name="branch"]').val();
                var queueCode = $('input[name="QueueCode"]').val();

                if (window.checkedRowsData && window.checkedRowsData.length > 0) {
                console.log('Saving Data:', window.checkedRowsData);

                $.ajax({
                    url: `/specimen-receiving/specimen/${queueId}/receive?subgroup=MICROSCOPY,MICROBIOLOGY&type=specimen`,
                    method: 'POST',
                    data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    branch: branch,
                    queueCode: queueCode,
                    rows: window.checkedRowsData
                    },
                    success: function(response) {
                        if (response.redirect) {
                            updateDataTable(response.remainingItems);
                            window.location.href = response.redirect;
                            return;
                        }

                        updateDataTable(response.remainingItems);

                        window.checkedRowsData = [];
                        $('#btnsave').addClass('hide');
                        $('#verifyCodeInput').val('').focus();
                    },
                    error: function(err) {
                    alert('Error saving data, please try again.');
                    console.error(err);
                    },
                    complete: function() {
                        // re-enable after AJAX completes
                        $btn.prop('disabled', false);
                    }
                });
                } else {
                console.log('No rows selected');
                }

                // Optionally don't close modal here, since we want to show updated table
                // modalRef.close();
            }
        }]
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
	$html += "<th>Items</th>";
	$html += "<th>Received By</th>";
	$html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $received !!}; 
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('.table-queue2').append($html);
		
    var table = $('#QueueListTable2').DataTable({
        data			: data,
        autoWidth		: false,
        deferRender		: true,
        createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-queueIdQueue', data.IdQueueCMS); },
        columns			: [
        { "data": null },
        { "data": "QueueCode", "render": function(data, type, row, meta) { var cssClass = row.QueueStatus == "Adjusting Entry - For Approval" ? 'data-row-2' : 'wrap-row';return '<div class="' + cssClass + '">' + data + '</div>';} },
        { "data": "QFullName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
        { "data": "ItemCode", "render": function(data,type,row,meta) { var match = row.ItemDescription.match(/\((.*?)\)/); if (match) { var shortDescription = match[1] } else { var shortDescription = row.ItemDescription.split(' - ')[1] || row.ItemDescription; }  return '<div class="wrap-row">'+data+ ' - ' + shortDescription + '</div>'; } },
        { "data": "ReceivedBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
        responsive		: { details: { type: 'column' } },
        columnDefs		: [
            {className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
            { targets: 1, "width":"70px",className: 'data-row' },
            { targets: 2, "width":"200px" },
            { targets: 3, "width":"150px" },
            { targets: 4, "width":"150px" }
        ],
        order			: [ 1, 'asc' ],
        dom:            "frtiS",
        scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-280,
    });
    $('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
    
});


</script>
@endsection