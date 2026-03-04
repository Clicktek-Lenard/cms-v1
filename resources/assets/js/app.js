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
		
		if( $.trim(self.val()) == "" ){
			error++;
			
			msgModal.setTitle("Warning");
			msgModal.setType(BootstrapDialog.TYPE_WARNING);
			msgModal.setData("id",self);
			msgModal.setData("set",true);
			msgModal.setData("msg","Missing Required field");
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



window.addEventListener('load', callbackResize, false);
window.addEventListener('resize', callbackResize, false);