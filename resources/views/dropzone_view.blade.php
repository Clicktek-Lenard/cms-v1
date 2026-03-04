<html>
<head>
    <title>Laravel 9 Drag And Drop File Upload Using Dropzone JS - Techsolutionstuff</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center">Laravel 9 Drag And Drop File Upload Using Dropzone JS - Techsolutionstuff</h1><br>
            <form action="{{route('dropzone.store')}}" method="post" name="file" files="true" enctype="multipart/form-data" class="dropzone" id="image-upload">
                @csrf
                <div>
                <h3 class="text-center">Upload Multiple Images</h3>
            </div>    
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
 
  Dropzone.autoDiscover = false;
  $(".dropzone").dropzone({
  acceptedFiles: '.pdf', 
  addRemoveLinks: true,
  clickable: true,
    init: function() { 
      myDropzone = this;
      $.ajax({
        url: '/dropzone/getfiles',
        type: 'get',
        //data: {request: 'fetch'},
        dataType: 'json',
        success: function(response){
  
          $.each(response, function(key,value) {
            var mockFile = { name: value.name, size: value.size};
  
            myDropzone.emit("addedfile", mockFile);
            myDropzone.emit("thumbnail", mockFile, '/images/PDF.png');
            myDropzone.emit("complete", mockFile);
  
          });
  
        }
      });
      
      myDropzone.on('addedfile', function(file) {

	    var ext = file.name.split('.').pop();

	    if (ext == "pdf") {
		$(file.previewElement).find(".dz-image img").attr("src", "/public/images/PDF.png");
		var preview = document.getElementsByClassName('dz-preview');
		    preview = preview[preview.length - 1];

		    var imageName = document.createElement('span');
		    imageName.innerHTML = file.name;

		    preview.insertBefore(imageName, preview.firstChild);
		
		
	    } else if (ext.indexOf("doc") != -1) {
		$(file.previewElement).find(".dz-image img").attr("src", "/Content/images/word.png");
	    } else if (ext.indexOf("xls") != -1) {
		$(file.previewElement).find(".dz-image img").attr("src", "/Content/images/excel.png");
	    }
	});
      
      
    }
  });




	

</script>
</body>
</html>