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
                    <li class="active"><a href="{{ url(session('userBUCode').'/cms/settings/users') }}" class="waiting">Users <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 table-queue">
		</div>
    </div>
    <div class="navbar-fixed-bottom" >
        <div class="col-menu">
            <div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
            	<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
                    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
                    <a href="{{ url(session('userBUCode').'/cms/settings/users/create') }}" class="waiting btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Create </a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@section('script')
<script>
$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"UsersListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
			$html += "<thead>";
            $html += "<tr>";
				$html += "<th></th>";
				$html += "<th>Username</th>";
                $html += "<th>Full Name</th>";
                $html += "<th>Email</th>";
				$html += "<th>Default Clinic</th>";
                $html += "<th>Status</th>";
				$html += "<th>Input By</th>";
            $html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $users !!};
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('.table-queue').append($html);
		
		var table = $('#UsersListTable').DataTable({
			data			: data,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-userId', data.Id); },
			columns			: [
			{ "data": null },
			{ "data": "Username", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "FullName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "Email", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "DefaultClinic", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "Status", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "InputBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
				{className: 'all', targets: 1, "width":"70px",className: 'data-row' },
				{className: 'all', targets: 2, "width":"150px" },
				{ targets: 3, "width":"150px" },
				{ targets: 4, "width":"70px" },
				{ targets: 5, "width":"80px" }
			],
			order			: [ 1, 'asc' ],
			dom:            "frtiS",
			scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
		});
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
		
		$('#UsersListTable').on('click','.data-row',function(e){
			waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
			var id = $(this).closest('tr').data('toggle-userid'); 
			var hyperlink = document.createElement('a');
			hyperlink.href = 'users/'+id+'/edit';
			var mouseEvent = new MouseEvent('click', {
				view: window,
				bubbles: true,
				cancelable: true
			});
			
			hyperlink.dispatchEvent(mouseEvent);
			(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
			
			e.preventDefault();
			
			
		});
});

</script>
@endsection