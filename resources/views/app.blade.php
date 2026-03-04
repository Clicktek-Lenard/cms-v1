<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
         <meta name="_token" content="{!! csrf_token() !!}"/>
	<title>NWDI</title>
	<link href="{{ asset('/css/vendor.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('/css/app.css?4') }}" rel="stylesheet" type="text/css">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
		.connection{
			z-index: 1001;
			position: fixed;
		}
		.offline { color:red;}
		.online { color:green;}
	</style>
    @yield('style')
</head>
 
<body>
	<nav class="navbar navbar-default navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
            	<a class="navbar-brand" href="{{ url('/') }}">NWDI - {{ session('userClinicCode') }}</a>
            	@if(!Auth::guest())
				<button type="button" class="navbar-toggle menu-toggle" data-toggle="collapse" data-target="#menu-content">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
                @else
               	<div @if(Request::is('login'))class="active"@endif>
                    <a class="navbar-toggle"  href="login">Login</a>
                </div>
                @endif
				
			</div>

			<div class="collapse navbar-collapse">
				<!--<ul class="nav navbar-nav">
					<li><a href="{{ url('/') }}">Home</a></li>
				</ul>-->

				<ul class="nav navbar-nav navbar-right">
					@if (Auth::guest())
						<li @if(Request::is('login'))class="active"@endif><a href="{{ url('/login') }}">Login</a></li>
					@else
						<li><a href="#" style="color:red;"><b>PROD - {{ session('userClinicName') }}</b></a></li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->username }} <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="route('logout')" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a></li>
							</ul>
						</li>
					@endif
				</ul>
			</div>
		</div>
	</nav>
    @if(!Auth::guest())
    <div class="nav-side-menu">
        <div class="menu-list">
		
		<ul id="menu-content" class="menu-content collapse">

		@if( strpos(session('userRole') , '"module":"kiosk"') !== false  )
			<li data-toggle="collapse" data-target="#kiosk" class="@if(Request::is('kiosk/*')) active @endif" aria-expanded="@if(Request::is('kiosk/*')) true @endif">
				<a><i class="fa fa-ticket"></i>Queueing<span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse @if(Request::is('kiosk/*')) in @endif" id="kiosk">
				@if( strpos(session('userRole') , '"tab":"receptionqueue"') !== false && strpos(session('userRole') , '"ldap_role":"[KIOSK-RECEPTION]"') !== false   )
				<a href="{{ url('/kiosk/receptionqueue') }}" class="a-href KIOSK-RECEPTION">
					<li class="@if(Request::is('kiosk/receptionqueue') || Request::is('kiosk/queue*')) active @endif">Reception<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"extractionqueue"') !== false  )
				<a href="{{ url('/kiosk/extractionqueue') }}" class="a-href KIOSK-LABORATORY">
					<li class="@if(Request::is('kiosk/extractionqueue*')) active @endif">Extraction<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"imagingqueue"') !== false  )
				<a href="{{ url('/kiosk/imagingqueue') }}" class="a-href KIOSK-IMAGING">
					<li class="@if(Request::is('kiosk/imagingqueue*')) active @endif">Imaging<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"vitalsignsqueue"') !== false && strpos(session('userRole') , '"ldap_role":"[KIOSK-NURSE]"') !== false  )
				<a href="{{ url('/kiosk/vitalsignsqueue') }}" class="a-href KIOSK-VITALSIGN">
					<li class="@if(Request::is('kiosk/vitalsignsqueue')) active @endif">Vital Signs<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"consultationqueue"') !== false  )
				<a href="{{ url('/kiosk/consultationqueue') }}" class="a-href KIOSK-CONSULTATION">
					<li class="@if(Request::is('kiosk/consultationqueue*')) active @endif">Consultation<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"releasingqueue"') !== false  )
				<a href="{{ url('/kiosk/releasingqueue') }}" class="a-href KIOSK-RELEASING">
					<li class="@if(Request::is('kiosk/releasingqueue')) active @endif">Releasing<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
			</ul>
		@endif
		@if( strpos(session('userRole') , '"module":"doctor"') !== false  )
			<li data-toggle="collapse" data-target="#doctor" class="@if(Request::is('doctor/*')) active @endif" aria-expanded="@if(Request::is('doctor/*')) true @endif">
				<a><i class="fa fa-user-md"></i>Doctor<span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse @if(Request::is('doctor/*')) in @endif" id="doctor">
				@if( strpos(session('userRole') , '"tab":"vitals"') !== false  )
				<a href="{{ url('/doctor/vitals') }}" class="a-href ">
					<li class="@if(Request::is('doctor/vitals*')) active @endif">Vital Signs<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"queue"') !== false  )
				<a href="{{ url('/doctor/queue') }}" class="a-href ">
					<li class="@if(Request::is('doctor/queue*')) active @endif">Queue<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"historyqueue"') !== false  )
				<a href="{{ url('/doctor/historyqueue') }}" class="a-href ">
					<li class="@if(Request::is('doctor/historyqueue*')) active @endif">Completed Queue <span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"evaluation"') !== false  )
				<a href="{{ url('/doctor/evaluation') }}" class="a-href ">
					<li class="@if(Request::is('doctor/evaluation*')) active @endif">Medical Evaluation<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
			</ul>
		@endif
		@if( strpos(session('userRole') , '"module":"cms"') !== false  )
			@if( strpos(session('userRole') , '"tab":"queue"') !== false && (strpos(session('userRole') , '"ldap_role":"[QUEUE]"') !== false || strpos(session('userRole') , '"ldap_role":"[QUEUE-VIEW]"') !== false)  )
			<a href="{{ url('/cms/queue') }}" class="a-href QUEUE">
				<li class="limain toggle QUEUE @if(Request::is('cms/queue*')) active @endif">
					<i class="fa fa-qrcode fa-lg"></i> Today's Queue <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
			@if( strpos(session('userRole') , '"tab":"payment"') !== false  )
			<a href="{{ url('/cms/payment') }}" class="a-href PAYMENT">
				<li class="@if(Request::is('cms/payment*')) active @endif">
					<i class="fa fa-credit-card fa-lg"></i> Payment <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
			@if( strpos(session('userRole') , '"tab":"pastqueue"') !== false  )
			<a href="{{ url('/cms/pastqueue') }}" class="a-href QUEUE">
				<li class="limain toggle QUEUE @if(Request::is('cms/pastqueue/*') || Request::is('cms/pastqueue')) active @endif">
					<i class="fa fa-qrcode fa-lg"></i> Past Queue <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
			@if( strpos(session('userRole') , '"tab":"pastqueueonsite"') !== false  )
			<a href="{{ url('/cms/pastqueueonsite') }}" class="a-href QUEUE">
				<li class="limain toggle QUEUE @if(Request::is('cms/pastqueueonsite/*') || Request::is('cms/pastqueueonsite')) active @endif">
					<i class="fa fa-qrcode fa-lg"></i> Past Queue (On-Site) <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
			<!-- @if( strpos(session('userRole') , '"tab":"specimen-receiving"') !== false  )
			<a href="{{ url('/cms/specimen-receiving') }}" class="a-href SPECIMEN-RECEIVING">
				<li class="@if(Request::is('cms/specimen-receiving')) active @endif">
					<i class="fa fa-tags"></i> Specimen Receiving <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif -->
			@if( strpos(session('userRole') , '"tab":"laboratoryxxxx"') !== false  )
			<a href="{{ url('/cms/lab') }}" class="a-href LABORATORY">
				<li class="@if(Request::is('/cms/lab*')) active @endif">
					<i class="fa fa-list-ol fa-lg"></i> <s>Laboratory</s> <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
			@if( strpos(session('userRole') , '"tab":"radiologyxxx"') !== false  )
			<a href="{{ url('/cms/rad') }}" class="a-href RADIOLOGY">
				<li class="@if(Request::is('/cms/rad*')) active @endif">
					<i class="fa fa-file-video-o fa-lg"></i> <s>Radiology</s> <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
		@endif
		@if( strpos(session('userRole') , '"module":"specimen-receiving"') !== false  )
			<li data-toggle="collapse" data-target="#specimenreceiving" class="@if((Request::is('specimen-receiving/bloodextraction*')||Request::is('specimen-receiving/specimen*')||Request::is('specimen-receiving/imaging*'))) active @endif" aria-expanded="@if(Request::is('specimen-receiving/bloodextraction*') || Request::is('specimen-receiving/specimen*') || Request::is('specimen-receiving/imaging*')) true @endif">
				<a><i class="fa fa-tags"></i>Receiving (On-Site)<span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse @if((Request::is('specimen-receiving/bloodextraction*')||Request::is('specimen-receiving/specimen*')||Request::is('specimen-receiving/imaging*'))) in @endif" id="specimenreceiving">
				@if( strpos(session('userRole') , '"tab":"bloodextraction"') !== false  )
				<a href="{{ url('/specimen-receiving/bloodextraction') }}" class="a-href BLOODEXTRACTION">
					<li class="@if(Request::is('specimen-receiving/bloodextraction') || Request::is('specimen-receiving/blood-extraction*')) active @endif">Blood Extraction<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"specimen"') !== false  )
				<a href="{{ url('/specimen-receiving/specimen') }}" class="a-href SPECIMEN">
					<li class="@if(Request::is('specimen-receiving/specimen*')) active @endif">Specimen<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"imaging"') !== false  )
				<a href="{{ url('/specimen-receiving/imaging') }}" class="a-href SPECIMEN">
					<li class="@if(Request::is('specimen-receiving/imaging*')) active @endif">Imaging<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
			</ul>
		@endif
		@if( strpos(session('userRole') , '"module":"specimen-receiving"') !== false  )
			@if( strpos(session('userRole') , '"tab":"rejection"') !== false  )
			<a href="{{ url('/specimen-receiving/rejection') }}" class="a-href REJECTION">
				<li class="limain toggle REJECTION @if(Request::is('specimen-receiving/rejection*')) active @endif">
					<i class="fa fa-times-circle fa-lg"></i> Rejection <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
		@endif
		@if( strpos(session('userRole') , '"module":"specimen-receiving"') !== false  )
			@if( strpos(session('userRole') , '"tab":"rejected"') !== false  )
			<a href="{{ url('/specimen-receiving/rejected') }}" class="a-href REJECTED">
				<li class="limain toggle REJECTED @if(Request::is('specimen-receiving/rejected*')) active @endif">
					<i class="fa fa-exclamation-triangle fa-lg"></i> Rejected <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
		@endif
		@if( strpos(session('userRole') , '"module":"specimen-receiving"') !== false  )
			@if( strpos(session('userRole') , '"tab":"transport"') !== false  )
			<a href="{{ url('/specimen-receiving/transport') }}" class="a-href TRANSPORT">
				<li class="limain toggle TRANSPORT @if(Request::is('specimen-receiving/transport*')) active @endif">
					<i class="fa fa-truck fa-lg"></i> Transport <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
		@endif
		@if( strpos(session('userRole') , '"tab":"laboratory-receiving"') !== false  )
			<a href="{{ url('/cms/laboratory-receiving') }}" class="a-href LABORATORY-RECEIVING">
				<li class="@if(Request::is('cms/laboratory-receiving*')) active @endif">
					<i class="fa fa-flask"></i> Laboratory Receiving<span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
		@endif
		@if( strpos(session('userRole') , '"module":"processing"') !== false  )
			<li data-toggle="collapse" data-target="#processing" class="@if(Request::is('processing/*')) active @endif" aria-expanded="@if(Request::is('processing/*')) true @endif">
				<a><i class="fa fa-user-md"></i>Medical Processing<span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse @if(Request::is('processing/*')) in @endif" id="processing">
				@if( strpos(session('userRole') , '"tab":"evaluated"') !== false  )
				<a href="{{ url('/processing/evaluated') }}" class="a-href ">
					<li class="@if(Request::is('processing/evaluated*')) active @endif">Evaluated<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
			</ul>
		@endif
		@if( strpos(session('userRole') , '"tab":"bmmodule"') !== false  )
			<a href="{{ url('/cms/bmmodule') }}" class="a-href BM-MODULE">
				<li class="limain toggle BM-MODULE bmmodule @if(Request::is('cms/bmmodule*')) active @endif">
					<i class="fa fa-list fa-lg"></i> Branch Manager Module 
					<span class="badge" style=" background-color: #f5051d; top:-9px; position:relative;">{{ session('queueCount', 0) }}</span>
				</li>
			</a>
			@endif
		@if( strpos(session('userRole') , '"module":"card"') !== false  )
			@if( strpos(session('userRole') , '"tab":"demographics"') !== false  )
			<a href="{{ url('/card/demographics') }}" class="a-href CMS-DEVTEAM">
				<li class="limain toggle CMS-DEVTEAM @if(Request::is('card/demographics*')) active @endif">
					<i class="fa fa-tasks fa-lg"></i> Health + Card Demographics <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif
		@endif
		@if( strpos(session('userRole') , '"module":"enrollment"') !== false  )
			<li data-toggle="collapse" data-target="#enrollment" class="@if(Request::is('enrollment/*')) active @endif" aria-expanded="@if(Request::is('enrollment/*')) true @endif">
				<a><i class="fa fa-book fa-lg"></i> Card Management<span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse @if(Request::is('enrollment/*')) in @endif" id="enrollment">
			@if( strpos(session('userRole') , '"tab":"cardnumber"') !== false  )
			<a href="{{ url('/enrollment/cardnumber') }}" class="a-href CARDNUMBER">
				<li class="limain toggle CARDNUMBER @if(Request::is('enrollment/cardnumber*')) active @endif">
					<i class="fa fa-barcode"></i> Generate Card Number <span class="badge" style="top:-9px; position:relative;"></span>
				</li>
			</a>
			@endif 
				@if( strpos(session('userRole') , '"tab":"cardverified"') !== false  )
				<a href="{{ url('/enrollment/cardverified') }}" class="a-href CARD-VERIFICATION">
					<li class="limain toggle CARD-VERIFICATION @if(Request::is('enrollment/cardverified*')) active @endif">
						<i class="fa fa-check-square"></i> Verification <span class="badge" style="top:-9px; position:relative;"></span>
					</li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"cardregistration"') !== false  )
				<a href="{{ url('/enrollment/cardregistration') }}" class="a-href">
					<li class="@if(Request::is('enrollment/cardregistration*')) active @endif">Registration<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"cardtransfer"') !== false  )
				<a href="{{ url('/enrollment/cardtransfer') }}" class="a-href">
					<li class="@if(Request::is('enrollment/cardtransfer*')) active @endif">Transfer<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"cardreceiving"') !== false  )
				<a href="{{ url('/enrollment/cardreceiving') }}" class="a-href">
					<li class="@if(Request::is('enrollment/cardreceiving*')) active @endif">Receiving/Received<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>	
				@endif
			</ul>
		@endif
			@if( strpos(session('userRole') , '"module":"erosui"') !== false  )
			<li data-toggle="collapse" data-target="#erosphysician" class="@if(Request::is('cmsphysician/*')) active @endif" aria-expanded="@if(Request::is('cmsphysician/*')) true @endif">
				<a><i class="fa fa-user"></i> Physician Accreditation<span class="arrow" style="padding-left: 0px !important; padding-right: 0px !important;"></span></a>
			</li>
			<ul class="sub-menu collapse @if(Request::is('cmsphysician/*')) in @endif" id="erosphysician">
				@if( strpos(session('userRole') , '"tab":"physician"') !== false  )
				<a href="{{ url('/cmsphysician/physician') }}" class="a-href">
					<li class="@if(Request::is('cmsphysician/physician*')) active @endif"> For Approval <span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"doctorsmodule"') !== false  )
				<a href="{{ url('/cmsphysician/doctorsmodule') }}" class="a-href">
					<li class="@if(Request::is('cmsphysician/doctorsmodule*')) active @endif">Accredited Physicians<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
			</ul>
			<li data-toggle="collapse" data-target="#eros" class="@if(Request::is('cms/workstation')) active @elseif (Request::is('cms/services')) active @elseif(Request::is('cms/displayqueue')) active @endif" aria-expanded="@if(Request::is('erosui/*')) true @endif">
				<a><i class="fa fa-user"></i> Admin Settings <span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse @if(Request::is('cms/workstation')) in @elseif (Request::is('cms/services')) in @elseif(Request::is('cms/displayqueue')) in @endif" id="eros">
				@if( strpos(session('userRole') , '"tab":"workstation"') !== false  )
				<a href="{{ url('/cms/workstation') }}" class="a-href">
					<li class="@if(Request::is('cms/workstation*')) active @endif">Workstation<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"displayqueue"') !== false  )
				<a href="{{ url('/cms/displayqueue') }}" class="a-href">
					<li class="@if(Request::is('cms/displayqueue*')) active @endif">Queue Display<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"services"') !== false  )
				<a href="{{ url('/cms/services') }}" class="a-href">
					<li class="@if(Request::is('cms/services*')) active @endif">Services<span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
			</ul>
			@endif
			
			<li data-toggle="collapse" data-target="#reports" class="@if(Request::is('reports/*')) active @endif" aria-expanded="@if(Request::is('reports/*')) true @endif">
				<a><i class="fa fa-tag fa-lg"></i> Clinic Reports <span class="arrow"></span></a>
			</li>
			<ul class="sub-menu collapse @if(Request::is('reports/*')) in @endif" id="reports">
				
				@if( strpos(session('userRole') , '"tab":"dailysales"') !== false  )
				<a href="{{ url('/reports/dailysales') }}" class="a-href">
					<li class="@if(Request::is('reports/dailysales*')) active @endif"> Daily Sales <span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"labreports"') !== false )
				<a href="{{ url('/reports/labreports') }}" class="a-href">
					<li class="@if(Request::is('reports/labreports*')) active @endif"> Lab Reports <span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
				@if( strpos(session('userRole') , '"tab":"turnaroundtime"') !== false  )
				<a href="{{ url('/reports/turnaroundtime') }}" class="a-href">
					<li class="@if(Request::is('reports/turnaroundtime*')) active @endif"> Turnaround Time <span class="badge" style="top:-9px; position:relative;"></span></li>
				</a>
				@endif
			</ul>
		
			<li class="limain navbar-toggle toggle user" data-toggle-what=".page-content-wrapper" data-toggle-type="user-ChangePassword.html">
			  <a href="#asset">
			    <i class="fa fa-shield fa-lg"></i> Change Password <span class="badge" style="top:-9px; position:relative;"></span>  </a>
			</li>
			<a href="route('logout')" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="a-href">
			    <li  class="limain navbar-toggle">
				<i class="fa fa-sign-out fa-lg"></i> Logout 
			    </li>
			</a>
		</ul>
		
		
		@if( strpos(session('userRole') , '"module":"settings"') !== false  )
		<li data-toggle="collapse" data-target="#settings" class="@if(Request::is(session('userBUCode').'/cms/settings/*')) active @endif" aria-expanded="@if(Request::is(session('userBUCode').'/cms/settings/*')) true @endif">
			<a><i class="fa fa-cog fa-lg"></i><span> Settings <span class="arrow"></span></a>
		</li>
		<ul class="sub-menu collapse @if(Request::is(session('userBUCode').'/cms/settings/*')) in @endif" id="settings">
			<a href="{{ url('/'.session('userBUCode').'/cms/settings/users') }}" class="a-href">
				<li class="@if(Request::is(session('userBUCode').'/cms/settings/users*')) active @endif">Users<span class="badge" style="top:-9px; position:relative;"></span></li>
			</a>
		</ul>
		@endif
                
            </ul>
        </div>
    </div>
    @endif
	<div class="app-content">
		<div class="col-xs-offset-8 col-sm-offset-8 col-md-offset-8 col-lg-offset-8 col-xs-4 col-sm-4 col-md-4 col-lg-4 notfound connection text-right"></div>
		<div id="initdiv" class="hide"></div>
		@yield('content')
	</div>
	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    <!-- Scripts -->
    <script src="{{ asset('/js/vendor.js?1') }}"></script>
    <script src="{{ asset('/js/app.js?16') }}"></script>  
    <script>
	$(document).ready(function(e)
	{
		  // Update the date and time every second
		setInterval(parent.netStat , 2000);
		

	});
    </script>
    @yield('script')
</body>
</html>
