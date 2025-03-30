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
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGO -->
	<div class="logo">
		<img src="{{ url('public/images/favicon.png') }}" alt="" style="margin-top: 0;width: 100%;" />
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN FORM -->
  <form class="login-form" method="post" action="{{ route('api_dev.send_link') }}">
    <p class="form-title text-center">
      Enter Email Address To Send Password Link
    </p>
    <input class="form-control placeholder-no-fix" type="text" name="email" required/>
    <br/>
    <div class="full-width" style="text-align: right;">
      <button class="btn green btn-signin" type="submit" name="submit_email">Submit</button>
    </div>
  </form>
	
	<div class="copyright"> {{ date('Y') }} &copy; JPH Equipment. All Rights Reserved </div>
</div>
	
<!-- <script src="{{ url('public/admin/pages/scripts/login.js') }}" type="text/javascript"></script> -->

@endsection
