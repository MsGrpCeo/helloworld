<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">

<div class="page-wrapper">
	@include('partials.topbar')
	<div class="page-container">
	@include('partials.sidebar')
			@yield('content')
	</div>
</div>

<div class="modal fade" id="modal_progress" tabindex="-1" role="basic" aria-hidden="true" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header bg-primary" style="padding: 7px 15px;display: flex;">
			<h4 class="modal-title" id="popup_title" style="width: calc(100% - 20px);">JPH Equipment</h4>
		</div>
		<div class="modal-body" style="max-height: 500px; overflow: auto;display: flex;"> 
			<p id="p_detail" class="" style="max-height: 70vh;overflow-y: auto;">
				Data processing
			</p>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_error" tabindex="-1" role="basic" aria-hidden="true" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header bg-dark" style="padding: 7px 15px;display: flex;">
			<h4 class="modal-title fs-white" id="popup_title" style="width: calc(100% - 20px);">JPH Equipment</h4>
			<a type="button" class="btn-close" data-dismiss="modal" aria-hidden="true" style="padding-top: 3px;color: #ffffff;">
				<i class="glyphicon glyphicon-remove"></i>
			</a>
		</div>
		<div class="modal-body" style="max-height: 500px; overflow: auto;"> 
			<p id="p_error_message" class="" style="max-height: 70vh;overflow-y: auto;"></p>
		</div>
		<div class="modal-footer">
			<button type="button" name="btn_modal_close" class="btn dark" data-dismiss="modal" aria-hidden="true">Ok</button>
		</div>
	</div>
</div>
<div class="modal fade" id="modal_progressing" tabindex="-1" role="basic" aria-hidden="true" data-backdrop="static">
	<div class="modal-content">
		<div class="modal-header bg-dark" style="padding: 7px 15px;display: flex;">
			<h4 class="modal-title fs-white" id="popup_title" style="width: calc(100% - 20px);">JPH Equipment</h4>
			<a type="button" class="btn-close" data-dismiss="modal" aria-hidden="true" style="padding-top: 3px;color: #ffffff;">
				<i class="glyphicon glyphicon-remove"></i>
			</a>
		</div>
		<div class="modal-body" style="max-height: 500px; overflow: auto;"> 
			<svg class="w-0">
				<defs>
					<filter id="w-0">
						<feGaussianBlur in="SourceGraphic" stdDeviation="7" result="blur" />
						<feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 20 -10" result="res" />
						<feComposite in="SourceGraphic" in2="res" operator="atop" />
					</filter>
				</defs>
			</svg>
			<svg class="f-w-0" width="200" height="200" viewBox="0 0 200 200">
				<defs>
					<linearGradient id="linear-gradient">
						<stop class="stop1" offset="0" />
						<stop class="stop2" offset="1" />
					</linearGradient>
					<linearGradient y2="160" x2="160" y1="40" x1="40" gradientUnits="userSpaceOnUse" id="gradient" xlink:href="#linear-gradient" />
				</defs>
				<path
					class="path-class"
					d="m 164,100 c 0,-35.346224 -28.65378,-64 -64,-64 -35.346224,0 -64,28.653776
						-64,64 0,35.34622 28.653776,64 64,64 35.34622,0 64,-26.21502 64,-64
						0,-37.784981 -26.92058,-64 -64,-64 -37.079421,0 -65.267479,26.922736
						-64,64 1.267479,37.07726 26.703171,65.05317 64,64 37.29683,-1.05317
						64,-64 64,-64"
				/>
				<circle class="cricle-class" cx="100" cy="100" r="64" />
			</svg>
			<svg class="svg-class-1" width="200" height="200" viewBox="0 0 200 200">
				<path
					class="path-class"
					d="m 164,100 c 0,-35.346224 -28.65378,-64 -64,-64 -35.346224,0 -64,28.653776
						-64,64 0,35.34622 28.653776,64 64,64 35.34622,0 64,-26.21502 64,-64
						0,-37.784981 -26.92058,-64 -64,-64 -37.079421,0 -65.267479,26.922736
						-64,64 1.267479,37.07726 26.703171,65.05317 64,64 37.29683,-1.05317 64,-64
						64,-64"
				/>
				<circle class="cricle-class" cx="100" cy="100" r="64" />
			</svg>
		</div>
	</div>
</div>
<style>
	.page-footer {
		padding: 15px 20px;
    height: auto;
		border-top: 1px solid #505050;
		font-size: 16px;
	}
	.scroll-to-top>i {
    color: #41c78b;
	}
	#modal_error {
		width: 50vw;
		left: 25vw;
		margin: auto;
		padding-right: 0!important;
	}
	#modal_error.fade.in {
		top: 50%;
	}
	@media (max-width: 979px) {
			.modal, .modal.container, .modal.modal-overflow {
					right: 5%;
					left: 5%;
					bottom: auto;
					width: auto !important;
					height: auto !important;
			}
	}

	.w-0 {
		width: 0;
	}
	.f-w-0 {
		filter: url(#w-0);
	}
	.stop1 {
		stop-color: rgb(168 85 247);
	}
	.stop2 {
		stop-color: rgb(37 99 235);
	}
	.path-class {
		animation: key01 8s infinite linear;
		stroke-dasharray: 180 800;
		fill: none;
		stroke: url(#gradient);
		stroke-width: 23;
		stroke-linecap: round;
	}
	.cricle-class {
		animation: key01 4s infinite linear;
		stroke-dasharray: 26 54;
		fill: none;
		stroke: url(#gradient);
		stroke-width: 23;
		stroke-linecap: round;
	}
	.svg-class-1 {
		filter: blur(5px);
		opacity: 0.3;
		position: absolute;
		transform: translate(3px, 3px);
	}
	@keyframes key01 {
		0% {
			stroke-dashoffset: 0;
		}
		100% {
			stroke-dashoffset: -403px;
		}
	}

</style>

@include('partials.javascripts')
</body>
</html>