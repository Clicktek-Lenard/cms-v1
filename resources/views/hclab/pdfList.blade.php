<!--@extends('app')-->
<style>
.dataTables_wrapper .dataTables_info { padding-top: 0 !important;}
</style>

@section('content')

	<div class="container-fluid">
		<div class="navbar-fixed-top crumb" >
			<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li class="active"><a href="{{ url(session('userBUCode').'/hclab') }}" class="waiting">HCLAB - Online PDF from April 1 to July 24 (2022) only<span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
			</div>
		</div>
		<div class="body-content row">
			<div class="col-menu-15 table-queue"></div>
		</div>
		<div class="navbar-fixed-bottom" >
			<div class="col-menu">
				<div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
					<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
					    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
					    <a href="{{ url(session('userBUCode').'/physician/create') }}" class="waiting btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6" style="visibility:hidden; border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Create</a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection	
@section('script')	
<script>
var editModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('pageId')+"/edit";
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ url(session('userBUCode').'/hclab/') }}"
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
		id: 'btnsave',
		cssClass: 'btn-success actionbtn saving',
		label: 'Save'
		
	}]
});




$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Patient Id</th>";
	$html += "<th>Full Name</th>";
	$html += "<th>DOB</th>";
	$html += "<th>OR no.</th>";
	$html += "<th>Transaction</th>";
	$html += "<th>Date</th>";
	$html += "</tr>";
	$html +="</thead><tbody>";

	var data = []; 
	var datas = {!! $pdfData !!}; 
	
	if( typeof(datas.length) === 'undefined')
		data.push(datas);
	else
		data = datas;
	
	$html +="</tbody></table></div>";
	$('.table-queue').append($html);
	
	var table = $('#QueueListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.id); },
		columns			: [
		{ "data": null },
		{ "data": "patient_id", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "FullName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "birthdate", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "or_no", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "trans_no", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data +'</div>'; } },
		{ "data": "created_at", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data +'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ targets: 1, "width":"100px",className: 'data-row' },
			{ targets: 2, "width":"300px" },
			{ targets: 3, "width":"150px" },
			{ targets: 4, "width":"80px" },
			{ targets: 5, "width":"100px" },
			{ targets: 5, "width":"50px" }
		],
		order	 : [ 1, 'asc' ],
		dom:     "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-135,
	});


	$('#QueueListTable').on('click','.data-row',function(e){
		waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
		var id = $(this).closest('tr').data('toggle-queueid'); 
		var hyperlink = document.createElement('a');
		hyperlink.href = 'hclab/'+id;
		var mouseEvent = new MouseEvent('click', {
			view: window,
			bubbles: true,
			cancelable: true
		});
		
		//hyperlink.dispatchEvent(mouseEvent);
		//window.open(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
		window.open(hyperlink, '_blank', 'location=yes,height=800,width=6000,scrollbars=yes,status=yes');
		e.preventDefault();
		waitingDialog.hide();
		
	});
	
	
	
	
	
	
});
</script>
@endsection



