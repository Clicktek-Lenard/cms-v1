
<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>
.form-control
{
    font-weight: bolder;
}

.cms-font
{
    font-weight: bolder;
}

.wrap-row {
    font-weight: bolder;
}
@media (min-width: 992px) {
  .modal-lg {
    width: 100%;
  }
}
.modal-dialog{
    position: relative;
    display: table; 
    overflow-y: auto;    
    overflow-x: auto;
    width: auto;
    min-width: 50%;   
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
		 <li><a href="{{ '/cms/payment' }}">Payment  <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Billing Summary  <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-queue">
         <form id="formQueueEdit" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}" autocomplete="off">
		<input type="hidden" name="reUpdate" value="reUpdate">
		<input type="hidden" name="_queueid" value="{{$datas->Id}}">
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
							<input type="hidden" name="IdPatient" value="{{ $datas->IdPatient }}" />
							<div id="PatientName">
								<input type="text" style="color:blue;" class=" form-control" name="PatientName" value="{{ $datas->FullName }}" placeholder="Patient Name" readonly="readonly"  required="required" >
							</div>
                                    			</div>
					</div>
				<div class="row form-group row-md-flex-center">
                        	<div class="col-sm-2 col-md-2 pad-0-md text-right-md ">
								<label class="bold ">Date of Birth<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-3 col-md-3">
								<input type="text" class="form-control" name="DOB" value="{{ date('d-M-Y', strtotime($datas->DOB)) }}" placeholder="Date of Birth" readonly="readonly" required="required">
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
								<input type="text" class="form-control" name="Age" value="{{ $datas->AgePatient }}" placeholder="Age" readonly="readonly" required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 text-right-md">
								<label class="bold">Notes</label>
                            </div>
                            <div class="col-sm-10 col-md-10">
								<textarea class="form-control" name="Notes" placeholder="Notes" readonly="readonly" >{{ $datas->Notes }}</textarea>
							</div>
						</div>
                        
					</div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
					<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
								<label class="bold ">PID</label>                            
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" value="{{ $datas->PatientCode }}" placeholder="System Generated" readonly="readonly">
							</div>
							
                            <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
								<label class="bold">Queue No.</label>
                            </div>
                            <div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" placeholder="System Generated" value="{{ $datas->Code }}" readonly="readonly">
							</div>
						</div>

						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
								<label class="bold ">Clinic</label>
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="hidden" name="ClinicCode" value="{{ $datas->IdClinic }}" />
								<select name="Clinic" class="form-control disabled" placeholder="Clinic" required="required" disabled="disabled">
									<option value=""></option>
									@foreach ($clinics as $clinic) 
										<option value="{{ $clinic->Code }}"  @if($datas->IdBU == $clinic->Code  ) selected @else '' @endif  >{{ $clinic->Description }}</option>
									@endforeach
								</select>

							</div>
										
							<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md ">
								<label class="bold ">Queue Status.</label>
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" value="{{ $datas->QueueStatus }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>

						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
								<label class="bold">Patient Type</label>
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<select name="PatientType" class="form-control" placeholder="Patient Type" data-placeholder="Patient Type" required="required" disabled="disabled" >
									<option></option>
								</select>
							</div>

							<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md ">
								<label class="bold ">Input By</label>
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" value="{{ $datas->InputBy }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>
			
			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 col-md-offset-6 pad-left-0-md  text-right-md ">
					<label class="bold ">Date Time</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" class="form-control " name="Date" value="{{ date('d-M-Y H:i:s',strtotime($datas->DateTime)) }}" placeholder="Date" readonly="readonly">
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
		<div class="col-xs-12 col-sm-10 col-sm-offset-2 col-md-8 col-md-offset-4 col-lg-6 col-lg-offset-6">
			<a @if($datas->Status != 300 && $datas->Status != 210) disabled="disabled" @endif class="rtngbtn btn btn-info col-xs-3 col-sm-3 col-md-3 col-lg-3" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Routing Slip </a>
			<a @if( $CS == 0 )  disabled="disabled"  @endif class="chargebtn btn btn-danger col-xs-3 col-sm-3 col-md-3 col-lg-3" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Charge Slip</a>
			<a @if( $OR == 0 )  disabled="disabled"  @endif  class="orbtn btn btn-warning col-xs-3 col-sm-3 col-md-3 col-lg-3" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> O.R.</a>
			<a  class="postbtn saving btn btn-primary col-xs-3 col-sm-3 col-md-3 col-lg-3" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Post</a>					
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
    var now = new Date();
    var past = new Date(birthday);
    var nowYear = now.getFullYear();
    var pastYear = past.getFullYear();
    var age = nowYear - pastYear;

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
function formatDate(date) {
    // Get month, day, and year
    var month = (date.getMonth() + 1).toString().padStart(2, '0');
    var day = date.getDate().toString().padStart(2, '0');
    var year = date.getFullYear();

    // Return formatted date string
    return month + '/' + day + '/' + year;
}
$(document).ajaxSend(function(event, request, settings) { 
  $('.tt-input').addClass('loading');
});

$(document).ajaxComplete(function(event, request, settings) {
  $('.tt-input').removeClass('loading');
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
		'pageToLoad': "{{ '/cms/payment/pages/transactions/'.$datas->Id.'/edit?_ntoken='.csrf_token() }}"
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
		cssClass: 'btn-success actionbtn hide',
		label: 'Save',
		action: function (modalRef){
			
			var withCard = "";
			var itemSelected = []; //fnGetNodes
			$("input:checked", $('#QueueListTable').dataTable().fnGetNodes()).each(function(){ 
				itemSelected.push({"Id":$(this).val(),"toggle-group":$(this).data('toggle-group'), "toggle-itemused":$(this).data('toggle-itemused'), "toggle-compasubgroup":$(this).data('toggle-compasubgroup') }); 
				if( $(this).data('toggle-group') == "CARD" && parseFloat($(this).data('toggle-itemused')) == 0   )
				{
					withCard = "Yes";
				}
		
			});
			
			var PaymentType = [];
			var oscaPwdDate = $('input[name="OscaPwdDate"]').val();
				var expirationDate = new Date(oscaPwdDate);
				var currentDate = new Date();


			if( $('select[name=modalDiscountType]').val() != '')
			{
				PaymentType.push({"Id": "Discounted", "discountType" :$('select[name=modalDiscountType] :selected').text() });
			}
			
			if(  $('select[name=modalProviderType]').val() != 'PATIENT' )
			{
				PaymentType.push({"Id": "HMOCORP" });
			}
			
			if(withCard == "Yes" && $('select[name=Agent]').val() == 0 )
			{
				alert('Please select an "Agent Name"');
				return false;
			}
			if($('input[name=Hcard').val() == '' && $(this).data('toggle-compasubgroup') == 'CARD')
			{
				alert('Please enter your Card Number to proceed');
				return false;
			}
			// if (currentDate > expirationDate) {
			// 	BootstrapDialog.show({
			// 		title: 'Expired OSCA/PWD ID',
			// 		message: '<strong>BALE ITO YUN NOOSCA/PWD Id</strong> is already <strong>Expired</strong>! <br> Please check validity and try again',
			// 		size: BootstrapDialog.SIZE_WIDE,
			// 		type: BootstrapDialog.TYPE_WARNING,
			// 		buttons: [{
			// 			label: 'Close',
			// 			action: function(dialog) {
			// 				dialog.close();
			// 			}
			// 		}]
			// 	});
			// 	return false;
			// }
			else{
				validateDiscount();
			}
			
			
			
			var $selectedOptions = $('select[name=modalSelect]').find('option:selected');
			$selectedOptions.each(function(){
				PaymentType.push({"Id":$(this).val()});
			});

			if( parent.required($('#BillingPost')) ) return false;
			
			if( itemSelected.length == 0 ) 
			{
				alert('Please select Item Procedure');
				return false;
			}
			console.log(PaymentType);
			var noCommas = $('input[name=lessAmount]').val().replace(/,/g, ''),
			asANumber = +noCommas;
			parent.postData(
				"<?php echo e('/cms/payment/pages/transactions'); ?>",
				{
					'QueueID': $('input[name=_queueid]').val(),
					'_Id':  '{{$datas->Id}}',
					'itemSelected' : itemSelected,
					'modalProviderType' : $('select[name=modalProviderType]').val(),
					'BillTo' : $('select[name=billTo]').val(),
					'DiscType' :$('select[name="modalDiscountType"]').find(':selected').data('per'),
					'DiscId' : $('input[name=discountID]').val(),
					'DiscAmount' : noCommas,
					'coPayAmount' : $('input[name=coPayAmount]').val(),
					'hmoId' : $('input[name=hmoId]').val(),
					'cardName' : $('input[name=cardName]').val(),
					'modalSelect' : $('select[name=modalSelect]').val(),
					'ORnumber' : $('input[name=ORnumber]').val(),
					'cashAmount' : $('input[name=cashAmount]').val(),
					'gcashRefNo' : $('input[name=gcashRefNo]').val(),
					'gcashAmount' : $('input[name=gcashAmount]').val(),
					'modalCreditBank' : $('select[name=modalCreditBank]').val(),
					'creditRefNo' : $('input[name=creditRefNo]').val(),
					'creditAmount' : $('input[name=creditAmount]').val(),
					'modalChequeBank' : $('select[name=modalChequeBank]').val(),
					'chequeRefNo' : $('input[name=chequeRefNo]').val(),
					'chequeAmount' : $('input[name=chequeAmount]').val(),
					'modalOnlineBank' : $('select[name=modalOnlineBank]').val(),
					'onlineRefNo' : $('input[name=onlineRefNo]').val(),
					'onlineAmount' : $('input[name=onlineAmount]').val(),
					'totalAmount': $('input[name=totalAmount]').val(),
					'Agent': $('select[name=Agent]').val(),
					'AgentName': $('input[name=_agent]').val(),
					'Cardnumber': $('input[name=Hcard').val(),
					'PWD': $('input[name=discountID').val(),
					'ExpiryDatePWD':$('input[name="ExpiryDatePWD"]').val(),
					'pwdId': $('input[name="pwdId"]').val(),
					'PaymentType' : PaymentType,
					'Id':0,
					'_token': $('input[name=_token]').val()
				},
				
				function($data)
				{ 	
					
					parent.waitingDialog.hide();
					location.reload();
					modalRef.close();
					console.log("Response from the server:" + $data);
				}
			);
		}
	}]
});
var editOrModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="patientAdd-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		console.log(pageToLoad);
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
		id: 'btnsave',
    cssClass: 'btn-success hide',
    label: 'Save',
    action: function (modalRef){
		if( parent.required($('#pastqueueEditOr')) ) return false;
        var checkedRows = $('#ItemListTable tbody input[type="checkbox"]:checked');
        if (checkedRows.length === 0) {
            alert('Please select at least one row to save.');
            return false;
        }

        parent.waitingDialog.show('Saving...', { dialogSize: 'sm', progressType: 'warning' });

        checkedRows.each(function(index, element) {
            var $row = $(element).closest('tr');
            var queueId = $('input[name="_queueid"]').val();
            var ORNum = $row.find('input[name="ORNum"]').val();
			var PrevOR = $row.find('input[name="PrevOR"]').val();
            var Reasons = $row.find('input[name="Reasons"]').val();
			var TransId = $row.find('input[name="TransIds"]').val();
            parent.postData(
                "{{ '/cms/pastqueue/EditOr/' }}" + queueId,
                {
                    'ORNum': ORNum,
                    'Reasons': Reasons,
					'PrevOR' : PrevOR,
                    'Id': TransId,
                    '_method': "PUT",
                    '_token': $('input[name=_token]').val()
                },
                function($data) { 
                    // Handle success callback here if needed
                }
            );
        });

        location.reload();
       alert('OR Number is Updated Successfully');
    }
	
	}]
});
var packageModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('Code')+"/edit?id="+dialog.getData('IdItemPrice');
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


$(document).ready(function(e) {

	parent.getData("{{ asset('/json/PatientType.json') }}",null,
		function($data){
			$.each($data.patient, function(key,val){
			var selected =  false;
			$('select[name="PatientType"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="PatientType"]').selectize();
		var PatientType  = $('select[name="PatientType"]')[0].selectize;
		PatientType.setValue('{{ $datas->PatientType }}');
	});
	


	$html = "<div class=\"table-responsive\"><table id=\"TransactionListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th>Company Name</th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Doctor</th>";
			$html += "<th>Item Description</th>";
			$html += "<th>Card Number</th>";
			$html += "<th>Type</th>";
			$html += "<th>Status</th>";
			$html += "<th>Item Amount</th>";
			$html += "<th>Input By</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	var data = [];
	var datas = {!! $trans !!}; 
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
		
			$(row).attr('data-toggle-rowId', data.Id).attr('data-toggle-IdCompany', data.IdCompany).attr('data-toggle-IdDoctor', data.IdDoctor).attr('data-toggle-IdItemPrice', data.IdItemPrice).attr('data-toggle-code', data.CodeItemPrice).attr('data-toggle-group', data.PriceGroupItemPrice).attr('data-toggle-description', data.DescriptionItemPrice);
		},
		columns			: [
		{ "data": null },
		{ "data": "NameCompany", "render": function(data,type,row,meta) { return '<div class="wrap-row row-cis">'+data+'</div>'; }, className: 'header-company' },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) {  var isPackage = (row.PriceGroupItemPrice == "Package") ? 'row-package':'' ; return '<div class="wrap-row '+isPackage+'">'+data+'</div>'; }, className: 'header-package'  },
		{ "data": "NameDoctor", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{"data": "HCardNumber","render": function (data, type, row, meta) {if (row.GroupItemMaster === "CARD") {if (data && data.length >= 16) {var formattedData = data.slice(0, 4) + '-' + data.slice(4, 8) + '-' + data.slice(8, 12) + '-' + data.slice(12, 16);return '<div class="wrap-row">' + formattedData + '</div>'; } else { return '<div class="wrap-row">' + (data || '') + '</div>';} } else { return '';}}},
		{ "data": "TransactionType", "render": function(data,type,row,meta) {  return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "QueueStatus", "render": function(data,type,row,meta) { var red = (data == "For Billing")?'<font style="color:red;">'+data+'</font>':data; return '<div class="wrap-row">'+red+'</div>'; } },
		{ "data": "AmountItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row text-right">'+data+'</div>'; } },
		{ "data": "InputBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{ data: null, className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ data: null, targets: 1, "width":"300px" },
			{ data: null, className: 'data-row',targets: 2, "width":"90px" },
			{ "data": "NameDoctor", targets: 3, "width":"300px" },
			{ "data": "DescriptionItemPrice",targets: 4, "width":"500px", defaultContent: "" },
			{ "data": "",targets: 5, "width":"180px"},
			{ "data": "TransactionType",targets: 6, "width":"80px"},
			{ "data": "QueueStatus",targets: 7, "width":"80px"},
			{ "data": "AmountItemPrice",targets: 8, "width":"150px"},
			{ "data": "InputBy",targets: 9, "width":"50px"}

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
	//$('#TransactionListTable_filter').append('<button class="addbtn btn btn-success pull-left" type="button"> Add</button>');
	// Check if the userRole contains the specified string
  	$('#TransactionListTable_filter').append('<button id="editButton" class="editor btn btn-success pull-left" type="button"> Edit OR</button>');

	$('#TransactionListTable').dataTable().rowGrouping({
            	iGroupingColumnIndex: 3,
				iExpandGroupOffset:-1
            	/*sGroupingColumnSortDirection: "asc",
            	iGroupingOrderByColumnIndex: 0*/
	});
	var userRoleEncoded = '{{ session('userRole') }}';
	var userRole = decodeURIComponent(userRoleEncoded.replace(/&quot;/g, '"'));

	if (userRole.toLowerCase().includes('"ldap_role":"[RECEPTION-OIC]"'.toLowerCase())) {
		$('#editButton').show();
	} else {
		$('#editButton').hide();
	}


	$('#TransactionListTable').DataTable().rows().every(function () {
    var data = this.data();
    var queueStatus = data.QueueStatus;
    var editButton = $('#TransactionListTable_filter .editor');

    if (queueStatus !== 'For Payment') {
        editButton.removeClass('hidden');
    } else {
        editButton.addClass('hidden');
    }
	});
	// if(strpos(session('userRole') , '"ldap_role":"[RECEPTION-OIC]"') !== false ) {
		
	// } else{

	// } 
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

	$('.editor').on('click', function(e){
		var rowid = $('#TransactionListTable tr[data-toggle-rowid]').data('toggle-rowid');
		editOrModal.setTitle('Edit OR Number');
		editOrModal.setType(BootstrapDialog.TYPE_SUCCESS);
		editOrModal.setData('pageToLoad', '/cms/pastqueue/EditOr/' + $('input[name="_queueid"]').val() + '/edit');
		editOrModal.setData('transactionid', rowid);
		editOrModal.realize();
		editOrModal.open();
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

	// Buttons
	$('.postbtn').on('click',function(e){
		if(!$('input[name=IdPatient]').val()) return;
		var rows_selected = [];
		var loadData = table.rows().data();
		 loadData.each(function (value, index) {
			rows_selected.push(value.Id);
		});
		//table.clear().draw();
		addModal.setTitle("Billing - Transaction ({{$datas->FullName}})");
		addModal.realize();
		addModal.open();
		e.preventDefault();
	});
	
	$('.chargebtn').on('click',function(e){  
		if($(this).attr('disabled') !== 'disabled' )
		{
			var id = '{{ $datas->Id }}';
			var userId = '{{ Auth::user()->id }}';
			var hyperlink = document.createElement('a');
			hyperlink.href = "http://{{$_SERVER['SERVER_ADDR']}}:8080/jasperserver/flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FBilling&reportUnit=%2Freports%2FBilling%2FCS_v2&j_username=report&j_password=DnCMSReport&output=pdf&appointmentID="+id+"&myID="+userId;
			var mouseEvent = new MouseEvent('click', {
				view: window,
				bubbles: true,
				cancelable: true
			});
			//hyperlink.dispatchEvent(mouseEvent);
			//window.open(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
			window.open(hyperlink, '_blank', 'location=yes,height=800,width=6000,scrollbars=yes,status=yes');
			//e.preventDefault();
			waitingDialog.hide();
		}
	});

	$('.rtngbtn').on('click',function(e){  
		if($(this).attr('disabled') !== 'disabled' )
		{
			var id = '{{ $datas->Id }}';
			var userId = '{{ Auth::user()->id }}';
			var hyperlink = document.createElement('a');
			hyperlink.href = "http://{{$_SERVER['SERVER_ADDR']}}:8080/jasperserver/flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FBilling&reportUnit=%2Freports%2FBilling%2FRS_v2&j_username=report&j_password=DnCMSReport&output=pdf&appointmentID="+id+"&myID="+userId;
			var mouseEvent = new MouseEvent('click', {
				view: window,
				bubbles: true,
				cancelable: true
			});
			//hyperlink.dispatchEvent(mouseEvent);
			//window.open(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
			window.open(hyperlink, '_blank', 'location=yes,height=800,width=6000,scrollbars=yes,status=yes');
			//e.preventDefault();
			waitingDialog.hide();
		}
	});

	
	$('.orbtn').on('click',function(e){
		if($(this).attr('disabled') !== 'disabled' )
		{
			var id = '{{ $datas->Id }}';
			var userId = '{{ Auth::user()->id }}';

			var hyperlink = document.createElement('a');
			hyperlink.href = "http://{{$_SERVER['SERVER_ADDR']}}:8080/jasperserver/flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FBilling&reportUnit=%2Freports%2FBilling%2FOR_v2&j_username=report&j_password=DnCMSReport&output=pdf&appointmentID="+id+"&myID="+userId;
								
			var mouseEvent = new MouseEvent('click', {
				view: window,
				bubbles: true,
				cancelable: true
			});
			//hyperlink.dispatchEvent(mouseEvent);
			//window.open(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
			window.open(hyperlink, '_blank', 'location=yes,height=800,width=6000,scrollbars=yes,status=yes');
			//e.preventDefault();
			waitingDialog.hide();
		}
	});
	
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
	
	
	
}); //{{$_SERVER['SERVER_ADDR']}}
</script>
@endsection