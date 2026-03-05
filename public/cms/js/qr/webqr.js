// QRCODE reader Copyright 2011 Lazar Laszlo
// http://www.webqr.com



var gCtx = null;
var gCanvas = null;
var c=0;
var stype=0;
var gUM=false;
var webkit=false;
var moz=false;
var v=null;
var mediaStream;


var imghtml='<div id="qrfile"><canvas id="out-canvas" width="320" height="240"></canvas>'+
    '<div id="imghelp">drag and drop a QRCode here'+
	'<br>or select a file'+
	'<input type="file" onchange="handleFiles(this.files)"/>'+
	'</div>'+
'</div>';

var vidhtml = '<video id="v" autoplay="autoplay"></video>';


function dragenter(e) {
  e.stopPropagation();
  e.preventDefault();
}

function dragover(e) {
  e.stopPropagation();
  e.preventDefault();
}
function drop(e) {
  e.stopPropagation();
  e.preventDefault();

  var dt = e.dataTransfer;
  var files = dt.files;
  if(files.length>0)
  {
	handleFiles(files);
  }
  else
  if(dt.getData('URL'))
  {
	qrcode.decode(dt.getData('URL'));
  }
}

function handleFiles(f)
{
	var o=[];
	
	for(var i =0;i<f.length;i++)
	{
        var reader = new FileReader();
        reader.onload = (function(theFile) {
        return function(e) {
            gCtx.clearRect(0, 0, gCanvas.width, gCanvas.height);

			qrcode.decode(e.target.result);
        };
        })(f[i]);
        reader.readAsDataURL(f[i]);	
    }
}

function initCanvas(w,h)
{
    gCanvas = document.getElementById("qr-canvas");
    gCanvas.style.width = w + "px";
    gCanvas.style.height = h + "px";
    gCanvas.width = w;
    gCanvas.height = h;
    gCtx = gCanvas.getContext("2d");
    gCtx.clearRect(0, 0, w, h);
	
	
	//gCtx.webkitImageSmoothingEnabled = false; // deprecated chrome v4.2
    gCtx.mozImageSmoothingEnabled = false;
    gCtx.imageSmoothingEnabled = false; //future
}


function captureToCanvas() {
    if(stype!=1)
        return;
    if(gUM)
    {
        try{
          	gCtx.drawImage(v,0,0);
			//gCanvas.toDataURL("image/jpeg", 1.0);
			try{
				qrcode.decode( );
            }
            catch(e){       
                //console.log(e); 
                setTimeout(captureToCanvas, 300);
            };
        }
        catch(e){  
                //console.log(e);
                setTimeout(captureToCanvas, 300);
        };
    }
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

	

function isCanvasSupported(){
  var elem = document.createElement('canvas');
  return !!(elem.getContext && elem.getContext('2d'));
}
function success(stream) {
	$("#result").html("<font color=\"#0F0\"> - scanning - </font>").addClass('blink');
    if(webkit)
        v.src = window.webkitURL.createObjectURL(stream);
    else if(moz)
    {
        v.mozSrcObject = stream;
        v.play();
    }
    else
        v.src = stream;
	
	mediaStream = stream;
	
	
	gUM=true;
    setTimeout(captureToCanvas, 300);
}
		
function error(error) {
	if( error == "[object NavigatorUserMediaError]")
	{
		parent.msgModal.setTitle("Warning");
		parent.msgModal.setType(BootstrapDialog.TYPE_WARNING);
		parent.msgModal.setData("id",'');
		parent.msgModal.setData("set",true);
		parent.msgModal.setData("msg","No longer works on insecure origins.</br>To use this feature, you should consider switching your application to a secure origin, such as HTTPS.");
		parent.msgModal.setData("code","");
		parent.msgModal.realize();
		parent.msgModal.open();
	}
	else
	{
		parent.msgModal.setTitle("Warning");
		parent.msgModal.setType(BootstrapDialog.TYPE_WARNING);
		parent.msgModal.setData("id",'');
		parent.msgModal.setData("set",true);
		parent.msgModal.setData("msg","an error occurred, video source not found.");
		parent.msgModal.setData("code","");
		parent.msgModal.realize();
		parent.msgModal.open();
	}
    gUM=false;
    return;
}


function load(readcallback)
{
	if(isCanvasSupported() && window.File && window.FileReader)
	{
		initCanvas(800, 600);
		qrcode.callback = readcallback;
		setwebcam();
	}
	
}

function mediaStarted()
{
	alert('start');
	
}


function setwebcam()
{
	if(stype==1)
    {
        setTimeout(captureToCanvas, 300);    
        return;
    }
    var n=navigator;
    //document.getElementById("outdiv").innerHTML = vidhtml;
    v=document.getElementById("v");
	if ($.isFunction(MediaStreamTrack.getSources))
	{
		MediaStreamTrack.getSources(function(sourceInfos){
			sourceInfos.reverse(); 
			//alert(JSON.stringify(sourceInfos));
			if( sourceInfos[0].kind === 'video' )
			{
				var constraints = {
					video: {
						optional: [{sourceId: sourceInfos[0].id},{ /*frameRate*/ minFrameRate: 60} ],
						mandatory: {
						  minWidth: 1280,
						  minHeight: 720
						}
					},
					audio: false
				};
				if(n.getUserMedia)
					n.getUserMedia(constraints, success, error);
				else
				if(n.webkitGetUserMedia)
				{
					webkit=true;
					n.webkitGetUserMedia(constraints, success, error);
				}
				else
				if(n.mozGetUserMedia)
				{
					moz=true;
					n.mozGetUserMedia(constraints, success, error);
				}
				stype=1;
				setTimeout(captureToCanvas, 300);
			}

				
		});
		
		
	}
	else
	{
		if(n.getUserMedia)
			n.getUserMedia({ video: true}, success, error);
		else
		if(n.webkitGetUserMedia)
		{
			webkit=true;
			n.webkitGetUserMedia({ video: true}, success, error);
		}
		else
		if(n.mozGetUserMedia)
		{
			moz=true;
			n.mozGetUserMedia({ video: true}, success, error);
		}
	
		stype=1;
		setTimeout(captureToCanvas, 300);
	}
}
function setimg()
{
	document.getElementById("result").innerHTML="";
    if(stype==2)
        return;
    var qrfile = document.getElementById("qrfile");
    qrfile.addEventListener("dragenter", dragenter, false);  
    qrfile.addEventListener("dragover", dragover, false);  
    qrfile.addEventListener("drop", drop, false);
    stype=2;
}
