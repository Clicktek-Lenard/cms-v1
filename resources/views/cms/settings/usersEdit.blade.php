<!--@extends('app')-->
@section('style')
<style>
.table-access{ margin-top:-10px; margin-bottom:20px; z-index:0;}	
#UserAccessListTable_filter{ width:100%; padding-left:5px; padding-right:5px;}
td.group {
    background-color: #D1CFD0;
    border-bottom: 2px solid #A19B9E;
    border-top: 2px solid #A19B9E;
}
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}

</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li><a href="{{ url(session('userBUCode').'/cms/settings/users') }}">Users <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Edit <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-users">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="formSettingsUsersEdit" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}" autocomplete="off">
        <input type="hidden" name="_method" value="PUT">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
        	<div class="panel panel-primary">
				<div class="panel-heading" style="line-height:12px;">Info</div>
				<div class="panel-body">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
						<div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
								<label class="bold ">Username<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
                                <input type="text" class="form-control" name="Username" value="{{ $datas->Username }}" placeholder="Username" readonly="readonly" required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Full Name<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="FullName" value="{{ $datas->FullName }}" placeholder="Full Name"  required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Email<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="email" class="form-control" name="Email" value="{{ $datas->Email }}" placeholder="Email"  required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Default Clinic<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
                            	<input type="hidden" name="ClinicCode" value="{{ $datas->IdClinic }}" />
								<select name="Clinic" class="form-control" placeholder="Default Clinic" required="required">
                                	<option value=""></option>
                                    @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->Id }}">{{ $clinic->Code }}</option>
                                    @endforeach
                                </select>
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Status</label>
                            </div>
							<div class="col-sm-9 col-md-9">
                            	<div class="col-xs-6 col-sm-6 col-md-6">
								<input type="radio" id="status-active" name="Status" value="Active" {{ $datas->Status=="Active"?'checked='.'"'.'checked'.'"':'' }}  /> <label for="status-active" >Active</label>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                <input type="radio" id="status-inactive" name="Status" value="Inactive" {{ $datas->Status=="Inactive"?'checked='.'"'.'checked'.'"':'' }} /> <label for="status-inactive" >Inactive</label>
                                </div>
							</div>
						</div>
					</div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Last login date</label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="LastLogin" placeholder="System Generated" readonly="readonly"  >
							</div>
						</div>
                    </div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Input By</label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="InputBy" value="{{ $datas->InputBy }}" placeholder="System Generated" readonly="readonly"  >
							</div>
						</div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Input date</label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="InputDate" value="{{ $datas->created_at }}" placeholder="System Generated" readonly="readonly"  >
							</div>
						</div>
                    </div>
				</div>
			</div>
            <div class="panel panel-success">
				<div class="panel-heading" style="line-height:12px;">Access</div>
				<div class="panel-body">
                	<div class="row">
                    	<div class="table-access">
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
                    <button class="savebtn saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Save </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
var userAccessModalEdit = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('pageId')+"/edit";
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ url(session('userBUCode').'/cms/settings/pages/userAccess') }}"
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
		cssClass: 'btn-success actionbtn',
		label: 'Save',
		action: function (modalRef){
		}
	}]
});

$(document).ready(function(e) {
	$html = "<div class=\"table-responsive\"><table id=\"UserAccessListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th>Clinic</th>";
			$html += "<th>Role</th>";
			$html += "<th>Access</th>";
			$html += "<th>Special Role</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	$html +="</tbody></table></div>";
	var data = [];
		var datas = {!!$access!!};
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
	$('.table-access').append($html);
	var table = $('#UserAccessListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) {
			//$(row).attr('data-toggle-rowId', data.Id).attr('data-toggle-IdCompany', data.IdCompany).attr('data-toggle-IdDoctor', data.IdDoctor).attr('data-toggle-IdPriceCode', data.IdPriceCode);
		},
		columns			: [
		{ "data": null },
		{ "data": "Clinic", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Role", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Access", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "SpecialRole", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{ data: null, className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ "data": "Clinic",targets: 1, "width":"30px"},
			{ "data": "Role",targets: 2, "width":"50px", defaultContent: ""},
			{ "data": "Access", targets: 3, "width":"150px"},
			{ "data": "SpecialRole",targets: 4, "width":"50px"}
		],
		order			: [ 1, 'asc' ],
		dom:            "frti",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	$('#UserAccessListTable_filter').append('<button class="addbtn btn btn-success pull-left" type="button"> Add</button>');
	//$('#UserAccessListTable').dataTable().rowGrouping({
     //       							iGroupingColumnIndex: 2,
	//									iExpandGroupOffset:-1
            							/*sGroupingColumnSortDirection: "asc",
            							iGroupingOrderByColumnIndex: 0*/
	//							});
								
	$clinicSelect = $('select[name="Clinic"]').selectize({
		onChange: function(value) {
			if (!value.length )
			{
				$('input[name="ClinicCode"]').val('');
				return;	
			}
			$('input[name="ClinicCode"]').val( value );
		}
	});
	$clinic = $clinicSelect[0].selectize;
	$clinic.setValue("{{$datas->IdClinic}}");

	$('.addbtn').on('click',function(e){
		userAccessModalEdit.setTitle("User Access");
		userAccessModalEdit.setData("pageId","{{ $datas->Id }}");
		userAccessModalEdit.realize();
		userAccessModalEdit.open();
		e.preventDefault();
	});
	$('.savebtn').on('click',function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('#formSettingsUsersEdit').submit();
	});
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
});

</script>
@endsection