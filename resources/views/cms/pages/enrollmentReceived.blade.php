
<form id="patientAddModalForm" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}"  autocomplete="off">
	<input type="hidden" name="_method" value="PUT">
	<input type="hidden" name="_id"       value="{{$datas[0]->Id}}">
	<input type="hidden" name="_token"  value="{{ csrf_token() }}">
	<div class="modal-cms-header">
			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
					<label class="bold branchDefault " style="cursor:pointer;">Released To<font style="color:red;">*</font></label>
				</div>
				<div class="col-sm-10 col-md-10">
					<select name="Users" class="form-control" required="required" disabled>
						<option value=""></option>
						@foreach ($users as $user) 
							<option value="{{ $user->Code }}" {{ $user->Code == $datas[0]->ReleaseTo ? 'selected' : '' }}>
								{{ $user->Code }}
							</option>
						@endforeach
					</select>
				</div>
			</div>

				<div class="row form-group row-md-flex-center">
					<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
						<label class="bold LabNo " style="cursor:pointer;">Card#<font style="color:red;">*</font></label>
					</div>
					<div class="col-sm-10 col-md-10">
						<input type="text" class="form-control" name="CardNumber"  value= "{{ $datas[0]->CardNumber }}" readonly="readonly">
					</div> 
				</div>

			<div class="row form-group row-md-flex-center">
				<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
					<label class="bold LabNo " style="cursor:pointer;">Received By<font style="color:red;">*</font></label>
				</div>
				<div class="col-sm-10 col-md-10">
					<input type="text" class="form-control" name="ReceivedBy"  value= "{{ $datas[0]->ReceivedBy }}" placeholder="Received By" readonly="readonly">
				</div> 
			</div>
		</div> 

</form>
@section('script')
<script>

		$userSelect = $('select[name="Users"]').selectize({
			onChange: function(value) {
				if (!value.length )
				{
					$('input[name="UserCode"]').val('');
					return;	
				}
				$('input[name="UserCode"]').val( $('select[name="Users"] option:selected').text() );
				
			}
		});
		$user = $userSelect[0].selectize;


		$.ajaxSetup({
			headers: {
				'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
			}
		});

</script>
@endsection

