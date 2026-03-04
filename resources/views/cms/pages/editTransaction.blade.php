<form id="editTransactionModal" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
	<input type="hidden" name="_selected" value="">
	<input type="hidden" name="_method" value="PUT">
	<input type="hidden" name="item" id="hiddenCodeItemPrice" value="{{$datas[0]->CodeItemPrice}}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="modal-cms-header">
		<div class="row form-group row-md-flex-center">
			<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
				<label class="bold nodoctors " style="cursor:pointer;">Doctor's Name<font style="color:red;">*</font></label>
			</div>
			<div class="col-sm-10 col-md-10">
				<select name="modalDoctors" class="form-control rowview" placeholder="Doctor's Name">
					<option value=""></option>
					@foreach ($doctors as $doctor)
						<option value="{{ $doctor->Id }}">{{ $doctor->FullName }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="row form-group row-md-flex-center">
			<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
				<label class="bold companyDefault " style="cursor:pointer;">Company Name<font style="color:red;">*</font></label>
			</div>
			<div class="col-sm-10 col-md-10">
				<select name="modalCompanys" class="form-control rowview" placeholder="Company Name">
					<option value=""></option>
					@foreach ($companys as $company)
						<option value="{{ $company->Id }}">{{ $company->Name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="row form-group row-md-flex-center">
			<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
				<label class="bold transactionDefault " style="cursor:pointer;">Transaction Type<font style="color:red;">*</font></label>
			</div>
			<div class="col-sm-10 col-md-10">
				<select name="modalTransactionType" class="form-control rowview" placeholder="Transaction Type">
					<option value=""></option>
					@foreach ($transactionType as $tType)
						<option value="{{ $tType->Code }}">{{ $tType->Description }}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
</form>
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
	var codeItemPrice = $('#hiddenCodeItemPrice').val();
	
	if ($(window).width() < 767) mless = 130;
	
    $html = "<div class=\"table-responsive\"><table id=\"ItemListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"hidden\" disabled=\"disabled\"></th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Description</th>";
			$html += "<th>Qty</th>";
			$html += "<th>Price</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	var data = [];
				
	$html +="</tbody></table></div>";
	$('.table-items').append($html);
	
	var table = $('#ItemListTable').DataTable({
		data: data,
		autoWidth: false,
		deferRender: false,
		createdRow: function(row, data, index) {
			$(row).attr('data-toggle-subgroup', data.Group).attr('data-toggle-itemused', data.ItemUsed).attr('data-toggle-group', data.SubGroup).attr('data-toggle-IMAllowQty', data.IMAllowQty).attr('data-toggle-iditem', data.IdItem).attr('data-toggle-iddoctor', data.IdDoctor); 
			if ($.isArray(data.IdItem ) === 1) {
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			}
		},
		columns: [
			{ "data": null },
			{ "data": "Id", "orderDataType": "dom-checkbox", "render": function(data, type, row, meta) { return '<input type="checkbox" name="id[]" value="' + data + '">'; } },
			{ "data": "Code", "render": function(data, type, row, meta) { return '<div class="wrap-row">' + data + '</div>'; } },
			{ "data": "Description", "render": function(data, type, row, meta) { return '<div class="wrap-row">' + data + '&nbsp;<div class="text-right" style="color:red;">' + row['PDefault'] + '</div></div>'; } },
			{ "data": null, "render": function(data, type, row, meta) { 
				var content = (typeof data === 'object') ? (data.propertyName || '') : (data || ''); 
				if ((data.Group === 'CARD' && data.IMSubGroup !== "FREE") || (data.IMAllowQty == "1")) { 
					content += '<input type="number" name="Qty" min="1" max="25" oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" data-group="' + (data.Group || '') + '" placeholder="Qty" style="margin-left: 10px" class="form-control" value="1" required />'; 
				}
				return '<div class="wrap-row">' + content + '</div>'; 
			}},
			{ "data": "Price", "render": function(data, type, row, meta) { return '<div class="wrap-row">' + data + '</div>'; } }
		],
		responsive: { details: { type: 'column' } },
		columnDefs: [
			{ "data": null, className: 'control', orderable: false, targets: 0, "width": "10px", defaultContent: "" },
			{ "data": null, className: 'dt-body-center', 'searchable': false, "orderable": true, targets: 1, "width": "10px" },
			{ "data": "Code", targets: 2, "width": "30px" },
			{ "data": "Description", targets: 3, "width": "300px" },
			{ "data": "Price", targets: 4, "width": "80px" },
			{ "data": null, targets: 5, "width": "10px" },
			{ "data": "PriceGroup", targets: 6, "width": "1px" }
		],
		rowCallback: function(row, data, dataIndex) {
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).find('input[type="checkbox"]').prop('disabled', true);
				$(row).addClass('selected');
			
		},
		order: [[1, 'desc'], [2, 'asc']],
		dom: "frtiS",
		scrollY: $(window).height() - $('.modal-cms-header').height() - mless,
	});

	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text', 'readonly': true}).val(codeItemPrice).trigger('keyup');
	// Handle click on checkbox
	   

	var doctors = [];
	var datas = {!! $doctors !!};
	if( typeof(datas.length) === 'undefined')
		doctors.push(datas);
	else
		doctors = datas;
	$idoctors = $('select[name="modalDoctors"]').selectize({
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
			if (!value.length)
			{
				var itemTable = $('#ItemListTable').dataTable();
				itemTable.fnClearTable();
				return;
			}
			else
			{
				parent.getData(
					"{{ '/cms/queue/api/transactionItemPrice' }}/0",
					{
						'IdCompany':$('select[name=modalCompanys]').val(),
						'IdDoctor':value,
						'_token': $('input[name=_token]').val()
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
						itemTable.rows().invalidate().draw();
						
						// itemTable.rows.add( results.listItemPrice ).draw(false);
						
					}
				);
			}
		}
	});
	
	$('.nodoctors').on('click', function(e){
		//1699 No Physician = changed to 8498 Outside Physician as per request 3/15/23
		$sdoctor = $idoctors[0].selectize;
		$sdoctor.setValue("{{$datas[0]->IdDoctor}}");
	});
	$('.nodoctors').click();
	
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
		searchField: ['ErosCode','Name'],
		options : companys,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.Name) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.ErosCode) + '</span>' +
				'</div>';
			}
		},
		onChange: function(value) { 
			
			if (!value.length) 
			{
				var itemTable = $('#ItemListTable').dataTable();
				itemTable.fnClearTable();
				return;
			}
			else
			{
				parent.getData(
					"{{ '/cms/queue/api/transactionItemPrice' }}/0",
					{
						'IdCompany':value,
						'IdDoctor':$('select[name=modalDoctors]').val(),
						'_token': $('input[name=_token]').val()
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
						// test if working
						var num_rows = itemTable.api().page.info().recordsTotal;
						itemTable.api().page( 'last' ).draw( true );
						itemTable.api().row( num_rows-1 ).scrollTo();
						itemTable.rows().invalidate().draw();
					}
				);
			}
		}
	});
	//companyDefault set on the login session 
	$('.companyDefault').on('click', function(e){
		//session('userClinicDefault')
		$scompany = $company_s[0].selectize;
		$scompany.setValue("{{$datas[0]->IdCompany}}");
	});
	
	$('.companyDefault').click();
	
	company_s = $company_s[0].selectize;
	//package  = $package[0].selectize;
	//package.disable();
	
	
	var transactionType = [];
	var datas = {!! $transactionType !!}; 
	
	if( typeof(datas.length) === 'undefined')
		transactionType.push(datas);
	else
		transactionType = datas;
	$transaction_t = $('select[name="modalTransactionType"]').selectize({  
		sortField: 'Id',
		searchField: ['Code','Description'],
		options : transactionType,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.Code) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.Description) + '</span>' +
				'</div>';
			}
		}
		
	});
	transaction_t = $transaction_t[0].selectize;
	
	//companyDefault set on the login session 
	$('.transactionDefault').on('click', function(e){
		//session('userClinicDefault')
		transaction_t.setValue("{{$datas[0]->TransactionType}}");
	});
	
	$('.transactionDefault').click();
	
});
</script>
