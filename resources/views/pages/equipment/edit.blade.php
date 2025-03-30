<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title bold">Equipment Type Detail</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light bordered">
				
				<div class="portlet-body form">
					<form id="frm_input" method="post" action="{{ route('admin.equipment.save') }}" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
						<div class="form-group">
							<div class="row">
								<div class="col-md-4">
									<label class="fs-bold">Photo</label>
									<input type="file" class="form-control" name="photo" id="photo" placeholder=""  accept="image/*">
									<div class="crop_wrapper" style="margin-top: 20px; text-align: center;">
										<img id="uploaded_image" src="{{ ( $equipment_image == '' ) ? '' : $equipment_image }}" alt="" srcset="" width="150px">
									</div>
								</div>
								<div class="col-md-8">
									<div class="form-group">
										<label class="fs-bold">Equipment Name <span class='red'>*</span></label>
										<input type="link" class="form-control" name="equipment_name" value="{{ $equipment_name }}" autocomplete="off" required>
									</div>
									<div class="form-group">
										<label class="fs-bold">Type <span class='red'>*</span></label>
										<select name="form_type" class="form-control">
											@foreach($form_types as $key=>$value)
											<option value="{{ $key }}" {{ $key == $form_type ? 'selected' : '' }}>{{ $value }}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label class="fs-bold">Status</label>
										<div class="mt-radio inline-flex" style="padding-top: 5px; display: flex!important;">
											<label class="mt-radio rd-green">
												<input type="radio" class="rd-status" name="status" value="active" {{ $status == '1' ? 'checked' : '' }}> Active
												<span></span>
											</label> &nbsp;&nbsp;&nbsp;
											<label class="mt-radio rd-danger">
												<input type="radio" class="rd-status" name="status" value="inactive" {{ $status == 0 ? 'checked' : '' }}> Inactive
												<span></span>
											</label>
										</div>
									</div>
									<div class="form-group  {{ strtolower($transaction_id) == 'null' ? 'hidden' : '' }}">
										<label class="fs-bold">Created At</label>
										<p class="">{{ $created_at }}</p>
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class="text-right">
							<button type="button" class="btn default" data-dismiss="modal">Cancel</button>
							<button type="submit" name="btn_submit" class="btn btn-default green" >Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.modal {
		width: 70vw;
		left: 15vw;
		margin: auto;
	}
</style>
<script>
	var flag = true;
	$(document).ready(function() {
		$(".form_datetime").datepicker({
			autoclose: true,
			isRTL: App.isRTL(),
			format: "yyyy-mm-dd",
			fontAwesome: true,
			pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left")
		});

		$("input[name='photo']").on('change', function(){
			var reader = new FileReader();
			reader.onload = function (event) {
				var file = event.target;
				$('#uploaded_image').attr('src', file.result);
			}
			reader.readAsDataURL(this.files[0]);

		});
		
	});
</script>