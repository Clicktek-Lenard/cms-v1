var waitingDialog = (function ($) {
	var $dialog = $(
		'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;z-index:9999;">' +
			'<div class="modal-dialog modal-m">' +
				'<div class="tiktokcontainer">' +
					'<div class="tiktok"></div>' +
					'<div class="tiktok red"></div>' +
				'</div>' +
			'</div>' +
		'</div>');
	return {
		show: function (message, options) {
			var settings = $.extend({
				dialogSize: 'm',
				progressType: ''
			}, options);
			if (typeof message === 'undefined') {
				message = 'Loading';
			}
			if (typeof options === 'undefined') {
				options = {};
			}
			$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
			$dialog.find('.progress-bar').attr('class', 'progress-bar');
			if (settings.progressType) {
				$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
			}
			$dialog.find('h4').text(message);
			$dialog.modal();
		},
		hide: function () {
			$dialog.modal('hide');
		}
	}
})(jQuery);
var searchModal = new BootstrapDialog({
	//message: $('<div id="modalQR"></div>').load("../pages/scan.html"),
	message: function(dialog) {
		var $message = $('<div id="modalQR"></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	data: {
		'pageToLoad': '../pages/scan.html?1'
	},
	
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default closebtn',
		label: "Close",
		action: function (modalRef) {
			if (mediaStream) {
				mediaStream.stop();
				gCtx = null;
		        gCanvas = null;
		        c = 0;
		        stype = 0;
		        gUM = false;
		        webkit = false;
		        moz = false;
		        v = null;
		        mediaStream = null;
			}
			modalRef.close();
		}
	},
	{
		    id: 'rescanbtn',
		    icon: 'glyphicon glyphicon-asterisk icon-spin',
		    cssClass: 'btn-success actionbtn disabled',
		    label: 'Scan QR-Code',
		    autospin: true,
		    action: function (modalRef) {
		        this.addClass('disabled');
		        mediaStream.stop();
		        gCtx = null;
		        gCanvas = null;
		        c = 0;
		        stype = 0;
		        gUM = false;
		        webkit = false;
		        moz = false;
		        v = null;
		        mediaStream = null;
		        $("#scanClick").click();
		    }
		}
	]
});
var msgModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = "Unknown Error...";
		var $code = "";
		if( $.trim(dialog.getData('code')) != "" )
			$code = '<b>"'+decodeURIComponent(dialog.getData('code'))+'"</b>';
		if( dialog.getData('set') == true  )
			$message = $('<div class="msgModal">'+dialog.getData('msg')+" "+$code+' </div>');
		return $message;
	},
	data: {
		'id':""	
	},
	animate: false,
	closable: false,
	buttons: [{
		id : 'msgbtn',
		label: 'Yes',
		cssClass: 'hide'
	},
	{
		cssClass: 'btn-default closebtn',
		label: 'Close',
		action: function (modalRef) {
			if( $.trim(modalRef.getData('id')) != ""  )
			{
				setTimeout(function(){
					$(document).find( modalRef.getData('id') ).focus();
				},100);
			}
			modalRef.close();	
		}
	}]
});
function callbackResize()
{
	if ($(window).width() < 767)
	{ 
		$('#menu-content').removeClass('in').addClass('out');
	}
	else
	{
		$('#menu-content').removeClass('out').addClass('in');
	}
}
function postData($url,$data,$callback)
{
	var data = ( typeof $data === "undefined" )? "{}":$data;
	$.ajax({
		async: false,
		type: "POST",
		url: $url,
		data: data,
		success: $callback,
		error: function (XMLHttpRequest, textStatus, errorThrown) { debugger; }
	});
}
function getData($url,$data,$callback)
{
	var data = ( typeof $data === "undefined" )? "{}":$data;
	$.ajax({
		async: false,
		type: "GET",
		url: $url,
		data: data,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		success: $callback, 
		error: function (XMLHttpRequest, textStatus, errorThrown) { debugger; }
	});
}

function required($this)
{ 
	var error =0;
	$('[required]', $this).each(function () {
		var self = $(this);
		
		if( ((self.hasClass('cmsAmount')   &&   self.val() == "0") || $.trim(self.val()) == "" )  ){
			error++;
			
			msgModal.setTitle("Warning");
			msgModal.setType(BootstrapDialog.TYPE_WARNING);
			msgModal.setData("id",self);
			msgModal.setData("set",true);
			msgModal.setData("msg","Input required");
			msgModal.setData("code",encodeURIComponent(self.attr('placeholder')));
			if( typeof(self[0].selectize) !== 'undefined' && Object.keys(self[0].selectize).length !=0  ) msgModal.setData("id",'.selectize-input input[placeholder="'+self.attr('placeholder')+'"]');
			msgModal.realize();
			msgModal.open();
			return false;
		}
		
	});
	if ( error != 0 ) return true;	
	else return false;
}
function readQrsearch(a)
{
	var html = htmlEntities(a);
	
	$("#result").html('<span class="spinner"><i class="fa fa-refresh fa-2x fa-spin"></i></span><font color="#0F0"> Searching...</font>');
	if (mediaStream) mediaStream.stop();
	
	parent.loading();
	
	parent.getData('item/GetByQRCode',function($data){
		
		$data[0] = $data[0] || new Array();
		parent.vibrate(10);
		setTimeout(function(){
			if($data[0].ItemID != 0 && typeof $data[0].ItemID !== "undefined" )
				$('.body-tat-content').load("../pages/asset-ItemEditNew.html",doneSearchMain($data[0].ItemID,$data[0].Code),setTimeout( 'searchModal.close();' ,1000 ));
			else
				$("#result").html('<font color="#FFFFFF"> No record found...</font>');
			parent.loaddone();	
		},100);
	},{terms:html});
	
	$('#rescanbtn').removeClass('disabled');
	$('#rescanbtn span').removeClass('icon-spin');
}
function loadQRsearch()
{
	if(isCanvasSupported() && window.File && window.FileReader)
	{ 
		initCanvas(800, 600);
		qrcode.callback = parent.readQrsearch;
		setwebcam();
	}
}
function scanQrCode(){
	gCtx = null;
	gCanvas = null;
	c = 0;
	stype = 0;
	gUM = false;
	webkit = false;
	moz = false;
	v = null;
	mediaStream = null;
	
	searchModal.setTitle("Search QR-Code");
	searchModal.realize();
	searchModal.open();
	$('#modalQR').delegate('#scanClick', 'click', parent.loadQRsearch );
};
function vibrate(count){
	navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
	var count = count || 0;
	navigator.vibrate(count);
}
function connection()
{
	navigator.connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection || null;
	var connection = {
		type : navigator.connection.type,
		downlinkMax :navigator.connection.downlinkMax,
	};
}
function netStat(event)
{
	if( event.type == "offline" || ( event.type == "click" && navigator.onLine == false ) )
	{
		parent.vibrate(100);
		$('.notfound').text("No Internet Connection").removeClass('hide').addClass('offline');
		$('.spinner').addClass('hide');
	}//else
		//$('.notfound').text("").removeClass('offline').addClass('hide');		
}
function isMobile()
{
	return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
}

$(document).on('click','.waiting',function(){
	parent.waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
});
$(document).on('click','.saving',function(){
	parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
});

window.addEventListener('load', callbackResize, false);
window.addEventListener('resize', callbackResize, false);