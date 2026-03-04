@extends('app')

@section('content')
<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8" style="padding-right:0px;">

	<iframe src="https://www.google.com/maps/d/embed?mid=1wxiy4xbhL7jYw8FkyFoAzGcZcws4Gc8&ehbc=2E312F" width="100%" height="700"></iframe>
</div>
<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-left:0px;">
	<div class="container-fluid">
		<div class="row-notused">
			<div class="col-md-12 col-md-offset-2-notsued">
				<div class="panel panel-primary" style="">
					<div class="panel-heading">Login</div>
					<div class="panel-body">
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
						
						

						<form class="form-horizontal" role="form" method="POST"  action="{{ route('login') }}" autocomplete="off">
							@csrf
							

							<div class="form-group">
								<label class="col-md-4 control-label">Username</label>
								<div class="col-md-6">
									<input type="text" class="form-control"  type="text" name="username" :value="old('username')"  placeholder="Username" autofocus="autofocus">
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-4 control-label">Password</label>
								<div class="col-md-6">
									<input type="password" class="form-control" name="password" placeholder="Password" autocomplete="current-password" >
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-4 control-label">Business Units</label>
								<div class="col-md-6">
									<input type="hidden" name="modalClinicName" >
									<select name="modalClinics" class="form-control rowview" placeholder="Clinic Name" required="required" >
										@foreach ($clinics as $clinic)
											<option value="{{ $clinic->Code }}"   @if($defaultClinic == $clinic->Code  ) selected @else '' @endif    >{{ $clinic->Description }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary">Login</button>

									<a class="btn btn-link" href="{{ url('/password/email') }}"><s>Forgot Your Password?</s></a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(e) {

	var clinics = [];
	var Lclinics = {!! json_encode($clinics) !!};
	if( typeof(Lclinics.length) === 'undefined')
		clinics.push(Lclinics);
	else
		clinics = Lclinics;
	$('select[name="modalClinics"]').selectize({
		sortField: 'Description',
		searchField: ['Code','Description'],
		options : clinics,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.Description) + '</span>' + '<span class="Odescription">(' + escape(item.Code) + ')</span>' +
					'</span>' +
				'</div>';
			}
		}
	});
	$('select[name="modalClinics"]').on('change',function(){
		$('input[name="modalClinicName"]').val($('select[name="modalClinics"] option:selected').text());
	});
	$('input[name="modalClinicName"]').val($('select[name="modalClinics"] option:selected').text());
	
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
});
</script>
@endsection