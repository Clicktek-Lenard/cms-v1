<!--@extends('app')-->
@if(session('show_dialogue_box'))
    <script>
        // Show dialogue box
        alert("Counter information updated successfully.");
    </script>
@endif

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBUCode').'/cms/workstation') }}">Workstation <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a>{{ $counter->IPv4 }} <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
        <form id="formQueueEdit" class="form-horizontal" role="form" action="{{ route('workstation.update', ['workstation' => $counter->Id]) }}" method="POST" autocomplete="off">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
					
				<div class="panel panel-primary">
					<div class="panel-heading" style="line-height:12px;">Info</div>
					<div class="panel-body">
						<!--LEFT-->
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Station Number<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="station" value="{{ $counter->StationNumber }}" placeholder="Counter Number" required="required">
								</div>
							</div>

							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">IP Address<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="ip" value="{{ $counter->IPv4 }}" placeholder="IPv4" required="required" >
								</div>
							</div> 

							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
									<label class="bold">Department<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<select class="form-control" name="department" required="required" id="departmentSelect">										<option value="" disabled>Select Department</option>
										<option value="Reception" {{ $counter->Department == 'Reception' ? 'selected' : '' }}>Reception</option>
										<!-- <option value="Laboratory" {{ $counter->Department == 'Laboratory' ? 'selected' : '' }}>Laboratory</option> -->
										<option value="Extraction" {{ $counter->Department == 'Extraction' ? 'selected' : '' }}>Extraction</option>
										<option value="Imaging" {{ $counter->Department == 'Imaging' ? 'selected' : '' }}>Imaging</option>
										<option value="Vital Signs" {{ $counter->Department == 'Vital Signs' ? 'selected' : '' }}>Vital Signs</option>
										<option value="Consultation" {{ $counter->Department == 'Consultation' ? 'selected' : '' }}>Consultation</option>
										<option value="Releasing" {{ $counter->Department == 'Releasing' ? 'selected' : '' }}>Releasing</option>
									</select>
								</div>
							</div>

							<div class="row form-group row-md-flex-center" id="locationField" style="display: {{ $counter->Department == 'Imaging' ? 'block' : 'none' }};">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
									<label class="bold">Location<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="location" placeholder="Location" id="locationInput" 
										value="{{ old('location', $counter->Location) }}" 
										{{ $counter->Department == 'Imaging' ? 'required' : '' }}>
								</div>
							</div>

							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
									<label class="bold">Branch<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<select class="form-control" name="branch" required="required">
										<option value="" disabled>Select Branch Code</option>
										@foreach($idbu as $code)
											<option value="{{ $code }}" {{ $code == $counter->IdBU ? 'selected' : '' }}>
												{{ $code }}
											</option>
										@endforeach
									</select>
								</div>
							</div>


						</div>
						<!--RIGHT-->
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<!-- <div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Code<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="code" placeholder="Guarantor Code" readonly="readonly">
								</div>
								
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Eros Code<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="code" placeholder="Eros Code" readonly="readonly">
								</div>
							</div> -->
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Created by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="createdby" value="{{ $counter->InputBy }}"placeholder="Created by" readonly="readonly">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Date Created<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="datecreated" value="{{ $counter->InputDate }}" placeholder="Date Created" readonly="readonly">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updateby" value="{{ $counter->UpdateBy }}" placeholder="Update by" readonly="readonly">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update date<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updatedate" value="{{ $counter->UpdateDate }}"placeholder="Update date" readonly="readonly">
								</div>
							</div> 
							<!-- <div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Status<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="status" placeholder="Status" readonly="readonly">
								</div>
							</div>  -->
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
					<button @if( strpos(session('userRole') , '"ldap_role":"['.strtoupper(\Request::segment(2)).']"') !== false  )  @else disabled="disabled" @endif  class="updatebtn btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Update </button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(e) {

	$('.updatebtn').on('click',function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('#formQueueEdit').submit();
	});

	const departmentSelect = document.getElementById('departmentSelect');
	const locationField = document.getElementById('locationField');
	const locationInput = document.getElementById('locationInput');

	departmentSelect.addEventListener('change', function() {
		if (this.value === 'Imaging') {
			locationField.style.display = 'block';
			locationInput.setAttribute('required', 'required');
		} else {
			locationField.style.display = 'none';
			locationInput.removeAttribute('required');
			locationInput.value = '';
		}
	});

});
</script>
@endsection
