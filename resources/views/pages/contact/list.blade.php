@extends('layouts.app')

@section('content')
<div class="page-content-wrapper">
	<div class="page-content">
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<span class="caption-subject bold"> Contacts </span>
						</div>
						<div class="actions">
							<div class="btn-group">
								<a id="add_banner" class="btn sbold modal-trigger green" data-url="{{ route('admin.contact.edit', 'null') }}" data-toggle="modal"> Add
									<i class="fa fa-plus"></i>
								</a>
								<a class="btn sbold modal-trigger blue hidden" href="{{ route('admin.contact.edit', 'null') }}" style="margin-left: 10px;"> Download CSV
									<i class="fa fa-download"></i>
								</a>
							</div>
						</div>
					</div>
					<div class="portlet-body">
						
						<table class="table table-striped table-bordered table-hover order-column" id="contact_table">
							<thead>
								<tr role="row" class="heading">
									<th class="text-center"> No </th>
									<th class="text-center"> Name </th>
									<th class="text-center"> Email </th>
									<th class="text-center"> Phone </th>
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
		<div id="ajax-modal" class="modal fade" tabindex="-1" data-backdrop='static'> </div>
	</div>
</div>
<script>
	function doOnCompanyChange(dom) {
		grid.getDataTable().ajax.reload();
	}
	function doOnStateChange(dom) {
		grid.getDataTable().ajax.reload();
	}
</script>
@endsection
