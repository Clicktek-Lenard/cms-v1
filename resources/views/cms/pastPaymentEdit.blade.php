<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>
@media (min-width: 992px) {
  .modal-lg {
    width: 100%;
  }
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
		 <li><a href="{{ '/cms/pastqueue' }}">Past Queue <span class="badge" style="top:-9px; position:relative;"></span></a></li> 
		 <li><a href="{{ '/cms/pastqueue/'.$datas->Id.'/edit' }}">View <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Billing Summary <span class="badge" style="top:-9px; position:relative;"></span></a></li>
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
								<input type="text" style="color:blue;" class=" form-control" name="PatientName" value="{{ $datas->QFullName }}" placeholder="Patient Name" readonly="readonly"  required="required" >
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
                            <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
								<label class="bold">Queue No.</label>
                            </div>
                            <div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" placeholder="System Generated" value="{{ $datas->Code }}" readonly="readonly">
							</div>
						</div>
			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
					<label class="bold">Patient Type</label>
				</div>
				<div class="col-sm-4 col-md-4 pad-0-md">
					<select name="PatientType" class="form-control" placeholder="Patient Type" data-placeholder="Patient Type" disabled="disabled"  required="required" >
						<option></option>
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
				<div class="col-sm-2 col-md-2 col-md-offset-6 pad-left-0-md  text-right-md ">
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
				<a @if($datas->Status <= 201 && $datas->Status == 900) disabled="disabled" @endif class="drfbtn btn btn-success col-xs-2 col-sm-2 col-md-2 col-lg-2" style="visibility:hidden;  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> DRF </a>
				<a @if($datas->Status <= 201 && $datas->Status == 900) disabled="disabled" @endif class="rtngbtn btn btn-info col-xs-2 col-sm-2 col-md-2 col-lg-2" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Routing Slip </a>
				<a @if( $CS == 0 )  disabled="disabled"  @endif    class="chargebtn btn btn-danger col-xs-2 col-sm-2 col-md-2 col-lg-2" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Charge Slip</a>
				<a @if( $OR == 0 )  disabled="disabled"  @endif    class="orbtn btn btn-warning col-xs-2 col-sm-2 col-md-2 col-lg-2" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> O.R.</a>
				<a  class="postbtn saving btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4" style=" border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Post</a>					
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
		
			$(row).attr('data-toggle-rowId', data.Id).attr('data-toggle-IdCompany', data.IdCompany).attr('data-toggle-IdDoctor', data.IdDoctor).attr('data-toggle-IdItemPrice', data.IdItemPrice);
		},
		columns			: [
		{ "data": null },
		{ "data": "NameCompany", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "NameDoctor", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
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
			{ "data": "TransactionType",targets: 5, "width":"80px"},
			{ "data": "QueueStatus",targets: 6, "width":"80px"},
			{ "data": "AmountItemPrice",targets: 7, "width":"150px"},
			{ "data": "InputBy",targets: 8, "width":"50px"}
			
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

	
	$('.postbtn').on('click',function(e){
		if(!$('input[name=IdPatient]').val()) return;
		var rows_selected = [];
		var loadData = table.rows().data();
		 loadData.each(function (value, index) {
			rows_selected.push(value.Id);
		});
		//table.clear().draw();
		addModal.setTitle("Billing - Transaction ({{$datas->QFullName}})");
		addModal.realize();
		addModal.open();
		e.preventDefault();
	});
	$('.chargebtn').on('click',function(e){  
		if($(this).attr('disabled') !== 'disabled' )
		{
			var userClinicCode = "{{ session('userClinicCode') }}"; 

			if (userClinicCode === 'MED')
			{
				var id = '{{ $datas->Id }}';
				var userId = '{{ Auth::user()->id }}';
				var hyperlink = document.createElement('a');
				hyperlink.href = "http://{{$_SERVER['SERVER_ADDR']}}:8080/jasperserver/flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FBilling&reportUnit=%2Freports%2FBilling%2FCS_v2_MED&j_username=report&j_password=DnCMSReport&output=pdf&appointmentID="+id+"&myID="+userId;
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

			} else {

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
			
		}
	});
	
	var QueueStatus = '{{$datas->QueueStatus}}';
	if(QueueStatus == "For Payment")
	{
		$('.orbtn').attr('disabled', true);
					
	}
	
	$('.orbtn').on('click',function(e){
	
		if($(this).attr('disabled') !== 'disabled')
		{
			var QueueStatus = '{{$datas->QueueStatus}}';
			if(QueueStatus == "For Payment")
				{
					alert('Please make sure That you Paid All the Items Before Printing O.R');
					$('.orbtn').attr('disabled', true);
					return false;		
				}
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
	
	$('.rtngbtn').on('click',function(e){  
		if($(this).attr('disabled') !== 'disabled' )
		{
			var id = '{{ $datas->Id }}';
			var userId = '{{ Auth::user()->id }}';
			var hyperlink = document.createElement('a');
			hyperlink.href = "http://{{$_SERVER['SERVER_ADDR']}}:8080/jasperserver/flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FBilling&reportUnit=%2Freports%2FBilling%2FRS_v2_PastQ&j_username=report&j_password=DnCMSReport&output=pdf&appointmentID="+id+"&myID="+userId;
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