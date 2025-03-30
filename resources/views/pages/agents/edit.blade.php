<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title bold">Sales Agent Detail</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light bordered">
				
				<div class="portlet-body form">
					<form id="frm_input" method="post" action="{{ route('admin.agents.save') }}" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" name="user_id" value="{{ $user_id }}">
						<input type="hidden" name="hidden_email" value="{{ $email }}">
						
						<div class="form-group row">
							<div class="col-md-4">
								<label class="fs-bold">Photo</label>
								<input type="file" class="form-control" name="photo" id="photo" placeholder=""  accept="image/*">
								<div class="crop_wrapper" style="margin-top: 20px; text-align: center;">
									<img id="uploaded_image" src="{{ ( $photo_url == '' ) ? '' : $photo_url }}" alt="" srcset="" width="150px">
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group row">
									<div class="col-md-6">
										<label class="fs-bold">Name <span class='red'>*</span></label>
										<input type="text" class="form-control" name="name" placeholder="" value="{{ $name }}" required>
									</div>
									<div class="col-md-6">
										<label class="fs-bold">Email <span class='red'>*</span></label>
										<input type="email" class="form-control" name="email" placeholder="" value="{{ $email }}" autocomplete="off" required onkeydown="doOnRestore()" >
										<span id="email_error" class="red hidden">This email already exists.</span>
									</div>							
								</div>
								<div class="form-group row">
									<div class="col-md-6">
										<label class="fs-bold">Password</label>
										<input type="password" class="form-control" name="password" placeholder="" value="" autocomplete="new-password" />    
									</div>
									<div class="col-md-6">
										<label class="fs-bold">Confirm password</label>
										<input type="password" class="form-control" name="confirm_password" placeholder="" value="" onkeydown="doOnRestorePassword()" onblur="doOnCheckPassword()" />
										<span id="password_error" class="red hidden">Password does not match.</span>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-4">
										<label class="fs-bold">Phone <span class='red'>*</span></label>
										<input type="text" class="form-control" name="phone" placeholder="" value="{{ $phone }}" oninput="this.value = this.value.replace(/[^0-9-()]/g, '').replace(/(\..*)\./g, '$1');" required>
									</div>
									<div class="col-md-8">
										<label class="fs-bold">Company <span class='red'>*</span></label>
										<input type="text" class="form-control" name="company" placeholder="" value="{{ $company }}" required>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-6 {{ strtolower($user_id) != 'null' ? '' : 'hidden' }}">
										<label class="fs-bold">Created Time</label>
										<p>{{ $date_created }}</p>
									</div>
									<div class="col-md-6">
										<label class="fs-bold">Status</label>
										<div class="mt-radio inline-flex" style="padding-top: 5px; display: flex!important;">
											<label class="mt-radio rd-green">
												<input type="radio" class="rd-status" name="act" value="active" {{ $act == '1' ? 'checked' : '' }}> Active
												<span></span>
											</label> &nbsp;&nbsp;&nbsp;
											<label class="mt-radio rd-danger">
												<input type="radio" class="rd-status" name="act" value="inactive" {{ $act == 0 ? 'checked' : '' }}> Inactive
												<span></span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						
						<br>
						<div class="text-right">
							<button type="button" class="btn default" data-dismiss="modal">Cancel</button>
							<button type="button" name="btn_submit" class="btn btn-default green" >Save</button>
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
	function doOnCheckEmail() {
		var email = $('input[name="email"]').val();
		if ( $('input[name="user_id"]').val() == 'NULL' && email == $('input[name="hidden_email"]').val() ) return;
		url = "/admin/agents/checkemail/" + email;
		$('button[name="btn_submit"]').attr('disabled', true);
		$.get(url, function(resp) {
			if ( resp.status != 'none' ) {
				$('input[name="email"]').addClass('red-border');
				$('#email_error').removeClass('hidden');
				$('button[name="btn_submit"]').attr('disabled', true);
			}
			else {
				$('button[name="btn_submit"]').attr('disabled', false);
			}
		});
	}
	function doOnRestore() {
		$('input[name="email"]').removeClass('red-border');
		$('#email_error').addClass('hidden');
		$('button[name="btn_submit"]').attr('disabled', false);
	}
	function doOnCheckPassword() {
		var password = $('input[name="password"]').val();
		if ( password == $('input[name="confirm_password"]').val() ) return;
		$('input[name="confirm_password"]').addClass('red-border');
		$('input[name="password"]').addClass('red-border');
		$('#password_error').removeClass('hidden');
	}
	function doOnRestorePassword() {
		$('input[name="password"]').removeClass('red-border');
		$('input[name="confirm_password"]').removeClass('red-border');
		$('#password_error').addClass('hidden');
	}
	$(document).ready(function() {

		$("input[name='photo']").on('change', function(){
			var reader = new FileReader();
			reader.onload = function (event) {
				var file = event.target;
				$('#uploaded_image').attr('src', file.result);
			}
			reader.readAsDataURL(this.files[0]);

		});

		$('button[name="btn_submit"]').click(function() {
			if ( $('input[name="email"]').val() == "" ) {
				alert("Please enter the email");
				return;
			}
			if ( $('input[name="name"]').val() == "" ) {
				alert("Please enter the name");
				return;
			}
			if ( $('input[name="company"]').val() == "" ) {
				alert("Please enter the company name");
				return;
			}
			if ( $('input[name="phone"]').val() == "" ) {
				alert("Please enter the phone number");
				return;
			}
			var email = $('input[name="email"]').val();
			if ( email == $('input[name="hidden_email"]').val() && $('input[name="user_id"]').val() == "NULL" ) {
				alert("Please enter the valid email address");
				$('input[name="email"]').addClass('red-border');
				// $('#email_error').removeClass('hidden');
				// $('button[name="btn_submit"]').attr('disabled', true);
				return;
			}
			if ( $('input[name="user_id"]').val() == 'NULL' && $('input[name="password"]').val() == "" ) {
				alert("Please enter the password");
				// $('button[name="btn_submit"]').attr('disabled', true);
				return;
			}
			if ( $('input[name="user_id"]').val() == 'NULL' && $('input[name="confirm_password"]').val() == "" ) {
				alert("Please enter the confirm password");
				// $('button[name="btn_submit"]').attr('disabled', true);
				return;
			}
			if ( $('input[name="confirm_password"]').val() != "" && $('input[name="confirm_password"]').val() != "" && $('input[name="confirm_password"]').val() != $('input[name="password"]').val()) {
				$('input[name="password"]').addClass('red-border');
				$('input[name="confirm_password"]').addClass('red-border');
				$('#password_error').removeClass('hidden');
				return;
			}
			// var email = $('input[name="email"]').val();
			// if ( email == $('input[name="hidden_email"]').val() && $('input[name="user_id"]').val() == "NULL" ) return;
			url = "/admin/agents/checkemail/" + email + '?user_id=' + $('input[name="user_id"]').val();
			$.get(url, function(resp) {
				if ( resp.status != 'none' ) {
					$('input[name="email"]').addClass('red-border');
					$('#email_error').removeClass('hidden');
					$('button[name="btn_submit"]').attr('disabled', true);
				}
				else {
					$('button[name="btn_submit"]').attr('disabled', false);
					$('#frm_input').submit();
				}
			});
		});
		
	});
</script>