@extends('layouts.app')

@section('content')
<script>
    var msg = '{{ $message }}';
</script>
<script>	
	var message = undefined;
</script>
@if(session()->has('message'))
<script>
	message = '{!! session()->get("message") !!}';
</script>
@endif
<div id="kt_app_content_container" class="app-container ">
  <div class="card mt-xl-10 mb-xl-10" style="max-width: 500px; margin: auto;">
    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
      <div class="card-title m-0">
        <h3 class="fw-bold m-0">Change password</h3>
      </div>
    </div>
    <div id="kt_account_settings_profile_details" class="collapse show">
			<form id="frm_input" method="post" action="{{ route('home.change_admin_pass.save') }}" enctype="multipart/form-data" autocomplete="0">
				@csrf
				<div class="card-body border-top p-6">
					<div class="mb-5">
						<label for="exampleFormControlInput1" class="form-label">New password</label>
						<input type="password" class="form-control" name="password" placeholder="" value="" />
					</div>
					<div class="mb-5">
						<label for="exampleFormControlInput1" class="form-label">Confirm password</label>
						<input type="password" class="form-control" name="confirm_password" placeholder="" value="" onkeyup="doOnRestorePassword()" onblur="doOnCheckPassword()" />
					</div>

					<br>
					<div class="w-100 text-end mb-5">
						<button type="button" name="btn_submit" class="btn btn-primary" >Save</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	.modal {
		width: 40vw;
		left: 30vw;
		margin: auto;
		padding-right: 0!important;
	}
	.modal.fade.in {
    top: 35%;
	}
</style>
<script>
	if ( message != undefined ) {
		$('#p_detail').html(message);
		$('#modal_success').modal('show');
		alert(message);
	}
	$(document).ready(()=>{
		if ( msg != 'null' ) {
			alert(msg);
		}
		$('button[name="btn_submit"]').prop('disabled', true);
		
		$('button[name="btn_submit"]').on('click', function(){
			var fd = new FormData();  
      fd.append( '_token', $('input[name="_token"]').val() );
      fd.append( 'password', $('input[name="password"]').val() );
      fd.append( 'confirm_password', $('input[name="confirm_password"]').val() );
      var actionRoute = $('#frm_input').attr('action');
      $.ajax({
        url: actionRoute,
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function(data){
          $('input[name="password"]').val('');
          $('input[name="confirm_password"]').val('');
					alert(data.message)
        }
      });
		});
	});
	function doOnCheckPassword() {
		var password = $('input[name="password"]').val();
		if ( password == $('input[name="confirm_password"]').val() ) {
			$('button[name="btn_submit"]').prop('disabled', false);
			return;
		}
		$('input[name="confirm_password"]').addClass('red-border');
		$('input[name="password"]').addClass('red-border');
		$('#password_error').removeClass('hidden');
	}
	function doOnRestorePassword() {
		$('input[name="password"]').removeClass('red-border');
		$('input[name="confirm_password"]').removeClass('red-border');
		$('#password_error').addClass('hidden');
        
		
		if ( $('input[name="password"]').val() == $('input[name="confirm_password"]').val() ) {
			$('button[name="btn_submit"]').prop('disabled', false);
		}
		else {
			$('button[name="btn_submit"]').prop('disabled', true);
		}
	}
</script>
@endsection
