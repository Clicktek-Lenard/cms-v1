<!DOCTYPE html>
<html>
<head>
</head>
<body>
    
<div class="modal-cms-header">
     
    <form method="POST" >
        
        <div class="row form-group row-md-flex-center">
            <div class="col-sm-6 col-md-6">
                <div id="my_camera"></div>
                <input type=button value="Take Snapshot" onClick="take_snapshot()">
                <input type="hidden" name="image" class="image-tag">
            </div>
            <div class="col-sm-6 col-md-6">
                <div id="results">Preview Image</div>
            </div>
            
        </div>
    </form>
</div>
    
<script language="JavaScript">
    Webcam.set({
        width: 400,
        height: 350,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    
    Webcam.attach( '#my_camera' );
    
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img width="400" height="350" src="'+data_uri+'"/>';
        } );
    }
</script>
   
</body>
</html>