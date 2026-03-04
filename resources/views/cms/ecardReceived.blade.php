<!--@extends('app')-->
@section('style')
<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ url(session('userBUCode').'/enrollment/cardreceived') }}" class="waiting">Card - Received <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 table-queues">
		</div>
    </div>
    <div class="navbar-fixed-bottom" >
        <div class="col-menu">
            <div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
            	<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
                    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@section('script')
<script>

var addModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_WARNING,
	data: {
		pageToLoad: ''
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},

	{
		id: 'btnSave',
		cssClass: 'btn-primary actionbtn',
		label: 'Received',
		action: function (modalRef){
		
			if( parent.required($('#patientAddModalForm')) ) return false;
	
			var form = $('#patientAddModalForm');
			parent.postData(form.attr('action'),form.serialize(),function($rowId){ 
				// addModal.setTitle("Receiver - View");
				// addModal.setData("pageToLoad", "{{ '/cms/enrollment/pages/enrollmentReceived' }}"+'/'+$rowId+'/edit');
				// addModal.realize();
				addModal.close();
	
			});
		}
	
		
	}]
});

$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Card Number</th>";
	$html += "<th>Date Enrolled</th>";
	$html += "<th>Released To</th>";
	$html += "<th>Date Released</th>";
	$html += "<th>Released By</th>";
	$html += "<th>Received By</th>";
	$html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $data !!}; 
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('.table-queues').append($html);
		
		var table = $('#QueueListTable').DataTable({
			data			: data,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id); },
			columns			: [
			{ "data": null },
			{ "data": "CardNumber", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "DateEnrolled", "render": function(data,type,row,meta) {return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "ReleaseTo", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "DateRelease", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "ReleaseBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "ReceivedBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
				{ targets: 1, "width":"70px", },
				{ targets: 2, "width":"120px" },
				{ targets: 3, "width":"100px" },
				{ targets: 4, "width":"100px" },
				{ targets: 5, "width":"120px" },
				{ targets: 6, "width":"120px" }
			],
			order			: [ 3, 'desc' ],
			dom:            "frtiS",
			scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
		});
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});

		$('#QueueListTable').on('click', '.data-row', function(e) {
			var rowData = table.row($(this).closest('tr')).data();
			var rowId = rowData.Id;  // Extract the TestCode property

			//testcode was pass correctly into url also the groupName = working
			addModal.setTitle("Receiver - View");
			addModal.setData("pageToLoad", "{{ '/cms/enrollment/pages/enrollmentReceived/'}}" + rowId + '/edit');
			addModal.realize();
			addModal.open();
			e.preventDefault();
		});
});

</script>
@endsection