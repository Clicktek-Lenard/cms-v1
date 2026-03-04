<!--@extends('app')--> <!--MLB-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">


<style>
.ui-datepicker-div{ z-index:2003 !important;}
.ui-datepicker {
z-index: 1001;
}
.form-control
{
    font-weight: bolder;
}

.cms-font
{
    font-weight: bolder;
}

.table
{
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
.data-row-company{ color: #337AB7;text-decoration: none; cursor:pointer;}
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
.position-absolute {
    position: absolute;
	right:0px;
    top: 50%;
    transform: translateY(-50%);
}
.tt-input.loading {
    background: transparent url('/images/ajax-loader.gif') no-repeat scroll right center content-box !important;
}
.bordered-icon {
    display: inline-block;
    padding: 5px; /* Adjust the padding as needed */
    border: 1px solid transparent; /* Adjust the border color and style as needed */
    border-radius: 5px; /* Adjust the border radius as needed */
    margin: 2px; /* Adjust the margin as needed */
}
.queItemRemove{
    background-color: #ffcccc; 
}
.queItemRemove:hover{
    background-color: #c9302c;
}
.editTransaction{
	background-color: #ccffcc;
}
.editTransaction:hover{
	background-color: #449d44;
}
.pdfResult{
	background-color: #cfe2f3;
}
.pdfResult:hover{
	background-color: #3d85c6;
}

/* FOR PICKUP & EMAIL CSS */
.form-check-input {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    transition: color 0.3s ease; 
}


.form-check-label:hover {
    color: #007bff; 
}


.form-check-input:hover {
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.6); 
}


.form-check-input:checked {
    background-color: #007bff; 
    border-color: #007bff;    
}
/* FOR PICKUP & EMAIL CSS */
.queue-slip {
    align-items: center;
    display: flex;
    flex-direction: column;
    margin: 0;
    padding: 0;
    visibility: hidden;
	display: none;
    font-family: 'Cooper Black';
}
</style>
@endsection

@section('content')
<div class="queue-slip">
    <center><div class="branch">D. Tuazon</div></center>
	<br>
    <!-- <center>
	<div class="number"  style="font-family: 'Times New Roman', Times, serif;font-size: 50px;  font-weight: bold;">{{ substr(session('PrintQueue'), 3) }}</div>
    </center> -->
    <center>
  	<div class="station" style="font-family:'Times New Roman', Times, serif; font-size: 30px; font-weight: bold; border: 2px solid black; padding: 10px; display: inline-block;">To Comeback</div>
    </center>
	<br>
    <center>
	<div  class="datetime" style="font-family:'Times New Roman', Times, serif;font-size: 15px;"></div>
    </center>
    <center>
  	<div id="barcode"></div>
    </center>
	<br>
	<center>
	<div class="message"  style="font-family:'Times New Roman', Times, serif;font-size: 12px;">        This slip is for <strong>To Comeback</strong>.  
        <br>  
        Please scan the barcode when you return to the branch.
		<br>
		Transaction Date: <strong>{{ date('d-M-Y H:i:s',strtotime($datas->DateTime)) }}</strong>
	</div>
    </center>
</div>

<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li><a href="{{ '/cms/queue' }}">Queue <span class="badge cms-font" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Edit <span class="badge cms-font" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-queue">
         <form id="formQueueEdit" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}" autocomplete="off">
		<input type="hidden" name="reUpdate" value="reUpdate">
		<input type="hidden" name="_queueid" value="{{$datas->Id}}">
		<input type="hidden" name="_idphysician" value="{{ isset($IdDoctor[0]) ? $IdDoctor[0]->IdDoctor : '' }}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		
		<div class="hide col-sm-2 col-md-2 pad-left-1-md text-left-md">
			<label class="bold">Patient Type</label>
		</div>
		<div class="hide col-sm-4 col-md-4 pad-0-md">
			<select name="PatientType" class="form-control" placeholder="Patient Type" data-placeholder="Patient Type" required="required" >
				<option></option>
			</select>
		</div>
		
		<div class="hide col-sm-2 col-md-2 pad-left-1-md text-left-md">
			<label class="bold ">Clinic</label>
		</div>
		<div class="hide col-sm-4 col-md-4 pad-0-md">
			<input type="hidden" name="ClinicCode" value="{{ $datas->IdClinic }}" />
			<select name="Clinic" class="form-control disabled" placeholder="Clinic" required="required" disabled="disabled">
				<option value=""></option>
				@foreach ($clinics as $clinic) 
					<option value="{{ $clinic->Code }}"  @if($datas->IdBU == $clinic->Code  ) selected @else '' @endif  >{{ $clinic->Description }}</option>
				@endforeach
			</select>
		</div>

        	<div class="panel panel-primary">
				<div class="panel-heading cms-font" style="line-height:12px;">Info </div>
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
                                    <input type="text" class="typeahead form-control cms-font" name="PatientName" value="{{ $datas->FullName }}" placeholder="Patient Name" required="required" >
                                   	</div>
                                    <div class="input-group-btn">
					@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') !== false)
						<button class="btn btn-warning disabled" type="button" disabled="disabled"> New </button>
						<button class="btn btn-success " type="button" disabled="disabled"  > Edit </button>
					@else
						@if( $datas->QStatusId  >= '201')
							<button class="btn btn-warning disabled" type="button" disabled="disabled"> New </button>
						@else
							<button class="newbtn btn btn-warning" type="button"> New </button>
						@endif
						<button class="editbtn btn btn-success" type="button"> Edit </button>
					@endif
				</div>
                                </div>
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-2 col-md-2 pad-0-md text-right-md ">
								<label class="bold ">Date of Birth<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-3 col-md-3">
								<input type="text" class="form-control" name="DOB" value="{{ $datas->DOB && $datas->DOB !== '0000-00-00' && preg_match('/\d/', $datas->DOB) ? date('d-M-Y', strtotime($datas->DOB)) : '' }}" placeholder="Date of Birth" readonly="readonly" required="required">
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
								<textarea class="form-control" name="Notes" placeholder="Notes">{{ $datas->Notes }}</textarea>
							</div>
						</div>

						{{-- PICKUP & EMAIL --}}
						<div class="form-group row">
							<div class="col-sm-2 col-md-2"></div>
							<div class="col-sm-10 col-md-10">
								<div class="form-check form-check-inline">
									<input type="hidden" name="forPU" value="0"> 
									<input class="form-check-input" type="checkbox" name="forPU" id="forPU" value="1" {{ $forPU == 1 ? 'checked' : '' }}>
									<label class="form-check-label" for="forPU" style="font-weight: bold;">PICK-UP</label>
									
									<input type="hidden" name="forEmail" value="0">
									<input class="form-check-input" type="checkbox" name="forEmail" id="forEmail" value="1" {{ $forEmail == 1 ? 'checked' : '' }}>
									
									<label class="form-check-label" for="forEmail" style="font-weight: bold;">EMAIL</label>
								</div>
							</div>
						</div>
						{{-- PICKUP & EMAIL --}}
                        
					</div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
						<!-- START -->
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
					<label class="bold">Medication</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" name="Medication" class="form-control" placeholder="Medication" value="{{ $medication }}">
				</div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
					<label class="bold ">Queue Status.</label>
                </div>
                    <div class="col-sm-4 col-md-4 pad-0-md">
						<div class="input-group">
							<input type="text" class="form-control" name = "qstatus" value="{{ $datas->QueueStatus }}" placeholder="System Generated" readonly="readonly">
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
					<label class="bold">Last Dose</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="datetime-local" name="LastDose" class="form-control datepicker LastDose" placeholder="Last Dose" value="{{ $lastDose }}" step="1" >
				</div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
					<label class="bold ">Input By</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" class="form-control" value="{{ $datas->InputBy }}" placeholder="System Generated" readonly="readonly">
				</div>
			</div>
			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
					<label class="bold">Last Period</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" name="LastPeriod" class="form-control datepicker LastPeriod" placeholder="Last Menstrual Period" @if(empty($lastPeriod)){ value='' }@else{ value={{ date('m/d/Y',strtotime($lastPeriod)) }} }@endif" >
				</div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
					<label class="bold ">Date Time</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<input type="text" class="form-control " name="Date" value="{{ date('d-M-Y H:i:s',strtotime($datas->DateTime)) }}" placeholder="Date" readonly="readonly">
				</div>
			</div>

			<div class="row form-group row-md-flex-center">
				<div class="col-sm-6 col-md-6"></div>
				<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md"></div>
				<div class="col-sm-2 col-md-2 pad-0-md">
					<input class="form-check-input" type="checkbox" name="toComeback" id="toComeback">
					<label class="form-check-label" for="toComeback" style="font-weight: bold;">To Comeback</label>
				</div>
				<div class="col-sm-2 col-md-2 pad-0-md text-right">
					<button type="button" id="generateQR" class="btn btn-success" style="display: none;">Generate QR</button>
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
				<button class="viewbtn btn btn-info col-xs-4 col-sm-4 col-md-4 col-lg-4" @if($disableButton) disabled="disabled" @endif style="border-radius:0px; line-height:29px;" type="button"> View - Deleted </button>
			@if(strpos(session('userRole'), '"ldap_role":"[BM-ROLE]"') !== false)		
				<a href="{{ url(session('userBU').'/cms/pastpayment/'.$datas->Id.'/edit') }}" class="approvalbtn btn btn-success col-xs-4 col-sm-4 col-md-4 col-lg-4"  @if($approveButtonDisabled) disabled="disabled" @endif style="border-radius:0px; line-height:29px;" type="button"> Amendment Approve </a>		
			@endif
			@if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') !== false)
				<button class="reGeneratebtn btn btn-danger col-xs-4 col-sm-4 col-md-4 col-lg-4" style="border-radius:0px; line-height:29px;" type="button"> Re-Generate Lab PDF Result </button>				
			@endif
		</div>
		<div class="col-xs-6">
			@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
			<button class="col-xs-4 col-sm-4 col-md-4 col-lg-4 " style="visibility:hidden;"></button>
			<a href="{{ url(session('userBU').'/cms/payment/'.$datas->Id.'/edit') }}" class=" paymentbtn btn btn-warning col-xs-4 col-sm-4 col-md-4 col-lg-4" @if($paymentButtonDisabled) disabled="disabled" @endif style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Payment</a>
				@if( $datas->QStatusId  >= '210')
					<a class="savebtn btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4 disabled" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Save</a>					
				@else
					<a class="savebtn saving btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Save</a>					
				@endif
			@endif	
		</div>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.0/jQuery.print.min.js"></script>

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
function BirthDateFormat(dateString) {
	if (dateString == '0000-00-00') {
        return '';
    }
    // Creating a Date object from the provided date string
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
				$('input[name="DOB"]').val(BirthDateFormat($dataSelected.DOB));
				$('input[name="Gender"]').val($dataSelected.Gender);
				$('input[name="PatientName"]').val($dataSelected.FullName);
				$('input[name="Age"]').val(calculateAge($dataSelected.DOB));
				$('.newbtn').addClass('disabled').attr('disabled', true);
				$('.editbtn').removeClass('disabled').attr('disabled', false);
				patientAddModal.close();
				
			});
		}
	},
	{
		id: 'btnSave',
		cssClass: 'btn-primary actionbtn',
		label: 'Save',
		action: function (modalRef){
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
		'pageToLoad': "{{ '/cms/queue/pages/transactionTemp/'.$datas->Id.'/edit?_ntoken='.csrf_token() }}"
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
		cssClass: 'btn-success actionbtn saving',
		label: 'Add Transaction/ Close',
		action: function (modalRef){
			parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
			var itemSelected = []; //fnGetNodes
			$("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function(){
				var qty = parseInt($(this).closest('tr').find('input[name=Qty]').val()) || 1;
				var id = $(this).val();
				var group = $(this).closest('tr').find('input[name=Qty]').data('group'); // assuming you have a data attribute for Group
				var subgroup = $(this).closest('tr').data('toggle-subgroup');
				var itemused = $(this).closest('tr').data('toggle-itemused');
				console.log("Subgroup:", subgroup);
				for (var i = 0; i < qty; i++) {
					itemSelected.push({ "Id": id, "Group": subgroup, "ItemUsed": itemused});
				}
			});
		
			parent.postData(
				"{{ '/cms/queue/pages/transactionTemp' }}",
				{
					'PatientId': $('input[name=IdPatient]').val(),
					'itemSelected':itemSelected,
					'queueId':  $('input[name="_queueid"]').val(),
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
					if($data.length != 0)
					{
						if( $data[0].Status == "50")
						{
							$('.savebtn').removeClass('disabled');
						}
					}
					addModal.close();
					parent.waitingDialog.hide();
				}
			);
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
	}]
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

var removeModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="remove-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('pageId')+"/edit";
		$message.load(pageToLoad);
		console.log(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/cms/pages/transaction' }}"
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
		cssClass: 'btn-warning actionbtn',
		label: 'Remove & Save',
		action: function (modalRef){
			if( parent.required($('#queueTransactionRemoveModalForm')) ) return false;
			parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
			
			parent.postData(
				"{{ '/cms/pages/transaction/' }}"+modalRef.getData('pageId'),
				{
					'reason':$('input[name=modalReason]').val(),
					'Id':modalRef.getData('pageId'),
					'QID': '{{$datas->Id}}',
					'_method': "PUT",
					'_token': $('input[name=_token]').val()
				},
				function($data)
				{ 	
					location.reload();
					alert($data);
					
					//$('#TransactionListTable').dataTable().fnClearTable();
					//$('#TransactionListTable').DataTable().rows.add( $data ).draw();
					//removeModal.close();
					//parent.waitingDialog.hide();
				}
			);	
			
		}
	}]
});

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
var editTransactionModal = new BootstrapDialog({
    message: function(dialog) {
        var $message = $('<div class="edit-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
        var pageToLoad = dialog.getData('pageToLoad') + "/" + dialog.getData('transid') + "/edit";
        $message.load(pageToLoad);
      
        return $message;
    },
    size: BootstrapDialog.SIZE_WIDE,
    type: BootstrapDialog.TYPE_SUCCESS,
    data: {
        'pageToLoad': "{{ url('/cms/pages/editTransaction') }}"
    },
    animate: false,
    closable: false,
    buttons: [{
        cssClass: 'btn-default modal-closebtn',
        label: 'Close',
        action: function(modalRef) {
            console.log('Close button clicked');
            location.reload(true); 
        }
    }, 
	{
        id: 'btnsave',
        cssClass: 'btn-primary actionbtn saving',
        label: 'Save',
        action: function(modalRef) {
            console.log('Save button clicked');
            if (parent.required($('#editTransactionModal'))) return false;  

            parent.waitingDialog.show('Saving...', { dialogSize: 'sm', progressType: 'warning' });
            var itemSelected = [];
		
            $("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function() {
				var id = $(this).closest('tr').attr('data-toggle-iditem'); 
                var qty = parseInt($(this).closest('tr').find('input[name=Qty]').val()) || 1;
				var subgroup = $(this).closest('tr').data('toggle-subgroup');
				var itemused = $(this).closest('tr').data('toggle-itemused');
                itemSelected.push({ "IdItem": id, "Group": subgroup, "ItemUsed": itemused });  
	
            });

            parent.postData(
               "{{ url('/cms/pages/editTransaction') }}/" + modalRef.getData('transid'),
                {
                    'itemSelected': itemSelected,
                    'DoctorId': $('select[name=modalDoctors]').val(),
                    'CompanyId': $('select[name=modalCompanys]').val(),
                    'TransactionTypeId': $('select[name=modalTransactionType]').val(),
                    'Id':modalRef.getData('transid'),
                    '_method': "PUT",
                    '_token': $('input[name=_token]').val()
                },
                function($data) {
                    console.log('Post data success callback');
                    $('#TransactionListTable').dataTable().fnClearTable();
                    $('#TransactionListTable').DataTable().rows.add($data).draw();
                    editTransactionModal.close();
                    parent.waitingDialog.hide();
                }
            );
			location.reload();
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
var physicianEditInfo = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="physicianTableModalInfo"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_WARNING,
	data: {
		// pageToLoad: '/cms/queue/physicianTableListEdit'
	},
	animate: false,
	closable: false,
	buttons: [
		{
			cssClass: 'btn-default modal-closebtn',
			label: 'Close',
			action: function (modalRef) {
				//$('.physicianTableModal').close();
				modalRef.close();		
			}
		},
		{
			id: 'btnsave',
			cssClass: 'btn-success actionbtn disabled',
			label: 'Save',
			action: function (modalRef){

				if ($(this).prop('disabled')) {
					return false;
				}

				var csrfToken = $('meta[name="csrf-token"]').attr('content');

				var selectedId = $('input[name="_selected"]').val();
				var lastname = $('input[name="lastname"]').val();
				var firstname = $('input[name="firstname"]').val();
				var middlename = $('input[name="middlename"]').val();
				var fullname = $('input[name="fullname"]').val();
				var prcno = $('input[name="prcno"]').val();
				var description = $('select[name="description"]').val();
				var status = $('input[name="status"]').val();
				var myimage = $('input[name="myimage"]').val();
				var doctorStatus = $('input[name="_status"]').val();

				if (!lastname || !firstname || !prcno || !myimage || myimage === 'no-image.jpg') {
					alert('Please fill out all required fields (Last Name, First Name, PRC No, Prescription).');
					return;
				}

				if (doctorStatus === "RP - Leads" || doctorStatus === "Active" || doctorStatus === "Inactive" || doctorStatus === "RP - For Approval")
				{
						// empty
				}else{
						$.ajax({
						method: 'POST',
						url: '/cms/queue/physician-edit?idQueue=' + $('input[name="_queueid"]').val(),
						headers: {
							'X-CSRF-TOKEN': csrfToken,
						},					
						data: {selectedId: selectedId,
								lastname: lastname, 
								firstname: firstname, 
								middlename: middlename, 
								fullname: fullname, prcno: prcno, 
								description: description, 
								status: status, 
								myimage: myimage 
							},
						success: function(response) {
							alert('Physician updated successfully');
							location.reload();
							modalRef.close();
							
							if(myimage) {
								$('.image-tag').val(myimage);
							}
							var $sdoctor = $('select[name="modalDoctors"]')[0].selectize;
								$sdoctor.setValue(selectedId);
							// $sdoctor = $idoctors[0].selectize;
							// $sdoctor.setValue(selectedId);
							// $('select[name="modalDoctors"]').val(selectedId).trigger('change');
						},
						error: function(xhr) {
							alert('Error occurred: ');
						}
					});
				}				
			}
		}
		
	],
	onshown: function (dialogRef) {
		setTimeout(function () {
			var doctorStatus = $('input[name="_status"]').val();
			var branchCode = $('input[name="branchCode"]').val();
			var userClinicCode = '{{ session('userClinicCode') }}';

			console.log('STATUS:', doctorStatus);

			if (doctorStatus === "RP - Leads" || doctorStatus === "Active" || doctorStatus === "Inactive" || doctorStatus === "RP - For Approval") {
				dialogRef.getButton('btnsave').disable(); // Disable Save button
			} 
			else if (!branchCode) {
				dialogRef.getButton('btnsave').enable();
			} 
			else if (userClinicCode !== branchCode) {
				dialogRef.getButton('btnsave').disable();
			} 
			else {
				dialogRef.getButton('btnsave').enable();
			}
		}, 100);
	},
});

var physicianViewInfo = new BootstrapDialog({
    message: function(dialog) {
        var $message = $('<div class="physicianTableModalView"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
        var pageToLoad = dialog.getData('pageToLoad');
        $message.load(pageToLoad);
        return $message;
    },
    size: BootstrapDialog.SIZE_WIDE,
    type: BootstrapDialog.TYPE_WARNING,
    data: {

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
            id: 'btnapprove',
            cssClass: 'btn-success actionbtn disabled',
            label: 'Approve',
            action: function (modalRef) {
				if ($(this).prop('disabled')) {
					return false;
				}
                var idphysician = $('input[name="_selected"]').val();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var fullname = $('input[name="fullname"]').val();
				var doctorStatus = $('input[name="_status"]').val();
				if (doctorStatus === "RP - Leads" || doctorStatus === "Active" || doctorStatus === "Inactive") {
						// empty
				}else{
					$.ajax({
						type: 'POST',
						url: '/cms/queue/physician-approval?idQueue=' + $('input[name="_queueid"]').val(),
						headers: {
							'X-CSRF-TOKEN': csrfToken,
						},
						data: {
							idphysician: idphysician,
							fullname: fullname,
							
						},
						success: function(response) {
							alert('Physician Updated');
							location.reload();
							modalRef.close();
						},
						error: function(error) {
							console.error('An error occurred');
						}
					});
				}
            }
        },
        {
			id: 'btndecline',
			cssClass: 'btn-danger actionbtn disabled',
			label: 'Decline',
			action: function (modalRef) {
				if ($(this).prop('disabled')) {
					return false;
				}

				var idDoctor = $('input[name="_selected"]').val();
				var doctorStatus = $('input[name="_status"]').val(); // make sure this is retrieved here
				console.log("idDoctor:", idDoctor);

				if (doctorStatus === "RP - Leads" || doctorStatus === "Active" || doctorStatus === "Inactive") {
					// empty
				} else {
					modalRef.close(); 
					declinePhysician.setTitle('Reason For Declining');
					declinePhysician.setType(BootstrapDialog.TYPE_WARNING);
					declinePhysician.setData('pageToLoad', '/cms/queue/physicianDeclineModal/' + idDoctor + '/edit');
					declinePhysician.realize();
					declinePhysician.open();
				}
			}
		}    
    ],
	onshown: function (dialogRef) { //
		setTimeout(function () { // add delay to load the data after the modal is render
			var branchCode = $('input[name="branchCode"]').val();
			var userClinicCode = '{{ session('userClinicCode') }}';
			var userRole = @json(session('userRole'));
			var accessRole = userRole && userRole.includes('"ldap_role":"[BM-ROLE]"');
			var doctorStatus = $('input[name="_status"]').val();
console.log('Physician Status:', doctorStatus);

				if (doctorStatus === "RP - Leads" || doctorStatus === "Active" || doctorStatus === "Inactive") {
					dialogRef.getButton('btnapprove').disable();
					dialogRef.getButton('btndecline').disable();
				} else if (!branchCode) {
					dialogRef.getButton('btnapprove').enable();
					dialogRef.getButton('btndecline').enable();
				} else if (userClinicCode !== branchCode) {
					dialogRef.getButton('btnapprove').disable();
					dialogRef.getButton('btndecline').disable();
				} else {
					// Apply the additional condition for BM-ROLE
					if (accessRole) {
						dialogRef.getButton('btnapprove').enable();
						dialogRef.getButton('btndecline').enable();
					} else {
						dialogRef.getButton('btnapprove').disable();
						dialogRef.getButton('btndecline').disable();
					}
				}
		}, 100);
	},
});


var declinePhysician = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="physicianReasonModal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_MEDIUM,
	type: BootstrapDialog.TYPE_INFO,
	data: {
		// pageToLoad: '/cms/queue/physicianTableList'
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
			cssClass: 'btn-primary actionbtn',
			label: 'Submit',
			action: function (modalRef){

				var physicianId =  $('input[name="_selected"]').val();
				var reason = $('select[name="reason[]"]').val();
				
				var csrfToken = $('meta[name="csrf-token"]').attr('content');

				$.ajax({
					type: 'POST',
					url: '/cms/queue/physician-decline?idQueue=' + $('input[name="_queueid"]').val(),
					headers: {
						'X-CSRF-TOKEN': csrfToken,
					},
					data: { 		
						reason : reason,
						physicianId:physicianId
					  },
					success: function (response) {
						alert('Reason Submit');
						modalRef.close(); 
						location.reload();
					},
					error: function (error) { 
						console.error('test'); 
					}
				});
			}
		}
	]
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

function formatDate(date, setTimeToEndOfDay = false) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear(),
        hour = setTimeToEndOfDay ? '23' : '00',
        minute = setTimeToEndOfDay ? '59' : '00',
        second = setTimeToEndOfDay ? '59' : '00';

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-') + ' ' + [hour, minute, second].join(':');
}

let msgQueue = @json($msgQueue ?? []);

var editTransactionModal = new BootstrapDialog({
    message: function(dialog) {
        var $message = $('<div class="edit-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
        var pageToLoad = dialog.getData('pageToLoad') + "/" + dialog.getData('transid') + "/edit";
        $message.load(pageToLoad);
      
        return $message;
    },
    size: BootstrapDialog.SIZE_WIDE,
    type: BootstrapDialog.TYPE_SUCCESS,
    data: {
        'pageToLoad': "{{ url('/cms/pages/editTransaction') }}"
    },
    animate: false,
    closable: false,
    buttons: [{
        cssClass: 'btn-default modal-closebtn',
        label: 'Close',
        action: function(modalRef) {
            console.log('Close button clicked');
            location.reload(true);  // Example: Reload page on close
        }
    }, 
	{
        id: 'btnsave',
        cssClass: 'btn-primary actionbtn saving',
        label: 'Save',
        action: function(modalRef) {
            console.log('Save button clicked');
            if (parent.required($('#editTransactionModal'))) return false;  // Example: Frontend validation check

            parent.waitingDialog.show('Saving...', { dialogSize: 'sm', progressType: 'warning' });
            var itemSelected = [];
		
            $("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function() {
				var id = $(this).closest('tr').attr('data-toggle-iditem'); 
                var qty = parseInt($(this).closest('tr').find('input[name=Qty]').val()) || 1;
				var subgroup = $(this).closest('tr').data('toggle-subgroup');
				var itemused = $(this).closest('tr').data('toggle-itemused');
                itemSelected.push({ "IdItem": id, "Group": subgroup, "ItemUsed": itemused });  
	
            });

            parent.postData(
               "{{ url('/cms/pages/editTransaction') }}/" + modalRef.getData('transid'),
                {
                    'itemSelected': itemSelected,
                    'DoctorId': $('select[name=modalDoctors]').val(),
                    'CompanyId': $('select[name=modalCompanys]').val(),
                    'TransactionTypeId': $('select[name=modalTransactionType]').val(),
                    'Id':modalRef.getData('transid'),
                    '_method': "PUT",
                    '_token': $('input[name=_token]').val()
                },
                function($data) {
                    console.log('Post data success callback');
                    $('#TransactionListTable').dataTable().fnClearTable();
                    $('#TransactionListTable').DataTable().rows.add($data).draw();
                    editTransactionModal.close();
                    parent.waitingDialog.hide();
                }
            );
			location.reload();
        }
    }]
});



$(document).ready(function(e) {

	var Canceltransaction = "{{$trans}}";

	//var TodayDate = new Date();
	//var dateLastDose = $('input[name="LastDose"]');
	//var dateLastPeriod = $('input[name="LastPeriod"]');

	//dateLastDose.val(formatDate({{'$lastDose'}}));
	//dateLastPeriod.val(formatDate(TodayDate, false));

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
			$html += "<th class=\"text-center\"><input class=\"hidden\" name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\" ></th>";
			$html += "<th>Company Name</th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Doctor</th>";
			$html += "<th>Item Description</th>";
			$html += "<th>Card Number</th>";
			$html += "<th>Type</th>";
			$html += "<th>Status</th>";
			$html += "<th>Stat</th>";
			$html += "<th>Item Amount</th>";
			$html += "<th class=\"text-center\">Action</th>";
			$html += "<th>Input By</th>";
			$html += "<th>Readers Fee</th>";
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
			$(row).attr('data-toggle-rowId', data.Id).attr('data-toggle-IdCompany', data.IdCompany).attr('data-toggle-IdDoctor', data.IdDoctor).attr('data-toggle-IdItemPrice', data.IdItemPrice).attr('data-toggle-itemused', data.ItemUsedItemPrice).attr('data-toggle-code', data.CodeItemPrice).attr('data-toggle-group', data.PriceGroupItemPrice)
			.attr('data-toggle-description', data.DescriptionItemPrice).attr('data-toggle-IdPastQueue', data.AnteDateQueueID);
		},
		columns			: [
		{ "data": null },
		{ "data": "Id", "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) {  return '<input type="checkbox" checked="checked" class="hidden"  name="id[]" data-toggle-QueueStatus="'+row.QueueStatus+'" value="'+data+'">';}  },
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
		{ "data": "QueueStatus", "render": function(data,type,row,meta) 
		{var qStatus = data === null ? "Click Save" : data; return '<div class="wrap-row">'+ qStatus +'</div>';}},
		{ "data": "Stat", "render": function(data,type,row,meta) { if (row.GroupItemMaster === "CARD") {return "";}else{ var thisChecked = (data == "Yes") ? 'checked="checked"' : ""; 	return (row.CodeItemPrice == "SF")?"": '<input type="checkbox" name="Statfee_' + row.Id + '" class="wrap-row text-right"  '+thisChecked+' >';} } },
		{ "data": "AmountItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row text-right">'+data+'</div>'; } },
		{ 
			"data": "", 
			"render": function(data, type, row, meta) {  
				var showDiv = "";
				var hasReceptionOIC = {!! json_encode(strpos(session('userRole'), '"ldap_role":"[RECEPTION-OIC]"') !== false) !!};
				var hasHL7BTN = {!! json_encode(strpos(session('userRole'), '"ldap_role":"[HL7BTN]"') !== false) !!};

				let isDisabled = row.PriceGroupItemPrice === "Package" || row.Status === 888 || row.Status === 899 || row.Status === 900 || row.GroupItemMaster === "CLINIC" ? "disabled" : "";
				let isQueued = typeof msgQueue !== "undefined" && msgQueue.some(q =>
					q.ItemGroup === row.ItemGroup &&
					q.AccessionNo === row.AccessionNo &&
					q.ReceivedBU === row.ReceivedBU
				);

				// Container flex — side-by-side layout
				showDiv += `<div class="d-flex justify-content-center align-items-center flex-wrap" style="gap:6px;">`;

				// --- Existing BM-ROLE / Reception logic ---
				if ('{{ strpos(session("userRole"), '"ldap_role":"[BM-ROLE]"') !== false }}' == "1") {
					showDiv += `<div class="queItemRemove wrap-row text-center bordered-icon" style="display:inline-flex;">
						<i style="color:red;cursor:pointer;" class="fa fa-times fa-6" aria-hidden="true"></i>
					</div>`;
					if ('{{ $datas->AnteDate }}' != "") {
						showDiv += `<div class="editTransaction wrap-row text-center bordered-icon" style="display:inline-flex;">
							<i style="color:green;cursor:pointer;" class="fa fa-pencil fa-6" aria-hidden="true"></i>
						</div>`;
					}
					if (row.Status == "500" || row.Status == "600") {
						showDiv += `<div class="pdfResult wrap-row text-center bordered-icon" style="display:inline-flex;">
							<i style="color:blue;cursor:pointer;" class="fa fa-file-pdf-o fa-6" aria-hidden="true"></i>
						</div>`;
					}
				} else {
					if ('{{ $datas->AnteDate }}' != "") {
						showDiv += `<div class="editTransaction wrap-row text-center bordered-icon" style="display:inline-flex;">
							<i style="color:green;cursor:pointer;" class="fa fa-pencil fa-6" aria-hidden="true"></i>
						</div>`;
					}
					if (row.Status == "500" || row.Status == "600") {
						showDiv += `<div class="pdfResult wrap-row text-center bordered-icon" style="display:inline-flex;">
							<i style="color:blue;cursor:pointer;" class="fa fa-file-pdf-o fa-6" aria-hidden="true"></i>
						</div>`;
					} else {
						if (row.Status >= "201" && row.Status <= "210") {
							showDiv += `<div class="queItemRemove wrap-row text-center bordered-icon" style="display:inline-flex;">
								<i style="color:red;cursor:pointer;" class="fa fa-times fa-6" aria-hidden="true"></i>
							</div>`;
						} else if (row.Status >= "280" && row.Status <= "300" && hasReceptionOIC) {
							showDiv += `<div class="queItemRemove wrap-row text-center bordered-icon" style="display:inline-flex;">
								<i style="color:red;cursor:pointer;" class="fa fa-times fa-6" aria-hidden="true"></i>
							</div>`;
						}
					}
				}

				// --- Add HL7BTN / Repeat button side-by-side ---
				if (hasHL7BTN) {
					if (isQueued) {
						// Locked repeat button (green check)
						showDiv += `<div class="wrap-row text-center bordered-icon" style="display:inline-flex;">
							<button class="btn btn-sm btn-success btnActionItem locked" data-id="${row.Id}" title="Repeat">
								<i class="fa fa-check"></i>
							</button>
						</div>`;
					} else {
						// Active repeat button (blue repeat)
						showDiv += `<div class="wrap-row text-center bordered-icon" style="display:inline-flex;">
							<button class="btn btn-sm btn-primary btnActionItem" data-id="${row.Id}" title="Repeat" ${isDisabled}>
								<i class="fa fa-repeat"></i>
							</button>
						</div>`;
					}
				}

				showDiv += `</div>`; // Close flex container

				return (row.QueueStatus === null || row.CodeItemPrice == "SF") ? "" : showDiv; 
			} 
		},

		{ "data": "InputBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "ReadersFee", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{ data: null, className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
			{ data: null, targets: 1, orderable: false,"width":"10px" },
			{ data: null, targets: 2, "width":"300px" },
			{ data: null, targets: 3, "width":"90px" },
			{ "data": "NameDoctor", targets: 4, "width":"300px" },
			{ "data": "DescriptionItemPrice",targets: 5, "width":"500px", defaultContent: "" },
			{ "data": "",targets: 6, "width":"180px"},
			{ "data": "TransactionType",targets: 7, "width":"80px"},
			{ "data": "Stat",targets: 8, "width":"50px"},
			{ "data": "QueueStatus",targets: 9, "width":"80px"},
			{ "data": "AmountItemPrice",targets: 10, "width":"100px"},
			{ "data": null, className: '', targets: 11, "width":"50px"},
			{ "data": "InputBy",targets:12, "width":"50px"},
			{ "data": "ReadersFee",targets:13, "width":"50px"}
		],
		order			: [ 4, 'asc' ],
		dom:            "frtiS",
		scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-160,
		fnInfoCallback: function( settings, start, end, max, total, pre ) {	
			return "<div class=\" col-xs-12 col-sm-12 col-md-12 col-lg-12\"> <div  class=\" col-xs-6 col-sm-6 col-md-6\"  >"+"Got a total of"+" "+total+" "+"entries "+"</div><div  class=\" col-xs-6 col-sm-6 col-md-6\"  ><span class=\"selected_info\">"+selectedInfo(true,data);
		}
	});
	
	@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') !== false)
		$('#TransactionListTable_filter').append('<button class="btn btn-success pull-left" type="button" disabled="disabled"> Add</button>');
	@else
		$('#TransactionListTable_filter').append('<button class="addbtn btn btn-success pull-left"  type="button" style="margin-right: 10px;"> Add</button>');
	@endif
		
	@if(strpos(session('userRole'), '"ldap_role":"[BM-MODULE]"') !== false) //12-02-24 added 
		if ('{{ $datas->AnteDate }}' != "") {
			var compareHref = "{{ url(session('userBU').'/cms/pastqueue/'.$datas->AnteDateQueueID.'/edit') }}";
			$('#TransactionListTable_filter').append(
				'<button id="compareTransactionBtn" class="comparebtn btn btn-warning pull-left" style="border-radius: 4px; line-height: 20px;" type="button">Compare Transaction</button>'
			);
		}
	@endif
	
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});

	$('#TransactionListTable_info').addClass('col-xs-12 col-sm-12 col-md-12 col-lg-12'); //for edit and view button disbaled if the user does not meet the condition
	@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') !== false)
	$('#TransactionListTable').dataTable().rowGrouping({
            							iGroupingColumnIndex: 4,
										iExpandGroupOffset: -1,
            							fnGroupLabelFormat: function(doctorName) {
					var rows = $('#TransactionListTable').DataTable().rows().data();
					var idDoctor = '';
					var qStatus = $('input[name="qstatus"]').val(); // Get data from Queue Status
					console.log('QueueStatus:', qStatus);

					for (var i = 0; i < rows.length; i++) {
						if (rows[i].NameDoctor === doctorName) {
							idDoctor = rows[i].IdDoctor;
							break;
						}
					}

					// Add condition on the buttons. Hide the button if trx is adjusting entry 
					@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
						if (qStatus === 'Adjusting Entry' || qStatus === 'Adjusting Entry - For Approval' || qStatus === 'For Payment') { //add 'For Payment' status to hide the button if the trx is not already paid
							return '<span style="display: inline-block; margin-top: 10px;">' + doctorName + '</span>';
						} else {
							return '<button class="physicianviewbtn btn btn-info pull-left" value="' + idDoctor + 
								'" type="button" style="margin-right: 10px; disabled="disabled">View</button>' + 
								'<button class="physicianeditbtn btn btn-success pull-left" value="' + idDoctor + 
								'" type="button" disabled="disabled" >Edit</button>&nbsp; &nbsp; &nbsp;' + 
								'<span style="display: inline-block; margin-top: 10px;">' + doctorName + '</span>';
						}
					@else
						return '<span style="display: inline-block; margin-top: 10px;">' + doctorName + '</span>';
					@endif
				}
			});
	@else
		$('#TransactionListTable').dataTable().rowGrouping({
			iGroupingColumnIndex: 4,
			iExpandGroupOffset: -1,
			fnGroupLabelFormat: function(doctorName) {
				var rows = $('#TransactionListTable').DataTable().rows().data();
				var idDoctor = '';
				var qStatus = $('input[name="qstatus"]').val(); // Get data from Queue Status
				console.log('QueueStatus:', qStatus);

				for (var i = 0; i < rows.length; i++) {
					if (rows[i].NameDoctor === doctorName) {
						idDoctor = rows[i].IdDoctor;
						break;
					}
				}

				@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
					// Add condition on the buttons. Hide the button if trx is adjusting entry
					if (qStatus === 'Adjusting Entry' || qStatus === 'Adjusting Entry - For Approval' || qStatus === 'For Payment') { //add 'For Payment' status to hide the button if the trx is not already paid 
						return '<span style="display: inline-block; margin-top: 10px;">' + doctorName + '</span>';
					} else {
						return '<button class="physicianviewbtn btn btn-info pull-left" value="' + idDoctor + 
							'" type="button" style="margin-right: 10px; ">View</button>' + 
							'<button class="physicianeditbtn btn btn-success pull-left" value="' + idDoctor + 
							'" type="button">Edit</button>&nbsp; &nbsp; &nbsp;' + 
							'<span style="display: inline-block; margin-top: 10px;">' + doctorName + '</span>';
					}
				@else
					return '<span style="display: inline-block; margin-top: 10px;">' + doctorName + '</span>';
				@endif
			}
		});
	@endif							
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
		if( parent.required($('form')) ) return false;
		if(!$('input[name=IdPatient]').val()) return;
		var rows_selected = [];
		var cardNumbers  = [];
		var loadData = table.rows().data();
		 loadData.each(function (value, index) {
			rows_selected.push(value.Id);
			cardNumber = $('input[name="CardNumber_' + value.Id + '"]').val();
			cardNumbers.push(cardNumber);

		});
		table.clear().draw();
		addModal.setTitle("Transaction - Edit");
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
	$('.cancelbtn').on('click', function(e){
		var rowid = $('#TransactionListTable tr[data-toggle-rowid]').data('toggle-rowid');
		cancelTransaction.setTitle('Cancel Queue');
		cancelTransaction.setType(BootstrapDialog.TYPE_WARNING);
		cancelTransaction.setData('pageToLoad', '/cms/pastqueue/cancelTransaction/' + $('input[name="_queueid"]').val() + '/edit');
		cancelTransaction.setData('transactionid', rowid);
		cancelTransaction.realize();
		cancelTransaction.open();
		e.preventDefault();
	})
	$('.savebtn').on('click',function(e){
	
		if( $(this).hasClass('disabled') )
		{
			return false;
		}
	
		var rows_selected = [];
		var cardError = "";
		$("input:checked", $('#TransactionListTable').dataTable().fnGetNodes()).each(function(){ 
			
			var cardNumbers  = "";	
			var tr = $(this);
			var status = $(this).data('toggle-queuestatus');
			//cardNumber = $('input[name="CardNumber_' + value.Id + '"]');
			var cardNumber = tr.closest("tr").find('input[name="CardNumber_' + $(this).val() + '"]'); 
			
			if(status === null && cardNumber.length  && cardNumber.val() ){
				if (validateCardNumber(cardNumber) != "Error" ) 
				{
					cardNumbers = cardNumber.val();
				}else
				{
					cardError =  'Error';
				}
			}	
			
			var trVal =tr.closest("tr").find('input[name="Statfee_' + $(this).val() + '"]:checked').val(); 
			var isStat =  (trVal == "on") ? "on" : "off";
			rows_selected.push({ "isId" : $(this).val(), "isStat" : isStat, 'CardNumber' : cardNumbers });
			
			
		});
		if(cardError == 'Error') {
			//e.preventDefault();
			return false;
		}

		if( parent.required($('form')) ) return false;

		//$('#formQueueCreate').submit();
		parent.postData(
				"{{ '/cms/queue/'.$datas->Id }}",
				{
					'itemSelected':rows_selected
					,'IdPatient' : $('input[name=IdPatient]').val()
					,'Age' : $('input[name=Age]').val()
					,'Notes' : $('textarea[name=Notes]').val()
					,'PatientType' : $('select[name=PatientType]').val()
					,'forPU' : $('input[name=forPU]').is(':checked') ? 1 : 0
					,'forEmail' : $('input[name=forEmail]').is(':checked') ? 1 : 0
					,'Medication' : $('input[name=Medication]').val()
					,'LastDose' : $('input[name=LastDose]').val()
					,'LastPeriod' : $('input[name=LastPeriod]').val()
					,'_token': $('input[name=_token]').val()
					,'_method':'PUT'
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
	$('.approvalbtn').on('click', function(e){

		if($(this).attr('disabled') == "disabled")
			{
				return false;
			}
		var queueId=$('input[name="_queueid"]').val();
		var csrfToken = $('meta[name="csrf-token"]').attr('content');

		$.ajax({
		    type: 'POST',
			async: false, // added for undefined array issue
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
		//update for checking status if all paid b4 approving 
		function updateApprovalButtonStatus() {
		var allStatus = true; 
		var table = $('#TransactionListTable').DataTable();

		// Loop through all rows in the DataTable
		table.rows().every(function () {
			var data = this.data();
			if (data.QueueStatus !== "Fully Paid") {
				allStatus = false;
				return false; 
			}
		});

		if (allStatus  &&  '{{ $datas->QStatusId }}'  == '203') {
			$('.approvalbtn').removeAttr('disabled');
		} else {
			$('.approvalbtn').attr('disabled', 'disabled');
		}
	}

	updateApprovalButtonStatus();

	$('#TransactionListTable').on('draw.dt', function () {
		updateApprovalButtonStatus();
	});
	$('.paymentbtn').on('click', function(e){

		if($(this).attr('disabled') == "disabled")
		{
			return false;
		}
	});
	$(document).on('click', '#compareTransactionBtn', function() { //12-02-24 added
        var width = Math.floor(window.innerWidth); 
        var height = Math.floor(window.innerHeight);  
        var left = Math.floor(window.innerWidth);  
        var options = 'width=' + width + ',height=' + height + ',left=' + left + ',top=0,resizable=yes,scrollbars=yes,status=yes';

    	window.open(compareHref, 'CompareTransactionWindow', options);
	});

	$('#TransactionListTable').on('click','.queItemRemove',function(e){
		removeModal.setTitle("Transaction - Remove");
		removeModal.setData("pageId",$(this).closest('tr').data('toggle-rowid'));
		removeModal.realize();
		removeModal.open();
		e.preventDefault();
	});
	
	$('#TransactionListTable').on('click','.editTransaction',function(e){

		editTransactionModal.setTitle('Amendment For - (DOCTOR / COMPANY / TRANSACTION TYPE)');
		editTransactionModal.setData("transid",$(this).closest('tr').data('toggle-rowid'));
		editTransactionModal.realize();
		editTransactionModal.open();
		e.preventDefault();
	});
	
	$('#TransactionListTable').on('click','.pdfResult',function(e){
		pdfResultsModal.setTitle("PDF - Results");
		pdfResultsModal.setData("pageId",$('input[name="_queueid"]').val());
		pdfResultsModal.setData("transid",$(this).closest('tr').data('toggle-rowid') );
		pdfResultsModal.realize();
		pdfResultsModal.open();
		e.preventDefault();
	});	
	
	$(document).on('focusin.modal','.ui-datepicker-month,.ui-datepicker-year', function (e) {
		var that = this
		if (that[0] !== e.target && !that.has(e.target).length) {
			that.focus()
		}
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
	
	$('.reGeneratebtn').on('click', function(e){

		var queueId=$('input[name="_queueid"]').val();
		var csrfToken = $('meta[name="csrf-token"]').attr('content');

		$.ajax({
		    type: 'POST',
		    url: '{{ route('regeneratepdf') }}',
		    headers: {
			'X-CSRF-TOKEN': csrfToken,
		    },
		    data: { idQueue : queueId },
		    success: function (response) {
					//alert(response);
					alert('Re Generate pdf has been submitted');
					location.reload(true);
		    },
		    error: function (error) { 
			console.error('Please check you connection');
		    }
		});
	});
	$('.physicianeditbtn').on('click', function(e) {
		// Get the idDoctor directly from the clicked button's value
		var idDoctor = $(this).val();  // Using 'this' refers to the clicked button
		console.log(idDoctor, 'click me');
		physicianEditInfo.setTitle('Physician Information Edit');
		physicianEditInfo.setType(BootstrapDialog.TYPE_SUCCESS);
		physicianEditInfo.setData('pageToLoad', '/cms/queue/physicianInfoEdit/' + idDoctor + '/edit');
		physicianEditInfo.realize();
		physicianEditInfo.open();
		
		e.preventDefault();
	});


	$('.physicianviewbtn').on('click', function(e){
		var idDoctor = $(this).val();  // Using 'this' refers to the clicked button
		console.log(idDoctor, 'click me');
		physicianViewInfo.setTitle('Physician Information View');
		physicianViewInfo.setType(BootstrapDialog.TYPE_SUCCESS);
		physicianViewInfo.setData('pageToLoad', '/cms/queue/physicianInfoView/'  + idDoctor + '/edit?idQueue=' + $('input[name="_queueid"]').val());
		physicianViewInfo.realize();
		physicianViewInfo.open();
		e.preventDefault();
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
	
	$('.LastPeriod').datepicker({ maxDate: '+0', firstDay: 1, dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true, yearRange: 'c-100:c+10'});
	$('.LastDose').datepicker({ maxDate: new Date(), firstDay: 1, dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true, yearRange: 'c-100:c+10'});
	
	
	var dobLoadVal = $('input[name="DOB"]').val();
	$('input[name="DOB"]').val(BirthDateFormat(dobLoadVal));
	var ageInput = document.getElementsByName('Age')[0];
	ageInput.value = calculateAge(dobLoadVal);
		
	//ResendHL7btn//
	$(document).on('click', '#resendBtn', function() {

		var queueId=$('input[name="_queueid"]').val();

		$.ajax({
			url: '/cms/queue/resendHL7',  
			type: 'POST',
			data: {
				queueId: queueId,   
				_token: '{{ csrf_token() }}'   
			},
			success: function(response) {
				
				if (response.success) {
					alert('HL7 sent successfully!');
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

	//HIDE OR SHOW TO COMEBACK GENERATE QR BUTTON
	window.addEventListener("load", function() {
        const checkbox = document.querySelector("#toComeback");
        const button = document.querySelector("#generateQR");

        checkbox.addEventListener("change", function() {
            button.style.display = this.checked ? "inline-block" : "none";
			var printQueueValue = '{{ $datas->Code }}'; // Get value for QR
			updateBarcode(printQueueValue);
        });
    });
	
	$(document).on('click', '#generateQR', function() {


		// Delay printing to ensure updates are visible
		$('.queue-slip').print({
				globalStyles: false,
				silent: true  // Add this option for silent printing
			});
	});

	function updateBarcode(printQueueValue) {
    var barcodeContainer = document.getElementById("barcode");
    barcodeContainer.innerHTML = ''; // Clear previous QR code

		// Generate and place QR code inside barcode div
		var qrcode = new QRCode(barcodeContainer, {
			text: printQueueValue,
			width: 100, // Adjust size as needed
			height: 100,
			correctLevel: QRCode.CorrectLevel.M // Error correction level
		});
	}
	
});

$(document).on('click', '.btnActionItem', function(e) {
    e.preventDefault(); // prevent the form submission
	let $btn = $(this);
    let id = $btn.data('id');
    console.log('Repeat clicked:', id);

    $btn.blur();
	
    if ($btn.hasClass('locked')) return;

    $btn.addClass('locked');
    $btn.html('<i class="fa fa-spinner fa-spin"></i>');

	setTimeout(function() {
		$.ajax({
			url: "{{ route('hl7.resend') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				id: id,
				type: 'Item'
			},
			success: function (response) {

                // ✅ success state for clicked button
                $btn
                    .removeClass('btn-primary')
                    .addClass('btn-success locked')
                    .html('<i class="fa fa-check"></i>');


                let dt = $('#TransactionListTable').DataTable();

                $('#TransactionListTable tbody tr').each(function () {
                    let rowData = dt.row(this).data();
                    if (!rowData) return;

                    if (
                        rowData.ItemGroup === response.ItemGroup &&
                        rowData.AccessionNo === response.AccessionNo &&
                        rowData.ReceivedBU === response.ReceivedBU
                    ) {
                        $(this).find('.btnActionItem')
                            .removeClass('btn-primary')
                            .addClass('btn-success locked')
                            .html('<i class="fa fa-check"></i>')
                            .blur();
                    }
                });
            },
			error: function() {
				$btn
					.html('<i class="fa fa-repeat"></i>')
					.removeClass('locked'); // allow retry
			}
		});
	}, 1000);
});

</script>
@endsection