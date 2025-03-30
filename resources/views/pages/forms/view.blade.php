<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title bold">Form Submitted Detail</h4>
</div>
<div class="modal-body">
	<div class="portlet light bordered mb-0">
		
		<div class="portlet-body form">
			<form id="frm_input" method="post" action="" enctype="multipart/form-data" autocomplete="off">
				@csrf
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">ID</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $id }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Created At</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $date_created }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Agent Name</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $agent_name }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Equipment Name</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $equipment_name }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Contact Name</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $contact_name }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Contact Phone</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $contact_phone }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Contact Email</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $contact_email }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Machine Location</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $machine_location }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Serial</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $serial }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Year</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $year }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Make</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $make }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Model</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $model }}</p>
				</div>
				@if ($form_type != 19 && $form_type != 20)
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Track/Tire Size</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $track_tire_size }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Hours</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $hours }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Stick Length</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $stick_length }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Pad Width</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $pad_width }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">EPA Label</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $epa_label }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Webasto Heater</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $webasto_heater }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Positive Air shut off</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $positive_air_shut_off }}</p>
				</div>
				@endif
				@if ($form_type == 1 || $form_type == 2)
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Quick Coupler Type</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $quick_couple_type }}</p>
				</div>
				@endif
				@if (count($value_json) > 0) 
					@foreach($value_json as $key=>$val)
					<div class="form-group row" style="margin-bottom: 5px;">
						<label class="fs-bold col-sm-5 col-md-3">{{ $key }}</label>
						<p class="col-sm-7 col-md-9 mb-0">{{ $val }}</p>
					</div>
					@endforeach
				@endif
				<!-- Excavator Case -->
				@if ($form_type == 1)
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">- Model</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_base }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">- A Nominal Stick Pin Diameter</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_a }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">- B Nominal Link Pin Diameter</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_b }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">- C Pin Centres</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_c }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">- D Bucket Width at Stick</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_d }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">- E Bucket Width at Link</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_e }}</p>
				</div>
				@elseif ($form_type == 2)
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Manufacture</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_base0 }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Series</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_base }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">&nbsp;&nbsp;&nbsp;T- Thickness</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_a }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">&nbsp;&nbsp;&nbsp;R- Centre of hook to centre of hole</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_b }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">&nbsp;&nbsp;&nbsp;D1-Inside diameter of hole</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_c }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">&nbsp;&nbsp;&nbsp;D2-Inside diameter of hook</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_d }}</p>
				</div>
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">&nbsp;&nbsp;&nbsp;CL-Centre to centre of lug spacing</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $value_e }}</p>
				</div>
				@endif
				<div class="form-group row" style="margin-bottom: 5px;">
					<label class="fs-bold col-sm-5 col-md-3">Additional Information</label>
					<p class="col-sm-7 col-md-9 mb-0">{{ $additional_info }}</p>
				</div>
				<div class="text-right">
					<button type="button" class="btn default" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<style>
	.modal {
		width: 80vw;
		left: 10vw;
		margin: auto;
	}
</style>
<script>
	var flag = true;
	$(document).ready(function() {
		
	});
</script>