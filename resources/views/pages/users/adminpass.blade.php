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
<div class="page-content-wrapper">
	<div class="page-content">
		<div class="row">
    		<div class="col-md-12">
    			<div class="portlet light bordered">
    				<p>Change password</p>
    				<div class="portlet-body form">
    					<form id="frm_input" method="post" action="{{ route('admin.change_admin_pass.save') }}" enctype="multipart/form-data" autocomplete="0">
    						@csrf
    						<div class="form-group">
								<div style="padding-top: 24px;">
								    <label>New password</label>
								    <input type="password" class="form-control" name="password" placeholder="" value="" />
								</div>
								<div style="padding-top: 24px;">
								    <label>Confirm password</label>
								    <input type="password" class="form-control" name="confirm_password" placeholder="" value="" onkeyup="doOnRestorePassword()" onblur="doOnCheckPassword()" />
								    <span id="password_error" class="red hidden">Password does not match.</span>
								</div>
    						</div>
    
    						<br>
    						<div class="text-right">
    							<button type="submit" name="btn_submit" class="btn green" >Save</button>
    						</div>
    					</form>
    				</div>
    			</div>
    		</div>
    	</div>
		<div class="clearfix"></div>
		<div id="ajax-modal" class="modal fade" tabindex="-1"> </div>
	</div>
</div>
<div class="modal fade" id="modal_success" tabindex="-1" role="basic" aria-hidden="true" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header bg-primary" style="padding: 7px 15px;display: flex;">
			<h4 class="modal-title" id="popup_title" style="width: calc(100% - 20px);">JPH Equipment</h4>
			<a type="button" class="btn-close" data-dismiss="modal" aria-hidden="true" style="padding-top: 3px;color: #ffffff;">
				<i class="glyphicon glyphicon-remove"></i>
			</a>
		</div>
		<div class="modal-body" style="max-height: 500px; overflow: auto;"> 
			<p id="p_detail" class="" style="max-height: 70vh;overflow-y: auto;"></p>
		</div>
		<div class="modal-footer">
			<button type="button" name="btn_modal_close" class="btn green" data-dismiss="modal" aria-hidden="true">Ok</button>
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
		// alert(message);
	}
	$(document).ready(()=>{
		if ( msg != 'null' ) {
			alert(msg);
		}
		$('button[name="btn_submit"]').prop('disabled', true);
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
