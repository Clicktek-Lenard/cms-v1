
<div class="modal-cms-header">
                        <div class="col-menu-15 table-items">
                        </div>
                </div>



<script>
$(document).ready(function(e)
{
    var mless = 300;
    if ($(window).width() < 767) mless = 130;
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Date & Time</th>";
	$html += "<th>Upload By</th>";
	$html += "<th>Notes</th>";
	$html += "<th>Filename</th>";
	$html += "</tr>";
	$html +="</thead><tbody>";

	var data = []; 
	var datas = {!! $CISData !!}; 
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('.table-items').append($html);
		
		var table = $('#QueueListTable').DataTable({
			data			: data,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-cislink', data.FileLink).attr('data-toggle-companyCode', data.CompanyCode); },
			columns			: [
			{ "data": null },
			{ "data": "SystemUpdateTime", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "UploadBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "Notes", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "FileName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }
			],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
				{ targets: 1, "width":"80px",className: 'data-row' },
				{ targets: 2, "width":"80px",className: 'data-name' },
				{ targets: 3, "width":"200px" },
				{ targets: 4, "width":"150px" }
			],
			order	 : [ 1, 'desc' ],
			dom:     "frtiS",
			scrollY: $(window).height() - $('.modal-cms-header').height() - mless,
		});
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
		
		$('#QueueListTable').on('click','.data-row',function(e){
		waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
		var linkid = $(this).closest('tr').data('toggle-cislink'); 
		var companyCode = $(this).closest('tr').data('toggle-companycode'); 
		var hyperlink = document.createElement('a');
		hyperlink.href = 'http://cms.nwdi.ad/uploads/CIS/'+companyCode+'/'+linkid;
		var mouseEvent = new MouseEvent('click', {
			view: window,
			bubbles: true,
			cancelable: true
		});
		
		//hyperlink.dispatchEvent(mouseEvent);
		//(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
		window.open(hyperlink, '_blank', 'location=yes,height=800,width=6000,scrollbars=yes,status=yes');
		waitingDialog.hide();
		
		
		
	});
});

</script>



