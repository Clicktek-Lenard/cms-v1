<form id="pastqueueEditOr" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
    <input type="hidden" name="_selected" value="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="modal-cms-header">
        <div class="col-menu-15 table-items">
        </div>
    </div>
</form>
<script>
$(document).ready(function(e) {
	var itemSelected = [];
	var rows_selected = [];
	var mless = 300;
	if ($(window).width() < 767) mless = 130;
	$.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).prop('checked') ? '1' : '0';
    } );
}
	
    $html = "<div class=\"table-responsive\"><table id=\"ItemListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\"></th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Description</th>";
         $html += "<th>active</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";

      var datas = {!! json_encode($physicianorder) !!};
var data = {!! json_encode($data) !!};

// Append the table
$html += "</tbody></table></div>";
$('.table-items').append($html);

// Add a `selected` flag to each item for sorting
data = data.map(item => {
    item.selected = datas.some(orderItem => orderItem.ItemCode === item.ItemCode) ? 1 : 0;
    return item;
});

// Initialize the DataTable
var table = $('#ItemListTable').DataTable({
    data: data,
    autoWidth: false,
    deferRender: true,
    createdRow: function (row, data, index) {
        $(row).attr('id', data.IdItem);

        // Check if the row should be marked as selected
        if (data.selected) {
            $(row).find('input[type="checkbox"]').prop({ 'checked': true, 'disabled': false });
            $(row).addClass('selected').attr('data-toggle-TransId', data.TransId);

            if (typeof data.Notes !== 'undefined') {
                $(row).find('input[type="text"]').val(data.Notes);
            }
        }
    },
    columns: [
        { "data": null },
        { 
            "data": "ItemCode", 
            "render": function (data, type, row) { 
                return '<input type="checkbox" name="id[]" value="' + data + '"' + (row.selected ? ' checked' : '') + '>'; 
            } 
        },
        { 
            "data": "ItemCode", 
            "render": function (data) { 
                return '<div class="wrap-row">' + data + '</div>'; 
            } 
        },
        { 
            "data": "Description", 
            "render": function (data) { 
                return '<div class="wrap-row">' + data + '</div>'; 
            } 
        },
        { 
            "data": "selected",
            "visible": false
        },
    ],
    responsive: { details: { type: 'column' } },
    columnDefs: [
        { "data": null, className: 'control', orderable: false, targets: 0, "width": "15px", defaultContent: "" },
        { "data": null, className: 'dt-body-center', 'searchable': false, "orderable": true, targets: 1, "width": "10px" },
        { "data": "Code", targets: 2, "width": "50px" },
        { "data": "Description", targets: 3, "width": "50px" },
        { "data": null, className: 'hidden', targets: 4, "width": "100px" },
    ],
		'rowCallback': function(row, data, dataIndex){
			var itemId = $(row).attr('id');
			 if($.inArray(itemId, rows_selected) !== -1){
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
		  },
		order			: [[ 4, 'desc' ]],
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
      //   parent.$('#btnsave').addClass('hide');
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