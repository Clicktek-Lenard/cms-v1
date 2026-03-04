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
			<li class="active"><a href="#">Lab to Lab Uploading <span class="badge" style="top:-9px; position:relative;"></span></a></li>
		</ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
	
    	<div class="col-menu-15 table-queue">
	@if ($message = Session::get('success'))
	<div class="alert alert-success alert-block">
	    <button type="button" class="close" data-dismiss="alert">x</button>
		<strong>{{ $message }}</strong>
	</div>
	@endif

	@if (count($errors) > 0)
	    <div class="alert alert-danger">
		<strong>Whoops!</strong> There were some problems with your input.
		<ul>
		    @foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		    @endforeach
		</ul>
	    </div>
	@endif
		<form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ url(session('userBUCode').'/erosui/company/itemspackages/lab2lab') }}" enctype="multipart/form-data">
		<input type="hidden" name="_erosCode" value="{{ $datas[0]->ErosCode }}">
		<input type="hidden" name="_companyCode" value="{{ $datas[0]->Code }}">		
		@csrf
		<div class="panel panel-primary">
			<div class="panel-heading" style="line-height:12px;">Allowed only Excel file with Lab to Lab format</div>
			<div class="panel-body">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">
					<div class="row form-group row-md-flex-center">

						<div class="col-md-6">
						    <input type="file" name="file" class="form-control">
						</div>

						<div class="col-md-6">
						    <button type="submit" class="btn btn-success">Upload</button>
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
                    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style="visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
                    <button class="download-template btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style="  border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Download Template</button>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection 
@section('script')
<script>
$(document).ready(function(e)
{
	$('.download-template').click(function(){
		waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
		var hyperlink = document.createElement('a');
		hyperlink.href = "{{ url('downloads/LabtoLabItemPrice.xlsx') }}";
		
		var mouseEvent = new MouseEvent('click', {
			view: window,
			bubbles: true,
			cancelable: true
		});
		
		hyperlink.dispatchEvent(mouseEvent);
		(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
		waitingDialog.hide();
		e.preventDefault();
	});

});
</script>
@endsection