<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>

.ui-datepicker-div{ z-index:2003 !important;}
.ui-datepicker {
z-index: 1001 !important;
}

.wrap-row {
    font-weight: bolder;
}
img{border:none;}
#preview{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:5px;
	display:none;
	color:#fff;
	z-index:100;
	}




.table-transaction{ margin-top:-10px; margin-bottom:20px; z-index:0;}	
#TransactionListTable_filter{ width:100%; padding-left:5px; padding-right:5px;}
td.group {
    background-color: #D1CFD0;
    border-bottom: 2px solid #A19B9E;
    border-top: 2px solid #A19B9E;
}
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}
/* .data-row2 {color: #337AB7;text-decoration: none;cursor: pointer;position: relative;} */

/* .data-row2::after {
    content: "View Client Information Sheet";
    position: absolute;
    top: -40%;
    left: 50%;
    transform: translateX(-50%);
	color: white;
    background-color: rgba(169, 169, 169, 0.8); 
    padding: 5px 10px;
    border-radius: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
} */

/* .data-row2:hover::after {
    opacity: 1;
} */

.row-package:not(.header-package) {
    color: #337AB7;
    text-decoration: none;
    cursor: pointer;
    position: relative;
}
.row-package:not(.header-package)::after {
    content: "View Package Composition";

    position: absolute;
    top: -40%;
    left: 50%;
    transform: translateX(-50%);
	color: white;
    background-color: rgba(169, 169, 169, 0.8);
    padding: 5px 10px;
    border-radius: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
	white-space: nowrap;
    max-width: none;
}

.row-package:not(.header-package):hover::after {
    opacity: 1;
}

.row-cis:not(.header-company) {
    color: #337AB7;
    text-decoration: none;
    cursor: pointer;
    position: relative;
}

.row-cis:not(.header-company)::after {
    content: "View Client Information Sheet";
    position: absolute;
    top: -40%;
    left: 50%;
    transform: translateX(-50%);
	color: white;
    background-color: rgba(169, 169, 169, 0.8);
    padding: 5px 10px;
    border-radius: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
	white-space: nowrap;
    max-width: none;
}

.row-cis:not(.header-company):hover::after {
    opacity: 1;
}
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
                    <li><a href="{{ '/kiosk/receptionqueue' }}">Reception Queue <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Create <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-queue">
        <form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ '/kiosk/queue' }}" autocomplete="off">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="hide col-sm-2 col-md-2 pad-left-1-md text-left-md">
			<label class="bold">Patient Type</label>
		</div>
		<div class=" hide col-sm-4 col-md-4 pad-0-md">
			<select name="PatientType" class="form-control" placeholder="Patient Type" data-placeholder="Patient Type" required="required" >
				<option></option>
			</select>
		</div>

        	<div class="panel panel-primary">
		@if ($message = Session::get('success'))
		<div class="alert alert-success alert-block">
		    <button type="button" class="close" data-dismiss="alert">x</button>
			<strong>{{ $message }}</strong>
		</div>
		@endif
	  
		@if (count($errors) > 0)
		    <div class="alert alert-danger">
			<strong>Whoops!</strong> There were some problems with your input.
			<ul>
			    @foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			    @endforeach
			</ul>
		    </div>
		@endif
		
		
				<div class="panel-heading" style="line-height:12px;">Info</div>
				<div class="panel-body">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
						<div class="row form-group row-md-flex-center">
                        	<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
								<label class="bold ">Patient's Name<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-10 col-md-10">
                                <div class="input-group">
                                	<input type="hidden" name="IdPatient" value="{{ $datas->ErosPatientId}}" />
									<input type="hidden" name="QueueIdPatient" value="{{ $datas->IdPatient}}" /> <!--QUEUE ID PATIENT-->
                                    <div id="PatientName">
                                    <input type="text" class="typeahead form-control" name="PatientName" value="{{ $datas->FullName}}" placeholder="Patient Name" readonly="readonly" required="required" >
                                   	</div>
                                    <div class="input-group-btn">
                                    	<button class="newbtn btn btn-warning" type="button" disabled="disabled"> New </button>
										<button class="editbtn btn btn-success " type="button"   > Edit </button>
										<button class="scanQrCode btn btn-primary" type="button"><span class="glyphicon glyphicon-qrcode"></span></button>
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
								<textarea class="form-control" name="Notes" placeholder="Notes"></textarea>
							</div>
						</div>

						{{-- PICKUP & EMAIL --}}
						<div class="form-group row">
							<div class="col-sm-2 col-md-2"></div>
							<div class="col-sm-10 col-md-10">
								<div class="form-check form-check-inline">
									<input type="hidden" name="forPU" value="0"> 
									<input class="form-check-input" type="checkbox" name="forPU" id="forPU" value="1">
									<label class="form-check-label" for="forPU" style="font-weight: bold;">PICK-UP</label>
								
									<input type="hidden" name="forEmail" value="0">
									<input class="form-check-input" type="checkbox" name="forEmail" id="forEmail" value="1">
									<label class="form-check-label" for="forEmail" style="font-weight: bold;">EMAIL</label>
								</div>
							</div>
						</div>
					{{-- PICKUP & EMAIL --}}
                        
					</div>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                    	<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
					<label class="bold ">Clinic</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="hidden" name="ClinicCode" />
					<select name="Clinic" class="form-control disabled" placeholder="Clinic" required="required" disabled="disabled">
						<option value=""></option>
						@foreach ($clinics as $clinic) 
							<option value="{{ $clinic->Code }}"  @if(session('userClinicCode') == $clinic->Code  ) selected @else '' @endif  >{{ $clinic->Description }}</option>
						    @endforeach
					</select>
				</div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
					<label class="bold">Queue No.</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" class="form-control" placeholder="System Generated" readonly="readonly">
				</div>
			</div>
	
			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
					<label class="bold">Medication</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" name="Medication" class="form-control" placeholder="Medication" >
				</div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
					<label class="bold ">Queue Status.</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" class="form-control" placeholder="System Generated" readonly="readonly">
				</div>
			</div>
			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
					<label class="bold">Last Dose</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="datetime-local" name="LastDose" class="form-control datepicker LastDose" placeholder="Last Dose" step="1" >
				</div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
					<label class="bold ">Input By</label>
				    </div>
				    <div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" class="form-control" placeholder="System Generated" readonly="readonly">
				</div>
			</div>
			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
					<label class="bold">Last Period</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" name="LastPeriod" class="form-control datepicker LastPeriod" placeholder="Last Menstrual Period" >
				</div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
					<label class="bold ">Date Time</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" class="form-control myDate" name="Date" value="{{ date('Y-m-d H:m:s') }}" placeholder="Date" readonly="readonly">
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
                    <button class="savebtn saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Save </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
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
function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
  }

var calculateAge = function(birthday) {
     dob = new Date(birthday);
   var today = new Date();
   var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));

    return age;
};

	
var patientNameTT = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: "{{ 'api/getPatientName' }}/"+'%QUERY',
    wildcard: '%QUERY',
	rateLimitWait: 1000
  }
});
function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status!=404;
}

$(document).ajaxSend(function(event, request, settings) { 
  $('.tt-input').addClass('loading');
});

$(document).ajaxComplete(function(event, request, settings) {
  $('.tt-input').removeClass('loading');
});

$('input[name="PatientName"].typeahead').typeahead(
	{
		hint: true,
		highlight: true,
		minLength: 3  
	},
	{
		name: 'FullName',
		displayKey: 'FullName',
		source: patientNameTT.ttAdapter(),
		limit: 1000,
		remote: {
			//url: '/Search?q=%QUERY',
			xhrSending: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				} 
				//add loading class to tt-hint element when request sending.
				$ttHint.addClass("loading");
			},
			xhrDone: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				}
				//remove loading class from tt-hint element when response arrived.
				$ttHint.removeClass("loading"); 
				
			}
		},
		
		templates: {
			empty: [
			  '<div class="empty-message">',
				'Name not found...',
			  '</div>'
			].join('\n'),
			suggestion: function (data) {
			return 	'<div class="man-section">' +
						'<div class="description-section">'+data.FullName+'</div>' +
						'<div class="description-section">'+BirthDateFormat(data.DOB)+'</div>' +
						'<div class="description-section">'+data.Gender+'</div>' +
						'<div class="image-section text-right" style="margin-top:-50px; z-index:205;"><a href="/uploads/PatientPicture/'+data.PictureLink+'" class="preview"><img src="/uploads/PatientPicture/'+data.PictureLink+'" width="100" height="100"></a></div>' +
						'<div style="clear:both;"></div>' +
					'</div>';
	
		}
	}
})
/*.on('blur',function(){
	if( $('input[name="IdPatient"]').val() == "" )
		alert('Please select patient name...');
})*/
.on('keyup',function(e){
	if( $.inArray(e.keyCode,[40,38,9,13,27]) === -1 )
	{
		$('.newbtn').removeClass('disabled').attr('disabled', false);
		$('.editbtn').addClass('disabled').attr('disabled', true);
		$('input[name="IdPatient"],input[name="DOB"],input[name="Gender"],input[name="Age"]').val('');
	}
})
.on('typeahead:selected', function (e, data) {
   	$('input[name="IdPatient"]').val(data.Id);
   	$('input[name="DOB"]').val(BirthDateFormat(data.DOB));
   	$('input[name="Gender"]').val(data.Gender);
	$('input[name="Age"]').val(calculateAge(data.DOB));
	$('.newbtn').addClass('disabled').attr('disabled', true);
	$('.editbtn, .addbtn').removeClass('disabled').attr('disabled', false);
});




var patientAddModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="patientAdd-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_WARNING,
	data: {
		//'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/patient/create') }}"
		pageToLoad: ''
	},
	animate: false,
	closable: false,
	buttons: [
	{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnSaveSelected',
		cssClass: 'btn-warning actionbtn',
		label: 'Save and Select',
		action: function (modalRef){
			$('input[name="DOB"]').attr('disabled', false);
			$('input[name="Gender"]').attr('disabled', false);
		
			if( parent.required($('#patientAddModalForm')) ) return false;
			// if( !$('#genderM').is(':checked') && !$('#genderF').is(':checked') )
			// {
			// 	parent.msgModal.setTitle("Warning");
			// 	parent.msgModal.setType(BootstrapDialog.TYPE_WARNING);
			// 	parent.msgModal.setData("id","");
			// 	parent.msgModal.setData("set",true);
			// 	parent.msgModal.setData("msg","Missing");
			// 	parent.msgModal.setData("code",encodeURIComponent("Gender"));
			// 	parent.msgModal.realize();
			// 	parent.msgModal.open();
			// 	return false;	
			// }
			$('input[name="_selected"]').val('true');
			$(this).addClass('disabled');
			var form = $('#patientAddModalForm');
			parent.postData(form.attr('action'),form.serialize(),function($dataSelected){ 
				$('input[name="IdPatient"]').val($dataSelected.Id);
				$('input[name="DOB"]').val($dataSelected.DOB);
				$('input[name="Gender"]').val($dataSelected.Gender);
				$('input[name="PatientName"]').val($dataSelected.FullName);
				$('input[name="Age"]').val(calculateAge($dataSelected.DOB));
				$('.newbtn').addClass('disabled').attr('disabled', true);
				$('.editbtn').removeClass('disabled').attr('disabled', false);
				$('.addbtn').removeClass('disabled').attr('disabled', false);
				patientAddModal.close();
				
			});
		}
	},
	{
		id: 'btnSave',
		cssClass: 'btn-primary actionbtn',
		label: 'Save',
		action: function (modalRef){
			$('input[name="DOB"]').attr('disabled', false);
			$('input[name="Gender"]').attr('disabled', false);
			
			if( parent.required($('#patientAddModalForm')) ) return false;
			// if( !$('#genderM').is(':checked') && !$('#genderF').is(':checked') )
			// {
			// 	parent.msgModal.setTitle("Warning");
			// 	parent.msgModal.setType(BootstrapDialog.TYPE_WARNING);
			// 	parent.msgModal.setData("id","");
			// 	parent.msgModal.setData("set",true);
			// 	parent.msgModal.setData("msg","Missing");
			// 	parent.msgModal.setData("code",encodeURIComponent("Gender"));
			// 	parent.msgModal.realize();
			// 	parent.msgModal.open();
			// 	return false;	
			// }
			$('input[name="_selected"]').val('false');
			$(this).addClass('disabled');
			var form = $('#patientAddModalForm');
			parent.postData(form.attr('action'),form.serialize(),function($patientId){ 
				patientAddModal.close();
				patientAddModal.setTitle("Patient - Edit");
				patientAddModal.setType(BootstrapDialog.TYPE_SUCCESS);
				patientAddModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient' }}"+'/'+$patientId+'/edit');
				patientAddModal.realize();
				var btn = patientAddModal.getButton('btnSaveSelected');
				btn.removeClass('btn-warning').addClass('btn-success');
				patientAddModal.open();
			});
		}
	}
	]
});

var addModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/cms/queue/pages/transactionTemp' }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn hidden',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-success actionbtn',
		label: 'Add Transaction/ Close',
		action: function (modalRef){
			if($('select[name=modalDoctors]').val() == "" &&  $('select[name=modalCompanys]').val() == "") 
			{
				alert("Missing Required Field!, please check you input");
				return false;
			}
			else
			{
			
				parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
				var itemSelected = [];
				$("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function(){
					var qty = parseInt($(this).closest('tr').find('input[name=Qty]').val() || 1);
					var id = $(this).val();
					// var group = $(this).closest('tr').find('input[name=Qty]').data('group'); // assuming you have a data attribute for Group
					var subgroup = $(this).closest('tr').data('toggle-subgroup');
					var Group = $(this).closest('tr').data('toggle-group');
					var itemused = $(this).closest('tr').data('toggle-itemused');
					console.log("Subgroup:", subgroup);
					for (var i = 0; i < qty; i++) {
						itemSelected.push({ "Id": id, "Group": subgroup, "ItemUsed": itemused});
					}
				});
				console.log('12345',itemSelected);
				parent.postData(
					"{{ '/cms/queue/pages/transactionTemp' }}",
					{
						'PatientId': $('input[name=IdPatient]').val(),
						'itemSelected':itemSelected,
						'DoctorId':$('select[name=modalDoctors]').val(),
						'CompanyId':$('select[name=modalCompanys]').val(),
						'TransactionTypeId':$('select[name=modalTransactionType]').val(),
						'Id':0,
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
		}
	}]
});
$(document).on('change', 'select[name=modalDoctors]', function () {
	var selectedDoctorId = $(this).val();
	console.log('DOCTORID:', selectedDoctorId);
	if (selectedDoctorId === "8498") {
		$('#btnsave').prop('disabled', true);
		
	} else {
		$('#btnsave').prop('disabled', false);
	}
});
var scanModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_PRIMARY,
	data: {
		'pageToLoad': "{{ '/cms/queue/pages/scanner' }}"
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
		id: 'btncancel',
		cssClass: 'btn-warning actionbtn cancel',
		label: 'Reject Specimen',
		action: function (modalRef){
			
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-primary actionbtn saving',
		label: 'Accept Specimen',
		action: function (modalRef){
			parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
			var itemSelected = [];
			$("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function(){
				itemSelected.push({"Id":$(this).val()});
			});
			parent.postData(
				"{{ '/cms/queue/pages/scanner' }}",
				{
					
					'itemSelected':itemSelected,
					'DoctorId':$('select[name=modalDoctors]').val(),
					'CompanyId':$('select[name=modalCompanys]').val(),
					'Id':$('input[name=scanId]').val(),
					'_token': $('input[name=_token]').val()
				},
				function($data)
				{ 	scanModal.close();
					parent.waitingDialog.hide();
					
					var hyperlink = document.createElement('a');
					hyperlink.href = '/cms/queue/'+$data+'/edit';
					var mouseEvent = new MouseEvent('click', {
						view: window,
						bubbles: true,
						cancelable: true
					});
					
					hyperlink.dispatchEvent(mouseEvent);
					(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
					
					e.preventDefault();
				}
			);
		}
	}]
});
var editModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('pageId')+"/edit";
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/cms/pages/transactionTemp' }}"
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
			parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
			var itemSelected = [];
			$("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function(){
				itemSelected.push({"Id":$(this).val(),"Notes":$(this).closest('tr').find('input.item-notes').val()});
			});
			parent.postData(
				"{{ url('/'.session('userBUCode').'/cms/pages/transactionTemp') }}",
				{
					'itemSelected':itemSelected,
					'DoctorName':$('select[name=modalDoctors]').val(),
					'CompanyName':$('select[name=modalCompanys]').val(),
					'Id':modalRef.getData('pageId'),
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
var packageModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('Code')+"/edit";
		$message.load(pageToLoad);
		console.log(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/cms/queue/Package' }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	}]
});

var cisModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('IdCompany');
		$message.load(pageToLoad);
		console.log(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/erosui/company/cisview' }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	}]
});
function refreshTime() {
  const dateString = new Date().toLocaleString();
  const formattedString = dateString.replace(", ", " - ");
$('.myDate').val(formattedString); //it will print on html page
}

function selectedInfo( selectedName, datas )
{ 
	var selectedName = selectedName || "";
	var localdatas = datas || [];
	var iselect = "";
	var itemSelected = []; 
	var selected = itemSelected.length;
	var chargeToPatientAmount = 0;
	var totalAmount = 0; 
		var loadData = $('#TransactionListTable').DataTable().rows().data();
		 loadData.each(function (value, index) {
			itemSelected.push(value.Id);
			totalAmount += parseFloat(value.AmountItemPrice);
		});
	iselect = "</span><div class=\"text-right col-xs-12 col-sm-12 col-md-12\" ><b>Grand Total Amount :</b> <span class=\"Gtotal-amount row-discount\"><B><font style=\"color:blue; font-size:20px;\">"+commaSeparateNumber(totalAmount.toFixed(2))+"</font></B></span></div></div>";
	$('.selected_info').html("<a class=\"iselected\">"+selected+"</a> "+"row(s) selected"+"</span>");
	$('.Gtotal-amount').html(totalAmount.toFixed(2));
	if( selectedName ) return iselect;
}
//for Card Validation
function validateCardNumber(inputField) {
		var cardNumber = inputField.val();
		var itemUsed = inputField.closest('tr').data('toggle-itemused');
		var iReturn ="";
		console.log(itemUsed);
		$.ajax({
			async: false,
			type: 'POST',
			url: '/cms/queue/validate',
			data: {
				'_token': '{{ csrf_token() }}',
				'CardNumber1': cardNumber,
				'itemPrice1' :itemUsed
			},
			success: function (data) { 
				console.log('Validation Status:', data.status);
				if (data.status === 'error') {
					$(document).click();
					var cardErrorModal = new BootstrapDialog({
						type: BootstrapDialog.TYPE_WARNING,
						title: data.title,
						message: data.message,
						buttons: [{
							cssClass: 'cardbtn-default',
							label: 'OK',
							action: function(dialogRef){
								dialogRef.close();
								inputField.val('');
								inputField.focus();
							}
						}]
					});
					$('.cardbtn-default').click();
					//cardErrorModal.close();
					cardErrorModal.realize();
					cardErrorModal.open();
					iReturn = "Error";
					//inputField.val('');
				}
				
			}
		});
		
		return iReturn;
	
}
//End Card Validation
function formatCardNumber(input) {
  var inputValue = input.value.replace(/[^0-9a-zA-Z]/g, ''); // Remove characters other than numbers and letters

  if (inputValue.length > 16) {
    inputValue = inputValue.slice(0, 16); // Truncate to 16 characters
  }

  var formattedValue = '';
  for (var i = 0; i < inputValue.length; i += 4) {
    formattedValue += inputValue.slice(i, i + 4) + '-';
  }

  formattedValue = formattedValue.replace(/-$/, ''); // Remove the trailing hyphen

  input.value = formattedValue;

}
function BirthDateFormat(dateString) {
    // Creating a Date object from the provided date string
if (dateString == '0000-00-00') {
        return '';
    }

    var dateObj = new Date(dateString);

    // Extracting day, month, and year from the date object
    var day = ("0" + dateObj.getDate()).slice(-2); // Ensure two digits for day
    var monthNames = [
        "Jan", "Feb", "Mar",
        "Apr", "May", "Jun",
        "Jul", "Aug", "Sep",
        "Oct", "Nov", "Dec"
    ];
    var monthIndex = dateObj.getMonth();
    var month = monthNames[monthIndex];
    var year = dateObj.getFullYear();

    // Concatenating the formatted date in "DD-Mon-YYYY" format
    var formattedDate = day + '-' + month + '-' + year;

    return formattedDate;
}


$(document).ready(function(e) {
	
	parent.getData("{{ asset('/json/PatientType.json') }}",null,
		function($data){
			$.each($data.patient, function(key,val){
			var selected =  false;
			$('select[name="PatientType"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="PatientType"]').selectize();
		var PatientType  = $('select[name="PatientType"]')[0].selectize;
		PatientType.setValue('Walk-in');
	});
	
	


	$html = "<div class=\"table-responsive\"><table id=\"TransactionListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th>Company Name</th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Doctor</th>";
			$html += "<th>Item Description</th>";
			$html += "<th></th>";
			$html += "<th>Type</th>";
			$html += "<th>Item Amount</th>";
			$html += "<th>Input By</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	var data = [];
	
			
	$html +="</tbody></table></div>";
	$('.table-transaction').append($html);
	
	var table = $('#TransactionListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) {
			$(row).attr('data-toggle-rowId', data.Id).attr('data-toggle-IdCompany', data.IdCompany).attr('data-toggle-IdDoctor', data.IdDoctor).attr('data-toggle-IdItemPrice', data.IdItemPrice).attr('data-toggle-itemused', data.ItemUsedItemPrice).attr('data-toggle-code', data.CodeItemPrice).attr('data-toggle-group', data.PriceGroupItemPrice).attr('data-toggle-description', data.DescriptionItemPrice); 
		},
		columns			: [
		{ "data": null },
		{ "data": "NameCompany", "render": function(data,type,row,meta) { return '<div class="wrap-row row-cis">'+data+'</div>'; }, className: 'header-company' },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) {  var isPackage = (row.PriceGroupItemPrice == "Package") ? 'row-package':'' ; return '<div class="wrap-row '+isPackage+'">'+data+'</div>'; }, className: 'header-package'  },
		{ "data": "NameDoctor", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{"data": "GroupItemMaster","render": function (data, type, row, meta) {if (data === "CARD") {return '<div class="wrap-row"><div><input type="text" placeholder="Card Number" name="CardNumber_' + row.Id + '" style="margin-left: 10px;" class="form-control card-number-input" oninput="formatCardNumber(this)" required/></div></div>';} else { return '<div class="wrap-row"></div>';}}},
		{ "data": "TransactionType", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "AmountItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "InputBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{ data: null, className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ data: null, className: 'data-row2',targets: 1, "width":"80px", headerClassName: 'header-company' },
			{ "data": "NameDoctor", targets: 2, "width":"50px" },
			{ "data": "DescriptionItemPrice",targets: 3, "width":"450px", defaultContent: "" },
			{ "data": "",targets: 4, "width":"180px"},
			{ "data": "TransactionType",targets: 5, "width":"180px"},
			{ "data": "AmountItemPrice",targets: 6, "width":"50px"},
			{ "data": "InputBy",targets: 7, "width":"50px"}

			
		],
		order			: [ 3, 'asc' ],
		dom:            "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
		fnInfoCallback: function( settings, start, end, max, total, pre ) {	
			return "<div class=\" col-xs-12 col-sm-12 col-md-12 col-lg-12\"> <div  class=\" col-xs-6 col-sm-6 col-md-6\"  >"+"Got a total of"+" "+total+" "+"entries "+"</div><div  class=\" col-xs-6 col-sm-6 col-md-6\"  ><span class=\"selected_info\">"+selectedInfo(true,data);
		}
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	$('#TransactionListTable_info').addClass('col-xs-12 col-sm-12 col-md-12 col-lg-12');
	$('#TransactionListTable_filter').append('<button class="addbtn btn btn-success pull-left" type="button"> Add </button>');
	$('#TransactionListTable').dataTable().rowGrouping({
            							iGroupingColumnIndex: 3,
										iExpandGroupOffset:-1
            							/*sGroupingColumnSortDirection: "asc",
            							iGroupingOrderByColumnIndex: 0*/
								});
	$('#TransactionListTable').on('click','.row-package',function(e){



		    if($(this).closest('tr').data('toggle-group') == 'Package')
		    {


				packageModal.setTitle($(this).closest('tr').data('toggle-description') + " ("+$(this).closest('tr').data('toggle-code')+")");
				packageModal.setData("Code", $(this).closest('tr').data('toggle-code'));
				packageModal.setData("IdItemPrice", $(this).closest('tr').data('toggle-iditemprice'));
				packageModal.realize();
				packageModal.open();
				e.preventDefault();
		    }
	});
	
	$('#TransactionListTable').on('click','.row-cis',function(e){
		console.log('1');
		var rowData = table.row($(this).closest('tr')).data();
		var idCompany = rowData.IdCompany;
		var nameCompany = rowData.NameCompany;
		console.log(idCompany);
		cisModal.setTitle('Client Information Sheet' + " ("+nameCompany+")");
		cisModal.setData('IdCompany', idCompany);
		cisModal.realize();
		cisModal.open();
		e.preventDefault();
	});


	$('.card-number-input').prop('required', true);
	
	$('#TransactionListTable').on('focusout','.card-number-input', function (e) {
	
    var $input = $(this);
    var currentValue = $input.val();
    var itemused = $(this).closest('tr').data('toggle-itemused');
    var duplicate = false;
    console.log('item used:', itemused);

	// if ($(e.relatedTarget).hasClass('savebtn')) {
		
    //     e.stopPropagation();
    //     return;
    // }
	
    if (currentValue) {
	
     
            $('.card-number-input').not($input).each(function () {
                if ($(this).val() === currentValue) {
                    duplicate = true;
                    return false;
                }
            });
		
            if (duplicate) {
                BootstrapDialog.show({
                    type: BootstrapDialog.TYPE_WARNING,
                    title: 'Error',
                    message: 'You can only use one card at a time',
                    buttons: [{
                        label: 'OK',
                        action: function (dialogRef) {
                            dialogRef.close();
                            $input.focus();
                        }
                    }]
                });
                $input.val('');
            } else {
		
                if(validateCardNumber($input) == "Error") ;
				
	    
            }
	
        }
	
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
	
	$('.newbtn').on('click',function(e){
		patientAddModal.setTitle("Patient - New");
		patientAddModal.setType(BootstrapDialog.TYPE_WARNING);
		patientAddModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient/create/' }}");
		patientAddModal.realize();
		patientAddModal.open();
		e.preventDefault();
	});
	$('.editbtn').on('click',function(e){
		patientAddModal.setTitle("Patient - Edit");
		patientAddModal.setType(BootstrapDialog.TYPE_SUCCESS);
		patientAddModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient' }}/"+$('input[name="IdPatient"]').val()+'/edit');
		patientAddModal.realize();
		patientAddModal.open();
		e.preventDefault();
	});
	
	$('.addbtn').on('click',function(e){
		if(!$('input[name=IdPatient]').val()) return;
		addModal.setTitle("Transaction - New");
		addModal.realize();
		addModal.open();
		e.preventDefault();
	});
	$('.scanQrCode').on('click',function(e){
		scanModal.setTitle("Scan - New Transaction");
		scanModal.realize();
		scanModal.open();
		e.preventDefault();
	});
	$('.savebtn').on('click',function(e){
		var rows_selected = [];
		var cardNumbers  = [];

		var loadData = table.rows().data();
		var cardError = "";

		loadData.each(function (value, index) { 
			var indexX = (index + 1);
			var tr = $("#TransactionListTable tbody tr:eq('"+indexX +"')");
			var status = value.QueueStatus;
			//cardNumber = $('input[name="CardNumber_' + value.Id + '"]');
			var cardNumber = tr.find('input[name="CardNumber_' + value.Id + '"]'); 
			if(status === null && cardNumber.length  && cardNumber.val() ){
				if (validateCardNumber(cardNumber) != "Error" ) 
				{
					cardNumbers.push(cardNumber.val());
				}else
				{
					cardError =  'Error';
				}
			}	

			
			rows_selected.push(value.Id);
			
		});
		
		if(cardError == 'Error') {
			 e.preventDefault();
			return false;
		}

		$.ajax({
            type: 'POST',
            url: '{{ route('actionlog') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=_token]').attr('content'),
            },
            data: { actionBy : "{{ Auth::user()->username }}", room : 'Reception', action : 'QUEUE CREATE', queueno : "{{ $queueno }}", idpatient : $('input[name=QueueIdPatient]').val(), erosidpatient : $('input[name=IdPatient]').val(), kioskid : "{{ $idpatient }}"},
            success: function (response) {

            },
            error: function (error) {
                console.error('Error updating status:', error);
            }
        });
		
		if( parent.required($('form')) ) return false;
		parent.postData(
				"{{ '/cms/queue' }}",
				{
					'CardNumber': cardNumbers,
					'itemSelected':rows_selected,
					'IdPatient' : $('input[name=IdPatient]').val(),
					'QueueIdPatient' : $('input[name=QueueIdPatient]').val(),
					'Age' : $('input[name=Age]').val(),
					'Notes' : $('textarea[name=Notes]').val(),
					'QFullName' :$('input[name=PatientName]').val(),
					'PatientType' : $('select[name=PatientType]').val(),
					'forPU' : $('input[name=forPU]').is(':checked') ? 1 : 0,
					'forEmail' : $('input[name=forEmail]').is(':checked') ? 1 : 0,
					'Medication' : $('input[name=Medication]').val(),
					'LastDose' : $('input[name=LastDose]').val(),
					'LastPeriod' : $('input[name=LastPeriod]').val(),
					'_token': $('input[name=_token]').val()
				},
				function($data)
				{ 
					var hyperlink = document.createElement('a');
					hyperlink.href = '/cms/queue/'+$data+'/edit';
					var mouseEvent = new MouseEvent('click', {
						view: window,
						bubbles: true,
						cancelable: true
					});
					
					hyperlink.dispatchEvent(mouseEvent);
					(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
					
					e.preventDefault();
				}
			);
		e.preventDefault();

	});
	
	$(document).on('focusin.modal','.ui-datepicker-month,.ui-datepicker-year', function (e) {
		var that = this
		if (that[0] !== e.target && !that.has(e.target).length) {
			that.focus()
		}
	});
	
	
	setInterval(refreshTime, 1000);

	var xOffset = 10;
	var yOffset = 30;
	/*$(document).on('mousemove', 'a.preview', function(e){	
		$("#preview")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});*/

	$(document).on('mouseover', 'a.preview', function(e){ 
		this.t = this.title;
		this.title = "";	
		var c = (this.t != "") ? "<br/>" + this.t : "";
		$("body").append("<p id='preview'><img src='"+ this.href +"' alt='Image preview'  width='300' height='300' />"+ c +"</p>");								 
		$("#preview")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");						
	 });
	 $(document).on('mouseout', 'a.preview', function(e){ 
		$("#preview").remove();
	 });

	/*$('.scanQrCode').on('click',function(){
		parent.vibrate(10);
		parent.scanQrCode();	
	});*/
	
	if( !$('input[name=IdPatient]').val()  )
	{
		$('.addbtn').addClass('disabled');
	}
	else
	{
		$('.addbtn').removeClass('disabled');
	}
	
	var dobLoadVal = $('input[name="DOB"]').val();
	$('input[name="DOB"]').val(BirthDateFormat(dobLoadVal));
	
	$('.LastPeriod').datepicker({ maxDate: '+0', firstDay: 1, dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true, yearRange: 'c-100:c+10'});
	$('.LastDose').datepicker({ maxDate: new Date(), firstDay: 1, dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true, yearRange: 'c-100:c+10'});
	 
	     // FOR AGE FROM
    
	var ageInput = document.getElementsByName('Age')[0];
	ageInput.value = calculateAge(dobLoadVal);
	 
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
	
	
	
	
});
</script>
@endsection