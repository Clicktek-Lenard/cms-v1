<!--@extends('app')-->
@section('style')
<style>
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li><a href="{{ url(session('userBUCode').'/cms/settings/users') }}">Users <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Create <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-users">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="formSettingsUsersCreate" class="form-horizontal" role="form" method="POST" action="{{ url('/'.session('userBUCode').'/cms/settings/users') }}" autocomplete="off">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
        	<div class="panel panel-primary">
				<div class="panel-heading" style="line-height:12px;">Info</div>
				<div class="panel-body">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
						<div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
								<label class="bold ">Username<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
                                <input type="text" class="form-control" name="Username" value="{{ old('Username') }}" placeholder="Username" required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Full Name<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="FullName" value="{{ old('FullName') }}" placeholder="Full Name"  required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Email<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="email" class="form-control" name="Email" value="{{ old('Email') }}" placeholder="Email"  required="required">
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Default Clinic<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="hidden" name="ClinicCode" value="" />
								<select name="Clinic" class="form-control" placeholder="Default Clinic" required="required">
                                	<option value=""></option>
                                    @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->Id }}">{{ $clinic->Code }}</option>
                                    @endforeach
                                </select>
							</div>
						</div>
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Status<font style="color:red;">*</font></label>
                            </div>
							<div class="col-sm-9 col-md-9">
                            	<div class="col-xs-6 col-sm-6 col-md-6">
								<input type="radio" id="status-active" name="Status" value="Active" {{ (old('Status')=="Active" || old('Status')=='')?'checked='.'"'.'checked'.'"':'' }}  /> <label for="status-active" >Active</label>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                <input type="radio" id="status-inactive" name="Status" value="Inactive" {{ old('Status')=="Inactive"?'checked='.'"'.'checked'.'"':'' }} /> <label for="status-inactive" >Inactive</label>
                                </div>
							</div>
						</div>
					</div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Last login date</label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="LastLogin" placeholder="System Generated" readonly="readonly"  >
							</div>
						</div>
                    </div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Input By</label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="InputBy" placeholder="System Generated" readonly="readonly"  >
							</div>
						</div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                        <div class="row form-group row-md-flex-center">
                        	<div class="col-sm-3 col-md-3 pad-0-md text-right-md ">
								<label class="bold ">Input date</label>
                            </div>
							<div class="col-sm-9 col-md-9">
								<input type="text" class="form-control" name="InputDate" placeholder="System Generated" readonly="readonly"  >
							</div>
						</div>
                    </div>
				</div>
			</div>
            <div class="panel panel-success">
				<div class="panel-heading" style="line-height:12px;">Access</div>
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
	$('select[name="Clinic"]').selectize({
		onChange: function(value) {
			if (!value.length )
			{
				$('input[name="ClinicCode"]').val('');
				return;	
			}
			$('input[name="ClinicCode"]').val( value );
		}
	});
	
	$('.savebtn').on('click',function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('#formSettingsUsersCreate').submit();
	});
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
});

</script>
@endsection