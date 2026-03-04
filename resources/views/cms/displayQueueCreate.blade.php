<!--@extends('app')-->
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>
.preview-container {
    width: 300px;
    height: 300px;
    border: 2px dashed #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}
</style>

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBUCode').'/cms/displayqueue') }}">Display Queue <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a href="{{ url(session('userBUCode').'/cms/displayqueue/create') }}">Create <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
			<form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ url(session('userBUCode').'/cms/displayqueue') }}" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
					
				<div class="panel panel-primary">
					<div class="panel-heading" style="line-height:12px;">Info</div>
					<div class="panel-body">
						<!--LEFT-->
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="row form-group row-md-flex-center" style="display: flex; justify-content: center; align-items: center;">
                                    <div class="preview-container" id="previewFrame" style="text-align: center;">
                                        <span>No image uploaded yet</span>
                                    </div>
                            </div>


                            <div class="row form-group row-md-flex-center" style="display: flex; justify-content: center; align-items: center;">
                                <input type="file" id="imageUpload" name="imageUpload" accept="image/*">
                            </div>

						</div>
						<!--RIGHT-->
						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">File Name<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="filename" placeholder="File Name" required="required">
								</div>
							</div>
							<!-- <div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Date Created<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="datecreated" placeholder="Date Created" readonly="readonly">
								</div>
							</div> -->
                            <div class="row form-group row-md-flex-center">
                                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                                    <label class="bold ">Status<font style="color:red;">*</font></label>
                                </div>

                                <div class="col-sm-9 col-md-9">
                                    <select id="status" class="form-control" name="status" required="required">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Start Date<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-3 col-md-3">
									<input type="text" class="form-control datepicker" name="startdate" placeholder="Start Date" required="required" >
								</div>
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">End Date<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-3 col-md-3">
									<input type="text" class="form-control datepicker" name="enddate" placeholder="End Date" required="required" >
								</div>
							</div>

                            <div class="row form-group row-md-flex-center">
                                <div class="col-sm-3 col-md-3 pad-0-md text-right-md">
                                    <label class="bold">Notes<font style="color:red;">*</font></label>
                                </div>
                                <div class="col-sm-9 col-md-9">
                                    <textarea class="form-control" name="notes" placeholder="Notes" required="required"></textarea>
                                </div>
                            </div>

                            <div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Date Uploaded<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="dateuploaded" placeholder="Date Uploaded" readonly="readonly">
								</div>
							</div>

                            <div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Uploaded by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="uploadedby" placeholder="Uploaded by" readonly="readonly">
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
	$('.datepicker').datepicker({
		minDate: 0, // Prevents selecting dates in the past
		dateFormat: "yy-mm-dd", // Optional: Change the format to suit your backend
	});


	$('.savebtn').on('click',function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('#formQueueCreate').submit();
	});

    const imageUpload = document.getElementById('imageUpload');
    const previewFrame = document.getElementById('previewFrame');

    imageUpload.addEventListener('change', function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
          // Create an img element and set its src to the file data
          const img = document.createElement('img');
          img.src = e.target.result;

          // Clear the preview frame and insert the image
          previewFrame.innerHTML = '';
          previewFrame.appendChild(img);
        };

        // Read the uploaded file as a data URL
        reader.readAsDataURL(file);
      }
    });


});
</script>
@endsection
