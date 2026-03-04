<!--@extends('app')-->

@section('style')
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
@endsection
<style>
        .dropzone {

            width: 90%;
            min-height: 220px;
            border: 1px dashed #ddd;
            border-radius: 5px;
            background: #f5f7f5;
            margin: 0 auto;
            transition: background border .43s linear;

        }
        .dropzone:hover{
            border: 1px dashed #53d335;
            background: #efffdd;
	 cursor:pointer;
        }

        .dropzone_bx{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 23px;
        }
        .dropzone_bx button{
            border-style: none;
            width: 70%;
            display: block;
            padding: 10px 25px;
            background: #1dbb63;
            color: white;
            border-radius: 3px;
            font-size: 14px;
        }

        .action-buttons{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            font-size: 23px;
            margin-top: 10px;
        }
        .action-buttons button{
            border: none;
            border-style: none;
            display: block;
            padding: 10px 25px;
            background: #62766b;
            color: white;
            border-radius: 3px;
            font-size: 14px;
            margin: 0 5px;
        }
        .action-buttons button:hover{
            background: #5e7066;
        }
	.dz-filename, .dz-size{ visibility:hidden;}
	

    </style>

@section('content')

<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li><a href="{{ url(session('userBUCode').'/cms/resultuploading') }}">EROS - Patient  <span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a>{{ $datas[0]['id'] }} <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
			@if ($message = Session::get('success'))
			<div class="alert alert-success alert-block">
			    <button type="button" class="close close-alert" data-dismiss="alert">x</button>
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
			<form action="{{route('dropzone.store')}}" method="post" name="file" files="true" enctype="multipart/form-data" class="dropzone" id="image-upload">
				@csrf
				
				<div class="panel panel-primary">
					<div class="panel-heading" style="line-height:12px;">Info</div>
					<div class="panel-body">
						<!--LEFT-->
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Full Name<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="guarantor" placeholder="Full Name" readonly="readonly"  value="{{ $datas[0]['FullName'] }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">DOB<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="subgroup" placeholder="Sub Group " readonly="readonly"  value="{{ $datas[0]['birthdate'] }}" >
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Address<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="address" placeholder="Address" readonly="readonly" value="{{ $datas[0]['Address'] }}" >
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Phone<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="phone" placeholder="Phone" readonly="readonly" value="{{ $datas[0]['Phone'] }}" >
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Email<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="email" placeholder="Email" readonly="readonly"  value="{{ $datas[0]['Email'] }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">City<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="city" placeholder="City" readonly="readonly"  value="{{ $datas[0]['City'] }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Guarantor Name<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="guarantor" placeholder="Guarantor Name" readonly="readonly"  value="{{ $datas[0]['Company'] }}">
								</div>
							</div>
						</div>
						<!--RIGHT-->
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Transaction No.<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="code"  readonly="readonly" value="{{ $datas[0]['trans_no'] }}">
								</div>
								
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Patient Id<font style="color:red;">*</font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="pcode"  readonly="readonly" value="{{ $datas[0]['patient_id'] }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Created by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="createdby" placeholder="Created by" readonly="readonly" value="">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Date Created<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="datecreated"  readonly="readonly"  value="{{ $datas[0]['order_date'] }}">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update by<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updateby" placeholder="Update by" readonly="readonly" value="">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Update date<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="updatedate" placeholder="Update date" readonly="readonly" value="">
								</div>
							</div>
							<div class="row form-group row-md-flex-center">
								<div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
									<label class="bold ">Status<font style="color:red;"></font></label>
								</div>
								<div class="col-sm-9 col-md-9">
									<input type="text" class="form-control" name="status" style="color:blue;" placeholder="Status" readonly="readonly" value="{{ (isset($submitStatus[0]->Description))?$submitStatus[0]->Description:'' }}">
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
					<button class="portalPost saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="@if (session('userDepartmentCode') != 'ICT') visibility:hidden; @endif border-radius:0px; line-height:29px;" type="button"> Submit to HR Portal </button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
<script>
$(document).ready(function(e) {

	

	$('.updatebtn').on('click',function(e){
		if( parent.required($('form')) ) return false;
		e.preventDefault();
		$('.updatebtn').attr('disabled', true);
		$('#formQueueEdit').submit();
		
	});
	
	$('.viewbtn').on('click',function(e){
		e.preventDefault();
		alert('i-click viewbtn');
	});
	$('.close-alert').on('click', function(e){
		$('.close-alert').addClass('hide');
	});
	$('.portalPost').on('click', function(e){
		e.preventDefault();
		parent.postData(
			"/cms/dropzone/updatefile",
			{
				'code' : $('input[name="code"]').val(),
				'_token': $('input[name=_token]').val()
			},
			function($data)
			{ 	
				parent.waitingDialog.hide();
				alert($data);
				location.reload();
				modalRef.close();
			}
		
		); 
		
	});
	

	
	Dropzone.autoDiscover = false;
	$(".dropzone").dropzone({
		uploadMultiple:false,
		maxFiles: 1,
		acceptedFiles: '.pdf, .png, .jpg, .jpeg', 
		addRemoveLinks: true,
		clickable: true,
		init: function() { 
			myDropzone = this;
			
			var submitStatus = "{{ (isset($submitStatus[0]->Description))?$submitStatus[0]->Description:'Allow' }}";
			if( submitStatus !=  'Allow' )
			{
				 myDropzone.removeEventListeners();
			}
			
			myDropzone.on('success', function(){
				if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
					location.reload();
				}
			});
			myDropzone.on('addedfile', function(file) {
				
				var ext = file.name.split('.').pop();
				var fname  = file.name.split('.');
				var iCode = $('input[name="code"]').val();
				if (ext == "pdf") {
					$(file.previewElement).find(".dz-image img").attr("src", "/images/PDF.png");
					var preview = document.getElementsByClassName('dz-preview');
					preview = preview[preview.length - 1];

					var imageName = document.createElement('span');
					imageName.innerHTML = file.name;

					preview.insertBefore(imageName, preview.firstChild);
					if("{{ session('userDepartmentCode') }}" !=  fname[0] || submitStatus !=  'Allow' )
					{
						$(file.previewElement).find('.dz-remove').addClass('hide');
					}

				} else if (ext.indexOf("png") != -1 || ext.indexOf("jpg") != -1 || ext.indexOf("jpeg") != -1) {
					$(file.previewElement).find(".dz-image img").attr("src", "/APE/"+iCode+"/"+file.name);
					var preview = document.getElementsByClassName('dz-preview');
					preview = preview[preview.length - 1];

					var imageName = document.createElement('span');
					imageName.innerHTML = file.name;

					preview.insertBefore(imageName, preview.firstChild);
					var iDept =  fname[0].split('-');
					if("{{ session('userDepartmentCode') }}" != iDept[0] || submitStatus !=  'Allow'  )
					{
						$(file.previewElement).find('.dz-remove').addClass('hide');
					}
				} else if (ext.indexOf("xls") != -1) {
					$(file.previewElement).find(".dz-image img").attr("src", "/Content/images/excel.png");
				}
				
			});
			$.ajax({
				url: '/cms/dropzone/getfiles',
				type: 'get',
				data: {code: $('input[name="code"]').val()},
				dataType: 'json',
				success: function(response){

					$.each(response, function(key,value) { 
						var ext = value.name.split('.').pop();
						var iCode = $('input[name="code"]').val();
						var mockFile = { name: value.name, size: value.size};

						myDropzone.emit("addedfile", mockFile);
						if( ext == 'pdf')
						{
							myDropzone.emit("thumbnail", mockFile, '/images/PDF.png');
						}
						else if(ext == 'png' || ext == 'jpg' || ext == 'jpeg')
						{
						
							myDropzone.emit("thumbnail", mockFile, '/APE/'+iCode+'/'+value.name);
						}
						
						myDropzone.emit("complete", mockFile);

					});

				}
			});
		},
		removedfile: function(file) 
		{
			var name = file.name;
			$.ajax({
			    type: 'POST',
			    url: '/cms/dropzone/deletefile',
			    data: {code: $('input[name="code"]').val(), filename: name},
			    success: function (data){
				console.log("File has been successfully removed!!");
			    },
			    error: function(e) {
				console.log(e);
			    }});
			    var fileRef;
			    return (fileRef = file.previewElement) != null ? 
			    fileRef.parentNode.removeChild(file.previewElement) : void 0;
		}
	});
	
	$(".dropzone").on("click", ".dz-complete", function() { 
		var spanVal = $(this).closest('div').find('span').html();
		
		var hyperlink = document.createElement('a');
		var iCode = $('input[name="code"]').val();
		hyperlink.href = '/APE/'+iCode+'/'+spanVal;
		var mouseEvent = new MouseEvent('click', {
			view: window,
			bubbles: true,
			cancelable: true
		});
		
		hyperlink.dispatchEvent(mouseEvent);
		(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
		
		e.preventDefault();
	});
	
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});

});
</script>
@endsection
