@extends('layouts.auth')

@section('content')
<style>
	.login .content {
    width: 768px;
		/* height: 268px;
		margin: auto; */
		margin-top: 30vh;
	}
	.login .content .forget-form, .login .content .login-form {
    display: inline-flex;
		width: 100%;
	}
	.form-inline .control-label, .form-inline .form-group {
    width: 40%;
    padding-right: 10px;
	}
	.input-icon {
		border: 1px solid #ffffff;
    border-radius: 5px!important;
    color: var(--white);
	}
	.input-icon>.form-control {
    width: 100%;
	}
	.input-icon>i {
		color: #ffffff;
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
</style>
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGO -->
	<div class="logo">
		<img src="{{ url('public/images/favicon.png') }}" alt="" style="margin-top: 0;width: 400px;" />
	</div>
	<!-- END LOGO -->
	@if (count($errors) > 0)
	<div class="alert alert-danger">
		<strong>Whoops!</strong> There were problems with input:
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div> 
	@endif
	@if (!is_null(session()->get('status')) && strtolower(session()->get('status')) == 'failed')
	<div class="alert alert-danger">
		<strong>Whoops!</strong> There were problems with input:
		<ul>
			<li>{{ session()->get('message') }}</li>
		</ul>
	</div>
	@endif
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form form-inline" action="{{ route('login') }}" method="post" role="form">
		<h3 class="form-title text-center hidden ">Login</h3>
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Email</label>
			<div class="input-icon">
				<i class="fa fa-envelope"></i>
				<input class="form-control placeholder-no-fix trans-input" type="text" autocomplete="off" placeholder="Email" name="email" value="" /> </div>
				<!-- <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" value="{{ old('email') }}" /> </div> -->
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix trans-input" type="password" autocomplete="off" placeholder="Password" name="password" value="" /> </div>
				<!-- <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" /> </div> -->
		</div>
		<div class="form-actions">
			<label class="rememberme mt-checkbox mt-checkbox-outline hidden">
				<input type="checkbox" name="remember" value="1" />Remember me 
				<span></span>
			</label>
			<button type="submit" class="btn green btn-outline btn-signin pull-right"> Login </button>
			<br/>
			<br/>

		</div>

	</form>
		
		<p class="p-signup hidden">
			Registering as a new restaurant? 
			<a href="{{ route('register') }}" class="btn red btn-circle">Register</a>
		</p>
	
	<div class="copyright"> {{ date('Y') }} &copy; JPH Equipment. All Rights Reserved </div>
</div>
	
<!-- <script src="{{ url('public/admin/pages/scripts/login.js') }}" type="text/javascript"></script> -->

@endsection
