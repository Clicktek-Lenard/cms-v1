<!--@extends('app')-->

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBUCode').'/physician') }}">Physician - EROS <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a href="#">Create <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
			<form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ url(session('userBUCode').'/physician') }}" autocomplete="off">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
				<div class="panel panel-primary">
					<div class="panel-heading" style="line-height:12px;">Info</div>
					<div class="panel-body">
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
									<label class="bold ">Last Name<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-4 col-md-4">
									<input type="text" class="form-control" name="lastname" placeholder="Last Name" required="required">
								</div>
								<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
									<label class="bold ">First Name<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-4 col-md-4">
									<input type="text" class="form-control" name="firstname" placeholder="First Name" required="required">
								</div>
								<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
									<label class="bold ">Middle Name<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-4 col-md-4">
									<input type="text" class="form-control" name="middlename" placeholder="Middle Name" >
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
									<label class="bold ">Display Name<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-12 col-md-12 text-left-md">
									<input type="text" class="form-control" name="fullname" placeholder="Display Name" required="required">
								</div>
								
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
									<label class="bold ">PRC No.<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-4 col-md-4">
									<input type="text" class="form-control" name="prcno" placeholder="PRC No." required="required">
								</div>
								<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
									<label class="bold ">Specialization<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-4 col-md-4">
									<input type="text" class="form-control" name="specialization" placeholder="Specialization" >
								</div>
								<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
									<label class="bold ">Eros Code<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-4 col-md-4">
									<input type="text" class="form-control" name="eroscode" readonly="readonly" placeholder="System Generated" >
								</div>
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
				<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
					<button class="savebtn saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"> Save </button>
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

});
</script>
@endsection
