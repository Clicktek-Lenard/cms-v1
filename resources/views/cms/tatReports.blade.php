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
                    <li class="active"><a href="{{ '/reports/turnaroundtime' }}" class="waiting">Turnadound Time - Reports <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 table-queue">
		<form id="formReportCreate" class="form-horizontal" role="form" method="POST" action="{{ '/reports/turnaroundtime' }}" autocomplete="off">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_repType" value="">
		<div class="panel panel-primary" style="margin-top:20px;">
			<div class="panel-heading" style="line-height:12px;">Selection</div>
				<div class="panel-body">
					<div class="row form-group row-md-flex-center" >
					    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
							<label class="bold nodoctors " style="cursor:pointer;">Clinic Branch<font style="color:red;" placeholder="Select Branch">*</font></label>
						</div>
						
					    <div class="col-sm-2 col-md-4">
                            @if(count($ClinicCode) != 1)
                                <select class="form-control" name="Clinic" id="Clinic" disabled>
									<option value="" selected disabled>Select Branch</option>
                                    @foreach($Clinics as $clinic)
                                        <option value="{{ $clinic->Code }}" 
                                            {{ session('userClinicCode') == $clinic->Code ? 'selected' : '' }}>
                                            {{ strtoupper($clinic->Description) }}
                                        </option>
                                    @endforeach
									<!-- @foreach($Clinics as $clinic)
										<option value="{{$clinic->Code}}"> {{ strtoupper($clinic->Description)}}</option>
									@endforeach -->
                                </select>
                                <input type="hidden" name="Clinic" value="{{ session('userClinicCode') }}">
                            @else
                                <input type="hidden" name="Clinic" value="{{ $clinicName[0]->Code }}">
                                <input type="text" class="form-control" value="{{ $clinicName[0]->Description }}">
                            @endif
                        </div>

					</div>
					<div id="perPatientInputs" style="display:none;">
						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">Date From:<font style="color:red;">*</font></label>
							</div>
							<div class="col-sm-2 col-md-4">
								<input type="datetime-local" onkeydown="return false;" class="form-control datepicker" name="dateFrom" id="dateFrom" placeholder="Date of From" required step="1">
							</div>
						</div>

						<div class="row form-group row-md-flex-center" style="margin-top:20px;">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">Date To:<font style="color:red;">*</font></label>
							</div>
							<div class="col-sm-2 col-md-4">
								<input type="datetime-local"  onkeydown="return false;" class="form-control datepicker" name="dateTo" id="dateTo" placeholder="Date of To" required step="1">
							</div>
						</div>
					</div>

					<div id="summaryInputs" style="display:none;">
						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">Date:<font style="color:red;">*</font></label>
							</div>
							<div class="col-sm-2 col-md-4">
								<input type="datetime-local" onkeydown="return false;" class="form-control datepicker" name="dateFrom" id="dateFrom" placeholder="Date of From" required step="1">
							</div>
						</div>

						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">
									Year<font style="color:red;">*</font>
								</label>
							</div>
							<div class="col-sm-2 col-md-1">
								<select name="year" id="year" placeholder="Year" class="form-control" required>
									<option value="" selected disabled>Select Year</option>
								</select>
							</div>

							
								<label class="bold nodoctors" style="cursor:pointer;">
									Month<font style="color:red;">*</font>
								</label>
							
							<div class="col-sm-2 col-md-1">
								<select name="month" id="month" placeholder="Month" class="form-control" required>
									<option value="" selected disabled>Select Month</option>
								</select>
							</div>
						</div>
					</div>

					<div id="releasingInputs" style="display:none;">
						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">Date From:<font style="color:red;">*</font></label>
							</div>
							<div class="col-sm-2 col-md-4">
								<input type="datetime-local" onkeydown="return false;" class="form-control datepicker" name="dateFrom" id="dateFrom" placeholder="Date of From" required step="1">
							</div>
						</div>

						<div class="row form-group row-md-flex-center" style="margin-top:20px;">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">Date To:<font style="color:red;">*</font></label>
							</div>
							<div class="col-sm-2 col-md-4">
								<input type="datetime-local"  onkeydown="return false;" class="form-control datepicker" name="dateTo" id="dateTo" placeholder="Date of To" required step="1">
							</div>
						</div>
					</div>

					<div id="releasingSummaryInputs" style="display:none;">
						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">Date:<font style="color:red;">*</font></label>
							</div>
							<div class="col-sm-2 col-md-4">
								<input type="datetime-local" onkeydown="return false;" class="form-control datepicker" name="dateFrom" id="dateFrom" placeholder="Date of From" required step="1">
							</div>
						</div>

						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
								<label class="bold nodoctors" style="cursor:pointer;">Month<font style="color:red;">*</font></label>
							</div>
							<div class="col-sm-2 col-md-4">
								<select name="month" id="monthReleasingSummary" placeholder="Month" class="form-control" placeholder="Select Month" required>
									<option value="" selected disabled>Select Month</option>
								</select>
							</div>
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
									<option value="perPatient">Turnaround Time - Per Patient</option>
                                    <option value="summary">Turnaround Time - Per Patient Summary </option>
									<option value="releasing">Turnaround Time - Releasing </option>
									<option value="releasingsummary">Turnaround Time - Releasing Summary </option>
                                    <!-- <option value="receptocomplete">Turnaround time</option> -->
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
const monthNames = [
	"January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December"
];

const startingYear = 2025;
const currentYear = new Date().getFullYear();
const currentMonthIndex = new Date().getMonth(); // 0 = Jan

// Populate year dropdown
for (let y = startingYear; y <= currentYear; y++) {
	$('#year').append(
		$('<option>', {
			value: y,
			text: y
		})
	);
}

// Populate months depending on selected year
function updateMonthOptions(selectedYear) {
	$('#month').empty().append('<option value="" selected disabled>Select Month</option>');

	let limit = 11; // December by default
	if (parseInt(selectedYear) === currentYear) {
		limit = currentMonthIndex; // Only show up to current month
	}

	for (let i = 0; i <= limit; i++) {
		$('#month').append(
			$('<option>', {
				value: i + 1,
				text: monthNames[i]
			})
		);
	}
}

// Populate #monthReleasingSummary with months up to current month (always current year)
function populateMonthReleasingSummary() {
	$('#monthReleasingSummary').empty().append('<option value="" selected disabled>Select Month</option>');

	for (let i = 0; i <= currentMonthIndex; i++) {
		$('#monthReleasingSummary').append(
			$('<option>', {
				value: i + 1,
				text: monthNames[i]
			})
		);
	}
}

populateMonthReleasingSummary();


// On year change, update the months
$('#year').on('change', function () {
	const selectedYear = $(this).val();
	updateMonthOptions(selectedYear);
});


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

        return `${year}-${month}-${day}T${hour}:${minute}:${second}`;
    }

    var currentDate = new Date();
    currentDate.setDate(currentDate.getDate() - 1);

    var dateFromInput = $('input[name="dateFrom"]');
    var dateToInput = $('input[name="dateTo"]');

    // Set initial value with locked time
    dateFromInput.val(formatDate(currentDate));
    dateToInput.val(formatDate(currentDate, true));

    // Reapply locked time if user changes date
    dateFromInput.on('change', function () {
        let newDate = new Date($(this).val());
        $(this).val(formatDate(newDate));
    });

    dateToInput.on('change', function () {
        let newDate = new Date($(this).val());
        $(this).val(formatDate(newDate, true));
    });
	    
	$('.generate').on('click', function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('#formReportCreate').submit();
	});

    // function toggleDateInputs() {
    //     const reportType = $('.generateType').val();
    //     if (reportType === 'summary') {
    //         $('#dateFrom, #dateTo').prop('disabled', true);

    //         // Show 'Not Applicable' labels for both
    //         $('#dateFrom').closest('.row').find('label.notApplicableLabel').show();
    //         $('#dateTo').closest('.row').find('label.notApplicableLabel').show();
    //     } else {
    //         $('#dateFrom, #dateTo').prop('disabled', false);

    //         // Hide 'Not Applicable' labels for both
    //         $('#dateFrom').closest('.row').find('label.notApplicableLabel').hide();
    //         $('#dateTo').closest('.row').find('label.notApplicableLabel').hide();
    //     }
    // }

    // toggleDateInputs();

    // $('.generateType').on('change', function() {
    //     toggleDateInputs();
    // });

	$('.generateType').on('change', function () {
		const selected = $(this).val();

		$('#perPatientInputs, #summaryInputs, #releasingInputs, #releasingSummaryInputs').hide()
    		.find('input, select').prop('disabled', true).removeAttr('required');


		if (selected === 'perPatient') {
			$('#perPatientInputs').show()
				.find('input, select').prop('disabled', false).attr('required', true);
		} else if (selected === 'summary') {
			$('#summaryInputs').show()
				.find('input, select').prop('disabled', false).attr('required', true);
		} else if (selected === 'releasing') {
			$('#releasingInputs').show()
				.find('input, select').prop('disabled', false).attr('required', true);
		} else if (selected === 'releasingsummary') {
			$('#releasingSummaryInputs').show()
				.find('input, select').prop('disabled', false).attr('required', true);
		}
	});

    $('.generateType').trigger('change');
	
});
</script>
@endsection