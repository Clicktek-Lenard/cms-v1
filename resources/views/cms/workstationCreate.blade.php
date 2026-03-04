<!--@extends('app')-->

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBUCode').'/cms/workstation') }}">Workstation <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a href="{{ url(session('userBUCode').'/cms/workstation/create') }}">Create <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
			<form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ url(session('userBUCode').'/cms/workstation') }}" autocomplete="off">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
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
									<input type="text" class="form-control" name="station" placeholder="Counter Number" required="required">
								</div>
							</div>

							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">IP Address<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="ip" placeholder="IPv4" required="required" >
								</div>
							</div> 

							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
									<label class="bold">Department<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<select class="form-control" name="department" required="required" id="departmentSelect">
										<option value="" disabled selected>Select Department</option>
										<option value="Reception">Reception</option>
										<!-- <option value="Laboratory">Laboratory</option> -->
										<option value="Extraction">Extraction</option>
										<option value="Imaging">Imaging</option>
										<option value="Vital Signs">Vital Signs</option>
										<option value="Consultation">Consultation</option>
										<option value="Releasing">Releasing</option>
									</select>
								</div>
							</div>

							<div class="row form-group row-md-flex-center" id="locationField" style="display: none;">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md">
									<label class="bold">Location<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="location" placeholder="Location">
								</div>
							</div>

							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Branch<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<select class="form-control" name="branch" required="required">
										<option value="" disabled selected>Select Branch Code</option>
										@foreach($idbu as $code)
											<option value="{{ $code }}">{{ $code }}</option>
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
									<input type="text" class="form-control" name="createdby" placeholder="System Generated" readonly="readonly">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Date Created<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="datecreated" placeholder="Date Created" readonly="readonly">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updateby" placeholder="Update by" readonly="readonly">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update date<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updatedate" placeholder="Update date" readonly="readonly">
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
					<button @if( strpos(session('userRole') , '"ldap_role":"['.strtoupper(\Request::segment(2)).']"') !== false  )  @else disabled="disabled" @endif  class="savebtn saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Save </button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(e) {

	$('.savebtn').on('click',function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('#formQueueCreate').submit();
	});

	const departmentSelect = document.getElementById('departmentSelect');
	const locationField = document.getElementById('locationField');

	departmentSelect.addEventListener('change', function() {
		if (this.value === 'Imaging') {
			locationField.style.display = 'block';
			locationInput.setAttribute('required', 'required');
		} else {
			locationField.style.display = 'none';
			locationInput.removeAttribute('required');
		}
	});

});
</script>
@endsection
