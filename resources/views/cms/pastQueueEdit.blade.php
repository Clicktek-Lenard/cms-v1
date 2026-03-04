<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>
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
.position-absolute {
    position: absolute;
	right:0px;
    top: 50%;
    transform: translateY(-50%);
}
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li><a href="{{ '/cms/pastqueue' }}">Past Queue <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">View <span class="badge" style="top:-9px; position:relative;"></span></a></li>
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
					
		<div class="panel @if($datas->QueueStatus === 'Cancel for Adjusting Entry') panel-danger @else panel-primary @endif">
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
                                    <input type="text" class="typeahead form-control" name="PatientName" value="{{ $datas->FullName }}" placeholder="Patient Name" required="required" disabled="disabled" >
                                   	</div>
                                    <div class="input-group-btn">
                                       <button class="editbtn btn btn-success " type="button"  > view </button>
				</div>
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
								<textarea class="form-control" name="Notes" placeholder="Notes" disabled="disabled"  >{{ $datas->Notes }}</textarea>
							</div>
		</div>

						{{-- PICKUP & EMAIL --}}
						<div class="form-group row">
							<div class="col-sm-2 col-md-2"></div>
							<div class="col-sm-10 col-md-10">
								<div class="form-check form-check-inline">
									<input type="hidden" name="forPU" value="0"> 
									<input class="form-check-input" type="checkbox" name="forPU" id="forPU" value="1" {{ $forPU == 1 ? 'checked' : '' }} disabled>
									<label class="form-check-label" for="forPU" style="font-weight: bold;">PICK-UP</label>
									
									<input type="hidden" name="forEmail" value="0">
									<input class="form-check-input" type="checkbox" name="forEmail" id="forEmail" value="1" {{ $forEmail == 1 ? 'checked' : '' }} disabled>
									
									<label class="form-check-label" for="forEmail" style="font-weight: bold;">EMAIL</label>
								</div>
							</div>
						</div>
						{{-- PICKUP & EMAIL --}}

						
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
					<div class="input-group">
					
						<input type="text" class="form-control" value="{{ $datas->QueueStatus }}" placeholder="System Generated" readonly="readonly">
						<div class="input-group-btn">
							@if(strpos(session('userRole'), '"ldap_role":"[HL7BTN]"') !== false)
								<button @if($hl7Btn) disabled="disabled" @endif class="rebtn btn btn-primary" type="button" id="resendBtn">HL7</button>
							@endif
						</div>
					</div>
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
			<div class="col-xs-6">
			@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)	
				<button class="cancelbtn btn btn-warning col-xs-8 col-sm-8 col-md-6 col-lg-4 "  @if($datas->QueueStatus == 'Cancelled' || $datas->QueueStatus == 'Cancel for Adjusting Entry' || $datas->QueueStatus == 'Adjusting Entry' )  disabled="disabled" @else '' @endif style="border-radius:0px; line-height:29px;" type="button"> Amendment Que </button>
			@else
				<button class="btn btn-warning col-xs-8 col-sm-8 col-md-6 col-lg-4 "  style="border-radius:0px; line-height:29px;" disabled="disabled"  type="button"> Amendment Que </button>
			@endif	
				
				<button class="viewbtn btn btn-info col-xs-8 col-sm-8 col-md-6 col-lg-4" @if($disableButton) disabled="disabled" @endif style="border-radius:0px; line-height:29px;" type="button"> View - Deleted </button>
				@if(strpos(session('userRole'), '"ldap_role":"[BM-ROLE]"') !== false)		
					<a href="{{ url(session('userBU').'/cms/pastpayment/'.$datas->Id.'/edit') }}" class="approvalbtn btn btn-success col-xs-4 col-sm-4 col-md-4 col-lg-4"  @if($approveButtonDisabled) disabled="disabled" @endif style="border-radius:0px; line-height:29px;" type="button"> Amendment Approve </a>		
				@endif
			</div>
			@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
			<div class="col-md-6">
				<a href="{{ url(session('userBU').'/cms/pastpayment/'.$datas->Id.'/edit') }}" class="btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4 pull-right" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Payment</a>
			</div>
			@endif
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
						'<div class="description-section">'+data.DOB+'</div>' +
						'<div class="description-section">'+data.Gender+'</div>' +
						'<div class="image-section text-right" style="margin-top:-50px; z-index:205;"><a href="/picture/'+data.PictureLink+'" class="preview"><img src="/picture/'+data.PictureLink+'" width="100" height="100"></a></div>' +
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
   	$('input[name="DOB"]').val(data.DOB);
   	$('input[name="Gender"]').val(data.Gender);
	
	
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
	}
	]
});
var packageModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="edit-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('Code')+"/edit?id="+dialog.getData('IdItemPrice')+"&qid={{$datas->Id}}";
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


var deletedTransaction = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_INFO,
	data: {
		pageToLoad: ''
	},
	animate: false,
	closable: false,
	onshown: function(dialogRef){
        // Set focus to the 'btnSave' button after the dialog is shown
        $('#viewbtn').focus();
    },
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},

	]
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

var cancelTransaction = new BootstrapDialog({

	message: function(dialog) {
		var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_WARNING,
	data: {
		pageToLoad: ''
	},
	animate: false,
	closable: false,
	onshown: function(dialogRef){
        // Set focus to the 'btnSave' button after the dialog is shown
        $('#cancelbtn').focus();
    },
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnSave',
		cssClass: 'btn-warning actionbtn',
		label: 'Submit',
		action: function (modalRef){
		
			if( parent.required($('#cancelTransaction')) ) return false;
	
			var form = $('#cancelTransaction');
				parent.postData(form.attr('action'),form.serialize(),function($QueId){ 
				
				var hyperlink = document.createElement('a');
				
				var qURL = '/cms/queue/'+$QueId+'/edit';
				
				hyperlink.href = qURL;
				var mouseEvent = new MouseEvent('click', {
					view: window,
					bubbles: true,
					cancelable: true
				});
				
				hyperlink.dispatchEvent(mouseEvent);
				(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
				
				cancelTransaction.close();
				//window.location.reload();
			});
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
			totalAmount += parseInt(value.AmountItemPrice, 10);
		});
	iselect = "</span><div class=\"text-right col-xs-12 col-sm-12 col-md-12\" ><b>Grand Total Amount :</b> <span class=\"Gtotal-amount row-discount\"><B><font style=\"color:blue; font-size:20px;\">"+commaSeparateNumber(totalAmount)+"</font></B></span></div></div>";
	$('.selected_info').html("<a class=\"iselected\">"+selected+"</a> "+"row(s) selected"+"</span>");
	$('.Gtotal-amount').html(totalAmount);
	if( selectedName ) return iselect;
}

var pdfResultsModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="hpdf-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('pageId')+"/edit/?transid="+dialog.getData('transid');
		$message.load(pageToLoad);
		console.log(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/cms/api/hpdf' }}"
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
			$html += "<th>Action</th>";
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
		{ "data": "HCardNumber",
			"render": function (data, type, row, meta) {
			if (row.GroupItemMaster === "CARD" && row.QueueStatus === null) {
				var input = '<input type="text" placeholder="Card Number" name="CardNumber_' + row.Id + '" style="margin-left: 10px;" class="form-control card-number-input" oninput="formatCardNumber(this)"';
				if (row.HCardNumber && row.HCardNumber.length >= 16) {
					var dash = row.HCardNumber.slice(0, 4) + '-' + row.HCardNumber.slice(4, 8) + '-' + row.HCardNumber.slice(8, 12) + '-' + row.HCardNumber.slice(12, 16);
					input += ' value="' + dash + '"';
				} else {
					input += ' value="' + (row.HCardNumber || '') + '"';
				}
				input += ' required/></div></div>';
				return '<div class="wrap-row"><div>' + input + '</div></div>';
			} else {
				var formattedCardNumber = '';
				if(row.GroupItemMaster === 'CARD'){
				if (row.HCardNumber && row.HCardNumber.length >= 16) {
					formattedCardNumber = row.HCardNumber.slice(0, 4) + '-' + row.HCardNumber.slice(4, 8) + '-' + row.HCardNumber.slice(8, 12) + '-' + row.HCardNumber.slice(12, 16);
				} else {
					formattedCardNumber = row.HCardNumber || '';
				}
				}
				return '<div class="wrap-row">' + formattedCardNumber + '</div>';
			}
		}},
		{ "data": "TransactionType", "render": function(data,type,row,meta) {  return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "QueueStatus", "render": function(data,type,row,meta) { var red = (data == "Need to Save")?'<font style="color:red;">'+data+'</font>':data; return '<div class="wrap-row">'+red+'</div>'; } },
		{ "data": "AmountItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row text-right">'+data+'</div>'; } },
		{ "data": "", "render": function(data,type,row,meta) 
			{  
				var showDiv = "";
				
					if( row.Status == "500" ||  row.Status == "600" )
					{
						showDiv +=  '<div class="pdfResult wrap-row text-center bordered-icon"><i style="color:blue;cursor:pointer;" class="fa fa-file-pdf-o fa-6" aria-hidden="true"></i></div>';
					}
				
				
				return  showDiv; 
		} },
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
			{ "data": null, className: '', targets: 9, "width":"50px"},
			{ "data": "InputBy",targets: 10, "width":"50px"}
			
		],
		order			: [ 4, 'asc' ],
		dom:            "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
		fnInfoCallback: function( settings, start, end, max, total, pre ) {	
			return "<div class=\" col-xs-12 col-sm-12 col-md-12 col-lg-12\"> <div  class=\" col-xs-6 col-sm-6 col-md-6\"  >"+"Got a total of"+" "+total+" "+"entries "+"</div><div  class=\" col-xs-6 col-sm-6 col-md-6\"  ><span class=\"selected_info\">"+selectedInfo(true,data);
		}
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	$('#TransactionListTable_info').addClass('col-xs-12 col-sm-12 col-md-12 col-lg-12');
	@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
		$('#TransactionListTable_filter').append('<button id="editButton" class="editor btn btn-success pull-left hidden" type="button"> Edit OR</button>');
	@endif
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

		
	$('.viewbtn').on('click', function(e){
		var rowid = $('#TransactionListTable tr[data-toggle-rowid]').data('toggle-rowid');
		deletedTransaction.setTitle('Transaction History');
		deletedTransaction.setType(BootstrapDialog.TYPE_INFO);
		deletedTransaction.setData('pageToLoad', '/cms/pastqueue/deletedTransaction/' + $('input[name="_queueid"]').val() + '/edit');
		deletedTransaction.setData('transactionid', rowid);
		deletedTransaction.realize();
		deletedTransaction.open();
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
	//$clinic.setValue("{{$defaultClinic}}");
	
	
	
	$('.editbtn').on('click',function(e){
		patientAddModal.setTitle("Patient - Edit");
		patientAddModal.setType(BootstrapDialog.TYPE_SUCCESS);
		patientAddModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient' }}/"+$('input[name="IdPatient"]').val()+'/edit');
		patientAddModal.realize();
		patientAddModal.open();
		e.preventDefault();
	});
	
	$('.cancelbtn').on('click', function(e){
		var rowid = $('#TransactionListTable tr[data-toggle-rowid]').data('toggle-rowid');
		cancelTransaction.setTitle('Amendment Que');
		cancelTransaction.setType(BootstrapDialog.TYPE_WARNING);
		cancelTransaction.setData('pageToLoad', '/cms/pastqueue/cancelTransaction/' + $('input[name="_queueid"]').val() + '/edit');
		cancelTransaction.setData('transactionid', rowid);
		cancelTransaction.realize();
		cancelTransaction.open();
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


	$('.approvalbtn').on('click', function(e){

		if($(this).attr('disabled') == "disabled")
		{
			return false;
		}

		var queueId=$('input[name="_queueid"]').val();
		var csrfToken = $('meta[name="csrf-token"]').attr('content');

		$.ajax({
			type: 'POST',
			sync: false, // added for undefined array issue
			url: '{{ route('approvaltransaction') }}',
			headers: {
				'X-CSRF-TOKEN': csrfToken,
			},
			data: { queueId : queueId },
			success: function (response) {
				alert('Amendment Approve');
				//location.reload(true);
			},
			error: function (error) { 
				console.error('test');
			}
		});
	});
	
	$('#TransactionListTable').on('click','.pdfResult',function(e){
		pdfResultsModal.setTitle("PDF - Results");
		pdfResultsModal.setData("pageId",$('input[name="_queueid"]').val());
		pdfResultsModal.setData("transid",$(this).closest('tr').data('toggle-rowid') );
		pdfResultsModal.realize();
		pdfResultsModal.open();
		e.preventDefault();
	});
		
	//pastresendHL7btn//
	$(document).on('click', '#resendBtn', function() {

		var queueId=$('input[name="_queueid"]').val();

		$.ajax({
			url: '/cms/queue/pastresendHL7',  
			type: 'POST',
			data: {
				queueId: queueId,   
				_token: '{{ csrf_token() }}'   
			},
			success: function(response) {
				
				if (response.success) {
					alert('HL7 sent successfully.!');
					location.reload();
				} else {
					alert('Failed to send HL7.');
				}	
			},
			error: function(xhr, status, error) {
				console.error('Error occurred:', error);
			}
		});
	});	

	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
	
	
	
});
</script>
@endsection