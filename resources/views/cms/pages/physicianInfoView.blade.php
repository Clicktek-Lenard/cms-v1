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
<form id="physicianTableModalView" class="form-horizontal"  role="form" autocomplete="off">
    <input type="hidden" name="_selected" value="{{ $physicianViewDatas[0]->Id }}">
    <input type="hidden" id="branchCode" name="branchCode" value="{{ $physicianViewDatas[0]->BranchCode }}">
    <input type="hidden" id="_status" name="_status" value="{{ $physicianViewDatas[0]->Status }}">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">   

    <div class="modal-cms-header">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 4px;">
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Last Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required="required"  value="{{ $physicianViewDatas[0]->LastName }} " disabled>
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">First Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="firstname" placeholder="First Name" required="required" value="{{ $physicianViewDatas[0]->FirstName }}"disabled>
                </div>
            </div>			
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Middle Name<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="middlename" placeholder="Middle Name" value="{{ $physicianViewDatas[0]->MiddleName }}"disabled >
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Display Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="fullname" placeholder="Display Name" required="required" value="{{ $physicianViewDatas[0]->FullName }}"disabled>
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">PRC No.<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="prcno" placeholder="PRC No." required="required" value="{{ $physicianViewDatas[0]->PRCNo }}"disabled>
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Specialization<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <!-- <input type="text" class="form-control" name="description" placeholder="Specialization" value="{{ $physicianViewDatas[0]->Description }}" disabled> -->
                    <select  class="form-control" name="description" placeholder="Description" required="required" disabled>
                        <option value="">CHOOSE SPECIALIZATION</option>
                        <option value="ALLERGOLOGY" {{ $physicianViewDatas[0]->Description == 'ALLERGOLOGY' ? 'selected' : '' }}>ALLERGOLOGY</option>
                        <option value="ANESTHESIOLOGY" {{ $physicianViewDatas[0]->Description == 'ANESTHESIOLOGY' ? 'selected' : '' }}>ANESTHESIOLOGY</option>
                        <option value="CARDIOLOGY" {{ $physicianViewDatas[0]->Description == 'CARDIOLOGY' ? 'selected' : '' }}>CARDIOLOGY</option>
                        <option value="DENTAL MEDICINE" {{ $physicianViewDatas[0]->Description == 'DENTAL MEDICINE' ? 'selected' : '' }}>DENTAL MEDICINE</option>
                        <option value="DERMATOLOGY" {{ $physicianViewDatas[0]->Description == 'DERMATOLOGY' ? 'selected' : '' }}>DERMATOLOGY</option>
                        <option value="DIABETOLOGY" {{ $physicianViewDatas[0]->Description == 'DIABETOLOGY' ? 'selected' : '' }}>DIABETOLOGY</option>
                        <option value="ENDOCRINOLOGY" {{ $physicianViewDatas[0]->Description == 'ENDOCRINOLOGY' ? 'selected' : '' }}>ENDOCRINOLOGY</option>
                        <option value="ENT" {{ $physicianViewDatas[0]->Description == 'ENT' ? 'selected' : '' }}>ENT</option>
                        <option value="FAMILY MEDICINE" {{ $physicianViewDatas[0]->Description == 'FAMILY MEDICINE' ? 'selected' : '' }}>FAMILY MEDICINE</option>
                        <option value="GASTROENTEROLOGY" {{ $physicianViewDatas[0]->Description == 'GASTROENTEROLOGY' ? 'selected' : '' }}>GASTROENTEROLOGY</option>
                        <option value="GENERAL PRACTICE" {{ $physicianViewDatas[0]->Description == 'GENERAL PRACTICE' ? 'selected' : '' }}>GENERAL PRACTICE</option>
                        <option value="GENERAL SURGERY" {{ $physicianViewDatas[0]->Description == 'GENERAL SURGERY' ? 'selected' : '' }}>GENERAL SURGERY</option>
                        <option value="INTERNAL MEDICINE" {{ $physicianViewDatas[0]->Description == 'INTERNAL MEDICINE' ? 'selected' : '' }}>INTERNAL MEDICINE</option>
                        <option value="NEPHROLOGY" {{ $physicianViewDatas[0]->Description == 'NEPHROLOGY' ? 'selected' : '' }}>NEPHROLOGY</option>
                        <option value="NUCLEAR MEDICINE" {{ $physicianViewDatas[0]->Description == 'NUCLEAR MEDICINE' ? 'selected' : '' }}>NUCLEAR MEDICINE</option>
                        <option value="OB-GYNECOLOGY" {{ $physicianViewDatas[0]->Description == 'OB-GYNECOLOGY' ? 'selected' : '' }}>OB-GYNECOLOGY</option>
                        <option value="OB-SONOLOGY" {{ $physicianViewDatas[0]->Description == 'OB-SONOLOGY' ? 'selected' : '' }}>OB-SONOLOGY</option>
                        <option value="OCCUPATIONAL MEDICINE" {{ $physicianViewDatas[0]->Description == 'OCCUPATIONAL MEDICINE' ? 'selected' : '' }}>OCCUPATIONAL MEDICINE</option>
                        <option value="ONCO-SURGERY" {{ $physicianViewDatas[0]->Description == 'ONCO-SURGERY' ? 'selected' : '' }}>ONCO-SURGERY</option>
                        <option value="OPHTHALMOLOGY" {{ $physicianViewDatas[0]->Description == 'OPHTHALMOLOGY' ? 'selected' : '' }}>OPHTHALMOLOGY</option>
                        <option value="OPTOMETRY" {{ $physicianViewDatas[0]->Description == 'OPTOMETRY' ? 'selected' : '' }}>OPTOMETRY</option>
                        <option value="PATHOLOGY" {{ $physicianViewDatas[0]->Description == 'PATHOLOGY' ? 'selected' : '' }}>PATHOLOGY</option>
                        <option value="ORTHOPEDICS" {{ $physicianViewDatas[0]->Description == 'ORTHOPEDICS' ? 'selected' : '' }}>ORTHOPEDICS</option>
                        <option value="PEDIATRICS" {{ $physicianViewDatas[0]->Description == 'PEDIATRICS' ? 'selected' : '' }}>PEDIATRICS</option>
                        <option value="PLASTIC SURGERY" {{ $physicianViewDatas[0]->Description == 'PLASTIC SURGERY' ? 'selected' : '' }}>PLASTIC SURGERY</option>
                        <option value="PSYCHIATRY" {{ $physicianViewDatas[0]->Description == 'PSYCHIATRY' ? 'selected' : '' }}>PSYCHIATRY</option>
                        <option value="PULMONOLOGY" {{ $physicianViewDatas[0]->Description == 'PULMONOLOGY' ? 'selected' : '' }}>PULMONOLOGY</option>
                        <option value="RADIO-SONOLOGY" {{ $physicianViewDatas[0]->Description == 'RADIO-SONOLOGY' ? 'selected' : '' }}>RADIO-SONOLOGY</option>
                        <option value="RADIOLOGY" {{ $physicianViewDatas[0]->Description == 'RADIOLOGY' ? 'selected' : '' }}>RADIOLOGY</option>
                        <option value="REHABILITATION MEDICINE" {{ $physicianViewDatas[0]->Description == 'REHABILITATION MEDICINE' ? 'selected' : '' }}>REHABILITATION MEDICINE</option>
                        <option value="RHEUMATOLOGY" {{ $physicianViewDatas[0]->Description == 'RHEUMATOLOGY' ? 'selected' : '' }}>RHEUMATOLOGY</option>
                        <option value="THORACO-VASCULAR SURGERY" {{ $physicianViewDatas[0]->Description == 'THORACO-VASCULAR SURGERY' ? 'selected' : '' }}>THORACO-VASCULAR SURGERY</option>
                        <option value="UROLOGY" {{ $physicianViewDatas[0]->Description == 'UROLOGY' ? 'selected' : '' }}>UROLOGY</option>
                    </select>
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Status<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <select class="form-control" name="status" placeholder="Status" required="required" disabled>
                        <option value="Active" @if($physicianViewDatas[0]->Status == 'Active') selected @endif>Active</option>
                        <option value="Inactive" @if($physicianViewDatas[0]->Status == 'Inactive') selected @endif>Inactive</option>
                        <option value="RP - For Approval" @if($physicianViewDatas[0]->Status == 'RP - For Approval') selected @endif>RP - For Approval</option>
                        <option value="RP - For Revision" @if($physicianViewDatas[0]->Status == 'RP - For Revision') selected @endif>RP - For Revision</option>
                        <option value="RP - RP - Leads" @if($physicianViewDatas[0]->Status == 'RP - Leads') selected @endif>RP - Leads</option>
                    </select>
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Branch Request Origin<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="branchCode" placeholder="Branch Code" value="{{ $physicianViewDatas[0]->BranchCode }}" disabled>
                </div>
            </div> 
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 4px;">
    <div class="d-flex justify-content-between align-items-center">             
        <input type="hidden" name="myimage" class="image-tag" value="{{ $physicianViewDatas[0]->Prescription_Link }}">
        
        @if(!empty($physicianViewDatas[0]->Prescription_Link) && file_exists(public_path('uploads/PhysicianPrescription/' . $physicianViewDatas[0]->Prescription_Link)))
            <!-- Image with zoom functionality -->
            <img src="{{ asset('uploads/PhysicianPrescription/' . $physicianViewDatas[0]->Prescription_Link) }}" 
                alt="Captured Rx" 
                class="img-fluid mt-2 webcam"
                data-bs-toggle="modal" 
                data-bs-target="#imageModal">
        @else
            <img src="{{ asset('images/RP_Prescription.png') }}" style="width: 430px; height: 330px; object-fit: contain;">
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- Image will be dynamically updated here -->
                <img id="modalImage" src="" class="img-fluid" style="max-height: 80vh;" alt="Zoomed Image">
            </div>
        </div>
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
    $(document).on('click', '.webcam', function() {
        // Get the source of the clicked image
        let imgSrc = $(this).attr('src');
        
        // Set the source in the modal image
        $('#modalImage').attr('src', imgSrc);
    });
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
			'pageToLoad': "{{ '/physicianWebcam' }}"
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
	
});
</script>