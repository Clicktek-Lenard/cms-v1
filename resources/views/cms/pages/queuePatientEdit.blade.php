<style>
@media (min-width: 767px){
	.m-top100 {
		margin-top: -100px !important;
	}
	.m-top170 {
		margin-top: -170px !important;
	}
	.m-top90 {
		margin-top: -90px !important;
	}
	.m-top20 {
		margin-top: -20px !important;
	}
}
.webcam { cursor:pointer; }

</style>

<form id="patientAddModalForm" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}"  autocomplete="off">
<input type="hidden" name="_selected" value="">
<input type="hidden" name="_method" value="PUT">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="modal-cms-header">
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Full Name<font style="color:red;">*</font></label>
		</div>
		<div class="col-sm-5 col-md-5">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="FullName" value="{{ $patient->FullName }}" placeholder="System Generated - (Last Name, First Name Middle Name)" readonly="readonly" >
		</div>
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Code<font style="color:red;">*</font></label>
		</div>
		<div class="col-sm-3 col-md-3">
		<input type="text" class="form-control" name="Code" value="{{ $patient->Code }}" placeholder="System Generated" readonly="readonly" >
		</div>
	</div>
	<!--LEFT-->
	<div class="row form-group row-md-flex-center">
		<div class="m-top100 col-sm-2 col-md-2 pad-0-md text-right-md " >
			<label class="bold ">Last Name<font style="color:red;">*</font></label>
		</div>
		<div class="m-top100 col-sm-7 col-md-7">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="LastName" value="{{ $patient->LastName }}" placeholder="Last Name"  required="required"  @if( strpos(session('userRole') , '"ldap_role":"[RECEPTION-OIC]"') !== false )  @else readonly="readonly" @endif>
		</div>
		<!--RIGHT-->
		<div class="float-md-right col-xs-3 col-sm-3 col-md-3 col-lg-3">
			<div class="text-center" >
				<img class="float-right webcam" src="{{ '/uploads/PatientPicture/'.$patient->PictureLink }}" width="150" height="150" />
				<input type="hidden" name="myimage" class="image-tag">
			</div>
		</div>
	</div>		
	<div class="row form-group row-md-flex-center">
		<div class="m-top170 col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">First Name<font style="color:red;">*</font></label>
		</div>
		<div class="m-top170 col-sm-7 col-md-7">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="FirstName" value="{{ $patient->FirstName }}" placeholder="First Name"  required="required" @if( strpos(session('userRole') , '"ldap_role":"[RECEPTION-OIC]"') !== false )  @else readonly="readonly" @endif> 
		</div>
	</div>
	<div class="row form-group row-md-flex-center">
		<div class="m-top90 col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Middle Name</label>
		</div>
		<div class="m-top90 col-sm-7 col-md-7">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="MiddleName" value="{{ $patient->MiddleName }}" placeholder="Middle Name"  @if( strpos(session('userRole') , '"ldap_role":"[RECEPTION-OIC]"') !== false )  @else readonly="readonly" @endif>
		</div>
	</div>		
	<div class="m-top20 row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md ">
			<label class="bold ">Date of Birth<font style="color:red;">*</font></label>
		</div>
		<div class="col-sm-3 col-md-3">
			@if( strpos(session('userRole') , '"ldap_role":"[RECEPTION-OIC]"') !== false )
			<input type="text" class="form-control datepicker datepickerPatient datepickerDOB" name="DOB" value="{{ $patient->DOB && $patient->DOB !== '0000-00-00' && preg_match('/\d/', $patient->DOB) ? date('m/d/Y', strtotime($patient->DOB)) : '' }}" placeholder="Date of Birth" required="required">   
			@else 
			<input type="text" class="form-control" name="DOB" value="{{ $patient->DOB && $patient->DOB !== '0000-00-00' && preg_match('/\d/', $patient->DOB) ? date('m/d/Y', strtotime($patient->DOB)) : '' }}" placeholder="Date of Birth" required="required" readonly> 
			@endif
			<span style="color: blue; text-align: center; font-size: x-small;"><i>mm/dd/yyyy</i></span>
		</div>
		<div class="col-sm-1 col-md-1 pad-0-md text-right-md">
			<label class="bold">Gender<font style="color:red;">*</font></label>
		</div>
		<div class="col-sm-3 col-md-3">
			@if( strpos(session('userRole'), '"ldap_role":"[RECEPTION-OIC]"') !== false )
			<select id="Gender" class="form-control" name="Gender" placeholder="Gender" required>
				<option value=""></option>
				<option value="Male" @if($patient->Gender == 'M') selected @else '' @endif>Male</option>
				<option value="Female" @if($patient->Gender == 'F') selected @else '' @endif>Female</option>
			</select>
		@else
			<input type="text" class="form-control" name="Gender" value="{{ $patient->Gender === 'F' ? 'Female' : ($patient->Gender === 'M' ? 'Male' : '') }}" readonly required>
		@endif
		</div>
		<div class="col-sm-1 col-md-1 text-right-md">
			<label class="bold">Age</label>
		</div>
		<div class="col-sm-2 col-md-2">
			<input type="text" class="form-control" name="Age" placeholder="Age" readonly="readonly" required="required">
		</div>
	</div>	
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Prefix Name</label>
		</div>
		<div class="col-sm-4 col-md-4">
			<select name="PrefixName" class="form-control" placeholder="Prefix Name" data-placeholder="Prefix Name" >
				<option></option>
			</select>
		</div>
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Suffix Name</label>
		</div>
		<div class="col-sm-4 col-md-4">
			<select name="SuffixName" class="form-control" placeholder="Suffix Name" data-placeholder="Suffix Name" >
				<option></option>
			</select>
		</div>
	</div>

	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Senior No.</label>
		</div>
		<div class="col-sm-3 col-md-3">
			<input type="text" class="form-control" name="SeniorId" value="{{ $patient->SeniorId }}" placeholder="Senior No" >
		</div>
		<div class="col-sm-1 col-md-1 pad-0-md text-right-md  ">
			<label class="bold ">PWD</label>
		</div>
		<div class="col-sm-3 col-md-3">
			<input type="text" class="form-control" name="PWD" value="{{ $patient->PWD }}" placeholder="PWD No." id ="oscaPwdInput">
		</div>
		<div class="col-sm-1 col-md-1 pad-0-md text-right-md  ">
			<label class="bold ">PWD Date Expire</label>
		</div>
		<div class="col-sm-2 col-md-2">
			<input type="" class="form-control datepickertwo" name="ExpiryDatePWD" id="oscaPwdDateExpire"value="{{ $patient->ExpiryDatePWD ? date('m/d/Y', strtotime($patient->ExpiryDatePWD)) : '' }}"placeholder="Date Expire" {{ $patient->ExpiryDatePWD ? '' : 'disabled' }}>
		</div>
	</div>
	<div class="row form-group row-md-flex-center">
	<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Passport No.</label>
		</div>
		<div class="col-sm-7 col-md-7">
			<input type="text" class="form-control" name="PassPortNo" value="{{ $patient->PassPortNo }}" placeholder="Passport No." >
		</div>
	</div>
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">Country</label>
        </div>
        <div class="col-sm-7 col-md-7">
            <select name="Address4" class="form-control" placeholder="Country" data-placeholder="Country" >
                <option></option>
            </select>
        </div>
    </div>
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">Province</label>
        </div>
        <div class="col-sm-10 col-md-10">
            <select name="province" class="form-control" placeholder="Province" data-placeholder="Province" >
                <option></option>
		@foreach($province as $p)
			 <option value="{{ $p->province_id }}">{{ $p->province_name }}</option>
		@endforeach
            </select>
        </div>
    </div>
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">City</label>
        </div>
        <div class="col-sm-10 col-md-10">
            <select name="city" class="form-control" placeholder="City" data-placeholder="City" >
                <option></option>
	         @foreach($city as $c)
			 <option value="{{ $c->city_id }}">{{ $c->city_name }}</option>
		@endforeach	
            </select>
        </div>
    </div>
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">Barangay / Municipality</label>
        </div>
        <div class="col-sm-10 col-md-10">
            <select name="barangay" class="form-control" placeholder="Barangay / Municipality" data-placeholder="Barangay / Municipality" >
                <option></option>
		 @foreach($zip as $z)
			 <option value="{{ $z->zip_id }}">{{ $z->zip_name }}</option>
		@endforeach
            </select>
        </div>
    </div>
   <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">No. Street</label>
        </div>
        <div class="col-sm-10 col-md-10">
        	<input type="hidden" class="form-control" name="FullAddress" value="" placeholder="Full Address" >
            <input type="text" class="form-control" name="Address1" value="{{ $patient->Address }}" placeholder="No. Street" >
        </div>
    </div>
	
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">Email</label>
        </div>
        <div class="col-sm-4 col-md-4">
            <input type="text" class="form-control" name="Email" value="{{ $patient->Email }}" placeholder="Email" >
        </div>
        <div class="col-sm-1 col-md-1 pad-0-md text-right-md  ">
            <label class="bold ">Phone No.</label>
        </div>
        <div class="col-sm-2 col-md-2">
            <input type="text" class="form-control" name="Phone1" value="{{ $patient->ContactNo }}" placeholder="Primary Phone" >
        </div>
        <div class="col-sm-1 col-md-1 pad-0-md text-right-md  ">
            <label class="bold ">Mobile No.</label>
        </div>
        <div class="col-sm-2 col-md-2">
            <input type="text" class="form-control" name="Phone2" value="{{ $patient->Moblie }}" placeholder="Phone" >
        </div>
    </div>
</div>
</form>
<script src="{{ asset('/js/webcam.js') }}"></script>
<script>
String.prototype.capitalizeFirstLetter = function() {
    return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
}
String.prototype.capitalizeAllLetter = function() {
	return this.charAt(0).toUpperCase() + this.slice(1).toUpperCase();
}
var calculateAge = function(birthday) {
   // var now = new Date();
    //var past = new Date(birthday);
    //var nowYear = now.getFullYear();
   // var pastYear = past.getFullYear();
    //var age = nowYear - pastYear;

    dob = new Date(birthday);
   var today = new Date();
   var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));


    return age;
};
concatStr = function(){
	var concatstr = '';
	concatstr = concatstr.concat(" "+$('input[name="LastName"]').val());
	concatstr = concatstr.concat(", "+$('input[name="FirstName"]').val());
	concatstr = concatstr.concat(" "+$('select[name="SuffixName"]').val());
	concatstr = concatstr.concat(" "+$('input[name="MiddleName"]').val());
	$('input[name="FullName"]').val($.trim(concatstr.replace(/\s\s+/g, ' ')));
	var concatstr = '';
	concatstr = concatstr.concat($('input[name="LastName"]').val().charAt(0));
	concatstr = concatstr.concat($('input[name="FirstName"]').val().charAt(0));
	concatstr = concatstr.concat($('input[name="MiddleName"]').val().charAt(0));
	//$('input[name="Code"]').val($.trim(concatstr.replace(/\s\s+/g, ' ')));
};
concatAdd = function(){
	var concatstr = '';
	concatstr = concatstr.concat(" "+$('input[name="Address1"]').val());
	concatstr = concatstr.concat(" "+$('input[name="Address2"]').val());
	concatstr = concatstr.concat(" "+$('input[name="Address3"]').val());
	concatstr = concatstr.concat(", "+$('select[name="Address4"]').val());
	$('input[name="FullAddress"]').val($.trim(concatstr.replace(/\s\s+/g, ' ')));
};

var webCamModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="webcam-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/webcam' }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-success actionbtn',
		label: 'Submit',
		action: function (modalRef){
			$('.webcam').attr('src', $('input[name="image"]').val());
			$('input[name="myimage"]').val($('input[name="image"]').val());
			modalRef.close();
		}
	}]
});
function isValidDate(dateString) {
    // Try creating a Date object with the provided date string
    let dateObject = new Date(dateString);

    // Check if the dateObject is a valid date
    return !isNaN(dateObject.getTime());
}
function isLeapYear(year) {
    return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
}
function validateDate(input) {
    var selectedDate = input.datepicker('getDate');
    var currentDate = new Date();

    if (selectedDate < currentDate) {
        alert('OSCA/PWD Id is Already expired! Please Check validity and try again!.');
        setTimeout(function () {
            input.val('');
        }, 0);
    }
}
$(document).ready(function(e) {
	$('select[name="Gender"]').selectize();
	$('.datepickerPatient').on('focusout', function() {
    let input = $(this);
    let value = input.val().replace(/\D/g, '').substring(0, 8);

    if (value && value.length < 8) {
        alert('Please complete the Date!');
        setTimeout(function () {
            input.val('');
			input.focus();
        }, 0);
        return;
    }
	});
	$('.datepickerPatient ').on('input', function () {
    let input = $(this);
    let value = input.val().replace(/\D/g, '').substring(0, 8);

    if (value.length === 8) {
        let month = parseInt(value.substring(0, 2));
        let day = parseInt(value.substring(2, 4));
        let year = parseInt(value.substring(4));

        month = Math.min(Math.max(month, 1),31);
        day = Math.min(Math.max(day, 1), 31);
		if (month === 2 && day > 29) {
            alert('February can have up to 29 days only.');
            input.val('');
            return;
        }
        value = `${month.toString().padStart(2, '0')}/${day.toString().padStart(2, '0')}/${year || ''}`;
	
        if (!isValidDate(value)) {
            alert('Invalid Birth date format: ' + value + '. it should be Month/Day/Year');
            setTimeout(function () {
                input.val('');
            }, 0);
			return;
        }

		if (month === 2 && day === 29 && !isLeapYear(year)) {
            alert('Invalid Birth date format: February 29 is only valid in a leap year!.');
			setTimeout(function () {
                input.val('');
            }, 0);
			return;
        }
		var currentDate = new Date();
		if (year > currentDate.getFullYear() || (year === currentDate.getFullYear() && month > currentDate.getMonth() + 1) || (year === currentDate.getFullYear() && month === currentDate.getMonth() + 1 && day > currentDate.getDate())) {
			alert('Invalid Birth date format: '+ value +' Please enter a date on or before today!.');
			setTimeout(function () {
				input.val('');
			}, 0);
			return;
		}
    } else if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d{0,2})/, '$1/$2');
    }

    input.val(value);
 	});

	 $(function() {
        // Check if the input field is readonly
        var isReadonly = $('.datepickerPatient').is('[readonly]');
        
        // Initialize datepicker accordingly
        if (!isReadonly) {
            $('.datepickerDOB').datepicker({
                maxDate: '+0',
                firstDay: 1,
                dateFormat: 'mm/dd/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: 'c-100:c+10'
            }).on('change', function() {
                $('input[name="Age"]').val(calculateAge($(this).val()));
            });
        }
    });
	// $('.datepicker').datepicker({ maxDate: '+0', firstDay: 1, dateFormat: 'mm/dd/yy',changeMonth: true, changeYear: true, yearRange: 'c-100:c+10'})
	// .on('change',function(){
	// 	$('input[name="Age"]').val(calculateAge($(this).val()));
	// });
	$('.datepickertwo').on('input', function() {
    let input = $(this);
    let value = input.val().replace(/\D/g, '').substring(0, 8);
	let currentYear = new Date().getFullYear();
		if (value.length === 8) {
			let month = parseInt(value.substring(0, 2));
			let day = parseInt(value.substring(2, 4));
			let year = parseInt(value.substring(4));

			month = Math.min(Math.max(month, 1), 31);
			day = Math.min(Math.max(day, 1), 31); // This allows 31 days for all months
			if (month === 2 && day > 29) {
				alert('February can have up to 29 days only.');
				input.val('');
				return;
			}
			value = `${month.toString().padStart(2, '0')}/${day.toString().padStart(2, '0')}/${year}`;
			
			if (!isValidDate(value)) {
            alert('Invalid Birth date format: ' + value + '. it should be Month/Day/Year');
            setTimeout(function () {
                input.val('');
            }, 0);
			return;
        }

		if (month === 2 && day === 29 && !isLeapYear(year)) {
            alert('Invalid Birth date format: February 29 is only valid in a leap year!.');
			setTimeout(function () {
                input.val('');
            }, 0);
			return;
        }

		} else if (value.length > 2) {
			value = value.replace(/^(\d{2})(\d{0,2})/, '$1/$2');
		}
		input.val(value);
		
	});
	$('#oscaPwdInput').on('input', function() {
    let oscaPwdInput = $(this);
    var value = oscaPwdInput.val();
    let oscaPwdDateExpire = $('#oscaPwdDateExpire');

    if (value === null || value === '') {
        oscaPwdDateExpire.prop('disabled', true);
        oscaPwdDateExpire.prop('required', false);
    } else {
        oscaPwdDateExpire.prop('disabled', false);
        oscaPwdDateExpire.prop('required', true);
    }
 	});

	$('.datepickertwo').datepicker({
    minDate: 0,
    dateFormat: 'mm/dd/yy',
	changeMonth: true, 
	changeYear: true, 
	yearRange: 'c-100:c+100',
    onClose: function(dateText, inst) {
        var selectedDate = $(this).datepicker('getDate');
        var currentDate = new Date();
		var input = $(this);
		var value = input.val();
		if (value && value.length < 8) {
				alert('Please complete the Date!');
				setTimeout(function () {
					input.val('');
					input.focus();
				}, 0);
		}else if (value && selectedDate < currentDate) {
            alert('OSCA/PWD Id is Already expired! Please Check validity and try again!.');
            setTimeout(function () {
            	input.val('');
				input.focus();
            }, 0);
			return;	
        }
    
    }
	});
	parent.getData("{{ asset('/json/Country.json') }}",null,
		function($data){
			$.each($data.country, function(key,val){
			var selected =  false;
			$('select[name="Address4"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="Address4"]').selectize();
		var address4  = $('select[name="Address4"]')[0].selectize;
		var iCountry = ('{{ $patient->Country }}' == '' )?"Philippines":"{{ $patient->Country }}";
		address4.setValue(iCountry);
	});
	parent.getData("{{ asset('/json/Prefix.json') }}",null,
		function($data){
			$.each($data.prefix, function(key,val){
			var selected =  false;
			$('select[name="PrefixName"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="PrefixName"]').selectize();
		var PrefixName  = $('select[name="PrefixName"]')[0].selectize;
		var iPrefixName = ('{{ $patient->Prefix }}' == '' )?"":"{{ $patient->Prefix }}";
		PrefixName.setValue(iPrefixName);
	});
	parent.getData("{{ asset('/json/Suffix.json') }}",null,
		function($data){
			$.each($data.suffix, function(key,val){
			var selected =  false;
			$('select[name="SuffixName"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="SuffixName"]').selectize();
		var SuffixName  = $('select[name="SuffixName"]')[0].selectize;
		var iSuffixName = ('{{ $patient->Suffix }}' == '' )?"":"{{ $patient->Suffix }}";
		SuffixName.setValue(iSuffixName);
	});
	
	$barangay = $('select[name="barangay"]').selectize({
		valueField: 'zip_id',
		labelField: 'zip_name',
		searchField: ['zip_name'],
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.zip_name) + '</span>' +
					'</span>' +
					'<span class="description">' + '</span>' +
				'</div>';
			}
		},
		onChange: function(value) {
			
		}
	});
	
	//barangay.disable();
	
	$city = $('select[name="city"]').selectize({
		valueField: 'city_id',
		labelField: 'city_name',
		searchField: ['city_name'],
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.city_name) + '</span>' +
					'</span>' +
					'<span class="description">' + '</span>' +
				'</div>';
			}
		},
		onChange: function(value) { 
			if (!value.length) return;
			barangay.disable();
			barangay.clearOptions();
			barangay.load(function(callback)
			{
				parent.getData(
					"{{ '/cms/queue/api/zip' }}/0",
					{
						'IdCity':value,
						'_token': $('input[name=_token]').val()
					},
					function(results){
						barangay.enable();
						callback(results);
						if( results.length == 1)
						{
							barangay.setValue(results[0].zip);
						}
					}
				);
			});
		}
	});
	
	//city.disable();
	
	$province = $('select[name="province"]').selectize({
		onChange: function(value) { 
			if (!value.length) return;
			city.disable();
			city.clearOptions();
			city.load(function(callback)
			{
				
				parent.getData(
					"{{ '/cms/queue/api/city' }}/0",
					{
						'IdProvince':value,
						'_token': $('input[name=_token]').val()
					},
					function(results){
						city.enable();
						callback(results);
						if( results.length == 1)
						{
							city.setValue(results[0].city);
						}
					}
				);
			});
		}
	});
	
	
	
	
	
	
	

	$('input[name="Age"]').val(calculateAge("{{ $patient->DOB }}"));
	
	
	$('input[name="LastName"],input[name="FirstName"],input[name="MiddleName"]').keyup(function(){
		$(this).val( $(this).val().capitalizeAllLetter() );
		concatStr();
		return false;
	});
	
	$('select[name="SuffixName"]').on('change', function(){
		concatStr();
		return false;
	});

		
	/*webcam*/
	$('.webcam').on('click', function(){
		webCamModal.setTitle("WebCam");
		webCamModal.realize();
		webCamModal.open();
		//e.preventDefault();
			
	});
	
	barangay  = $barangay[0].selectize;
	city  = $city[0].selectize;
	province  = $province[0].selectize;
	province.setValue({{ $patient->State }});
	city.setValue({{ $patient->City }});
	barangay.setValue({{ $patient->Barangay }});
	
	$(".modal-backdrop").height($(".modal-backdrop").height()+150);
	
});
</script>