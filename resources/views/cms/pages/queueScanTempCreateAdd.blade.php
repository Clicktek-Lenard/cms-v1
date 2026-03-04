<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="modal-cms-header">
<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Scan here</label>
    </div>
    <div class="col-sm-10 col-md-10">
    	<input type="text" style="text-transform: capitalize;" class="form-control" name="scanner" value="" placeholder="Scan here" autocomplete="off" >
    </div>
</div>
<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Patient Full Name</label>
    </div>
    <div class="col-sm-10 col-md-10">
    	<input type="text" style="text-transform: capitalize;" class="form-control" name="fullname" value="" placeholder="Patient Last Name, First Name Middle Name" readonly="readonly" >
	<input type="hidden" class="form-control" name="scanId" value=""  >
    </div>
</div>
<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Type</label>
    </div>
    <div class="col-sm-4 col-md-4">
    	<input type="text" style="text-transform: capitalize;" class="form-control" name="type" value="" placeholder="Tube Color" readonly="readonly" >
    </div>
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Label</label>
    </div>
    <div class="col-sm-4 col-md-4">
    	<input type="text" style="text-transform: capitalize;" class="form-control" name="label" value="" placeholder="Label Name" readonly="readonly" >
    </div>
</div>
<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Company Name<font style="color:red;">*</font></label>
    </div>
    <div class="col-sm-10 col-md-10">
        <select name="modalCompanys" class="form-control rowview" placeholder="Company Name" disabled="disabled" >
            <option value=""></option>
            @foreach ($companys as $company)
            <option value="{{ $company->Id }}">{{ $company->Name }}</option>
            @endforeach
        </select>
    </div>
</div>

</div>
<div class="row">
    <div class="table-items col-md-12"></div>
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
	
	
    $html = "<div class=\"table-responsive\"><table id=\"ItemListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"hidden\" disabled=\"disabled\"></th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Description</th>";
			$html += "<th>Price</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	var data = [];
	
			
	$html +="</tbody></table></div>";
	$('.table-items').append($html);
	
	var table = $('#ItemListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { 
			 $(row).attr('id', data.IdItem);
			 if($.inArray(data.IdItem, rows_selected) !== -1){ 
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
			 
		},
		columns			: [
		{ "data": null },
		{ "data": "IdItem", "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) { return '<input type="checkbox" name="id[]" value="'+data+'">'; } },
		{ "data": "Code", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Price", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"10px",defaultContent: ""},
			{ "data": null,className: 'dt-body-center', 'searchable':false,  "orderable": true, targets: 1, "width":"10px"  },
			{"data": "Code", targets: 2, "width":"30px" },
			{"data": "Description", targets: 3, "width":"300px" },
			{"data": "Price", targets: 4, "width":"80px" },
			{"data": "PriceGroup", targets: 5, "width":"1px" }
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
   table.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);
   });
	
	
	var doctors = [];
	var datas = {!! $doctors !!};
	if( typeof(datas.length) === 'undefined')
		doctors.push(datas);
	else
		doctors = datas;
	$('select[name="modalDoctors"]').selectize({
		sortField: 'FullName',
		searchField: ['FullName','Category'],
		options : doctors,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.FullName) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.Category) + '</span>' +
				'</div>';
			}
		},
		onChange: function(value) {
			//if (!value.length || !$('select[name=modalPackage]').val() || !$('select[name=modalCompanys]').val() )
			//{
				//$('#ItemListTable').dataTable().fnClearTable();
				return;	
			//}
			/*parent.getData(
				"{{ url(session('userBUCode').'/cms/api/itemPrice') }}/0",
				{
					'DoctorName':value,
					'PriceCode':$('select[name=modalPackage]').val(),
					'CompanyName':$('select[name=modalCompanys]').val()
				},
				function(results){ 
					var itemTable = $('#ItemListTable').dataTable();
					rows_selected = [];
					$.each(results.itemSelected,function(ikey,ival)
					{
						rows_selected.push(ival.IdItem);
					});
					itemTable.fnClearTable();
					itemTable.fnAddData(results.itemPrice);
				}
			);*/
		}
	});
	
	var xhr;
	var company_s, $company_s;
	var package, $package;
	
	var companys = [];
	var datas = {!! $companys !!}; 
	
	if( typeof(datas.length) === 'undefined')
		companys.push(datas);
	else
		companys = datas;
	$company_s = $('select[name="modalCompanys"]').selectize({
		sortField: 'Name',
		searchField: ['Code','Name'],
		options : companys,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.Name) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.Code) + '</span>' +
				'</div>';
			}
		},
		onChange: function(value) {
			if (!value.length) return;
			/*parent.getData(
				"{{ '/cms/queue/api/itemPrice' }}/0",
				{
					'CompanyId':value
				},
				function(results){
					var itemTable = $('#ItemListTable').dataTable();
					rows_selected = [];
					$.each(results.selectedItemPrice,function(ikey,ival)
					{
						rows_selected.push(ival.IdItem);
					});
					itemTable.fnClearTable();
					
					if(!results.listItemPrice.length) return;
					itemTable.fnAddData(results.listItemPrice);
					
					// itemTable.rows.add( results.listItemPrice ).draw(false);
					
				}
			);*/
		}
	});
	

	
	$('input[name="scanner"]').focus()
	.on('keypress',function(e) { 
		if(e.which == 13 && $(this).val() ) {
			waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'warning'});
			var $val = $(this).val();
			 setTimeout(function() {
				parent.getData(
					"{{ '/cms/queue/pages/scanner' }}/"+$val,
					{
						'CompanyId':0
					},
					function(results){
						if(results['code'] == 'INVALID_PRODUCT')
						{
							alert(' INVALID PRODUCT, no record found.');
							$('.modal input[name=scanner]').focus();
							waitingDialog.hide();
							
						}else
						{
							$('input[name="scanId"]').val(results['scanId']);
							$('input[name="fullname"]').val(results['last_name'] +", "+results['first_name']+" "+results['middle_name']);
							$('input[name="type"]').val(results['resource_type']);
							$('input[name="label"]').val(results['resource_label']);
							company_s.setValue(results['company_id']);
							
							var itemTable = $('#ItemListTable').dataTable();
							itemTable.fnClearTable();
							if(!results.listItemPrice.length) return;
								itemTable.fnAddData(results.listItemPrice);
							
							waitingDialog.hide();
						}
					}
				);
			}, 1000);
			
			e.preventDefault();
		}
		return;
		
	});
	
	
		
	company_s = $company_s[0].selectize;
	//package  = $package[0].selectize;
	//package.disable();
	
	
	
	
	
});
</script>


