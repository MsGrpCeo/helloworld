<!DOCTYPE html>
<html lang="en">

<head>
	@include('partials.head')
</head>

<body data-kt-name="metronic" id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
	
	<script>
		if ( document.documentElement ) { 
			const defaultThemeMode = "system"; 
			const name = document.body.getAttribute("data-kt-name"); 
			let themeMode = localStorage.getItem("kt_" + ( name !== null ? name + "_" : "" ) + "theme_mode_value"); 
			if ( themeMode === null ) { 
				if ( defaultThemeMode === "system" ) { 
					themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; 
				} 
				else { 
					themeMode = defaultThemeMode; 
				} 
			} 
			document.documentElement.setAttribute("data-theme", themeMode); 
		}
	</script>
	<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
			<!--begin::Page-->
			<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
				<!--begin::Wrapper-->
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
					<!--begin::Main-->
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<!--begin::Content wrapper-->
						<div class="d-flex flex-column flex-column-fluid" id="kt_app_page">
							@yield('content')
						</div>
						<!--begin::Footer-->
						@include('partials.footer')
						<!--end::Footer-->
					</div>
					<!--end:::Main-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
	<!--begin::Scrolltop-->
	<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
		<!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
		<span class="svg-icon">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
				<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
			</svg>
		</span>
		<!--end::Svg Icon-->
	</div>
	<!--end::Scrolltop-->
	


	@include('partials.modals')
	@include('partials.app-javascripts')
</body>
</html>