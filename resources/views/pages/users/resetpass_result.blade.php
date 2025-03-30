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
	
  <p class="form-title text-center">
    Thank you. We just updated your password.
  </p>
  
	<div class="copyright"> {{ date('Y') }} &copy; JPH Equipment. All Rights Reserved </div>
</div>


@endsection
