<!--@extends('app')-->

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBU').'/erosui/company') }}">Guarantor - EROS <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li><a href="{{ url(session('userBU').'/erosui/company/'.$datas[0]->Id.'/edit') }}">{{ $datas[0]->Name }} <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li><a href="{{ url(session('userBU').'/erosui/company/itemspackages/'.$datas[0]->Id) }}">Item and Packages <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a href="#">Item Add <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 table-queue">dfasdfasd</div>
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


});
</script>
@endsection