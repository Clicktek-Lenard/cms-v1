<!--@extends('app')-->

@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">                          <!--pcp v2-->
<style>
	.ui-datepicker-div{ z-index:2003 !important;}
	.ui-datepicker {
	z-index: 1001 !important;
	}
	.form-check-1 {
        transform: scale(1.5); 
        margin-right: 5px;
    }
	hr {
	border: 1px solid gray;
	}
	select.disabled {
	background-color: #e9ecef !important;
	pointer-events: none; 
	opacity: 0.8;
	}	
	input[type='checkbox'].disabled {
   	background-color: #e9ecef !important;
    pointer-events: none; 
	opacity: 0.8;
	}
	.disabled-checkbox {
    background-color: #e9ecef !important;
	pointer-events: none;
	opacity: 0.8;
	}
	.visually-disabled {
	background-color: #e9ecef !important;
	pointer-events: none;
	opacity: 0.8;
	}
	.disabled-state {
    pointer-events: auto; /* allows click */
    opacity: 0.6;
    cursor: not-allowed;
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
				<input type="hidden" name="doctor_id" value="{{ $datas[0]->Id }}">	
				<input type="hidden" name="doctor_status" value="{{ $datas[0]->Status }}">
				<input type="text" name="logged_in_user" value="{{ Auth::user()->username }}"  style="display:none;">
				<input type="text" name="update_timeDate" value="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}" style="display:none;">		
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
												<input type="text" class="form-control" name="lastname" placeholder="Last Name"  style="text-transform: uppercase;" value="{{ $datas[0]->LastName }}" required="required">
											</div>
											<div class="col-sm-12 col-md-2">
												<select name="SuffixName" class="form-control" placeholder="Suffix Name" data-placeholder="Suffix Name" >
													<option></option>
												</select>
											</div>
										</div>
										<div class="row form-group row-md-flex-center">
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="bold ">First Name<font style="color:red;">*</font></label>
											</div>
											<div class="col-sm-12 col-md-12">
												<input type="text" class="form-control" name="firstname" placeholder="First Name" value="{{ $datas[0]->FirstName }}" style="text-transform: uppercase;" required="required">
											</div>
										</div>
										<div class="row form-group row-md-flex-center">
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">	
												<label class="bold ">Middle Name<font style="color:red;"></font></label>
											</div>
											<div class="col-sm-12 col-md-8">
												<input type="text" class="form-control" name="middlename" placeholder="Middle Name" value="{{ $datas[0]->MiddleName }}" style="text-transform: uppercase;" >
											</div>
											<div class="col-sm-12 col-md-4">
												<input type="text" class="form-control visibleDOB" onpaste="return false;" value="{{ $datas[0]->DOB ? \Carbon\Carbon::parse($datas[0]->DOB)->format('d-M-Y') : '' }}" placeholder="Date of Birth">
												<input type="hidden" name="dob" class="actualDOB" value="{{ $datas[0]->DOB }}">
											</div>
										</div>
									</div> <!--RIGHT-->
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
										<div class="row form-group row-md-flex-center">
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="form-label fw-bold"><strong>Specialty</strong><font style="color:red;">*</font></label>
											</div>
											<div class="col-sm-6 col-md-6">
												<select class="form-control" name="specialty" id="specialtyDropdown" placeholder="SPECIALIZATION" required>
													<option value=""><strong>Select a Specialization</strong></option>
														@foreach($physicianType as $type)
															<option value="{{ $type->Description }}"
																{{ isset($physician->Description) && $physician->Description == $type->Description ? 'selected' : '' }}>
																{{ strtoupper($type->Description) }}
															</option>
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
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="bold ">Branch Origin<font style="color:red;"></font></label>
											</div>
											<div class="col-sm-6 col-md-4">
												<input type="text" class="form-control" name="branch_origin" value="{{ $datas[0]->BranchCode }}" readonly="readonly">
											</div>
										</div>								
										<div class="row form-group row-md-flex-center">
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="bold ">PRC No.<font style="color:red;">*</font></label>
											</div>
											<div class="col-sm-6 col-md-6">
												<input type="text" class="form-control" maxlength="7" pattern="\d*" id="prcno" name="prcno" value="{{ $datas[0]->PRCNo }}" placeholder="PRC No." required="required">
											</div>
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<span id="prcValidityAlert" class="badge badge-danger" style="display:none; color: white; background: red; margin-left: 10px;">
													Expired PRC
												</span>
												<label class="bold ">PRC Validity Date<font style="color:red;">*</font></label>
											</div>
											<div class="col-sm-6 col-md-6">
												<input type="text" class="form-control visibleValidity" onpaste="return false;" value="{{ $datas[0]->PRCValidity ? \Carbon\Carbon::parse($datas[0]->PRCValidity)->format('d-M-Y') : '' }}" placeholder="Validity Date" required="required">
												<input type="hidden" name="validity" class="actualValidity" value="{{ $datas[0]->PRCValidity }}">
											</div>
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="bold ">Status<font style="color:red;"></font></label>
											</div>
											<div  class="col-sm-4 col-md-4">
												<input type="text" name="status" class="form-control" value="{{ $datas[0]->Status }}" readonly="readonly">
											</div>
										</div>						
										<div class="row form-group row-md-flex-center">
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="bold ">Email Address<font style="color:red;">*</font></label>
											</div>
											<div class="col-sm-6 col-md-6">
												<input type="text" class="form-control" name="email" value="{{ $datas[0]->Email }}" placeholder="Email Address" required="required">
											</div>
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="bold ">Mobile Number<font style="color:red;">*</font></label>
											</div>
											<div class="col-sm-6 col-md-6">
												<input type="text" class="form-control" name="mobile" value="{{ $datas[0]->Mobile }}" placeholder="###########" required="required">
											</div>
											<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
												<label class="bold">Physician Position</label>
											</div>
											<div  class="col-sm-4 col-md-4">
												<select class="form-control" name="p_subgroup" id="p_subgroup">													
													<option value="PCP" {{ $datas[0]->SubGroup == 'PCP' ? 'selected' : '' }}>Primary Care Physician</option>
													<option value="SPL" {{ $datas[0]->SubGroup == 'SPL' ? 'selected' : '' }}>Specialist</option>
													<option value="RP" {{ $datas[0]->SubGroup == 'RP' ? 'selected' : '' }}>Referring Physician</option>
												</select>
											</div>
										</div>									
									</div>
								</div> <!--div row-->
							</div>
						</div>					
						<div class="panel panel-primary">
							<div class="panel-heading" style="line-height:12px;">CLINIC SCHEDULE</div>
							<div class="panel-body">
								<div class="schedule-container">
									@if (!empty($schedule) && is_array($schedule))
										@foreach($schedule as $index => $sched)
											<div class="schedule-group">
												<div class="row">
													<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" >
														<div class="row form-group row-md-flex-center" style="margin-left: 25px;">
															<div class="form-header">
																<button type="button" class="btn btn-danger removeSchedule">-</button>
															</div>
															<div class="col-sm-1 col-md-1 pad-1-md text-right-md">
																<label class="bold"><font style="color:red;">*</font>Branch</label>
															</div>
															<div class="col-sm-4 col-md-4">
																<select class="form-control branch-select" name="nwdBranch[]" placeholder=" Please Select Branch" required="required">
																	<option value="">Select a Branch</option>
																	@foreach($clinics as $clinic)
																		<option value="{{ $clinic->Code }}" 
																			{{ isset($nwdBranch[$index]) && $nwdBranch[$index] == $clinic->Code ? 'selected' : '' }}>
																			{{ strtoupper($clinic->Code) }}
																		</option>
																	@endforeach
																</select>
															</div>
														</div>
														<div class="row form-group row-md-flex-center">											
															<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
																<label class="bold"><font style="color:red;">*</font>Schedule</label>
															</div>
															<div class="col-sm-4 col-md-4">
																<input class="form-control" type="text" name="schedule[]" placeholder="e.g.,Monday,Tuesday,Wednesday" id="schedule"  
																	value="{{ $sched }}" placeholder="eg. M,T,W,TH,F,S,SU" required="required">											
															</div>
														</div>
														<div class="row form-group row-md-flex-center">	
															<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
																<label class="bold"><font style="color:red;">*</font>Time Start</label>
															</div>
															<div class="col-sm-2 col-md-2">
																<select class="form-control timestart" name="timestart[]" data-index="{{ $index }}" placeholder="Please Select Time Start" required="required">
																	<option value="{{ $timestart[$index] ?? '' }}">{{ $timestart[$index] ?? 'Select' }}</option>
																</select>
															</div>
															<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
																<label class="bold"><font style="color:red;">*</font>Time End</label>
															</div>
															<div class="col-sm-2 col-md-2">
																<select class="form-control timeend" name="timeend[]" data-index="{{ $index }}" placeholder="Please Select Time Start" required="required">
																	<option value="{{ $timeend[$index] ?? '' }}">{{ $timeend[$index] ?? 'Select' }}</option>
																</select>
															</div>
														</div>													
														<div class="row form-group row-md-flex-center">
															<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
																<label class="bold">By Appointment</label>
															</div>	
															<div class="form-check-1" style="margin-left: 20px;">
																<input type="hidden" name="appointment[{{ $index }}]" value="No">
																<input class="form-check-input appointment-checkbox" type="checkbox" name="appointment[{{ $index }}]" value="Yes" data-index="{{ $index }}"
																	{{ isset($byappointment[$index]) && $byappointment[$index] === 'Yes' ? 'checked' : '' }}>
															</div>
														</div>																											
													</div>
													<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
														<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
															<label class="bold"><font style="color:red;">*</font>First Engagement</label>
														</div>
														<div class="col-sm-6 col-md-6">
															<input class="form-control datepicker FirstEngagement" type="text" name="firstengagement[]" onpaste="return false;"
																value="{{ isset($firstengagement[$index]) ? $firstengagement[$index] : '' }}" placeholder="eg. 00-00-0000"  placeholder="Please Select First Engagement" required="required">										
														</div>
													</div>
													<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
														<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
															<label class="bold">Last Engagement</label>
														</div>
														<div class="col-sm-6 col-md-6">
															<input type="text" class="form-control datepicker LastEngagement" name="lastengagement[]" onpaste="return false;"
															value="{{ isset($lastengagement[$index]) ? $lastengagement[$index] : '' }}" placeholder="eg. 00-00-0000">
														</div>
													</div>
													<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
														<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
															<label class="bold">Input By</label>
														</div>
														<div class="col-sm-6 col-md-6">
															<!--<input class="form-control" type="text" name="inputby[]" value="{{ $inputby[$index] ?? Auth::user()->username }}" readonly="readonly">-->
															<input class="form-control" type="text" name="inputby[]" value="{{ $inputby[$index] ?? '' }}" readonly="readonly">											
														</div>
													</div>												
												</div>
												<hr class="schedule-separator">
											</div>
										@endforeach
									@else
									<div class="schedule-group">
										<div class="row">
											<!-- FIRST DIV-->
											<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
												<div class="row form-group row-md-flex-center" style="margin-left: 25px;">
													<div class="form-header">
														<button type="button" class="btn btn-danger removeSchedule">-</button>
													</div>
													<div class="col-sm-1 col-md-1 pad-1-md text-right-md">
														<label class="bold"><font style="color:red;">*</font>Branch</label>
													</div>
													<div class="col-sm-4 col-md-4">
														<select class="form-control branch-select" name="nwdBranch[]"  placeholder=" Please Select Branch" required="required">
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
														<select class="form-control timestart" name="timestart[]" id="timestart" placeholder="Please Select Time Start" required="required"></select>
													</div>
													<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
														<label class="bold"><font style="color:red;">*</font>Time End</label>
													</div>
													<div class="col-sm-2 col-md-2">
														<select class="form-control timeend" name="timeend[]" id="timeend" placeholder="Please Select Time End" required="required"></select>
													</div>
												</div>
												<div class="row form-group row-md-flex-center">
													<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
														<label class="bold">By Appointment</label>
													</div>	
													<div class="form-check-1" style="margin-left: 20px;">													
														<input type="hidden" name="appointment[0]" value="No">
														<input class="form-check-input appointment-checkbox" type="checkbox" name="appointment[0]" value="Yes" data-index="0">
													</div>
												</div>											
											</div>
											<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
												<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
													<label class="bold"><font style="color:red;">*</font>First Engagement</label>
												</div>
												<div class="col-sm-6 col-md-6">
													<input type="text" class="form-control datepicker FirstEngagement" onpaste="return false;" name="firstengagement[]" id="firstengagement" placeholder="First Engagement" required="required">
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
									@endif
								</div>
								<button type="button" class="btn btn-success addMore">+</button>
							</div> <!-- panel-body -->
						</div> <!-- panel primary -->
						@if(strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false)
							<div class="panel panel-primary">
								<div class="panel-heading" style="line-height:12px;">POSITION</div>
								<div class="panel-body">
									<div class="row">
										<!-- Form fields for Referring Physician here -->
										<div class="col-md-4">
											<h5><strong>Position</strong></h5>
											<div class="form-check mb-2">
												<input type="hidden" name="primaryCarePhysician" value="No">
												<input class="form-check-input" type="checkbox" name="primaryCarePhysician" id="primaryCarePhysician" value="Yes"
													{{ $datas[0]->PCP == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="primaryCarePhysician">Primary Care Physician</label>
											</div>
											<div class="form-check mb-2">
												<input type="hidden" name="specialistConsultant" value="No">
												<input class="form-check-input" type="checkbox" name="specialistConsultant" id="specialistConsultant" value="Yes"
													{{ $datas[0]->Specialist == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="specialistConsultant">Specialist/Consultant/Reader</label>
											</div>								
										</div>
										<div class="col-md-2">
											<h5><strong>Status</strong></h5>
											<div class="form-check mb-2">
												<input type="hidden" name="regular" value="No">
												<input class="form-check-input" type="checkbox" name="regular" id="regular" value="Yes"
													{{ $datas[0]->Regular == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="regular">Regular</label>
											</div>
											<div class="form-check mb-2">
												<input type="hidden" name="reliever" value="No">
												<input class="form-check-input" type="checkbox" name="reliever" id="reliever" value="Yes"
													{{ $datas[0]->Reliever == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="reliever">Reliever</label>
											</div>
											<div class="form-check mb-2">
												<input type="hidden" name="visiting" value="No">
												<input class="form-check-input" type="checkbox" name="visiting" id="visiting" value="Yes"
													{{ $datas[0]->Visiting == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="visiting">Visiting</label>
											</div>											
										</div>
										<div class="col-md-2">
											<div class="form-check mb-2">
												<input type="hidden" name="resigned" value="No">
												<input class="form-check-input" type="checkbox" name="resigned" id="resigned" value="Yes"
													{{ $datas[0]->ResignDoctor == 'Yes' ? 'checked' : '' }}>  	<!--create "Resigned" Column in DB-->
												<label class="form-check-label" for="resigned"><strong>Resigned</strong></label>
											</div>
										</div>																			
									</div> <!--div-row- position-->
								</div> <!--panel-body-position-->
							</div> <!--panel-panel primary position-->
						@else
							<input type="hidden" name="primaryCarePhysician" value="{{ $datas[0]->PCP == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="specialistConsultant" value="{{ $datas[0]->Specialist == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="regular" value="{{ $datas[0]->Regular == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="reliever" value="{{ $datas[0]->Reliever == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="visiting" value="{{ $datas[0]->Visiting == 'Yes' ? 'Yes' : 'No' }}">
						@endif

						@if(strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false)				
							<div class="panel panel-primary">
								<div class="panel-heading" style="line-height:12px;">REQUIREMENTS</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-3">
											<h5>Requirements: PCP/Consultant</h5>
											<div class="form-check">
												<input type="hidden" name="applicationLetter" value="No">
												<input class="form-check-input" type="checkbox" id="applicationLetter" name="applicationLetter" value="Yes" {{ $datas[0]->ApplicationLetter == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="applicationLetter">Application Letter</label>
											</div>
											<div class="form-check">
												<input type="hidden" name="curriculumVitae" value="No">
												<input class="form-check-input" type="checkbox" id="curriculumVitae" name="curriculumVitae" value="Yes" {{ $datas[0]->CurriculumVitae == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="curriculumVitae">Curriculum Vitae</label>
											</div>
											<div class="form-check">
												<input type="hidden" name="medicalSchoolDiploma" value="No">
												<input class="form-check-input" type="checkbox" id="medicalSchoolDiploma" name="medicalSchoolDiploma" value="Yes" {{ $datas[0]->Diploma == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="medicalSchoolDiploma">Medical School Diploma</label>
											</div>
											<div class="form-check">
												<input type="hidden" name="prcId" value="No">
												<input class="form-check-input" type="checkbox" id="prcId" name="prcId" value="Yes" {{ $datas[0]->PRCId == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="prcId">PRC ID</label>
											</div>
											<div class="form-check">
												<input type="hidden" name="residencySpecialtyCert" value="No">
												<input class="form-check-input" type="checkbox" id="residencySpecialtyCert" name="residencySpecialtyCert" value="Yes" {{ $datas[0]->ResidencyCertificate == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="residencySpecialtyCert">Residency/Specialty Board Cert</label>
											</div>
											<div class="form-check">
												<input type="hidden" name="diplomateFellowCert" value="No">
												<input class="form-check-input" type="checkbox" id="diplomateFellowCert" name="diplomateFellowCert" value="Yes" {{ $datas[0]->DiplomateCertificate == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="diplomateFellowCert">Diplomate/Fellow Certificate</label>
											</div>
										</div>	
										<div class="col-md-3">	
											<h5 class="mt-4">PCP only</h5>
											<div class="form-check">
												<input type="hidden" name="philHealth" value="No">
												<input class="form-check-input" type="checkbox" id="philHealth" name="philHealth" value="Yes" {{ $datas[0]->PhilHealth == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="philHealthAccreditationId">PhilHealth Accreditation ID</label>
											</div>
											<div class="form-check">
												<input type="hidden" name="ptr" value="No">
												<input class="form-check-input" type="checkbox" id="ptr" name="ptr" value="Yes" {{ $datas[0]->PTR == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="ptr">PTR</label>
											</div>
											<div class="form-check">
												<input type="hidden" name="bir" value="No">
												<input class="form-check-input" type="checkbox" id="bir" name="bir" value="Yes" {{ $datas[0]->BIR == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="birSwornDeclaration">BIR Sworn Declaration (AGS)</label>
											</div>
										</div>
										<div class="col-md-3">
											<h5 class="mt-4">Others</h5>
											<div class="form-check">
												<input type="hidden" name="MOA" value="No">
												<input class="form-check-input" type="checkbox" id="MOA" name="MOA" value="Yes" {{ $datas[0]->MOA == 'Yes' ? 'checked' : '' }}>
												<label class="form-check-label" for="signedMOA">Signed MOA</label>
											</div>
										</div>
									</div> <!--div row pcp-->
								</div> <!--panel body pcp-->
							</div> <!--panel panel-primary pcp -->
						@else
							<input type="hidden" name="applicationLetter" value="{{ $datas[0]->ApplicationLetter == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="curriculumVitae" value="{{ $datas[0]->CurriculumVitae == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="medicalSchoolDiploma" value="{{ $datas[0]->Diploma == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="prcId" value="{{ $datas[0]->PRCId == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="residencySpecialtyCert" value="{{ $datas[0]->ResidencyCertificate == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="diplomateFellowCert" value="{{ $datas[0]->DiplomateCertificate == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="philHealth" value="{{ $datas[0]->PhilHealth == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="ptr" value="{{ $datas[0]->PTR == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="bir" value="{{ $datas[0]->BIR == 'Yes' ? 'Yes' : 'No' }}">
							<input type="hidden" name="MOA" value="{{ $datas[0]->MOA == 'Yes' ? 'Yes' : 'No' }}">
						@endif
					</div> <!--panel body-->
				</div> <!--panel panel-primary -->		
			</form> <!--form--> 		        
		</div> <!--create-queue-->
	</div> <!--content row-->
	<div class="navbar-fixed-bottom" >
		<div class="col-menu">
			<div class="btn-group col-xs-12 col-sm-12 col-md-12 pull-left"  style="background-color:#8F8F8F; height:inherit;">
				<div class="col-xs-6">						
					<button  class="historybtn btn btn-danger col-xs-4 col-sm-4 col-md-4 col-lg-4 pull-left"  style="border-radius:0px; line-height:29px;" type="button"> Changes For Approval </button>											
				</div>
				<div class="col-xs-6">	
					<button class="col-xs-4 col-sm-4 col-md-4 col-lg-4 " style="visibility:hidden;"></button>
					<button  class="updatebtn btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4 pull-right"  style="border-radius:0px; line-height:29px;" type="button">Update </button>										
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

	function BirthDateFormat(dateString) {
		if (dateString === '0000-00-00') return '';

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
	
	parent.getData("{{ asset('/json/Suffix.json') }}",null,       //for suffix json
		function($data){
			$.each($data.suffix, function(key,val){
			var selected =  false;
			$('select[name="SuffixName"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="SuffixName"]').selectize();
		var SuffixName  = $('select[name="SuffixName"]')[0].selectize;
		var iSuffixName = ('{{ $datas->first()->Suffix ?? '' }}' == '') ? '' : '{{ $datas->first()->Suffix }}';
		SuffixName.setValue(iSuffixName);
	});
	
	const specialty = "{{ $datas[0]->Description }}";
	const subSpecialty = "{{ $datas[0]->SubDescription }}";

	$('#specialtyDropdown').val(specialty).trigger('change');

	if (specialty && subspecialties[specialty]) {
		const subSpecialtyDropdown = $('#subSpecialtyDropdown');
		subspecialties[specialty].forEach(subspecialty => {
			subSpecialtyDropdown.append($('<option>', {
				value: subspecialty,
				text: subspecialty,
				selected: subspecialty === subSpecialty 
			}));
		});
	}

	$('#specialtyDropdown').on('change', function () {
		const selectedSpecialty = $(this).val();
		const subSpecialtyDropdown = $('#subSpecialtyDropdown');

		subSpecialtyDropdown.empty().append('<option value="">Select Sub-Specialty</option>');

		if (selectedSpecialty && subspecialties[selectedSpecialty]) {
			subspecialties[selectedSpecialty].forEach(subspecialty => {
				const isSelected = subspecialty === subSpecialty;
				subSpecialtyDropdown.append($('<option>', {
					value: subspecialty,
					text: subspecialty,
					selected: isSelected 
				}));
			});
		}
	});

	let userClinicCode = '{{ session('userClinicCode') }}';
	let isPhysicianApprover = {{ strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false ? 'true' : 'false' }};

	toggleScheduleInputsBasedOnClinic(userClinicCode, isPhysicianApprover);

	function toggleScheduleInputsBasedOnClinic(userClinicCode, isPhysicianApprover) {
		$(".schedule-group").each(function () {
			const $group = $(this);
			const branchSelect = $group.find("select[name='nwdBranch[]']");
			const selectedBranch = branchSelect.val();
			const inputs = $group.find("input:not([name='inputby[]']), select");
			const removeBtn = $group.find(".removeSchedule");
			const checkboxes = $group.find("input[type='checkbox']");
			const selects = $group.find("select");
			const datepickers = $group.find(".FirstEngagement, .LastEngagement");

			if (isPhysicianApprover) {
				// Approver: Everything editable
				inputs.prop("readonly", false);
				selects.prop("disabled", false).removeClass("disabled");
				checkboxes.prop("disabled", false).removeClass("disabled");
				removeBtn.prop("disabled", false);

				datepickers.prop("readonly", false)
					.removeClass("visually-disabled")
					.css({ 'pointer-events': '', 'background-color': '', 'opacity': '' });
			} else {
				if (!selectedBranch) {
					inputs.prop("readonly", false);
					selects.prop("disabled", false).removeClass("disabled");
					checkboxes.prop("disabled", false).removeClass("disabled");
					removeBtn.prop("disabled", false);

					datepickers.prop("readonly", false)
						.removeClass("visually-disabled")
						.css({ 'pointer-events': '', 'background-color': '', 'opacity': '' });
				} else if (selectedBranch !== userClinicCode) {
					inputs.prop("readonly", true);
					selects.prop("disabled", true).addClass("disabled");
					checkboxes.prop("disabled", true).addClass("disabled");
					removeBtn.prop("disabled", true);

					datepickers.prop("readonly", true)
						.addClass("visually-disabled")
						.css({ 'pointer-events': 'none', 'background-color': '#e9ecef', 'opacity': '0.8' });
				} else {
					inputs.prop("readonly", false);
					selects.prop("disabled", false).removeClass("disabled");
					checkboxes.prop("disabled", false).removeClass("disabled");
					removeBtn.prop("disabled", false);

					datepickers.prop("readonly", false)
						.removeClass("visually-disabled")
						.css({ 'pointer-events': '', 'background-color': '', 'opacity': '' });
				}
			}
			selects.add(checkboxes).each(function () {
				if ($(this).hasClass("disabled")) {
					$(this).prop("disabled", false);
				}
			});
		});
	}


	$(".addMore").click(function () {	
		let clone = $(".schedule-group").first().clone();
		let index = $(".schedule-group").length;
		let loggedInUser = $('meta[name="logged-in-user"]').attr("content");
		// Reset values of input fields
		clone.find("input[name='schedule[]']").val(""); 
		clone.find("select[name='nwdBranch[]']").val("");
		clone.find("input[name='firstengagement[]']").val("");
		clone.find("input[name='lastengagement[]']").val("");
		clone.find("input[type='checkbox']").prop("checked", false);
		clone.find('input[name="inputby[]"]').val('').val(loggedInUser);
		clone.find("input, select, .removeSchedule").not('[name="inputby[]"]').each(function () {
			$(this).prop("readonly", false);
			$(this).prop("disabled", false);
			$(this).removeClass("disabled visually-disabled");
			$(this).css({
				'pointer-events': '',
				'background-color': '',
				'opacity': ''
			});
		});
		clone.find("input[name='inputby[]']").prop("readonly", true);

		// Update checkbox name and hidden input name to reflect new index
		clone.find("input[type='hidden'][name^='appointment']").attr("name", `appointment[${index}]`);
		clone.find("input[type='checkbox'][name^='appointment']").attr("name", `appointment[${index}]`).attr("data-index", index);

		// Reset select dropdowns
		let timeStartSelect = clone.find("select[name='timestart[]']");
		let timeEndSelect = clone.find("select[name='timeend[]']");
		timeStartSelect.empty();  
		timeEndSelect.empty();    

		// Remove existing datepicker and reset it
		clone.find(".datepicker").removeClass("hasDatepicker").removeAttr("id");    
		 clone.find(".FirstEngagement, .LastEngagement")
			.removeClass("visually-disabled")
			.prop("readonly", false)
			.css({
				'pointer-events': '',
				'background-color': '',
				'opacity': ''
			});

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
				$(this).val(formatted);  // overwrite displayed value with formatted date
			}
		});

		clone.find(".form-header").html('<button type="button" class="btn btn-danger removeSchedule">-</button>');
		clone.find("select[name='timestart[]']").each(function () {
			generateTimeOptions(this);
		});

		clone.find("select[name='timeend[]']").each(function () {
			generateTimeOptions(this);
		});

		 toggleScheduleInputsBasedOnClinic(userClinicCode, isPhysicianApprover);

		// Append the clone to the schedule container
		$(".schedule-container").append(clone);
	});

	function generateTimeOptions(selectElement, selectedValue = '') {						 //change the time option to add the 12:00 pm and 12:30 pm
		if (!selectElement) return;
		selectElement.innerHTML = "<option value=''>Select Time</option>";

		function formatTime(hour, minutes) {
			let period = hour < 12 ? "AM" : "PM";
			let displayHour = hour % 12 || 12;
			return `${displayHour}:${String(minutes).padStart(2, '0')} ${period}`;
		}

		let timeSlots = [];
		for (let hour = 6; hour <= 12; hour++) {
			for (let minutes of [0, 30]) {
				timeSlots.push(formatTime(hour, minutes));
			}
		}

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


	document.querySelectorAll(".timestart").forEach((selectElement, index) => {
		let storedStartTime = @json($timestart);
		generateTimeOptions(selectElement, storedStartTime[index] ?? '');
	});

	document.querySelectorAll(".timeend").forEach((selectElement, index) => {
		let storedEndTime = @json($timeend);
		generateTimeOptions(selectElement, storedEndTime[index] ?? '');
	});

	$(document).on('change', '.form-check-input', function() {
		let isChecked = $(this).is(':checked');
		let hiddenInput = $(this).siblings('.appointment-hidden');
		hiddenInput.val(isChecked ? 'Yes' : 'No');
	});

	$(document).on("click", ".removeSchedule", function () {
		$(this).closest(".schedule-group").remove();
		$(this).closest(".schedule-group").prev("hr").remove();
	});

	//for disabled of check box 
	$('#primaryCarePhysician').change(function() {
		if ($(this).is(':checked')) {
			$('#visiting, #specialistConsultant').addClass('disabled-checkbox').attr('data-disabled', 'true').prop('checked', false);
			$('#regular, #reliever').prop('checked', false);
		} else {
			$('#visiting, #specialistConsultant').removeClass('disabled-checkbox').removeAttr('data-disabled');
			$('#regular, #reliever').prop('checked', false);
			$('#applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert, #philHealth, #ptr, #bir, #MOA, #primaryCarePhysician').prop('checked', false);
		}
	});

	$('#regular, #reliever').change(function() {
		if ($(this).is(':checked')) {
			$('#visiting, #specialistConsultant').not(this).prop('checked', false).addClass('disabled-checkbox').attr('data-disabled', 'true');
			$('#primaryCarePhysician').prop('checked', true);
		} else {
			$('#visiting, #specialistConsultant').removeClass('disabled-checkbox').removeAttr('data-disabled');
			$('#applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert, #philHealth, #ptr, #bir, #MOA, #primaryCarePhysician').prop('checked', false);
			if (!$('#regular').is(':checked') && !$('#reliever').is(':checked')) {
				$('#primaryCarePhysician').prop('checked', false);
			}
		}
	});

	$('#specialistConsultant').change(function() {
		if ($(this).is(':checked')) {
			$('#regular, #reliever, #philHealth, #bir, #ptr, #primaryCarePhysician')
				.addClass('disabled-checkbox').attr('data-disabled', 'true').prop('checked', false);
			$('#visiting').prop('checked', true);
		} else {
			$('#regular, #reliever, #philHealth, #bir, #ptr, #primaryCarePhysician')
				.removeClass('disabled-checkbox').removeAttr('data-disabled');
			$('#visiting').prop('checked', false);
			$('#applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert,#primaryCarePhysician').prop('checked', false);
		}
	});
	$('#resigned').change(function() {
		if ($(this).is(':checked')) {
			$('#regular, #reliever, #visiting, #specialistConsultant, #primaryCarePhysician, #applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert, #philHealth, #ptr, #bir, #MOA')
				.addClass('disabled-checkbox').attr('data-disabled', 'true').prop('checked', false);
		} else {
			$('#regular, #reliever, #visiting, #specialistConsultant, #primaryCarePhysician, #applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert, #philHealth, #ptr, #bir, #MOA')
				.removeClass('disabled-checkbox').removeAttr('data-disabled');
		}
	});

	$('#visiting').change(function() {
		if ($(this).is(':checked')) {
			$('#specialistConsultant').prop('checked', true).trigger('change');
		} else {
			$('#specialistConsultant').prop('checked', false).trigger('change');
		}
	});

	function initializeCheckboxStates() {
		// PCP checked
		if ($('#primaryCarePhysician').is(':checked')) {
			$('#visiting, #specialistConsultant').addClass('disabled-checkbox').attr('data-disabled', 'true');
			if (!$('#regular').is(':checked') && !$('#reliever').is(':checked')) {
				$('#regular, #reliever').addClass('disabled-checkbox').attr('data-disabled', 'true');
			}
		}
		// Regular or Reliever checked
		if ($('#regular').is(':checked') || $('#reliever').is(':checked')) {
			$('#regular, #reliever, #visiting, #specialistConsultant').not(':checked').addClass('disabled-checkbox').attr('data-disabled', 'true');
			$('#primaryCarePhysician').removeClass('disabled-checkbox').removeAttr('data-disabled');
		}
		// Specialist checked
		if ($('#specialistConsultant').is(':checked')) {
			$('#regular, #reliever, #philHealth, #bir, #ptr, #primaryCarePhysician').addClass('disabled-checkbox').attr('data-disabled', 'true');
			$('#visiting').removeClass('disabled-checkbox').removeAttr('data-disabled');
		}
		// Visiting checked
		if ($('#visiting').is(':checked')) {
			$('#specialistConsultant').removeClass('disabled-checkbox').removeAttr('data-disabled');
		}
		// Resigned checked
		if ($('#resigned').is(':checked')) {
			$('#regular, #reliever, #visiting, #specialistConsultant, #primaryCarePhysician, #applicationLetter, #curriculumVitae, #medicalSchoolDiploma, #prcId, #residencySpecialtyCert, #diplomateFellowCert, #philHealth, #ptr, #bir, #MOA')
				.prop('checked', false)
				.addClass('disabled-checkbox').attr('data-disabled', 'true');
		}
	}
	// Prevent click/label 
	$('input[type="checkbox"]').on('click', function (e) {
		if ($(this).attr('data-disabled') === 'true') {
			e.preventDefault();
			return false;
		}
	});

	initializeCheckboxStates();
	
	//if physician is on state of disapproved, disable update button
	var status =  $('input[name="doctor_status"]').val();
				
	if (status === "Disapproved" || status === "For Approval") {
		$("#disapprovedNotice").show();
	} else {
		$("#disapprovedNotice").hide();
	}

	if (status === "Approved" || status === "RP - Leads") {
		$('.historybtn').prop('disabled', true);
	} else {
		$('.historybtn').prop('disabled', false);
	}

	$('.updatebtn').on('click', function(e) {
		e.preventDefault();

		var validityStr = $('.visibleValidity').val();
		var today = new Date();
		today.setHours(0,0,0,0);

		if (validityStr) {
			var validityDate = new Date(validityStr);
			validityDate.setHours(0,0,0,0);

			if (validityDate < today) {
				alert("The PRC ID  Validity is already expired.");
				return false;
			}
		}

		var pstatus = $('input[name="status"]').val();
		var pposition = $('#p_subgroup').val();

		if(pstatus === "RP - Leads" && pposition === "RP"){
				alert("The Physician is still Referring Physician, please change the Physician Position");
			return false;
		}

		if (status === "Disapproved") {
			const proceedUpdate = confirm("The physician is currently DISAPPROVED.\nAre you sure you want to continue with the update?");
			if (!proceedUpdate) {
				return false; 
			}
		}else if (status === "For Approval") {
			const proceedUpdate = confirm("The physician is currently FOR APPROVAL.\nAre you sure you want to continue with the update?");
			if (!proceedUpdate) {
				return false; 
			}
		}
		
		var dirty = parent.dirtyObject(); 
		var idphysician = $('input[name="doctor_id"]').val();

		if (dirty && Object.keys(dirty).length !== 0) {

			dirty["logged_in_user"] = {
				oldVal: "",
				newVal: $('input[name="logged_in_user"]').val()
			};

			dirty["update_timeDate"] = {
				oldVal: "",
				newVal: $('input[name="update_timeDate"]').val()
			};

			console.log("DIRTY value:", dirty);

			$.ajax({
					url: '/cmsphysician/api/storePhysicianData/' + idphysician,
				method: 'POST',
					data: {
						_dirty: JSON.stringify(dirty),
						_token: $('input[name="_token"]').val()
					},
				success: function(response) {
						console.log('Dirty data sent:', response);
				},
				error: function(xhr) {
						console.error('Failed to send dirty data:', xhr.responseText);
				}
			});
		}


		if (parent.required($('form'))) return false;
			
		$.ajax({
			url: $('#formQueueEdit').attr('action'),
			method: 'POST',
			data: $('#formQueueEdit').serialize(),
			success: function(response) {
				alert('Physician Updated');
				window.location.href = "{{ url(session('userBU').'/cmsphysician/physician') }}";
			},
			error: function(xhr) {
				alert('An error occurred while updating the physician.');
			}
		});
	});

	var declinePhysician = new BootstrapDialog({
		message: function(dialog) {
			var $message = $('<div class="declineModal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
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

					var doctorId =  $('input[name="doctor_id"]').val();
					var reasons = $('input[name="reasons"]').val();
					console.log('TEXT:',reasons);

					var csrfToken = $('meta[name="_token"]').attr('content');
					
					$.ajax({
						type: 'POST',
						url: "{{ route('declinedoctor') }}",
						headers: {
							'X-CSRF-TOKEN': csrfToken,
						},
						data: { 		
							reasons : reasons,
							doctorId:doctorId
						},
						success: function (response) {
							alert('Reason Submit Successfully');
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

	var historyInformation = new BootstrapDialog({
		message: function(dialog) {
			var $message = $('<div class="historyModal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
			var pageToLoad = dialog.getData('pageToLoad');
			$message.load(pageToLoad);
			return $message;
		},
		size: BootstrapDialog.SIZE_WIDE,
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
			id: 'disapprovebtn',
			cssClass: 'btn-danger actionbtn disabled',
			label: 'Disapprove',
				action: function (modalRef){
					
					if($(this).attr('disabled') == "disabled")
						{
							return false;
						}

				var idDoctor = $('input[name="doctor_id"]').val();
				
				declinePhysician.setTitle('Reason For Decline');
				declinePhysician.setType(BootstrapDialog.TYPE_WARNING);
				declinePhysician.setData('pageToLoad', '/cmsphysician/declinemodal/' + idDoctor + '/edit');
				declinePhysician.realize();
				declinePhysician.open();
				e.preventDefault();
			
					modalRef.close();
				}
			},
			{
			id: 'approvebtn',
			cssClass: 'btn-success actionbtn disabled',
			label: 'Approve',
				action: function (modalRef){

					if($(this).attr('disabled') == "disabled")
						{
							return false;
						}

					var doctorId=$('input[name="doctor_id"]').val();			
					var csrfToken = $('meta[name="_token"]').attr('content');
						
					$.ajax({
						type: 'POST',
						async: false, // added for undefined array issue
						url: "{{ route('approvaldoctor') }}",
						headers: {
							'X-CSRF-TOKEN': csrfToken,
						},
						data: { doctorId : doctorId },

						success: function (response) {
							console.log('RESPONSE:', response);
							alert('DOCTOR APPROVED!');
							href= "{{ url(session('userBU').'/cmsphysician/doctorsmodule') }}";
							window.location.href = href;
							// location.reload();
						},
						error: function (error) { 
							console.error('ERROR!!');
						}
					});
				modalRef.close();
				}
			}
		],
		onshown: function (dialogRef) { 
			setTimeout(function () { 
				var userRole = @json(session('userRole'));
				var accessRole = userRole && userRole.includes('"ldap_role":"[PHYSICIAN-APPROVER]"');

				// Apply the additional condition for APPROVER only
				if (accessRole) {
					dialogRef.getButton('approvebtn').enable();
					dialogRef.getButton('disapprovebtn').enable();
				} else {
					dialogRef.getButton('approvebtn').disable();
					dialogRef.getButton('disapprovebtn').disable();
				}

			}, 100);
		},
	});

	$('.historybtn').on('click', function(e){
		var idDoctor = $('input[name="doctor_id"]').val();
		console.log("ID:", idDoctor);
		historyInformation.setTitle('Physician History Information');
		historyInformation.setType(BootstrapDialog.TYPE_INFO);
		historyInformation.setData('pageToLoad', '/cmsphysician/historyInfo/' + idDoctor + '/edit');
		historyInformation.realize();
		historyInformation.open();
		e.preventDefault();
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
	
	// RP to PCP
    const select = document.getElementById('p_subgroup');
    let defaultValue = "{{ $datas[0]->SubGroup }}";
    let previousValue = select.value;

    select.addEventListener('change', function() {
        if (defaultValue === 'RP' && (this.value === 'SPL' || this.value === 'PCP')) {
            let label = this.value === 'SPL' ? 'Specialist' : 'Primary Care Physician';
            let confirmed = confirm(`This physician is referring. Are you sure you want to change it to ${label}?`);
            if (!confirmed) {
                this.value = previousValue;
                return;
            }
        }
        previousValue = this.value;
    });


	function checkPRCValidity() {
		var validityStr = $('.visibleValidity').val();
		var pstatus = $('input[name="status"]');
console.log('status:', pstatus.val());
		if (!validityStr) {
			$('#prcValidityAlert').hide();
			return;
		}

		var validityDate = new Date(validityStr);
		var today = new Date();

		validityDate.setHours(0,0,0,0);
		today.setHours(0,0,0,0);

		if (validityDate < today) {

			$('#prcValidityAlert').show();

			if (pstatus.val() !== 'For Approval') {
				pstatus.val('For Approval');
			}
		} else {
			$('#prcValidityAlert').hide();
		}
	}

	$('.Validity').on('change', checkPRCValidity);
	$(document).ready(checkPRCValidity);
	parent.initialObject();

});
</script>
@endsection
