<!--@extends('app')-->
<style>
.dataTables_wrapper .dataTables_info { padding-top: 0 !important;}
</style>
@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBU').'/erosui/company') }}">Guarantor - EROS <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li><a href="{{ url(session('userBU').'/erosui/company/'.$datas[0]->Id.'/edit') }}">{{ $datas[0]->Name }} <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li><a href="{{ url(session('userBU').'/erosui/company/itemspackages/'.$datas[0]->Id) }}">Item and Packages <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a href="#">Add Item<span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="companyCode" value="{{ $datas[0]->ErosCode }}">
			<div class="col-menu-15 table-queue"></div>
	</div>
	<div class="navbar-fixed-bottom" >
		<div class="col-menu">
			<div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
				<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
					<button class="savebtn  btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Save </button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script>
(function($) {
  $.fn.inputFilter = function(callback, errMsg) { 
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
      if (callback(this.value)) {
        // Accepted value
        if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
          $(this).removeClass("input-error");
          this.setCustomValidity("");
        }
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        // Rejected value - restore the previous one
        $(this).addClass("input-error");
        this.setCustomValidity(errMsg);
        this.reportValidity();
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        // Rejected value - nothing to restore
        this.value = "";
      }
    });
  };
}(jQuery));

jQuery.fn.dataTableExt.oApi.fnFindCellRowIndexes = function ( oSettings, sSearch, iColumn )
{
	var
		i,iLen, j, jLen, val,
		aOut = [], aData,
		columns = oSettings.aoColumns;

	for ( i=0, iLen=oSettings.aoData.length ; i<iLen ; i++ )
	{
		aData = oSettings.aoData[i]._aData;

		if ( iColumn === undefined )
		{
			for ( j=0, jLen=columns.length ; j<jLen ; j++ )
			{
				val = this.fnGetData(i, j);

				if ( val == sSearch )
				{
					aOut.push( i );
				}
			}
		}
		else if (this.fnGetData(i, iColumn) == sSearch )
		{
			aOut.push( i );
		}
	}

	return aOut;
};
$.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).prop('checked') ? '1' : '0';
    } );
}
var rows_selected = [];


$(document).ready(function(e) {

	
	$html = "<div class=\"table-responsive\"><table id=\"ItemListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\" disabled=\"disabled\"></th>";
	$html += "<th>Item Code</th>";
	$html += "<th>Item Name</th>";
	$html += "<th>Item Group</th>";
	$html += "<th>Item Price</th>";
	$html += "</tr>";
	$html +="</thead><tbody>";

	var idata = []; 
	var idatas = {!! $itemData !!}; 
	
	if( typeof(idatas.length) === 'undefined')
		idata.push(idatas);
	else
		idata = idatas;
	
	$html +="</tbody></table></div>";
	$('.table-queue').append($html);
	
	var table = $('#ItemListTable').DataTable({
		
		data			: idata,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-code', data.Code).attr('data-toggle-desc', data.Description).attr('data-toggle-group', data.DepartmentGroup); },
		columns			: [
		{ "data": null },
		{ "data": "Id",  "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) { return '<input type="checkbox" name="id[]" value="'+data+'">'; } },
		{ "data": "Code", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "PriceGroup", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "TagPrice", "render": function(data,type,row,meta) { var $price = (data == null )?'0':data; return '<input  type="text" class="form-control wrapTable iAmount" value="'+$price+'">'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ responsivePriority: 1, targets: 1, className: 'dt-body-center controlCheck', orderable: false,  "width":"10px", data: null  }, 
			{ responsivePriority: 2, targets: 2, "width":"50px" },
			{ responsivePriority: 3, targets: 3, "width":"400px" },
			{ responsivePriority: 4, targets: 4, "width":"50px" },
			{ responsivePriority: 5, targets: 5, "width":"50px", className:'controlSelect' }
		],
		order	 : [ 2, 'asc' ],
		dom:     "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-135,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	
	
	$("#ItemListTable tbody .iAmount").inputFilter(function(value) {
		return /^[0-9]*\.?[ 0-9]*$/.test(value);   
	},"Only digits allowed");
	
	$(".dataTables_scrollBody").on( 'scroll', function(){ 
		$(this, "tbody .iAmount").find('input[type="text"]').inputFilter(function(value) {
		  return /^[0-9]*\.?[ 0-9]*$/.test(value);   
		},"Only digits allowed");
		$('#ItemListTable tbody .iAmount').on('change', function(e){  
			//var scrollTop =  $('.dataTables_scrollBody').get(0).scrollHeight;
			var $row = $(this).closest('tr');
			var  inSelect = $(this).parent().find('.iAmount').val(); 
			var inCheck = $row.find('input[type=checkbox]').is(":checked"); 
			table.row($row).data().TagPrice = inSelect;
			table.row($row).invalidate().draw();
			$row.find('input[type=checkbox]').prop( "checked", inCheck);
			//$('.dataTables_scrollBody').scrollTop(scrollTop);
		});
	});
	$('#ItemListTable').on('click', 'tbody td', function(e){ 
		   if( $(this).hasClass('control') || $(this).hasClass('controlSelect')  ) return false;
		  $(this).parent().find('input[type="checkbox"]').trigger('click');
	});
	$('#ItemListTable tbody').on('click', 'input[type="checkbox"]', function(e){ 
		var $row = $(this).closest('tr');
		var rowId = table.row($row).data().Id;
		
		var index = $.inArray(rowId, rows_selected);
		if(this.checked && index === -1)
			rows_selected.push(rowId);
		else if (!this.checked && index !== -1)
			rows_selected.splice(index, 1);
		if(this.checked)
			$row.addClass('selected');
		else
			$row.removeClass('selected');
		//updateDataTableSelectAllCtrl(datas);
		e.stopPropagation();
	});
	$('#ItemListTable tbody .iAmount').on('change', function(e){ 
		//var scrollTop =  $('.dataTables_scrollBody').get(0).scrollHeight;
		var $row = $(this).closest('tr');
		var  inSelect = $(this).parent().find('.iAmount').val(); 
		var inCheck = $row.find('input[type=checkbox]').is(":checked");  
		table.row($row).data().TagPrice = inSelect;
		table.row($row).invalidate().draw();
		$row.find('input[type=checkbox]').prop( "checked", inCheck);
		//$('.dataTables_scrollBody').scrollTop(scrollTop);
	});
	
	
	
	
	
	
	$('.savebtn').on('click', function(e){
		if( parent.required($('form')) ) return false;
		
		e.preventDefault();
		var eEmpty = 0;
		if(rows_selected.length == 0) {alert("No Selected Item, please check...");}
		$.each(rows_selected,function(ik,iv){ 
			var rowId = $('#ItemListTable').dataTable().fnFindCellRowIndexes(iv, 1);
			var pType = $('#ItemListTable').DataTable().row(rowId).data().TagPrice; 
			if( pType == 0 || pType == null || pType == '')
			{
				eEmpty = eEmpty + 1;
			}
		});
		if(eEmpty > 0)
		{
			alert('You have selected item without amount, please check...'); 
			return false;
		}
		var withError = " Update Successfully!";
		$.when(
			$.each(rows_selected,function(ik,iv){
				var rowId = $('#ItemListTable').dataTable().fnFindCellRowIndexes(iv, 1);
				var sPrice = $('#ItemListTable').DataTable().row(rowId).data().TagPrice;
				var sCode = $('#ItemListTable').DataTable().row(rowId).data().Code;
				var sDescription = $('#ItemListTable').DataTable().row(rowId).data().Description;
				var sType = $('#ItemListTable').DataTable().row(rowId).data().PriceGroup;
				
				var dataSetPost = {"companyCode": $('input[name=companyCode]').val() ,"itemCode":sCode,"itemDescription":sDescription, "itemType":sType, "Price":sPrice, "_token": $('input[name=_token]').val()}; 
				
				//alert($('input[name=companyCode]').val());
				parent.postData(	"{{ url('erosui/company/itemspackages/item/newSaveAjax') }}", dataSetPost, function($data){ 
					if($data !== "Okay"){ withError = $data; }
				} );
				
			})
		).done(function( x ) {
		  alert (withError);
		});
		
		
	
		/*parent.postData(
			"{{ url('erosui/company/itemspackages/package/newSaveAjax') }}",
			{
				'itemSelected':rows_selected,
				'PackageName':$('input[name=packageName]').val(),
				'PackageAmount':$('input[name=packageAmount]').val(),
				'ClinicCode': $('select[name=modalClinics]').val(),
				'Id':$('input[name=_id]').val(),
				'_token': $('input[name=_token]').val()
			},
			function($data)
			{ 	
				if($data < 0){
					alert('Error! Please try it again');
				}else{
					alert('Successfully Save!');
					location.href="{{ url('erosui/company/itemspackages/'.$datas[0]->Id) }}";
				}
			}
		);*/
	});
	
	$("input[name='packageAmount']").inputFilter(function(value) {
	   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
	},"Only digits allowed");
	 	
	
	var clinics = [];
	var Lclinics = {!! json_encode($clinics) !!};
	if( typeof(Lclinics.length) === 'undefined')
		clinics.push(Lclinics);
	else
		clinics = Lclinics;
	$('select[name="modalClinics"]').selectize({
		sortField: 'Description',
		searchField: ['Code','Description'],
		options : clinics,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.Description) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.Code) + '</span>' +
				'</div>';
			}
		}
	});
	
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});

});
</script>
@endsection