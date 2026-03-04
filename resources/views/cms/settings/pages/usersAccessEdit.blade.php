<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="modal-cms-header">
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">Clinic</label>
        </div>
        <div class="col-sm-10 col-md-10">
            <select name="modalClinics" class="form-control rowview" placeholder="Clinic">
                <option value=""></option>
                @foreach ($clinics as $clinic)
                <option value="{{ $clinic->Id }}">{{ $clinic->Code }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">Role</label>
        </div>
        <div class="col-sm-10 col-md-10">
            <select name="modalRoles" class="form-control rowview" placeholder="Clinic">
                <option value=""></option>
                @foreach ($roles as $role)
                <option value="{{ $role->Id }}">{{ $role->Role }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
    	<div class="table-access col-md-12"></div>
	</div>
</div>

<script>
function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('#select_all').get(0);

   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}

/* Create an array with the values of all the checkboxes in a column */
$.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).prop('checked') ? '1' : '0';
    } );
}
$(document).ready(function(e) {
	var itemSelected = [];
	var rows_selected = [];
	var mless = 300;
	if ($(window).width() < 767) mless = 130;
	
	$html = "<div class=\"table-responsive\"><table id=\"AccessListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\"></th>";
			$html += "<th>Category</th>";
			$html += "<th>Module</th>";
			$html += "<th>Action</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	var data = [];
	
			
	$html +="</tbody></table></div>";
	$('.table-access').append($html);
	
	var table = $('#AccessListTable').DataTable({
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
		{ "data": "AccessID", "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) { return '<input type="checkbox" name="id[]" value="'+data+'">'; } },
		{ "data": "Category", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Module", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Action", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ "data": null,className: 'dt-body-center', 'searchable':false,  "orderable": true, targets: 1, "width":"10px"  },
			{"data": "Code", targets: 2, "width":"100px" },
			{"data": "Description", targets: 3, "width":"250px" },
			{"data": "Category", targets: 4, "width":"100px" }
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
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	// Handle click on checkbox
   $('#AccessListTable tbody').on('click', 'input[type="checkbox"]', function(e){
      var $row = $(this).closest('tr');

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

      if(this.checked){
         $row.addClass('selected');
      } else {
         $row.removeClass('selected');
      }

      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle click on table cells with checkboxes
   $('#AccessListTable').on('click', 'tbody td', function(e){
	   if( $(this).hasClass('control') || $(this).find('input').hasClass('item-notes') ) return false;
      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('#select_all').on('click', function(e){
      if(this.checked){
         $('#AccessListTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
         $('#AccessListTable tbody input[type="checkbox"]:checked').trigger('click');
      }
      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle table draw event
   table.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);
   });

});
</script>