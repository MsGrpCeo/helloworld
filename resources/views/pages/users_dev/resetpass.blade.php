@extends('layouts.auth')

@section('content')
<style>
  .container-fluid {
    width: 100vw;
    height: 100vh;
  }
	.login .content {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-right: -50%;
		transform: translate(-50%, -50%);
	}
	.login .content .form-actions {
    width: 20%;
		margin-left: 0;
	}
	.login .content .form-actions .btn {
    width: 100%;
	}
	.login .logo {
    margin: 0 auto 20px;
	}
  @media screen and (max-width: 768px) {
    .login-form input {
      text-align: center;
      max-width: 360px;
      margin: auto;
    }
    button {
      width: 100%;
    }
  }
</style>

<div class="content">
	
	<div class="logo">
		<img src="{{ url('public/images/favicon.png') }}" alt="" style="margin-top: 0;width: 100%;" />
	</div>
	@if (strtolower($status) == 'success')
  <form id="frm_submit" class="login-form" method="post" action="{{ route('api_dev.post_reset_pass') }}">
    <p class="form-title text-center">
      Enter New password. (at least 6 characters)
    </p>
    <input type="password" id="password" class="form-control placeholder-no-fix" name='password' placeholder="Enter New password. (at least 6 characters)">
    <input type="hidden" name="email" value="{{ $email }}">
    <br/>
    <div class="full-width" style="text-align: right;">
      <button class="btn green btn-signin" type="submit" name="submit_email">Submit</button>
    </div>
  </form>
	@else
  <p class="form-title text-center">
    Something went wrong.
  </p>
  <h4 class="text-danger text-center">{{ $message }}</h4>
  @endif
	<div class="copyright"> {{ date('Y') }} &copy; JPH Equipment. All Rights Reserved </div>
</div>

<script>
$(document).ready(function() {
  $("#frm_submit").submit(function(){
		var res; 
		var str = document.getElementById("password").value; 
		if (str.length >= 6) 
			res = true; 
		else 
			res = false;
			
		if (res) {
			return true;
		} else {
			alert("Password must be at least 6 characters, including 1 numeric character");
			return false;
		}
	})
});
</script>
@endsection
