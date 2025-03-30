@extends('layouts.app')

@section('content')
<div class="page-content-wrapper">
	<div class="page-content">
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<span class="caption-subject bold"> Users </span>
						</div>
						<div class="actions">
							<div class="btn-group">
								<!-- <a id="add_banner" class="btn sbold modal-trigger green" data-url="{{ route('admin.users.edit', 'null') }}" data-toggle="modal"> Add
									<i class="fa fa-plus"></i>
								</a> -->
								<a class="btn sbold modal-trigger blue" href="{{ route('admin.users.download_csv') }}" style="margin-left: 10px;"> Download CSV
									<i class="fa fa-download"></i>
								</a>
							</div>
						</div>
					</div>
					<div class="portlet-body">
						<table class="table table-striped table-bordered table-hover order-column" id="users_table">
							<thead>
								<tr role="row" class="heading">
									<th class="text-center"> No </th>
									<th class="text-center"> Email </th>
									<th class="text-center"> Name </th>
									<th class="text-center"> Photo </th>
									<th class="text-center"> DOB </th>
									<th class="text-center"> Occupation </th>
									<th class="text-center"> Address </th>
									<th class="text-center"> Gender </th>
									<th class="text-center"> Ghost Count </th>
									<th class="text-center"> DAM </th>
									<th class="text-center"> Member since </th>
									<th class="text-center"> Recency </th>
									<th class="text-center"> Platform </th>
									<th class="text-center"> Version </th>
									<th class="text-center"> Account Status </th>
									<th class="text-center"> Action </th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>						
					</div>		
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div id="ajax-modal" class="modal fade full-screen" tabindex="-1" data-backdrop="static"> </div>
	</div>
</div>
<script>
	function doOnChangeWageFrequency(dom) {
		grid.getDataTable().ajax.reload();
	}
	function doOnChangeNextPayDate(dom) {
		grid.getDataTable().ajax.reload();
	}
	function doOnChangePayStatus(dom) {
		if ($(`select[name="filter_payment_status"]`).val() == 'all') {
			$('#div_hide_declined_payment').removeClass('hidden');
		}
		else {
			$('#div_hide_declined_payment').addClass('hidden');
		}
		// if ($(`select[name="filter_payment_status"]`).val() == '-3') {
		// 	$('input[name="hide_declined_payment"]').val(0);
		// 	$(`input[name="input_hide_declined_payment"]`).prop('checked', false);
		// }
		// else {
		// 	$('input[name="hide_declined_payment"]').val(1);
		// 	$(`input[name="input_hide_declined_payment"]`).prop('checked', true);
		// }
		grid.getDataTable().ajax.reload();
	}
	function doOnChangeVerified(dom) {
		grid.getDataTable().ajax.reload();
	}
	function doOnChangeHideStatus() {
		var hideStatus = 1 - parseInt($('input[name="hide_declined_payment"]').val());
		$('input[name="hide_declined_payment"]').val(hideStatus);
		grid.getDataTable().ajax.reload();
	}
	var idleTime = 0;
	$(document).ready(function() {
		// Increment the idle time counter every 6 seconds.
		var idleInterval = setInterval(timerIncrement, 6000); // 6 seconds

		// Zero the idle timer on mouse movement.
		$(this).mousemove(function (e) {
			idleTime = 0;
		});
		$(this).keypress(function (e) {
			idleTime = 0;
		});
	});
	function timerIncrement() {
		idleTime = idleTime + 1;
		if (idleTime >= 5) { // 30 seconds
			grid.getDataTable().ajax.reload();
			idleTime = 0;
		}
	}
</script>
<style>
	.full-screen {
		width: 90vw!important;
		left: 5vw!important;
	}
	
</style>
@endsection
