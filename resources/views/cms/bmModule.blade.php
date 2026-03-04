<!--@extends('app')-->
@section('style')
<style>
.data-row{ 
	color: #337AB7;
	text-decoration: none; 
	cursor:pointer;
	
}

.wrap-row {
    font-weight: bolder;
}
.cms-font
{
    font-weight: bolder;
}
.data-row-2 {
    color: #fc2626;
	text-decoration: none; 
	cursor:pointer;
	font-weight: bold;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ url(session('userBUCode').'/cms/bmmodule') }}" class="waiting">BM Module <span class="badge" style="top:-9px; position:relative;"></span></a></li>
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
			@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
            	<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
                    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
                    <!-- <a href="{{ url(session('userBUCode').'/cms/queue/create') }}" class="waiting btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Create</a> -->

                </div>
			@endif	
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
	$html += "<th>Queue No.</th>";
    $html += "<th>Module</th>";
	$html += "<th>Date</th>";
	$html += "<th>Reasons</th>";
	$html += "<th>Topic</th>";
	$html += "<th>Request By</th>";
	$html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $queue !!}; 
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
			columns			: [
			{ "data": null },
			{ "data": "Code", "render": function(data, type, row, meta) { 
				var cssClass = row.QueueStatus == "Adjusting Entry - For Approval" ? 'data-row-2' : 'wrap-row'; 
				return '<div class="' + cssClass + '">' + data + '</div>';
			} },
            {"data": "QueueStatus","render": function(data, type, row, meta) {
                let statusTable = ''; // Default value
                if (data === 'Adjusting Entry - For Approval') {
                    statusTable = 'AMENDMENT MODULE';
                } else if (data === 'RP - For Approval') {
                    statusTable = 'RP-ENROLLMENT MODULE';
                } else {
                    statusTable = 'UNKNOWN MODULE';
                }
                return `<div class="wrap-row">${statusTable}</div>`;
            } },
			{ "data": "Date", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "AnteDateReason", "render": function(data,type,row,meta) { var dNotes = (data == null)?"":data;  return '<div class="wrap-row">'+dNotes+'</div>'; } },
			{ "data": "QueueStatus", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "InputBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
				{ targets: 1, "width":"80px",className: 'data-row' },
				{ targets: 2, "width":"80px" },
                { targets: 3, "width":"100px" },
				{ targets: 4, "width":"200px" },
				{ targets: 5, "width":"120px" },
				{ targets: 6, "width":"120px" }
			],
			order			: [ 1, 'asc' ],
			dom:            "frtiS",
			scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
		});
        var $searchContainer = $('.dataTables_filter');
        $searchContainer.addClass('form-inline'); 

        var moduleTopic = $('<select id="moduleTopic" class="form-control ml-2"><option value="">ALL</option><option value="AMENDMENT MODULE">AMENDMENT MODULE</option><option value="RP-ENROLLMENT MODULE">RP-ENROLLMENT MODULE</option></select>')
            .css('margin-right', '10px')
            .prependTo($searchContainer)
            .on('change', function() {
                var val = $(this).val();
                localStorage.setItem('moduleTopic', val);
                applyFilter(val);
            });

        var selectedFilter = localStorage.getItem('moduleTopic');
        if (selectedFilter) {
            moduleTopic.val(selectedFilter); // Set the selected value
            applyFilter(selectedFilter); // Apply the filter
        }

        // Function to apply filter
        function applyFilter(value) {
            var columnData = table.column(2).data().toArray();
            table.column(2).search(value).draw();
        }

		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
		// $('.dataTables_filter input').addClass('hide');			
	    // $('.dataTables_filter label').addClass('hide');

		$('#QueueListTable').on('click','.data-row',function(e){
			waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
			var id = $(this).closest('tr').data('toggle-queueid'); 
			var hyperlink = document.createElement('a');
			hyperlink.href = 'queue/'+id+'/edit';
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