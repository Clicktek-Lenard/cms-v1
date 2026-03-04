<!--@extends('app')-->

<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">


@section('style')
<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}

</style>

@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ '/reports/dailysales' }}" class="waiting">Daily Sales - Reports <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 table-queue">
		<form id="formReportCreate" class="form-horizontal" role="form" method="POST" action="{{ '/reports/dailysales' }}" autocomplete="off">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_repType" value="">
		<div class="panel panel-primary" style="margin-top:20px;">
			<div class="panel-heading" style="line-height:12px;">Selection</div>
				<div class="panel-body">
					<div class="row form-group row-md-flex-center" >
					    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
							<label class="bold nodoctors " style="cursor:pointer;">Clinic Branch<font style="color:red;">*</font></label>
						</div>
						
					    <div class="col-sm-2 col-md-4">
							@if(count($ClinicCode) != 1)
							<select class="form-control"name="Clinic" id="Clinic" placeholder="Choose Clinic Branch">
								{{-- <option value=""></option> --}}
								<option value="ALL">ALL BRANCHES</option>
								@foreach($Clinics as $clinic)
									<option value="{{$clinic->Code}}"> {{ strtoupper($clinic->Description)}}</option>
								@endforeach
							</select>
							@else
								<input type="hidden" name="Clinic" value="{{$clinicName[0]->Code}}">
								<input type="text" class="form-control" value="{{$clinicName[0]->Description}}" readonly="readonly">
							@endif
					    </div>
					</div>
					<div class="row form-group row-md-flex-center" >
					    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
							<label class="bold nodoctors " style="cursor:pointer;">Date From:<font style="color:red;">*</font></label>
					    </div>
					    <div class="col-sm-2 col-md-4">
							<input type="datetime-local" class="form-control datepicker" name="dateFrom" id="dateFrom" value="" placeholder="Date of From" required="required" step="1">
							{{-- <input type="text" class="form-control datepicker" name="dateFrom" id="dateFrom" value="" placeholder="Date of From" required="required" > --}}
					    </div>
					</div>
					<div class="row form-group row-md-flex-center" style="margin-top:20px;">
					    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
							<label class="bold nodoctors " style="cursor:pointer;">Date To:<font style="color:red;">*</font></label>
					    </div>
					    <div class="col-sm-2 col-md-4">
							<input type="datetime-local" class="form-control datepicker" name="dateTo" value="" placeholder="Date of To" required="required" step="1">
						{{-- <input type="text" class="form-control datepicker" name="dateTo" value="" placeholder="Date of To" required="required"> --}}
					    </div>
					</div>
					<div class="row form-group row-md-flex-center">
						<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
							<label class="bold nodoctors" style="cursor:pointer;">Report Type<font style="color:red;">*</font></label>
						</div>
						<div class="col-sm-2 col-md-4">
							<div class="input-group">
								<select class="form-control generateType" name="_repType"  placeholder="Select Report Type" required="required">
									<option value="" selected disabled>Choose Report Type</option>
									<option value="bookkeeper">Bookkeeper Report</option>
									<option value="cash">Cash Report</option>
									<option value="cashier">Cashier Summary Report</option>
									<option value="HmoCorporate">HMO/Corporate Report</option>
									<option value="perItem">Per Item Report</option>
									<option value="sendout">Sendout Report</option>
									<option value="summary">Summary Report</option>
									<option value="amendment">Amendment Transaction</option> <!--INSERT 01-09-2025-->
								</select>
								<span class="input-group-btn">
									<button class="btn btn-success generate" type="button">Generate</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</div>
    </div>
	<div class="navbar-fixed-bottom" >
		<div class="col-menu">
			<div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
				<div class="col-xs-12 col-sm-10 col-sm-offset-2 col-md-8 col-md-offset-4 col-lg-6 col-lg-offset-6">
					<button class="summarybtn btn btn-warning col-xs-4 col-sm-4 col-md-4 col-lg-4"  style="border-radius:0px; line-height:29px; visibility:hidden;" type="button"> Summary </button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection 
@section('script')
<script>
	
$(document).ready(function(e) {
// 	$('.datepicker').datepicker({
//     maxDate: '+0',
//     dateFormat: "yy-mm-dd",
//     timeFormat: 'HH:mm:ss',
//     firstDay: 1,
//     changeMonth: true,
//     changeYear: true
   
// });
$('select[name="Clinic"]').selectize();
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
	var currentDate = new Date();
	var dateFromInput = $('input[name="dateFrom"]');
	var dateToInput = $('input[name="dateTo"]');

	dateFromInput.val(formatDate(currentDate));

	dateToInput.val(formatDate(currentDate, true));
	$('.generate').on('click', function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('#formReportCreate').submit();
	});
	// $('.perItembtn').on('click',function(e){
	// 	if( parent.required($('form')) ) return false;
	// 	e.preventDefault();
	// 	$('input[name="_repType"]').val('perItem');
	// 	$('#formReportCreate').submit();
	// });
	
	// $('.summarybtn').on('click',function(e){
	// 	if( parent.required($('form')) ) return false;
	// 	e.preventDefault();
	// 	$('input[name="_repType"]').val('summary');
	// 	$('#formReportCreate').submit();
	// });
	
	// $('.bookkeeperbtn').on('click',function(e){
	// 	if( parent.required($('form')) ) return false;
	// 	e.preventDefault();
	// 	$('input[name="_repType"]').val('bookkeeper');
	// 	$('#formReportCreate').submit();
	// });
	
});
</script>
@endsection