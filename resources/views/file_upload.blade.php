<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel 8/9 Drag and Drop File Upload with Dropzone on Button Click</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/basic.min.css">
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

    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 mt-3">
                <h4 class="text-center">Laravel 8/9 Drag and Drop File Upload with Dropzone on Button Click</h4>
                <div id="dropzone" class="mt-3">
                    <form action="{{ route('file_upload') }}" class="dropzone" >

                    </form>
                    <div class="action-buttons">
                        <button type="button" id="uploadfiles">Upload files</button>
                        <button type="button" id="clear">Clear</button>
                    </div>

                </div>
            </div>
        </div>
    </div>





    
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

    <script>

        Dropzone.autoDiscover = false;

        var myDropzone = new Dropzone(".dropzone", {
            headers: {
               'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            autoProcessQueue: false,
            uploadMultiple:false,
            parallelUploads: 10, // Number of files process at a time (default 2)
            maxFilesize: 2, //maximum file size 2MB
            maxFiles: 4,
            addRemoveLinks: "true",
            acceptedFiles: ".jpeg,.jpg,.png,.pdf",
            dictDefaultMessage : '<div class="dropzone_bx"><button type="button">Browse a file</button><span>Or</span><b>Drag & Drop</b></div>',
            dictResponseError: 'Error uploading file!',
            thumbnailWidth: "150",
            thumbnailHeight: "150",
            createImageThumbnails: true,
            dictRemoveFile: "Remove",


            init: function () {
                var dropzone = this;
                $("#clear").click(function(){
                dropzone.removeAllFiles(true);
             });
            },


        });

        myDropzone.on("complete", function(file) {
            myDropzone.removeFile(file);

            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                alert('Files uploaded successfully'); //Show success message if all file uploaded successfully
            }

        });
        myDropzone.on("success", function(file, response) {
            console.log(response);
        });


        $('#uploadfiles').click(function(){
            myDropzone.processQueue();
        });

    </script>

</body>

</html>