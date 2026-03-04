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
<form id="physicianTableModal" class="form-horizontal"  role="form" autocomplete="off">
    <input type="hidden" name="_selected" value="{{ $physicianEditDatas[0]->Id }}">
    <input type="hidden" name="_status" value="{{ $physicianEditDatas[0]->Status }}">
    <input type="hidden" name="branchCode" value="{{ $physicianEditDatas[0]->BranchCode }}">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">   

    <div class="modal-cms-header">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 4px;">
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Last Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" style="text-transform: uppercase;" name="lastname" placeholder="Last Name" required="required" value="{{ $physicianEditDatas[0]->LastName }}">
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">First Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" style="text-transform: uppercase;" name="firstname" placeholder="First Name" required="required" value="{{ $physicianEditDatas[0]->FirstName }}">
                </div>
            </div>			
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Middle Name<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" style="text-transform: uppercase;" name="middlename" placeholder="Middle Name" value="{{ $physicianEditDatas[0]->MiddleName }}" >
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Display Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="fullname" placeholder="Display Name" required="required" value="{{ $physicianEditDatas[0]->FullName }}">
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">PRC No.<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" maxlength="7" pattern="\d*" id= "prcId" name="prcno" placeholder="PRC No." required="required" value="{{ $physicianEditDatas[0]->PRCNo }}">
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Specialization<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <select class="form-control" name="description" placeholder="Description" required="required">
                        <option value="">CHOOSE SPECIALIZATION</option>
                        <option value="ALLERGOLOGY" {{ $physicianEditDatas[0]->Description == 'ALLERGOLOGY' ? 'selected' : '' }}>ALLERGOLOGY</option>
                        <option value="ANESTHESIOLOGY" {{ $physicianEditDatas[0]->Description == 'ANESTHESIOLOGY' ? 'selected' : '' }}>ANESTHESIOLOGY</option>
                        <option value="CARDIOLOGY" {{ $physicianEditDatas[0]->Description == 'CARDIOLOGY' ? 'selected' : '' }}>CARDIOLOGY</option>
                        <option value="DENTAL MEDICINE" {{ $physicianEditDatas[0]->Description == 'DENTAL MEDICINE' ? 'selected' : '' }}>DENTAL MEDICINE</option>
                        <option value="DERMATOLOGY" {{ $physicianEditDatas[0]->Description == 'DERMATOLOGY' ? 'selected' : '' }}>DERMATOLOGY</option>
                        <option value="DIABETOLOGY" {{ $physicianEditDatas[0]->Description == 'DIABETOLOGY' ? 'selected' : '' }}>DIABETOLOGY</option>
                        <option value="ENDOCRINOLOGY" {{ $physicianEditDatas[0]->Description == 'ENDOCRINOLOGY' ? 'selected' : '' }}>ENDOCRINOLOGY</option>
                        <option value="ENT" {{ $physicianEditDatas[0]->Description == 'ENT' ? 'selected' : '' }}>ENT</option>
                        <option value="FAMILY MEDICINE" {{ $physicianEditDatas[0]->Description == 'FAMILY MEDICINE' ? 'selected' : '' }}>FAMILY MEDICINE</option>
                        <option value="GASTROENTEROLOGY" {{ $physicianEditDatas[0]->Description == 'GASTROENTEROLOGY' ? 'selected' : '' }}>GASTROENTEROLOGY</option>
                        <option value="GENERAL PRACTICE" {{ $physicianEditDatas[0]->Description == 'GENERAL PRACTICE' ? 'selected' : '' }}>GENERAL PRACTICE</option>
                        <option value="GENERAL SURGERY" {{ $physicianEditDatas[0]->Description == 'GENERAL SURGERY' ? 'selected' : '' }}>GENERAL SURGERY</option>
                        <option value="INTERNAL MEDICINE" {{ $physicianEditDatas[0]->Description == 'INTERNAL MEDICINE' ? 'selected' : '' }}>INTERNAL MEDICINE</option>
                        <option value="NEPHROLOGY" {{ $physicianEditDatas[0]->Description == 'NEPHROLOGY' ? 'selected' : '' }}>NEPHROLOGY</option>
                        <option value="NEUROLOGY" {{ $physicianEditDatas[0]->Description == 'NEUROLOGY' ? 'selected' : '' }}>NEUROLOGY</option>
                        <option value="NUCLEAR MEDICINE" {{ $physicianEditDatas[0]->Description == 'NUCLEAR MEDICINE' ? 'selected' : '' }}>NUCLEAR MEDICINE</option>
                        <option value="OB-GYNECOLOGY" {{ $physicianEditDatas[0]->Description == 'OB-GYNECOLOGY' ? 'selected' : '' }}>OB-GYNECOLOGY</option>
                        <option value="OB-SONOLOGY" {{ $physicianEditDatas[0]->Description == 'OB-SONOLOGY' ? 'selected' : '' }}>OB-SONOLOGY</option>
                        <option value="OCCUPATIONAL MEDICINE" {{ $physicianEditDatas[0]->Description == 'OCCUPATIONAL MEDICINE' ? 'selected' : '' }}>OCCUPATIONAL MEDICINE</option>
                        <option value="ONCO-SURGERY" {{ $physicianEditDatas[0]->Description == 'ONCO-SURGERY' ? 'selected' : '' }}>ONCO-SURGERY</option>
                        <option value="OPHTHALMOLOGY" {{ $physicianEditDatas[0]->Description == 'OPHTHALMOLOGY' ? 'selected' : '' }}>OPHTHALMOLOGY</option>
                        <option value="OPTOMETRY" {{ $physicianEditDatas[0]->Description == 'OPTOMETRY' ? 'selected' : '' }}>OPTOMETRY</option>
                        <option value="PATHOLOGY" {{ $physicianEditDatas[0]->Description == 'PATHOLOGY' ? 'selected' : '' }}>PATHOLOGY</option>
                        <option value="ORTHOPEDICS" {{ $physicianEditDatas[0]->Description == 'ORTHOPEDICS' ? 'selected' : '' }}>ORTHOPEDICS</option>
                        <option value="PEDIATRICS" {{ $physicianEditDatas[0]->Description == 'PEDIATRICS' ? 'selected' : '' }}>PEDIATRICS</option>
                        <option value="PLASTIC SURGERY" {{ $physicianEditDatas[0]->Description == 'PLASTIC SURGERY' ? 'selected' : '' }}>PLASTIC SURGERY</option>
                        <option value="PSYCHIATRY" {{ $physicianEditDatas[0]->Description == 'PSYCHIATRY' ? 'selected' : '' }}>PSYCHIATRY</option>
                        <option value="PULMONOLOGY" {{ $physicianEditDatas[0]->Description == 'PULMONOLOGY' ? 'selected' : '' }}>PULMONOLOGY</option>
                        <option value="RADIO-SONOLOGY" {{ $physicianEditDatas[0]->Description == 'RADIO-SONOLOGY' ? 'selected' : '' }}>RADIO-SONOLOGY</option>
                        <option value="RADIOLOGY" {{ $physicianEditDatas[0]->Description == 'RADIOLOGY' ? 'selected' : '' }}>RADIOLOGY</option>
                        <option value="REHABILITATION MEDICINE" {{ $physicianEditDatas[0]->Description == 'REHABILITATION MEDICINE' ? 'selected' : '' }}>REHABILITATION MEDICINE</option>
                        <option value="RHEUMATOLOGY" {{ $physicianEditDatas[0]->Description == 'RHEUMATOLOGY' ? 'selected' : '' }}>RHEUMATOLOGY</option>
                        <option value="THORACO-VASCULAR SURGERY" {{ $physicianEditDatas[0]->Description == 'THORACO-VASCULAR SURGERY' ? 'selected' : '' }}>THORACO-VASCULAR SURGERY</option>
                        <option value="UROLOGY" {{ $physicianEditDatas[0]->Description == 'UROLOGY' ? 'selected' : '' }}>UROLOGY</option>
                    </select>
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Status<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <select class="form-control" name="status" placeholder="Status" required="required" disabled >
                        <option value="Active" @if($physicianEditDatas[0]->Status == 'Active') selected @endif>Active</option>
                        <option value="Inactive" @if($physicianEditDatas[0]->Status == 'Inactive') selected @endif>Inactive</option>
                        <option value="RP - For Approval" @if($physicianEditDatas[0]->Status == 'RP - For Approval') selected @endif>RP - For Approval</option>
                        <option value="RP - For Revision" @if($physicianEditDatas[0]->Status == 'RP - For Revision') selected @endif>RP - For Revision</option>
                        <option value="RP - Leads" @if($physicianEditDatas[0]->Status == 'RP - Leads') selected @endif>RP - Leads</option>
                    </select> 
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Reason For Revision<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="reason" placeholder="Reason" value="{{ $physicianEditDatas[0]->DeclineReason }}" disabled>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 image_div" style="padding-left: 4px;">
            <div class="d-flex justify-content-center align-items-center flex-column">
                  
                <input type="hidden" name="myimage" class="image-tag" value="{{ $physicianEditDatas[0]->Prescription_Link }}">

                @if(!empty($physicianEditDatas[0]->Prescription_Link) && file_exists(public_path('uploads/PhysicianPrescription/' . $physicianEditDatas[0]->Prescription_Link)))
                    <!-- Display the actual image if Prescription_Link exists and the file is present -->
                    <img src="{{ asset('uploads/PhysicianPrescription/' . $physicianEditDatas[0]->Prescription_Link) }}" alt="Captured Rx" class="img-fluid mt-2">
                @else
                    <img src="{{ asset('images/RP_Prescription.png') }}" style="width: 430px; height: 330px; object-fit: contain;">
                @endif
                 <button type="button" class="btn btn-primary scanbtn pull-right" style="margin-top: 10px;">Open Camera</button>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="table-item col-md-12"></div>
</div>
<script src="{{ asset('/js/webcam.js') }}"></script>
<script>
$(document).ready(function(e)
{
	var physicianWebcam = new BootstrapDialog({
		message: function(dialog) {
			var $message = $('<div class="physicianWebcam-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
			var pageToLoad = dialog.getData('pageToLoad');
			$message.load(pageToLoad);
			return $message;
		},
		size: BootstrapDialog.SIZE_WIDE,
		type: BootstrapDialog.TYPE_INFO,
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
			cssClass: 'btn-primary actionbtn',
			label: 'Submit',
			action: function(modalRef) {
                var newImageUrl = $('input[name="image"]').val(); 
                $('input[name="myimage"]').val(newImageUrl); 
                $('div.image_div img').attr('src', newImageUrl);
                modalRef.close();
            }
		}]
	});

	$('.scanbtn').on('click', function(){
		physicianWebcam.setTitle("Camera");
		physicianWebcam.realize();
		physicianWebcam.open();
		//e.preventDefault();	
	});
    
    //added 121924 for limit prc input in 7 and starts input in zero
    document.getElementById('prcId').addEventListener('blur', function (){ 
        var prcInput = this.value;

        if (prcInput){
            this.value = prcInput.padStart(7, '0');
        }
    });
	
});
</script>