<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>
.table-transaction{ margin-top:-10px; margin-bottom:20px; z-index:0;}	
#TransactionListTable_filter{ width:100%; padding-left:5px; padding-right:5px;}
td.group {
    background-color: #D1CFD0;
    border-bottom: 2px solid #A19B9E;
    border-top: 2px solid #A19B9E;
}
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}

#PatientName .tt-menu {
  max-height: 250px;
  overflow-y: auto;
}
.tt-input.loading {
    background: transparent url('/images/ajax-loader.gif') no-repeat scroll right center content-box !important;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li><a href="{{ url(session('userBUCode').'/cms/nurse') }}" class="waiting">Nurse <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Edit <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-queue">
        <form id="formQueueEdit" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}" autocomplete="off">
       	<input type="hidden" name="_method" value="PUT">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
        	<div class="panel panel-primary">
				<div class="panel-heading" style="line-height:12px;">Info</div>
				<div class="panel-body">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
						<div class="row form-group row-md-flex-center">
                        	<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
								<label class="bold ">Patient's Name<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-10 col-md-10">
                                <div class="input-group">
                                	<input type="hidden" name="IdPatient" value="{{ $datas->IdPatient }}" />
                                    <div id="PatientName">
                                    <input type="text" class="typeahead form-control" name="PatientName" value="{{ $datas->FullName }}" placeholder="Patient Name" readonly="readonly" required="required" >
                                   	</div>
                                    <div class="input-group-btn">
                                    	<button class="newbtn btn btn-warning disabled" type="button"> New </button>
                                        <button class="editbtn btn btn-success disabled" type="button"> Edit </button>
                                	</div>
                                </div>
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-2 col-md-2 pad-0-md text-right-md ">
								<label class="bold ">Date of Birth<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-3 col-md-3">
								<input type="text" class="form-control" name="DOB" value="{{ $datas->DOB }}" placeholder="Date of Birth" readonly="readonly" required="required">
							</div>
                            <div class="col-sm-1 col-md-1 pad-0-md text-right-md">
                            	<label class="bold">Gender<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-3 col-md-3">
								<input type="text" class="form-control" name="Gender" value="{{ $datas->Gender }}" placeholder="Gender" readonly="readonly" required="required">
							</div>
                            <div class="col-sm-1 col-md-1 text-right-md">
                            	<label class="bold">Age</label>
                            </div>
							<div class="col-sm-2 col-md-2">
								<input type="text" class="form-control" name="Age" placeholder="Age" readonly="readonly" required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 text-right-md">
								<label class="bold">Notes</label>
                            </div>
                            <div class="col-sm-10 col-md-10">
								<textarea class="form-control" name="Notes" readonly="readonly" placeholder="Notes">{{ $datas->Notes }}</textarea>
							</div>
						</div>
                        
					</div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                    	<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2  text-right-md  ">
								<label class="bold ">Clinic</label>
                            </div>
							<div class="col-sm-4 col-md-4">
                            	<input type="hidden" name="ClinicCode" value="{{ $datas->IdClinic }}" />
								<select name="Clinic" class="form-control" placeholder="Clinic" required="required" disabled="disabled"  >
                                    <option value=""></option>
                                    @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->Id }}">{{ $clinic->Code }}</option>
                                    @endforeach
                                </select>
							</div>
                            <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
								<label class="bold">Queue No.</label>
                            </div>
                            <div class="col-sm-4 col-md-4">
								<input type="text" class="form-control" name="QueueNumber" value="{{ $datas->Code }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 text-right-md">
								<label class="bold">Date Time</label>
                            </div>
							<div class="col-sm-4 col-md-4">
								<input type="text" class="form-control" value="{{ $datas->DateTime }}" placeholder="Date" readonly="readonly">
							</div>
                            <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md ">
								<label class="bold ">Queue Status.</label>
                            </div>
                            <div class="col-sm-4 col-md-4">
								<input type="text" class="form-control" value="{{ $datas->QueueStatus }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 col-md-offset-6 pad-left-0-md  text-right-md ">
								<label class="bold ">Input By</label>
                            </div>
                            <div class="col-sm-4 col-md-4">
								<input type="text" class="form-control" value="{{ $datas->InputBy }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>
                        
                    </div>
					
				</div>
			</div>
            <div class="panel panel-success">
				<div class="panel-heading" style="line-height:12px;">Transaction(s)</div>
				<div class="panel-body">
                	<div class="row">
                    	<div class="table-transaction">
						</div>
                    </div>
                </div>
          	</div>
		</form>		        
        
		</div>
    </div>
    <div class="navbar-fixed-bottom" >
        <div class="col-menu">
            <div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
            	<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
                    <button class="vitalsbtn btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Vitals </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
var calculateAge = function(birthday) {
    var now = new Date();
    var past = new Date(birthday);
    var nowYear = now.getFullYear();
    var pastYear = past.getFullYear();
    var age = nowYear - pastYear;

    return age;
};

var addModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="add-modal"></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/{{ $datas->Id }}";
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/transaction') }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-success actionbtn saving',
		label: 'Save',
		action: function (modalRef){
			var itemSelected = [];
			$("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function(){
				itemSelected.push({"Id":$(this).val(),"TransId":$(this).closest('tr').data('toggle-transid'),"Notes":$(this).closest('tr').find('input.item-notes').val()});
			});
			parent.postData(
				"{{ url('/'.session('userBUCode').'/cms/pages/transaction') }}",
				{
					'itemSelected':itemSelected,
					'DoctorName':$('select[name=modalDoctors]').val(),
					'CompanyName':$('select[name=modalCompanys]').val(),
					'PriceCode':$('select[name=modalPackage]').val(),
					'QueueId':" {{ $datas->Id }} ",
					'PatientCode':" {{ $datas->PatientCode }} ",
					'_token': $('input[name=_token]').val()
				},
				function($data)
				{ 
					$('#TransactionListTable').dataTable().fnClearTable();
					$('#TransactionListTable').DataTable().rows.add( $data ).draw();
					addModal.close();
					parent.waitingDialog.hide();
				}
			);
		}
	}]
}); 
var editModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('pageId')+"/edit";
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/transaction') }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-success actionbtn saving',
		label: 'Save',
		action: function (modalRef){
			var itemSelected = [];
			$("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function(){
				itemSelected.push({"Id":$(this).val(),"Notes":$(this).closest('tr').find('input.item-notes').val()});
			});
			parent.postData(
				modalRef.getData('pageToLoad')+"/"+modalRef.getData('pageId'),
				{
					'itemSelected':itemSelected,
					'DoctorName':$('select[name=modalDoctors]').val(),
					'CompanyName':$('select[name=modalCompanys]').val(),
					'PriceCode':$('select[name=modalPackage]').val(),
					'QueueId': "{{ $datas->Id }}",
					'_method': 'PUT',
					'_token': $('input[name=_token]').val()
				},
				function($data)
				{ 
					$('#TransactionListTable').dataTable().fnClearTable();
					$('#TransactionListTable').DataTable().rows.add( $data ).draw();
					editModal.close();
					parent.waitingDialog.hide();
				}
			);	
		}
	}]
});

var vitalsModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+"{{ $datas->Id }}"+"/edit";
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/vitals') }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-success actionbtn saving',
		label: 'Save',
		action: function (modalRef){
			var form = $('#vitalsEditModalForm');
			parent.postData(form.attr('action'),form.serialize(),function($dataSelected){
				vitalsModal.close();	
			});
				
		}
	}]
});



$(document).ready(function(e) {
	$html = "<div class=\"table-responsive\"><table id=\"TransactionListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th>Trans. Code</th>";
			$html += "<th>Item</th>";
			$html += "<th>Doctor</th>";
			$html += "<th>Company</th>";
			$html += "<th>Package</th>";
			$html += "<th>Notes</th>";
			$html += "<th>Status</th>";
			$html += "<th>Input By</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	
	var data = [];
		var datas = JSON.parse("{{$trans}}".replace(/&quot;/ig,'"'));
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
			
	$html +="</tbody></table></div>";
	$('.table-transaction').append($html);
	
	var table = $('#TransactionListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) {
			$(row).attr('data-toggle-rowId', data.Id).attr('data-toggle-IdCompany', data.IdCompany).attr('data-toggle-IdDoctor', data.IdDoctor).attr('data-toggle-IdPriceCode', data.IdPriceCode);
		},
		columns			: [
		{ "data": null },
		{ "data": "Code", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Doctor", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Company", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Package", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Notes", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Status", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "InputBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{ data: null, className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ data: null, className: 'data-row',targets: 1, "width":"100px" },
			{ "data": "Description", targets: 2, "width":"150px" },
			{ "data": "Doctor",targets: 3, "width":"150px",defaultContent: "" },
			{ "data": "Company",targets: 4, "width":"150px"},
			{ "data": "Package",targets: 5, "width":"150px"},
			{ "data": "Notes", targets: 6, "width":"200px"},
			{ "data": "Status",targets: 7, "width":"100px"},
			{ "data": "InputBy",targets: 8, "width":"150px"}
			
		],
		order			: [ 2, 'asc' ],
		dom:            "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	$('#TransactionListTable_filter').append('<button class="addbtn btn btn-success pull-left" type="button"> Add</button>');
	$('#TransactionListTable').dataTable().rowGrouping({
            							iGroupingColumnIndex: 3,
										iExpandGroupOffset:-1
            							/*sGroupingColumnSortDirection: "asc",
            							iGroupingOrderByColumnIndex: 0*/
								});
	$('#TransactionListTable').on('click','.data-row',function(e){
		editModal.setTitle("Transaction - Edit");
		editModal.setData("pageId",$(this).closest('tr').data('toggle-rowid'));
		editModal.realize();
		editModal.open();
		e.preventDefault();
	});
	$('.tt-hint').addClass('form-control');								
								
	$clinicSelect = $('select[name="Clinic"]').selectize({
		onChange: function(value) {
			if (!value.length )
			{
				$('input[name="ClinicCode"]').val('');
				return;	
			}
			$('input[name="ClinicCode"]').val( $('select[name="Clinic"] option:selected').text() );
			
		}
	});
	$clinic = $clinicSelect[0].selectize;
	$clinic.setValue("{{$defaultClinic}}");
	$('input[name="Age"]').val(calculateAge('{{ $datas->DOB }}'));
		
	$('.addbtn').on('click',function(e){
		addModal.setTitle("Transaction - New");
		addModal.realize();
		addModal.open();
		e.preventDefault();
	});
	$('.vitalsbtn').on('click',function(e){
		vitalsModal.setTitle("Vitals - "+"{{ $datas->FullName }}");
		vitalsModal.realize();
		vitalsModal.open();
		e.preventDefault();
	});
		
	$(document).on('focusin.modal','.ui-datepicker-month,.ui-datepicker-year', function (e) {
		var that = this
		if (that[0] !== e.target && !that.has(e.target).length) {
			that.focus()
		}
	});
	
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
});
</script>
@endsection