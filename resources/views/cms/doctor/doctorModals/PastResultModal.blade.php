<style>
.bordered-icon {
display: inline-block;
padding: 5px; /* Adjust the padding as needed */
border: 1px solid transparent; /* Adjust the border color and style as needed */
border-radius: 5px; /* Adjust the border radius as needed */
margin: 2px; /* Adjust the margin as needed */
}
.pdfResult{
	background-color: #cfe2f3;
}
.pdfResult:hover{
	background-color: #3d85c6;
}
</style>

<form id="pastqueueEditOr" class="form-horizontal" role="form" method="GET" action=""  autocomplete="off">
    <input type="hidden" name="_selected" value="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

<div class="modal-cms-header">
    <div class="col-menu-15 table-items">
    </div>
</div>

<script>
$(document).ready(function(e) {
	var itemSelected = [];
	var rows_selected = [];
	var mless = 300;
	if ($(window).width() < 767) mless = 130;
	
	
    $html = "<div class=\"table-responsive\"><table id=\"ItemListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th>Code</th>";
			$html += "<th>Description</th>";
			$html += "<th>Status</th>";
            $html += "<th>Action</th>";
			$html += "<th></th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
        var data = {!! json_encode($pastResult) !!};
	
			
	$html +="</tbody></table></div>";
	$('.table-items').append($html);
	
	var table = $('#ItemListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) {
			 $(row).attr('id', data.IdItem);
			 if($.inArray(data.IdItem, rows_selected) !== -1){
				$(row).find('input[type="checkbox"]').prop({'checked':true,'disabled':true});
				$(row).addClass('selected').attr('data-toggle-TransId', data.TransId);
				if( typeof data.Notes !== 'undefind' ){
					$(row).find('input[type="text"]').val(data.Notes);
				}
			 }
			 
		},
		columns			: [
		{ "data": null },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
        { "data": "QueueStatus", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "", "render": function(data,type,row,meta) 
			{  
				var showDiv = "";
					if( row.Status == "500" ||  row.Status == "600" )
					{
						showDiv +=  '<div class="pdfResult wrap-row text-center bordered-icon"><i style="color:blue;cursor:pointer;" class="fa fa-file-pdf-o fa-6" aria-hidden="true"></i></div>';
					}
				return  showDiv; 
			} },
        // { "data": null, "render": function(data, type, row, meta) {
        //     // Adding clickable icons
        //     return '<div class="wrap-row">' +
        //            '<button class="btn btn-primary btn-sm pastresult" data-id="' + row.Id + '">' +
        //            '<i class="fa fa-file-pdf-o fa-xs"></i>' +
        //            '</button> ' +
        //            '</div>';
        // } },
        { "data": null }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ "data": null,className: 'dt-body-center', 'searchable':false,  "orderable": true, targets: 1, "width":"10px"  },
			{"data": "Description", targets: 2, "width":"250px" },
			{"data": "Status", targets: 3, "width":"50px" },
            {"data": "", targets: 4, "width":"50px" },
			{"data": null, targets: 5, "width":"100px" }
		],
		'rowCallback': function(row, data, dataIndex){
			var itemId = $(row).attr('id');
			 if($.inArray(itemId, rows_selected) !== -1){
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
		  },
		order			: [[ 1, 'asc' ],[ 2, 'asc' ]],
		dom:            "frtiS",
		scrollY: $(window).height()-$('.modal-cms-header').height()-mless,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	$("#ItemListTable").on('click','.data-row', function(){
		var id = $(this).closest('tr').data('toggle-id');
		console.log(id);
	})
	
});

</script>