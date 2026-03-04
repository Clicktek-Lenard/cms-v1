<form id="pastqueueEditOr" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
    <input type="hidden" name="_selected" value="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Queue No.:</label>
    </div>
    <div class="col-sm-10 col-md-4">
            <input type="text" class="form-control" value="{{ $PaymentEditOr->QCode }}" readonly>
     
    </div>
    
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Queue Date:</label>
    </div>
    <div class="col-sm-10 col-md-4">
    	<input type="text" class="form-control" value="{{ $PaymentEditOr->Date }}" readonly>
    </div>
    <div class="col-sm-10 col-md-1">
    	<input type="text" class="form-control hidden">
    </div>
</div>

<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Full Name:</label>
    </div>
    <div class="col-sm-10 col-md-10">
    	<input type="text" class="form-control" value="{{ $PaymentEditOr->FullName }}" readonly>
    </div>
    <div class="col-sm-10 col-md-1">
    	<input type="text" class="form-control hidden">
    </div>
</div>


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
			$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\"></th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Description</th>";
			$html += "<th>OR Number</th>";
            $html += "<th>Reasons</th>";
			$html += "<th>Category</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
        var data = {!! json_encode($Trans) !!};
	
			
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
		{ "data": "Id", "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) { return '<input type="checkbox" name="id[]" value="'+data+'">'; } },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": null, "render": function(data,type,row,meta) { return '<div class="wrap-row"><input type="hidden" name="PrevOR" value="'+data.ORNum+'"><input type="text" disabled class="form-control item-notes ORNum" value="'+data.ORNum+'" placeholder="OR Number" name="ORNum"/></div>'; } },
        { "data": null, "render": function(data,type,row,meta) { return '<div class="wrap-row"><input type="hidden" name="TransIds" value="'+data.Id+'"><input type="text" disabled class="form-control item-notes Reasons" value="" placeholder="Reasons" name="Reasons"/></div>'; } },
		{ "data": "PriceGroupItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ "data": null,className: 'dt-body-center', 'searchable':false,  "orderable": true, targets: 1, "width":"10px"  },
			{"data": "Code", targets: 2, "width":"100px" },
			{"data": "Description", targets: 3, "width":"250px" },
            {"data": "Description", targets: 4, "width":"150px" },
			{"data": "Category", targets: 5, "width":"250px" }
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
	// Handle click on checkbox
   $('#ItemListTable tbody').on('click', 'input[type="checkbox"]', function(e){
      var $row = $(this).closest('tr');
      var $ORNumInput = $row.find('.ORNum');
      var $ReasonsInput = $row.find('.Reasons');
      // Get row data
      var data = table.row($row).data();

      // Get row ID
      var rowId = data[0];

      // Determine whether row ID is in the list of selected row IDs 
      var index = $.inArray(rowId, rows_selected);

      // If checkbox is checked and row ID is not in list of selected row IDs
      if(this.checked && index === -1){
         rows_selected.push(rowId);

      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      } else if (!this.checked && index !== -1){
         rows_selected.splice(index, 1);
      }

      if (this.checked) {
        $row.addClass('selected');
        $ORNumInput.prop('disabled', false).prop('required', true);
        $ReasonsInput.prop('required', true).prop('disabled', false);
        parent.$('#btnsave').removeClass('hide');
       } else {
        $row.removeClass('selected');
        $ORNumInput.prop('disabled', true).prop('required', false);
        $ReasonsInput.prop('required', false).prop('disabled', true);
        parent.$('#btnsave').addClass('hide');
      }

      // Update state of "Select all" control
     // updateDataTableSelectAllCtrl(table);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle click on table cells with checkboxes
   $('#ItemListTable').on('click', 'tbody td', function(e){
	   if( $(this).hasClass('control') || $(this).find('input').hasClass('item-notes') ) return false;
      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('#select_all').on('click', function(e){
      if(this.checked){
         $('#ItemListTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
         $('#ItemListTable tbody input[type="checkbox"]:checked').trigger('click');
      }
      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle table draw event
//    table.on('draw', function(){
//       // Update state of "Select all" control
//       updateDataTableSelectAllCtrl(table);
//    });
});

</script>