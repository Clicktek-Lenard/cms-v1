
<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">                         <!--pcp v2-->
<style>
	.ui-datepicker-div{ z-index:2003 !important;}
	.ui-datepicker {
	z-index: 1001 !important;
	}

	.form-check-1 {
        transform: scale(1.5); /* Increase checkbox size */
        margin-right: 5px; /* Add spacing */
    }
	hr {
	border: 1px solid gray;
	}

</style>
@endsection
@section('content')
<meta name="logged-in-user" content="{{ Auth::user()->username }}">
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBUCode').'/cmsphysician/physician') }}">Physician Accreditation Form <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a href="#">Create <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">    
    <div class="col-menu-15 create-queue">
		@if(session('error'))
			<div class="alert alert-danger">
				{{ session('error') }}
			</div>
		@endif
        <form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ url(session('userBUCode').'/cmsphysician/physician') }}" autocomplete="off">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">  
            <div class="panel panel-primary">
				<div class="panel-heading" style="line-height:12px;">PHYSICIAN ACCREDITATION FORM</div>
                <div class="panel-body">
					<!-- Main Tab Content -->
					<div class="panel panel-primary" style="margin-top: 20px;">
						<div class="panel-heading" style="line-height:12px;">PERSONAL DATA</div>
						<div class="panel-body">
							<div class="row">                
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 4px;">
									<div class="row form-group row-md-flex-center">
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
											<label class="bold">Last Name<font style="color:red;">*</font></label>
										</div>
										<div class="col-sm-12 col-md-10">
											<input type="text" class="form-control" style="text-transform: uppercase;" name="lastname" placeholder="Last Name" required="required">
										</div>
										<div class="col-sm-12 col-md-2">
											<select name="SuffixName" class="form-control" placeholder="Suffix" data-placeholder="Suffix Name" >
												<option></option>
											</select>
										</div>
									</div>
									<div class="row form-group row-md-flex-center">
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
											<label class="bold ">First Name<font style="color:red;">*</font></label>
										</div>
										<div class="col-sm-12 col-md-12">
											<input type="text" class="form-control" name="firstname" placeholder="First Name" style="text-transform: uppercase;" required="required">
										</div>
									</div>
									<div class="row form-group row-md-flex-center">
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md">	
											<label class="bold ">Middle Name<font style="color:red;"></font></label>
										</div>
										<div class="col-sm-12 col-md-8">
											<input type="text" class="form-control" name="middlename" placeholder="Middle Name" style="text-transform: uppercase;" >
										</div>
										<div class="col-sm-12 col-md-4">
											<input type="text" class="form-control visibleDOB" onpaste="return false;" placeholder="Date of Birth">
											<input type="hidden" name="dob" class="actualDOB">
										</div>
									</div>
								</div> <!--RIGHT-->
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<div class="row form-group row-md-flex-center">
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
											<label class="form-label"><strong>Specialty</strong><font style="color:red;">*</font></label>
										</div>
										<div class="col-sm-6 col-md-8">
											<select class="form-control" name="specialty" id="specialtyDropdown" placeholder="SPECIALIZATION" required>
												<option value="">Select a Specialization</option>
												@foreach($physicianType as $type)
													<option value="{{ $type->Description }}">{{ strtoupper($type->Description) }}</option>
												@endforeach
											</select>
										</div>						
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
											<label class="form-label fw-bold"><strong>Sub-Specialty</strong></label>
										</div>
										<div class="col-sm-6 col-md-6">
											<select class="form-control" name="subSpecialty" id="subSpecialtyDropdown">
												<option value="">Select Sub-Specialty</option>
											</select>
										</div>
									</div>									
									<div class="row form-group row-md-flex-center">
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
											<label class="bold ">PRC No.<font style="color:red;">*</font></label>
										</div>
										<div class="col-sm-6 col-md-8">
											<input type="text" class="form-control" maxlength="7" pattern="\d*"  id="prcno" name="prcno" placeholder="PRC No." required="required">
										</div>
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
											<label class="bold ">PRC Validity Date<font style="color:red;">*</font></label>
										</div>
										<div class="col-sm-6 col-md-6">
											<input type="text" class="form-control datepicker visibleValidity" onpaste="return false;" placeholder="Validity Date" required="required">
											<input type="hidden" name="validity" class="actualValidity">
										</div>
									</div>						
									<div class="row form-group row-md-flex-center">
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
											<label class="bold ">Email Address<font style="color:red;">*</font></label>
										</div>
										<div class="col-sm-6 col-md-8">
											<input type="text" class="form-control" name="email" placeholder="Email Address" required="required">
										</div>
										<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
											<label class="bold ">Mobile Number<font style="color:red;">*</font></label>
										</div>
										<div class="col-sm-6 col-md-6">
											<input type="text" class="form-control" name="mobile" placeholder="###########"  required="required">
										</div>
									</div>									
								</div>
							</div> <!--div row-->
						</div><!--panel body-->
					</div><!--panel primary-->															
					<div class="panel panel-primary">
						<div class="panel-heading" style="line-height:12px;">CLINIC SCHEDULE</div>
						<div class="panel-body">
							<div class="schedule-container">
								<div class="schedule-group">
									<div class="row">
										<!-- FIRST DIV-->
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
											<div class="row form-group row-md-flex-center" style="margin-left: 22px;">
												<div class="form-header">
													<button type="button" class="btn btn-success addMore">+</button>
												</div>
												<div class="col-sm-1 col-md-1 pad-1-md text-right-md">
													<label class="bold"><font style="color:red;">*</font>Branch</label>
												</div>
												<div class="col-sm-4 col-md-4">
													<select class="form-control branch-select" name="nwdBranch[]" id="nwdBranch" placeholder="e.g.,Monday,Tuesday,Wednesday" required="required">
														<option value="">Select a Branch</option>
														@foreach($clinics as $clinic)
															<option value="{{ $clinic->Code }}">{{ strtoupper($clinic->Code) }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="row form-group row-md-flex-center">												
												<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
													<label class="bold"><font style="color:red;">*</font>Schedule</label>
												</div>
												<div class="col-sm-4 col-md-4">
													<input class="form-control" type="text" name="schedule[]" placeholder="e.g.,Monday,Tuesday,Wednesday" required="required">											
												</div>
											</div>
											<div class="row form-group row-md-flex-center">	
												<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
													<label class="bold"><font style="color:red;">*</font>Time Start</label>
												</div>
												<div class="col-sm-2 col-md-2">
													<select class="form-control" name="timestart[]" id="timestart" placeholder="Please Select Time Start" required="required"></select>
												</div>
												<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
													<label class="bold"><font style="color:red;">*</font>Time End</label>
												</div>
												<div class="col-sm-2 col-md-2">
													<select class="form-control" name="timeend[]" id="timeend" placeholder="Please Select Time End" required="required"></select>
												</div>
											</div>
											<div class="row form-group row-md-flex-center">
												<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
													<label class="bold">By Appointment</label>
												</div>	
												<div class="form-check-1" style="margin-left: 20px;">													
													<input class="form-check-input" type="checkbox" name="appointment[]" value="Yes">
													<input type="hidden" name="appointment[]" value="No">
												</div>
											</div>											
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
												<label class="bold"><font style="color:red;">*</font>First Engagement</label>
											</div>
											<div class="col-sm-6 col-md-6">
												<input type="text" class="form-control datepicker FirstEngagement" onpaste="return false;" name="firstengagement[]" id="firstengagement" placeholder="First Engagement" required>
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
												<label class="bold">Last Engagement</label>
											</div>
											<div class="col-sm-6 col-md-6">
												<input type="text" class="form-control datepicker LastEngagement" onpaste="return false;" name="lastengagement[]"  id="lastengagement" placeholder="Last Engagement">
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">												
											<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
												<label class="bold">Input By</label>
											</div>
											<div class="col-sm-6 col-md-6">
												<input class="form-control" type="text" name="inputby[]" value="{{ Auth::user()->username }}" readonly="readonly">											
											</div>
										</div>										
									</div>
									<hr class="schedule-separator">
								</div> <!-- schedule-group -->
							</div> <!-- schedule-container -->
						</div> <!-- panel-body -->
					</div> <!-- panel primary -->
					@if(strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false)
					<div class="panel panel-primary">
						<div class="panel-heading" style="line-height: 12px;">POSITION</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4">
									<h5><strong>Position</strong></h5>
									<div class="form-check mb-2">
										<input class="form-check-input" type="checkbox" name="primaryCarePhysician" id="primaryCarePhysician">
										<label class="form-check-label" for="primaryCarePhysician">Primary Care Physician</label>
									</div>
									<div class="form-check mb-2">
										<input class="form-check-input" type="checkbox" name="specialistConsultant" id="specialistConsultant">
										<label class="form-check-label" for="specialistConsultant">Specialist/Consultant/Reader</label>
									</div>
								</div>
								<div class="col-md-2">
									<h5><strong>Status</strong></h5>
									<div class="form-check mb-2">
										<input class="form-check-input" type="checkbox" name="regular" id="regular">
										<label class="form-check-label" for="regular">Regular</label>
									</div>
									<div class="form-check mb-2">
										<input class="form-check-input" type="checkbox" name="reliever" id="reliever">
										<label class="form-check-label" for="reliever">Reliever</label>
									</div>
									<div class="form-check mb-2">
										<input class="form-check-input" type="checkbox" name="visiting" id="visiting">
										<label class="form-check-label" for="visiting">Visiting</label>
									</div>
								</div>																
							</div>
						</div>
					</div>					
					@endif										
					@if(strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false)
					<div class="panel panel-primary">
						<div class="panel-heading" style="line-height:12px;">REQUIREMENTS</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-3">
									<h5>Requirements: PCP/Consultant</h5>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="applicationLetter" id="applicationLetter">
										<label class="form-check-label" for="applicationLetter">Application Letter</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="curriculumVitae" id="curriculumVitae">
										<label class="form-check-label" for="curriculumVitae">Curriculum Vitae</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="medicalSchoolDiploma" id="medicalSchoolDiploma">
										<label class="form-check-label" for="medicalSchoolDiploma">Medical School Diploma</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="prcId" id="prcId">
										<label class="form-check-label" for="prcId">PRC ID</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="residencySpecialtyCert" id="residencySpecialtyCert">
										<label class="form-check-label" for="residencySpecialtyCert">Residency Training Certificate</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="diplomateFellowCert" id="diplomateFellowCert">
										<label class="form-check-label" for="diplomateFellowCert">Diplomate/Fellowship Certificate</label>
									</div>
								</div>	
								<div class="col-md-3">	
								<h5 class="mt-4">PCP only</h5>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="philHealth" id="philHealth">
										<label class="form-check-label" for="philHealthAccreditationId">PhilHealth Accreditation ID</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="ptr" id="ptr">
										<label class="form-check-label" for="ptr">PTR</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="bir" id="bir">
										<label class="form-check-label" for="birSwornDeclaration">BIR Sworn Declaration (AGS)</label>
									</div>							
								</div>
								<div class="col-md-3">
									<h5 class="mt-4">Others</h5>
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="MOA" id="MOA">
										<label class="form-check-label" for="signedMOA">Signed MOA</label>
									</div>
								</div>
							</div> <!--div row pcp-->
						</div> <!--panel body pcp-->
					</div> <!--panel panel-primary pcp -->
					@endif
                </div> <!--panel body-->
            </div> <!--panel panel-primary -->		
        </form> <!--form-->        
    </div> <!--create-queue-->
</div> <!--content row-->
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
$(document).ready(function(e) {


	const subspecialties = {
		'DIABETOLOGY': ['DIABETES EDUCATION', 'DIABETES ENDOCRINOLOGY'],
		'ENDOCRINOLOGY': ['DIABETES', 'THYROID DISORDERS', 'ADRENAL DISORDERS', 'PITUITARY DISORDERS'],
		'ENT': ['OTOLOGY/NEUROTOLOGY', 'RHINOLOGY', 'LARYNGOLOGY', 'HEAD AND NECK SURGERY'],
		'FAMILY MEDICINE': ['ADOLESCENT MEDICINE', 'GERIATRICS', 'SPORTS MEDICINE', 'SLEEP MEDICINE'],
		'GASTROENTEROLOGY': ['HEPATOLOGY', 'ENDOSCOPY', 'NUTRITION', 'PANCREATOBILIARY DISORDERS'],
		'GENERAL PRACTICE': ['PRIMARY CARE', 'CHRONIC DISEASE MANAGEMENT'],
		'GENERAL SURGERY': ['ABDOMINAL SURGERY', 'BARIATRIC SURGERY', 'ENDOSCOPIC SURGERY'],
		'INTERNAL MEDICINE': ['CARDIOLOGY', 'ENDOCRINOLOGY', 'GASTROENTEROLOGY', 'HEMATOLOGY', 'INFECTIOUS DISEASE', 'NEPHROLOGY', 'PULMONOLOGY', 'RHEUMATOLOGY'],
		'NEPHROLOGY': ['HEMODIALYSIS', 'KIDNEY TRANSPLANTATION', 'RENAL DISEASES'],
		'NUCLEAR MEDICINE': ['PET SCAN', 'RADIOACTIVE THERAPY', 'RADIOPHARMACEUTICALS'],
		'OB-GYNECOLOGY': ['MATERNAL-FETAL MEDICINE', 'REPRODUCTIVE ENDOCRINOLOGY AND INFERTILITY', 'GYNECOLOGIC ONCOLOGY', 'UROGYNECOLOGY'],
		'OB-SONOLOGY': ['OBSTETRIC ULTRASOUND', 'GYNECOLOGIC ULTRASOUND'],
		'OCCUPATIONAL MEDICINE': ['WORKPLACE SAFETY', 'ENVIRONMENTAL MEDICINE'],
		'ONCO-SURGERY': ['BONE MARROW TRANSPLANT', 'CANCER SURGERY', 'ENDOSCOPIC SURGERY'],
		'OPHTHALMOLOGY': ['CORNEAL SURGERY', 'GLAUCOMA', 'RETINA', 'OPHTHALMIC ONCOLOGY'],
		'OPTOMETRY': ['CONTACT LENSES', 'VISION THERAPY'],
		'ORTHOPEDICS': ['SPINE SURGERY', 'JOINT REPLACEMENT', 'SPORTS MEDICINE'],
		'PATHOLOGY': ['CLINICAL PATHOLOGY', 'ANATOMIC PATHOLOGY', 'FORENSIC PATHOLOGY', 'HEMATOPATHOLOGY'],
		'PEDIATRICS': ['PEDIATRIC CARDIOLOGY', 'PEDIATRIC ENDOCRINOLOGY', 'PEDIATRIC GASTROENTEROLOGY', 'PEDIATRIC HEMATOLOGY'],
		'PLASTIC SURGERY': ['COSMETIC SURGERY', 'RECONSTRUCTIVE SURGERY'],
		'PSYCHIATRY': ['ADOLESCENT PSYCHIATRY', 'GERIATRIC PSYCHIATRY', 'FORENSIC PSYCHIATRY'],
		'PULMONOLOGY': ['PULMONARY DISEASES', 'SLEEP MEDICINE'],
		'RADIO-SONOLOGY': ['MEDICAL ULTRASOUND', 'DIAGNOSTIC SONOGRAPHY'],
		'RADIOLOGY': ['DIAGNOSTIC RADIOLOGY', 'INTERVENTIONAL RADIOLOGY', 'NEURORADIOLOGY'],
		'REHABILITATION MEDICINE': ['PHYSICAL MEDICINE', 'PAIN MEDICINE', 'SPORTS MEDICINE'],
		'RHEUMATOLOGY': ['ARTHRITIS', 'SYSTEMIC LUPUS', 'OSTEOARTHRITIS'],
		'THORACO-VASCULAR SURGERY': ['CARDIAC SURGERY', 'VASCULAR SURGERY'],
		'UROLOGY': ['UROLOGIC ONCOLOGY', 'PEDIATRIC UROLOGY']
	};

    $('#specialtyDropdown').on('change', function () {
        const specialty = $(this).val();
        const subSpecialtyDropdown = $('#subSpecialtyDropdown');
        
        subSpecialtyDropdown.empty().append('<option value="">Select Sub-Specialty</option>');

        if (specialty && subspecialties[specialty]) {
            subspecialties[specialty].forEach(subspecialty => {
                subSpecialtyDropdown.append($('<option>', {
                    value: subspecialty,
                    text: subspecialty
                }));
            });
        }
    });
	function BirthDateFormat(dateString) {
		if (dateString === '0000-00-00') return '';

		// Expect dateString in "yyyy-mm-dd"
		const parts = dateString.split('-');
		if(parts.length !== 3) return dateString; 

		const year = parts[0];
		const monthNum = parseInt(parts[1], 10) - 1; 
		const day = parts[2];
		const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
							"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		const month = monthNames[monthNum] || '';

		return `${day}-${month}-${year}`;
	}
	parent.getData("{{ asset('/json/Suffix.json') }}",null,
		function($data){
			$.each($data.suffix, function(key,val){
			var selected =  false;
			$('select[name="SuffixName"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="SuffixName"]').selectize();
		var SuffixName  = $('select[name="SuffixName"]')[0].selectize;
		
    });

	$(".addMore").click(function () {
		let clone = $(".schedule-group").first().clone();

		// Reset values of input fields
		clone.find("input[name='schedule[]']").val(""); 
		clone.find("input[name='nwdBranch[]']").val("");
		clone.find("input[name='timestart[]']").val("");
		clone.find("input[name='timeend[]']").val("");
		clone.find("input[type='checkbox'][name='appointment[]']").prop("checked", false);
		clone.find("input[name='firstengagement[]']").val("");
		clone.find("input[name='lastengagement[]']").val("");

		clone.find("input[type='checkbox'][name='appointment[]']").each(function () {
			$(this).attr("name", "appointment[]");
		});
	
		 // Remove existing datepicker instance & id before re-initializing
		clone.find(".datepicker").removeClass("hasDatepicker").removeAttr("id");    

		// Initialize datepicker with custom formatting on FirstEngagement and LastEngagement inputs
		clone.find(".FirstEngagement, .LastEngagement").datepicker({
			maxDate: null,
			firstDay: 1,
			dateFormat: 'yy-mm-dd',  
			changeMonth: true,
			changeYear: true,
			yearRange: 'c-100:c+10',
			onSelect: function(dateText, inst) {
				const formatted = BirthDateFormat(dateText);
				$(this).val(formatted);  
			}
		});		
		// Add remove button only to the cloned elements
		clone.find(".form-header").html('<button type="button" class="btn btn-danger remove">--</button>');

		$(".schedule-container").append(clone);
	});

	function generateTimeOptions(selectElement, selectedValue = '') {      //change the time option to add the 12:00 pm and 12:30 pm
		if (!selectElement) return;
		selectElement.innerHTML = "<option value=''>Select Time</option>";

		function formatTime(hour, minutes) {
			let period = hour < 12 ? "AM" : "PM";
			let displayHour = hour % 12 || 12;
			return `${displayHour}:${String(minutes).padStart(2, '0')} ${period}`;
		}

		let timeSlots = [];

		// Morning + 12:00 & 12:30 PM
		for (let hour = 6; hour <= 12; hour++) {
			for (let minutes of [0, 30]) {
				timeSlots.push(formatTime(hour, minutes));
			}
		}

		// Afternoon (1 PM to 5:30 PM)
		for (let hour = 13; hour < 18; hour++) {
			for (let minutes of [0, 30]) {
				timeSlots.push(formatTime(hour, minutes));
			}
		}

		timeSlots.forEach(timeValue => {
			let option = new Option(timeValue, timeValue);
			if (timeValue === selectedValue) {
				option.selected = true;
			}
			selectElement.appendChild(option);
		});
	}

    generateTimeOptions(document.getElementById("timestart"));
    generateTimeOptions(document.getElementById("timeend"));
	
	$(".schedule-container").on("change", "input[name='appointment[]']", function() {
		let hiddenInput = $(this).siblings("input[type='hidden']");
		if ($(this).is(":checked")) {
			hiddenInput.prop("disabled", true); // Disable "No"
		} else {
			hiddenInput.prop("disabled", false); // Enable "No"
		}
	});

	$(document).on("click", ".remove", function () {
		$(this).closest(".schedule-group").remove();
	});

	 $('.schedule-input').on('keydown', function(event) {
        if (event.which === 32) { 
            event.preventDefault(); 
            let inputValue = $(this).val();
            let cursorPos = this.selectionStart; 
            let newValue = inputValue.slice(0, cursorPos) + ' / ' + inputValue.slice(cursorPos);

            if (newValue.length > 7) {
                newValue = newValue.slice(0, 7).replace(/\s+$/, ''); 
            }
            $(this).val(newValue);
            this.selectionStart = this.selectionEnd = cursorPos + 2; 
        }
    });

	const defaultDoctorIncentive = 8;

    $('#SecIncentive').change(function() {
      
        let secIncentiveValue = parseFloat($(this).val().replace('%', ''));
        
        let doctorIncentiveValue = defaultDoctorIncentive - secIncentiveValue;
        
        doctorIncentiveValue = doctorIncentiveValue < 0 ? 0 : doctorIncentiveValue;

        $('#DocIncentive').val(doctorIncentiveValue + '%');
    });

	//for disabled of check box 
	$('#primaryCarePhysician').change(function() {
		if ($(this).is(':checked')) {
			$('#visiting, #specialistConsultant').prop('disabled', true).prop('checked', false);
			$('#regular, #reliever').prop('checked', false);
		} else {
			$('#visiting, #specialistConsultant').prop('disabled', false);
			$('#regular, #reliever').prop('checked', false);
			$('#applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert, #philHealth, #ptr, #bir, #MOA, #primaryCarePhysician').prop('checked', false);
		}
	});

	$('#regular, #reliever').change(function() {
		if ($(this).is(':checked')) {
			$('#regular, #reliever, #visiting, #specialistConsultant').not(this).prop('checked', false).prop('disabled', true);
			$('#primaryCarePhysician').prop('checked', true);
		} else {
			$('#regular, #reliever, #visiting, #specialistConsultant').prop('disabled', false);
			$('#applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert, #philHealth, #ptr, #bir, #MOA #primaryCarePhysician').prop('checked', false);
			if (!$('#regular').is(':checked') && !$('#reliever').is(':checked')) {
				$('#primaryCarePhysician').prop('checked', false);
			}
		}
	});

	$('#specialistConsultant').change(function() {
		if ($(this).is(':checked')) {
			$('#regular, #reliever, #philHealth, #bir, #ptr, #primaryCarePhysician')
				.prop('disabled', true).prop('checked', false);
			$('#visiting').prop('checked', true);
		} else {
			$('#regular, #reliever, #philHealth, #bir, #ptr, #MOA, #primaryCarePhysician')
				.prop('disabled', false);
			$('#visiting').prop('checked', false);
			$('#applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert,#primaryCarePhysician').prop('checked', false);
		}
	});

	$('#visiting').change(function() {
		if ($(this).is(':checked')) {
			$('#specialistConsultant').prop('checked', true).trigger('change');
		} else {
			$('#specialistConsultant').prop('checked', false).trigger('change');
		}
	});

    $('.savebtn').on('click', function(e) {
        if (parent.required($('form'))) return false;
        e.preventDefault();
		var validityStr = $('.visibleValidity').val();
		var today = new Date();
		today.setHours(0,0,0,0);

		if (validityStr) {
			var validityDate = new Date(validityStr);
			validityDate.setHours(0,0,0,0);

			if (validityDate < today) {
				alert("The PRC ID Validity is already expired.");
				return false;
			}
		}
        $('#formQueueCreate').submit();
    });

	document.getElementById('prcno').addEventListener('blur', function (){ //added 121924 for limit prc input in 7 and starts input in zero
        var prcInput = this.value;

        if (prcInput){
            this.value = prcInput.padStart(7, '0');
        }
    });

	document.getElementById("prcno").addEventListener("keypress", function (event) {
        if (!/[0-9]/.test(event.key)) {
            event.preventDefault();
        }
    });

	$(document).on('focusin.modal','.ui-datepicker-month,.ui-datepicker-year', function (e) {
		var that = this
		if (that[0] !== e.target && !that.has(e.target).length) {
			that.focus()
		}
	});

	$('.visibleDOB').datepicker({
		maxDate: null,
		firstDay: 1,
		dateFormat: 'yy-mm-dd', 
		changeMonth: true,
		changeYear: true,
		yearRange: 'c-100:c+10',
		onSelect: function(dateText, inst) {
			const formatted = BirthDateFormat(dateText);
			$(this).val(formatted); 
			$(this).closest('div').find('.actualDOB').val(dateText);
		}
	});
	
	$('.visibleValidity').datepicker({
		maxDate: null,
		firstDay: 1,
		dateFormat: 'yy-mm-dd', 
		changeMonth: true,
		changeYear: true,
		yearRange: 'c-100:c+10',
		onSelect: function(dateText, inst) {
			const formatted = BirthDateFormat(dateText);
			$(this).val(formatted);
			$(this).closest('div').find('.actualValidity').val(dateText); 
		}
	});

	$('.FirstEngagement').datepicker({
		maxDate: null,
		firstDay: 1,
		dateFormat: 'yy-mm-dd', 
		changeMonth: true,
		changeYear: true,
		yearRange: 'c-100:c+10',
		onSelect: function(dateText, inst) {
			const formatted = BirthDateFormat(dateText);
			$(this).val(formatted); 
		}
	});
	$('.LastEngagement').datepicker({
		maxDate: null,
		firstDay: 1,
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: 'c-100:c+10',
		onSelect: function(dateText, inst) {
			const formatted = BirthDateFormat(dateText);
			$(this).val(formatted); 
		}
	});	
});

</script>
@endsection
