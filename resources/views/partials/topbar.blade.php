<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner ">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="{{ route('admin.home.list') }}" class="no-underline">
				<img src="{{ url('public/images/favicon.png') }}" alt="logo" class="logo-default" /> 
				<!-- <span class="fs-white">Carol</span> -->
			</a>
			<div class="menu-toggler sidebar-toggler">
				<span></span>
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
			<span></span>
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<img alt="" class="img-circle" src="{{ url('public/admin/layouts/layout/img/avatar.jpg') }}" />
						
						<span class="username username-hide-on-mobile"> {{ Auth::user()->email }}  </span>
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li style="padding: 4px 4px;">
							<a href="{{ route('admin.change_admin_pass.frm') }}" class="" >
									<i class="fa fa-lock"></i>
									<span style="margin-left: -10px;"> Change password  </span>
								</a>
						</li>
						<li style="padding: 4px 4px;">
							<!-- <a href="#" onclick="$('#logout').submit();"> <i class="icon-key"></i> Log Out </a> -->
							<form action="{{ route('logout') }}" method="post">
								@csrf
								<button type="submit" class="full-width text-left"><i class="icon-key"></i> Log Out </button>
							</form>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"> </div>
<!-- END HEADER & CONTENT DIVIDER -->



