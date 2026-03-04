<!--@extends('app')-->

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBU').'/erosui/company') }}">Guarantor - EROS <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li><a href="{{ url(session('userBU').'/erosui/company/'.$datas[0]->Id.'/edit') }}">{{ $datas[0]->Name }} <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li><a href="{{ url(session('userBU').'/erosui/company/itemspackages/'.$datas[0]->Id) }}">Item and Packages <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a href="#">{{ $packageName[0]->Code }} <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		
		<div class="col-menu-15">
			<div class="panel panel-primary">
				<div class="panel-heading" style="line-height:12px;">Package</div>
				<div class="panel-body">
				<form id="formQueueEdit" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}" autocomplete="off">
				<input type="hidden" name="_method" value="PUT">
				<input type="hidden" name="_id" value="{{ $datas[0]->Id }}">	
				<input type="hidden" name="_iditem" value="{{ $packageName[0]->Id }}">	
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="row form-group row-md-flex-center">
						
							<div class="col-sm-6 col-md-6">
								<input type="text" class="form-control" name="packageName" placeholder="Package Name" required="required" value="{{ $packageName[0]->Description }}">
							</div>
							<div class="col-sm-2 col-md-2">
								<input type="text" class="form-control" name="packageAmount" placeholder="Package Amount" required="required" value="{{ $packageName[0]->Price }}">
							</div>
							<div class="col-sm-2 col-md-2">
								<select name="modalClinics" class="form-control rowview" placeholder="Clinic Name" required="required" >
									@foreach ($clinics as $clinic)
										<option value="{{ $clinic->Code }}"  @if($packageName[0]->ClinicCode == $clinic->Code  ) selected @else '' @endif  >{{ $clinic->Description }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-sm-2 col-md-2">
								<input type="text" class="form-control" name="LISCode" placeholder="LIS Code" readonly="readonly" value="{{ $packageName[0]->LISCode }}">
							</div>
						
					</div>
					<!--LEFT-->
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="panel panel-danger">
							<div class="panel-heading" style="line-height:12px;">Item Master</div>
							<div class="panel-body">
								<div class="row form-group row-md-flex-center">
									<div class=" col-sm-12 table-queue"></div>
								</div>
							</div>
						</div>
					</div>
					<!--RIGHT-->
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="panel panel-warning">
							<div class="panel-heading" style="line-height:12px;">Item Package</div>
							<div class="panel-body">
								<div class="row form-group row-md-flex-center">
									<div class=" col-sm-12 table-save"></div>
								</div>
							</div>
						</div>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
	<div class="navbar-fixed-bottom" >
		<div class="col-menu">
			<div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
				<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
					<button @if( strpos(session('userRole') , '"ldap_role":"['.strtoupper(\Request::segment(2)).']"') !== false  )  @else disabled="disabled" @endif class="savebtn saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Save </button>
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
$(document).ready(function(e) {
	
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Item Code</th>";
	$html += "<th>Item Name</th>";
	$html += "<th>Item From</th>";
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
	
	var table = $('#QueueListTable').DataTable({
		data			: idata,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-code', data.Code).attr('data-toggle-desc', data.Description).attr('data-toggle-group', data.SystemFrom); },
		columns			: [
		{ "data": null },
		{ "data": "Code", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "SystemFrom", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ targets: 1, "width":"50px",className: 'data-row' },
			{ targets: 2, "width":"200px" },
			{ targets: 3, "width":"50px" }
			
		],
		order	 : [ 1, 'asc' ],
		dom:     "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-329,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	
	$html = "<div class=\"table-responsive\"><table id=\"SaveListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Item Code</th>";
	$html += "<th>Item Name</th>";
	$html += "<th>Item From</th>";
	$html += "</tr>";
	$html +="</thead><tbody>";

	var pdata = []; 
	var pdatas = {!! $packageData !!}; 
	
	if( typeof(pdatas.length) === 'undefined')
		pdata.push(pdatas);
	else
		pdata = pdatas;
	
	$html +="</tbody></table></div>";
	$('.table-save').append($html);
	
	var tableNew = $('#SaveListTable').DataTable({
		data			: pdata,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-code', data.Code).attr('data-toggle-desc', data.Description).attr('data-toggle-group', data.SystemFrom); },
		columns			: [
		{ "data": null },
		{ "data": "Code", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "SystemFrom", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ targets: 1, "width":"50px",className: 'data-row' },
			{ targets: 2, "width":"200px" },
			{ targets: 3, "width":"50px" }
			
		],
		order	 : [ 1, 'asc' ],
		dom:     "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-329,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	
	
	$('#QueueListTable').on('click','.data-row',function(e){
		 table.row($(this).closest('tr')).remove().draw(false);
		 tableNew.row.add
			( { 
			'Id': $(this).closest('tr').data('toggle-queueid'),
			'Code':$(this).closest('tr').data('toggle-code'),
			'Description':$(this).closest('tr').data('toggle-desc'),
			'SystemFrom':$(this).closest('tr').data('toggle-group')
			} ).draw(false);
		e.preventDefault();
	});
	
	$('#SaveListTable').on('click','.data-row',function(e){
		 tableNew.row($(this).closest('tr')).remove().draw(false);
		 table.row.add
			( { 
			'Id': $(this).closest('tr').data('toggle-queueid'),
			'Code':$(this).closest('tr').data('toggle-code'),
			'Description':$(this).closest('tr').data('toggle-desc'),
			'SystemFrom':$(this).closest('tr').data('toggle-group')
			} ).draw(false);
		e.preventDefault();
	});
	
	$('.savebtn').on('click', function(){
		if( parent.required($('form')) ) return false;
		var rows_selected = [];
		var loadData = tableNew.rows().data();
		 loadData.each(function (value, index) {
			rows_selected.push(value.Id);
		     //console.log(`For index ${index}, data value is ${value.Code}`);
		 });
		parent.postData(
			"{{ url('erosui/company/itemspackages/package/editSaveAjax') }}",
			{
				'itemSelected':rows_selected,
				'PackageName':$('input[name=packageName]').val(),
				'PackageAmount':$('input[name=packageAmount]').val(),
				'ClinicCode': $('select[name=modalClinics]').val(),
				'Id':$('input[name=_id]').val(),
				'IdItem':$('input[name=_iditem]').val(),
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
		);
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