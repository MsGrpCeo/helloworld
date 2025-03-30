@extends('layouts.app')

@section('content')

<div class="page-content-wrapper">
	<div class="page-content">
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<span class="caption-subject bold"> Forms Submitted </span>
						</div>
						<div class="actions">
							<div class="btn-group">
								<div class="" style="display: flex;">
									<div class="input-group input-large date-picker input-daterange">
										<input type="text" class="form-control" name="filter_from" value="{{ $filter_from }}">
										<span class="input-group-addon"> ~ </span>
										<input type="text" class="form-control" name="filter_to" value="{{ $filter_to }}"> 
									</div>
									<button class="btn sbold green " name="csv_download" data-url="{{ route('admin.forms.download_csv', array('from'=>$filter_from, 'to'=>$filter_to)) }}" style="margin-left: 15px;" > Download CSV
										<i class="fa fa-download"></i>
									</button>
									<a href="" id="a_download_csv" name="a_download_csv" class=""></a>
								</div>
							</div>
						</div>
					</div>
					<div class="portlet-body">
						<table class="table table-striped table-bordered table-hover order-column" id="forms_table">
							<thead>
								<tr role="row" class="heading">
									<th class="text-center"> # </th>
									<th class="text-center"> Date/Time </th>
									<th class="text-center"> Agent Name </th>
									<th class="text-center"> Equipment Name </th>
									<th class="text-center"> Year | Make | Model </th>
									<th class="text-center"> Contact Name </th>
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
		<div id="ajax-modal" class="modal fade" tabindex="-1" data-backdrop="static"> </div>
	</div>
</div>
<style>
</style>
<script>
	$(document).ready(function() {
		$(".date-picker").datepicker({
			autoclose: true,
			isRTL: App.isRTL(),
			format: "yyyy-mm-dd",
			fontAwesome: true,
			pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left")
		});
		
		$('button[name="csv_download"]').click(function() {
			var from = $('input[name="filter_from"]').val();
			var to = $('input[name="filter_to"]').val();
			var url = `/admin/forms/download_csv/${from}/${to}`;
			console.log(url);
			$('a[name="a_download_csv"]').attr('href', url);
			// $('a[name="a_download_csv"]').trigger('click');
			// $('a[name="a_download_csv"]').click();

			document.getElementById('a_download_csv').click();
		});
	});


</script>
@endsection
