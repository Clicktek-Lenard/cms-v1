@extends('app')
<style>
.dataTables_wrapper .dataTables_info { padding-top: 0 !important;}
</style>

@section('content')
<div class="container-fluid">
    <div class="navbar-fixed-top crumb">
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style="border-radius:0;">
                    <li class="active"><a href="{{ url(session('userBUCode').'/cms/workstation') }}" class="waiting"> Workstation <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>

    <div class="body-content row">
        <!-- <div class="form-group col-md-1" style="left: 246px;">
            <select class="form-control" id="branchSelection" name="branchSelection" style="font-weight: bold; padding-left: 5px;">
                <option value="" disabled selected>ALL</option>
                @foreach ($users as $user) 
                    <option value="{{ $user->Code }}">{{ $user->Code }}</option>
                @endforeach
            </select>
        </div> -->
    
        <div class="col-menu-15 table-queue"></div>

    </div>
    <div class="navbar-fixed-bottom">
        <div class="col-menu">
            <div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
                <div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
                    <a href="" class="btn btn-success col-xs-6 col-sm-6 col-md-6 col-lg-6" style="visibility: hidden; border-radius:0px; line-height:29px;" role="button">
                        <span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Button 1
                    </a>
                    <a href="{{ url(session('userBUCode').'/cms/workstation/create') }}" class="btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-radius:0px; line-height:29px;" role="button">
                        <span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Create
                    </a>
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
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Station Number</th>";
	$html += "<th>IP Address</th>";
	$html += "<th>Department</th>";
    $html += "<th>Location</th>";
	$html += "<th>Branch</th>";
    $html += "<th>Input By</th>";
	$html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $counters !!}; 
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
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id); },
            columns: [
            { "data": null },
            { "data": "StationNumber", "render": function(data, type, row, meta) { return '<div class="wrap-row">'+data+'</div>'; } },
            { "data": "IPv4", "render": function(data, type, row, meta) { return '<div class="wrap-row">'+data+'</div>'; } },
            { "data": "Department", "render": function(data, type, row, meta) { return '<div class="wrap-row">'+data+'</div>'; } },
            { "data": "Location", "render": function(data, type, row, meta) { return '<div class="wrap-row">'+data+'</div>'; } },
            { "data": "IdBU", "render": function(data, type, row, meta) { return '<div class="wrap-row">'+data+'</div>'; } },
            { "data": "InputBy", "render": function(data, type, row, meta) { return '<div class="wrap-row">'+data+'</div>'; } }
            ],
            responsive: { details: { type: 'column' } },
            columnDefs: [
                { className: 'control', orderable: false, targets: 0, "width": "15px", defaultContent: "" },
                { targets: 1, "width": "30px", className: 'data-row' },
                { targets: 2, "width": "100px" },
                { targets: 3, "width": "50px" },
                { targets: 4, "width": "50px" },
                { targets: 5, "width": "50px" },
                { targets: 6, "width":"70px" }
            ],
			order			: [ 1, 'asc' ],
			dom:            "frtiS",
            scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-135,
		});
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});

    $('#QueueListTable').on('click','.data-row',function(e){
        waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
        var id = $(this).closest('tr').data('toggle-queueid'); 
        console.log(id);
        var hyperlink = document.createElement('a');
        hyperlink.href = 'workstation/'+id+'/edit';
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
