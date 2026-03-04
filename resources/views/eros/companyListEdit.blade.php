<!--@extends('app')-->

@section('content')

<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBUCode').'/erosui/company') }}">Guarantor - EROS <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a>{{ $datas[0]->ErosCode }} <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
			<form id="formQueueEdit" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}" autocomplete="off">
				<input type="hidden" name="_method" value="PUT">
				<input type="hidden" name="_id" value="{{ $datas[0]->Id }}">	
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				
				<div class="panel panel-primary">
					<div class="panel-heading" style="line-height:12px;">Info</div>
					<div class="panel-body">
						<!--LEFT-->
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Name<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="guarantor" placeholder="Guarantor Name" required="required" value="{{ $datas[0]->Name }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Group<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<select class="form-control" name="group" placeholder="Group" required="required">
										<option value=""></option>
										<option value="C" @if($datas[0]->Group == 'C') selected @else '' @endif  >Company</option>
										<option value="I" @if($datas[0]->Group == 'I') selected @else '' @endif >Insurance</option>
										<option value="P" @if($datas[0]->Group == 'P') selected @else '' @endif >Individual Person</option>
										<option value="D" @if($datas[0]->Group == 'D') selected @else '' @endif >Default</option>
									</select>
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Short Name<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="shortname" placeholder="Short Name " value="{{ $datas[0]->ShortName }}" required="required" >
								</div>
							</div> <!--
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Sub Group<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="subgroup" placeholder="Sub Group " value="{{ $datas[0]->SubGroup }}" >
								</div>
							</div> -->
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Address<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="address" placeholder="Address" required="required" value="{{ $datas[0]->Address }}" >
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Phone<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="phone" placeholder="Phone" required="required" value="{{ $datas[0]->Phone }}" >
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Email<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="email" placeholder="Email" required="required"  value="{{ $datas[0]->Email }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">City<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="city" placeholder="City" required="required"  value="{{ $datas[0]->City }}">
								</div>
							</div>
						</div>
						<!--RIGHT-->
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Code<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="code" placeholder="Guarantor Code" readonly="readonly" value="{{ $datas[0]->Code }}">
								</div>
								
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Eros Code<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="code" placeholder="Eros Code" readonly="readonly" value="{{ $datas[0]->ErosCode }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Created by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="createdby" placeholder="Created by" readonly="readonly" value="{{ $datas[0]->InputBy }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Date Created<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="datecreated" placeholder="Date Created" readonly="readonly" value="{{ $datas[0]->InputDate }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updateby" placeholder="Update by" readonly="readonly" value="{{ $datas[0]->UpdateBy }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update date<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updatedate" placeholder="Update date" readonly="readonly" value="{{ $datas[0]->UpdateDate }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Status<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="status" placeholder="Status" readonly="readonly" value="{{ $datas[0]->Status }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Result Uploading<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<select class="form-control" name="resultuploading" placeholder="Result Uploading" >
										<option value="">None</option>
										<option value="Yes" @if($datas[0]->ResultUploading == 'Yes') selected @else '' @endif  >Yes, Show this Company in Result Uploading </option>
									</select>
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
				<div class="col-xs-12 col-sm-10 col-sm-offset-2 col-md-8 col-md-offset-4 col-lg-6 col-lg-offset-6">
					<a @if( strpos(session('userRole') , '"ldap_role":"['.strtoupper(\Request::segment(2)).']"') !== false ) href="{{ url(session('userBU').'/erosui/company/cis/'.$datas[0]->Id.'/') }}"  @else disabled="disabled"  @endif class="btn btn-danger col-xs-4 col-sm-4 col-md-4 col-lg-4" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Client Information Sheet</a>
					<a href="{{ url(session('userBU').'/erosui/company/itemspackages/'.$datas[0]->Id.'/') }}" class="btn btn-warning col-xs-4 col-sm-4 col-md-4 col-lg-4" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> List Items/Packages</a>
					<a @if( strpos(session('userRole') , '"ldap_role":"['.strtoupper(\Request::segment(2)).']"') !== false )  @else disabled="disabled" @endif  class="updatebtn btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Update</a>					
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
	
	$('.viewbtn').on('click',function(e){
		e.preventDefault();
		alert('i-click viewbtn');
	});

});
</script>
@endsection
