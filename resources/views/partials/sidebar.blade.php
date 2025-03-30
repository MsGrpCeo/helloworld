@inject('request', 'Illuminate\Http\Request')
<div class="page-sidebar-wrapper">
	<div class="page-sidebar navbar-collapse collapse">
		<ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
			<li class="sidebar-toggler-wrapper hide">
				<div class="sidebar-toggler">
					<span></span>
				</div>
			</li>
			<!-- END SIDEBAR TOGGLER BUTTON -->
			<li class="nav-item start {{ $request->segment(2) == 'home' ? 'active open' : '' }}">
				<a href="{{ route('admin.home.list') }}" class="nav-link nav-toggle">
					<i class="fa fa-th-large"></i>
					<span class="title">Dashboard </span>
					<span class="{{ $request->segment(2) == 'home' ? 'selected' : '' }}"></span>
				</a>
			</li>
			<li class="nav-item start {{ $request->segment(2) == 'agents' ? 'active open' : '' }}">
				<a href="{{ route('admin.agents.list') }}" class="nav-link nav-toggle">
					<i class="fa fa-users"></i>
					<span class="title">Sales Agents </span>
					<span class="{{ $request->segment(2) == 'agents' ? 'selected' : '' }}"></span>
				</a>
			</li>
			<li class="nav-item start {{ $request->segment(2) == 'contact' ? 'active open' : '' }}">
				<a href="{{ route('admin.contact.list') }}" class="nav-link nav-toggle">
					<i class="fa fa-send"></i>
					<span class="title">Contacts </span>
					<span class="{{ $request->segment(2) == 'contact' ? 'selected' : '' }}"></span>
				</a>
			</li>
			<li class="nav-item start {{ $request->segment(2) == 'equipment' ? 'active open' : '' }}">
				<a href="{{ route('admin.equipment.list') }}" class="nav-link nav-toggle">
					<i class="fa fa-gears"></i>
					<span class="title">Equipment Types </span>
					<span class="{{ $request->segment(2) == 'equipment' ? 'selected' : '' }}"></span>
				</a>
			</li>
			<li class="nav-item start {{ $request->segment(2) == 'forms' ? 'active open' : '' }}">
				<a href="{{ route('admin.forms.list') }}" class="nav-link nav-toggle">
					<i class="fa fa-file-text"></i>
					<span class="title">Forms Submitted </span>
					<span class="{{ $request->segment(2) == 'forms' ? 'selected' : '' }}"></span>
				</a>
			</li>
			
			

		</ul>
	</div>
</div>


