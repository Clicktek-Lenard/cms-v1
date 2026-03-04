<style>

</style>
<form id="outsidePhysicianModal" class="form-horizontal" role="form" autocomplete="off">
    <input type="hidden" name="_selected" value="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="modal-cms-header">

		<div class="row text-center">		
		</div>
	</div>
</form>
<div class="row">
    <div class="table-item col-md-12"></div>
</div>
<script>
$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"PhysicianListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Last Name</th>";
	$html += "<th>Firstsasas Name</th>";
	$html += "<th>Middle Name</th>";
	$html += "<th>Specialization</th>";
	$html += "<th>PRC No</th>";
	$html += "</tr>";
	$html +="</thead><tbody>";

	$html +="</tbody></table></div>";
	$('.table-item').append($html);
	
	var table = $('#PhysicianListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id); },
		columns			: [
		{ "data": null },
		{ "data": "LastName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "FirstName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "MiddleName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "PRCNo", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ targets: 1, "width":"60px",className: 'data-row' },
			{ targets: 2, "width":"300px" },
			{ targets: 3, "width":"150px" },
			{ targets: 4, "width":"100px" },
			{ targets: 5, "width":"120px" }
		],

		scrollY: "550px",
		scrollCollapse: false,
		paging: false
	});
	
	$('.dataTables_filter input').addClass('hide');			
	$('.dataTables_filter label').addClass('hide');  
	
	$('#PhysicianListTable').on('click','.data-row',function(e){
		waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
		var id = $(this).closest('tr').data('toggle-queueid'); 
		var hyperlink = document.createElement('a');
		hyperlink.href = 'physician/'+id+'/edit';
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