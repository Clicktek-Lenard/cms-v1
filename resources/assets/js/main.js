/*!
 * jQuery Searchable Plugin v1.0.0
 * https://github.com/stidges/jquery-searchable
 *
 * Copyright 2014 Stidges
 * Released under the MIT license
 */
;(function( $, window, document, undefined ) {

    var pluginName = 'searchable',
        defaults   = {
            selector: 'tbody tr',
            childSelector: 'td',
            searchField: '#search',
            striped: false,
            oddRow: { },
            evenRow: { },
            hide: function( elem ) { elem.hide(); },
            show: function( elem ) { elem.show(); },
            searchType: 'default',
            onSearchActive: false,
            onSearchEmpty: false,
            onSearchFocus: false,
            onSearchBlur: false,
            clearOnLoad: false
        },
        searchActiveCallback = false,
        searchEmptyCallback = false,
        searchFocusCallback = false,
        searchBlurCallback = false;

    function isFunction(value) {
        return typeof value === 'function';
    }

    function Plugin( element, options ) {
        this.$element   = $( element );
        this.settings   = $.extend( {}, defaults, options );

        this.init();
    }

    Plugin.prototype = {
        init: function() {
            this.$searchElems = $( this.settings.selector, this.$element );
            this.$search      = $( this.settings.searchField );
            this.matcherFunc  = this.getMatcherFunction( this.settings.searchType );

            this.determineCallbacks();
            this.bindEvents();
            this.updateStriping();
        },

        determineCallbacks: function() {
            searchActiveCallback = isFunction( this.settings.onSearchActive );
            searchEmptyCallback = isFunction( this.settings.onSearchEmpty );
            searchFocusCallback = isFunction( this.settings.onSearchFocus );
            searchBlurCallback = isFunction( this.settings.onSearchBlur );
        },

        bindEvents: function() {
            var that = this;

            this.$search.on( 'change keyup', function() {
                that.search( $( this ).val() );

                that.updateStriping();
            });

            if ( searchFocusCallback ) {
                this.$search.on( 'focus', this.settings.onSearchFocus );
            }

            if ( searchBlurCallback ) {
                this.$search.on( 'blur', this.settings.onSearchBlur );
            }

            if ( this.settings.clearOnLoad === true ) {
                this.$search.val( '' );
                this.$search.trigger( 'change' );
            }

            if ( this.$search.val() !== '' ) {
                this.$search.trigger( 'change' );
            }
        },

        updateStriping: function() {
            var that     = this,
                styles   = [ 'oddRow', 'evenRow' ],
                selector = this.settings.selector + ':visible';

            if ( !this.settings.striped ) {
                return;
            }

            $( selector, this.$element ).each( function( i, row ) {
                $( row ).css( that.settings[ styles[ i % 2 ] ] );
            });
        },

        search: function( term ) {
            var matcher, elemCount, children, childCount, hide, $elem, i, x;

            if ( $.trim( term ).length === 0 ) {
                this.$searchElems.css( 'display', '' );
                this.updateStriping();

                if ( searchEmptyCallback ) {
                    this.settings.onSearchEmpty( this.$element );
                }

                return;
            } else if ( searchActiveCallback ) {
                this.settings.onSearchActive( this.$element, term );
            }

            elemCount = this.$searchElems.length;
            matcher   = this.matcherFunc( term );

            for ( i = 0; i < elemCount; i++ ) {
                $elem      = $( this.$searchElems[ i ] );
                children   = $elem.find( this.settings.childSelector );
                childCount = children.length;
                hide       = true;

                for ( x = 0; x < childCount; x++ ) {
                    if ( matcher( $( children[ x ] ).text() ) ) {
                        hide = false;
                        break;
                    }
                }

                if ( hide === true ) {
                    this.settings.hide( $elem );
                } else {
                    this.settings.show( $elem );
                }
            }
        },

        getMatcherFunction: function( type ) {
            if ( type === 'fuzzy' ) {
                return this.getFuzzyMatcher;
            } else if ( type === 'strict' ) {
                return this.getStrictMatcher;
            }

            return this.getDefaultMatcher;
        },

        getFuzzyMatcher: function( term ) {
            var regexMatcher,
                pattern = term.split( '' ).reduce( function( a, b ) {
                    return a + '[^' + b + ']*' + b;
                });

            regexMatcher = new RegExp( pattern, 'gi' );

            return function( s ) {
                return regexMatcher.test( s );
            };
        },

        getStrictMatcher: function( term ) {
            term = $.trim( term );

            return function( s ) {
                return ( s.indexOf( term ) !== -1 );
            };
        },

        getDefaultMatcher: function( term ) {
            term = $.trim( term ).toLowerCase();

            return function( s ) {
                return ( s.toLowerCase().indexOf( term ) !== -1 );
            };
        }
    };

    $.fn[ pluginName ] = function( options ) {
        return this.each( function() {
            if ( !$.data( this, 'plugin_' + pluginName ) ) {
                $.data( this, 'plugin_' + pluginName, new Plugin(this, options) );
            }
        });
    };

})( jQuery, window, document );
var waitingDialog = (function ($) {
	var $dialog = $(
		'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
		'<div class="modal-dialog modal-m">' +
		'<div class="modal-content">' +
			'<div class="modal-header"><h4 style="margin:0;"></h4></div>' +
			'<div class="modal-body">' +
				'<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
			'</div>' +
		'</div></div></div>');
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
		'pageToLoad': '../pages/scan.html'
	},
	
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default closebtn',
		label: 'Close',
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



function SaveToDisk(fileUrl, fileName) {
	parent.vibrate(10);
	var hyperlink = document.createElement('a');
    hyperlink.href = fileUrl;
    hyperlink.target = '_blank';
    hyperlink.download = fileName || fileUrl;

    var mouseEvent = new MouseEvent('click', {
        view: window,
        bubbles: true,
        cancelable: true
    });

    hyperlink.dispatchEvent(mouseEvent);
    (window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
	
}
function callbackResize()
{
	if ($(window).width() < 767)
	{
		$('#menu-content').removeClass('in').addClass('out');
		$(".page-content-wrapper").css("min-height", $(window).height() - 10 );
	}
	else
	{
		$('#menu-content').removeClass('out').addClass('in');
		$(".page-content-wrapper").css("min-height", $(window).height());
	}
}

function loading()
{
	$('.spinner').removeClass('hide');
	$('.notfound').addClass('hide');
	return false;
}
function unloading(notfound)
{
	$('.spinner').addClass('hide');
	$('.notfound').addClass('hide');
	setTimeout(function(){waitingDialog.hide();}, 100);
	var notfound = notfound || "";
	if(notfound ){
		$('.notfound').removeClass('hide');
	}
	return false;
}
function loaddone()
{
	$('.spinner').addClass('hide');
	$('.notfound').addClass('hide');
	setTimeout(function(){waitingDialog.hide();}, 100);
	return false;	
}
function toggelMenu($this)
{
	var $this = $($this);
	var href = $this.find('a').attr('href');
	$this.closest('ul').find('li').removeClass('active');
	$this.addClass('active'); 
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
			msgModal.setData("msg","Missing");
			msgModal.setData("code",encodeURIComponent(self.attr('placeholder')));
			if( typeof(self[0].selectize) !== 'undefined' && Object.keys(self[0].selectize).length !=0  ) msgModal.setData("id",'.selectize-input input[placeholder="'+self.attr('placeholder')+'"]');
			msgModal.realize();
			msgModal.open();
			
			
			/*alert("Missing "+self.attr('placeholder'));
			self.focus();*/
			return false;
		}
	});
	if ( error != 0 ) return true;	
	else return false;
}
function eAdvanceSearch()
{
	parent.$('.advanceSearch').removeClass("disabled").css('opacity',"1");
}
function dAdvanceSearch()
{
	parent.$('.advanceSearch').addClass("disabled").css('opacity',"0.25");	
}
function mainSearch()
{
	parent.$('.mainSearch').unbind();
	parent.$('.mainSearch')
	.on('keypress',function(e){
		if(e.which == 13 && !$('.advanceSearch').hasClass('disabled') ) {
			parent.loading();
			$('.clearbtn').trigger('click');	
			$('.searchbtn').trigger('click');
			$(this).trigger('blur');
			$(this).focus();
			return false;
		}
	});	
}

/* WEB API */
function getData($url,$callback,$data)
{
	var data = ( typeof $data === "undefined" )? "{}":$data;
	$.ajax({
		type: "GET",
		url: $url,
		data: data,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		async: false,
		success: $callback, 
		error: function (XMLHttpRequest, textStatus, errorThrown) { debugger; }
	});
}
function postData($url,$callback,$data)
{
	var data = ( typeof $data === "undefined" )? "{}":$data;
	$.ajax({
		type: "POST",
		url: $url,
		data: JSON.stringify(data),
		contentType: "application/json; charset=utf-8",
		dataType: "json",
		async: false,
		success: $callback, 
		error: function (XMLHttpRequest, textStatus, errorThrown) { debugger; }
	});
}
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
	}else
		$('.notfound').text("").removeClass('offline').addClass('hide');		
}


/* Body Load */
function getQueryVariable(item)
{
	var svalue = location.search.match(new RegExp("[\?\&]" + item + "=([^\&]*)(\&?)","i"));
	return svalue ? svalue[1] : svalue;
}
function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
}
function toggle($ithis)
{ 
	var $this = $($ithis),
	what = $this.data("toggle-what"),
	elem = $(what),
	type = $this.data("toggle-type"),
	subs = $this.data("toggle-sub"),
	href = $this.find('a').attr('href'),
	panel = $this.data("toggle-panel");
	
	if( elem && type)
	{ 
		if ( typeof subs === "undefined" && typeof href === "undefined"  )
			href = "?";
		else if( typeof subs !== "undefined" )
			href = "?"+subs+href;
		else
			href = "?"+href;

		var chash = window.location.hash;
		var cherf = "?"+window.location.search.substring(1)+chash;

		history.pushState({url: "" + href + "",curl: "" + cherf + ""}, cherf, "/TaT/pages/index.html"+href);
		loading();
		elem.load(type);
		callbackResize();
		toggelMenu($ithis);
		// bind general search
		parent.$('.scanQrCode').unbind();
		parent.mainSearch();
		
		parent.$('.scanQrCode').bind('click',scanQrCode);
		parent.$('.FixedHeader_Cloned').hide();
		dAdvanceSearch();
		if( typeof panel === "undefined" )
			parent.$("#filter-panel").load("../pages/searchDefault.html");
	}
	return false;
}
function urltoggle(e)
{
	parent.$('.FixedHeader_Cloned').hide();
	var hash = window.location.hash;
	var herf = "?"+window.location.search.substring(1)+hash;
	history.pushState({url: "" + herf + "", curl: "" + herf + ""}, herf, "/TaT/pages/index.html"+herf);
}
function doneSearchMain(id,name)
{
	$('.crumb').removeClass('hide');
	$(".breadcrumb li").removeClass("active");
	$(".breadcrumb").append("<li class=\"active\" data-asset-editnew-item=\""+id+"\"><a href=\"#asset\" class=\"toggleCrumb asset\" data-toggle-what=\".page-content-wrapper\" data-toggle-type=\"asset.html\" data-toggle-sub=\"asset-ItemEditNew.html\" >"+name+"</a></li>");	
	
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
function __ERROR__($data)
{
	//alert($data.__Error__);
	parent.msgModal.setTitle("Error");
	parent.msgModal.setType(BootstrapDialog.TYPE_DANGER);
	parent.msgModal.setData("id",'');
	parent.msgModal.setData("set",true);
	parent.msgModal.setData("msg",$data.__Error__);
	parent.msgModal.setData("code",'');
	parent.msgModal.realize();
	parent.msgModal.open();
	
	if($data.__Error__ == 'Login Required')
		window.location.href = window.location.origin+"/User/Login";	
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

function headerCrumb()
{
	$('.body-tat').css({'margin-top': (parseFloat($('.header-crumb').height())-30)+"px"});	
	return false;
}
function btnClick($this)
{
	parent.loading();
	$($this).addClass('disabled').find('.spinnerbtn').show();
	return false;
}

/* not use at this moment */
function scrollContent()
{
	if( $('#menu-content').hasClass('in') )
	{
		var $this = $('#menu-content'); 
		//alert($(window).height());
		if (($this.offset().top + $this.height()) >= $(window).height()) {
			//alert('dddd');
		}
	}
}
/* end not use at this moment */
function updateViews()
{
	if(is_landscape)
	{
		$('.navbar-fixed-top').css('position','absolute');
		$('.navbar-fixed-bottom').css('position','static');
	}
	else
	{
		if(is_keyboard)
		{
			$('.navbar-fixed-top').css('position','absolute');
			$('.navbar-fixed-bottom').css('position','static');
		}
		else
			$('.navbar-fixed-top,.navbar-fixed-bottom').css('position','fixed');	
	}
}
function detectOrientationMode()
{
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))
	{
		if (window.orientation != 0)
		{
			is_landscape = true;
			parent.$('.FixedHeader_Cloned').hide();
		}
		else
		{
			is_landscape = false;
			parent.$('.FixedHeader_Cloned').show();
		}
		updateViews();
	}
}
/* window type */

if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))
{
	//window.addEventListener('scroll', scrollContent, false);
	
	var is_keyboard = false;
	var is_landscape = false;
	var initial_screen_size = window.outerHeight;
	window.addEventListener("orientationchange", detectOrientationMode, false);
	
	var elem = document.body;
	if (elem.requestFullscreen) {
	  elem.requestFullscreen();
	} else if (elem.msRequestFullscreen) {
	  elem.msRequestFullscreen();
	} else if (elem.mozRequestFullScreen) {
	  elem.mozRequestFullScreen();
	} else if (elem.webkitRequestFullscreen) {
	  elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
	}
	
	
	if (/Android|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))
	{
		window.addEventListener("resize", function() {
			is_keyboard = (window.outerHeight < initial_screen_size);
			//alert(is_keyboard);
			if( is_keyboard == true && $('.mainSearch').is(":focus")  )
				$('.navbar-fixed-bottom').css('position','static');	
			//else if( is_keyboard == false && $('.mainSearch').is(":focus") )
			else
				//is_landscape = (window.outerHeight < window.outerWidth );
				updateViews();	
		}, false);
	}
	if (/iPhone|iPad|iPod/i.test(navigator.userAgent))
	{
/*		$(".page-content-wrapper").delegate('input, textarea','focusin',function(e)
		{
			
		});*/
		
		$('.mainSearch').on('focus',function(e){
			setTimeout(function () {$(document).scrollTop($(this).scrollTop());}, 10);
		});
		
		
		$(".page-content-wrapper").delegate('input, textarea','focus',function(e)
		{
			is_keyboard = true;
			updateViews();
		});
		$(".page-content-wrapper").delegate('input, textarea','blur',function(e)
		{
			is_keyboard = false;
			updateViews();
		});
	}
}
else
{ 
	if ($(window).width() < 767)
		$('#menu-content').removeClass('in').addClass('out');
	else
		$('#menu-content').removeClass('out').addClass('in');
	
	window.addEventListener('resize', callbackResize, false);
	window.addEventListener('scroll', scrollContent, false);
}
/* Action Type*/
$('.logout').on('click',function(){ 
	localStorage.removeItem('TaTKName');
	window.location.href =  $(this).find('a').attr('href');
});
$(".toggle").on('click',function(e){
	e.preventDefault();
	toggle(this);
});
$('.navbar-brand.TAT').on('click',function(){
	callbackResize();
	$("#menu-content li").removeClass('active');
});
$('.sub-menu li.toggle').on('click',function(){
	$("#menu-content li").removeClass('active');
	$(this).addClass('active');
	var id = $(this).closest('ul').attr('id'); 
	$("#menu-content").find("[data-target='#" + id + "']").addClass('active');
});
$('.scanQrCode').on('click',function(){
	parent.vibrate(10);
	scanQrCode();	
});
$('.menu-toggle').on('click',function(){
	window.scrollTo(0, 0);		
});
$(".page-content-wrapper").delegate('.toggleCrumb','click',function(e)
{
	window.scrollTo(0, 0);
	parent.$('.FixedHeader_Cloned').hide();
	e.preventDefault();
	//if( $(this).closest('li').hasClass('active') ) return false;
	var $this = $(this),
		what = $this.data("toggle-what"),
		elem = $(what),
		type = $this.data("toggle-type"),
		subs = $this.data("toggle-sub"),
		index = $this.closest('li').index();
	if( subs ){
		$('.breadcrumb li:gt("'+index+'")').remove();
		loadsub(subs);
	}
	else if( type )
		elem.load(type);	
	parent.headerCrumb();
});


/* Event Listener */
window.addEventListener('load', callbackResize, false);
window.addEventListener('online', netStat, false);
window.addEventListener('offline', netStat, false);
window.addEventListener('click', netStat, false);
$(document).ready(function () 
{
	$("#filter-panel")
	.on('click',function(e){
		e.preventDefault();
		return false;
	});
	
	$(this).unbind('keypress');
	$(this).on('keypress',function(e){
		if(e.altKey && e.which == 97 ) {
			$('.advanceSearch').trigger('click');
			$('#filter-panel').find('.filter:first').focus();
    	}
		else if(e.altKey && e.which == 99 ) {
			$('.clearbtn').trigger('click');	
		}
	});
	$('#filter-panel').on('keypress','.form-control',function(e){
		if(e.which == 13) {
        	$('.searchbtn').trigger('click');
    	}
	});
	$.ajaxSetup ({ cache: false	});
});