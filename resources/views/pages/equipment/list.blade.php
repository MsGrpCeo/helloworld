@extends('layouts.app')

@section('content')

<div class="page-content-wrapper">
	<div class="page-content">
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<span class="caption-subject bold"> Equipment Types </span>
						</div>
						<div class="actions">
							<div class="btn-group">
								<a id="add_banner" class="btn sbold modal-trigger green" data-url="{{ route('admin.equipment.edit', 'null') }}" style="margin-left: 10px;" data-toggle="modal"> Add
									<i class="fa fa-plus"></i>
								</a>
								<a class="btn sbold modal-trigger blue hidden" href="{{ route('admin.equipment.download_csv') }}" style="margin-left: 10px;"> Download CSV
									<i class="fa fa-download"></i>
								</a>
							</div>
						</div>
					</div>
					<div class="portlet-body">
						<table class="table table-striped table-bordered table-hover order-column" id="equipment_table">
							<thead>
								<tr role="row" class="heading">
									<th class="text-center"> # </th>
									<th class="text-center"> Equipment Name </th>
									<th class="text-center"> Image </th>
									<th class="text-center"> Form Type </th>
									<th class="text-center"> Created At </th>
									<th class="text-center"> Status </th>
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

<script>
	$(document).ready(function() {
		$(".bs-datetime").datetimepicker({
			autoclose: true,
			isRTL: App.isRTL(),
			format: "dd-mm-yyyy hh:ii:ss",
			fontAwesome: true,
			pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left")
		});
		
		// var user_id = window.localStorage.getItem("user_id");
		// if (user_id != null) {
		// 	$('select[name="filter_user_id"]').val(user_id);
		// 	// grid.getDataTable().ajax.reload();
		// }

		$("a.a-move-row").on('click', function() {
			
		})
	});
	function doOnMoveRow(dom) {
		var url = $(dom).attr('ahref');
		$('#modal_progress').modal('show');
		$.get(url, function(resp) {
			reloadGrid();
			$('#modal_progress').modal('hide');
		});
		console.log($(dom).attr('ahref'));
	}

	function reloadGrid() {
		grid.getDataTable().ajax.reload();
	}
	function doOnSelectUser(dom) {
		makeHtml();
		$('#modal_confirm').modal('show');

	}
	function doOnClickUserItem(dom) {
		var user_id = $(dom).attr('user-id');
		$('#modal_confirm').modal('hide');
		$('button[name="btn_selected_user"]').attr('user-id', user_id);
		if (user_id == 0) user_id = 'all';
		window.localStorage.setItem("user_id", user_id);
		$('input[name="filter_user_id"]').val(user_id);
		$('button[name="btn_selected_user"]').html($(dom).html());
		grid.getDataTable().ajax.reload();
	}
	function doOnSearchName(evt) {
		var searchName = $('#modal_search').val();
		makeHtml(searchName);
	}
	function makeHtml(searchName=undefined) {
		var htmlArr = [];
		for(user_id in user_json) {
			if (searchName == undefined) {
				htmlArr.push(
					`<div class="col-xs-12 col-md-12">
						<button type="button" class="btn default full-width" user-id="${user_id}" onclick="doOnClickUserItem(this)" style="text-align: left; margin: 1px;">${user_json[user_id]}</button>
					</div>
				`);
			}
			else {
				if ( user_json[user_id].toLowerCase().includes(searchName.toLowerCase()) ) {
					htmlArr.push(
					`<div class="col-xs-12 col-md-12">
						<button type="button" class="btn default full-width" user-id="${user_id}" onclick="doOnClickUserItem(this)" style="text-align: left; margin: 1px;">${user_json[user_id]}</button>
					</div>
				`);
				}
			}
		}
		$('#div_user_list').html(htmlArr.join(''));
	}

	function doOnChangeUser(dom) {
		// if ($(dom).val()=='all') {
		// 	$('#add_banner').addClass('hidden');
		// }
		// else {
		// 	$('#add_banner').removeClass('hidden');
		// }
		window.localStorage.setItem("user_id", $(dom).val());
		grid.getDataTable().ajax.reload();
	}
	function doOnChangeType(dom) {
		grid.getDataTable().ajax.reload();
		if ($('select[name="type"]').val() == '1') {
			$(`select[name="status"] option[value="5"]`).attr('disabled', false);
		}
		else {
			$(`select[name="status"] option[value="5"]`).attr('disabled', true);
		}
	}
	function doOnChangeStatus(dom) {
		grid.getDataTable().ajax.reload();
	}
	function doOnChangeHideStatus() {
		var hideStatus = 1 - parseInt($('input[name="hide_canceled_transactions"]').val());
		$('input[name="hide_canceled_transactions"]').val(hideStatus);
		grid.getDataTable().ajax.reload();
	}
	function doOnChangeFilterBy(dom) {
		if ( $(dom).val() == 'from_to' ) {
			$('#div_filterby_dates').removeClass('hidden');
		}
		else {
			$('#div_filterby_dates').addClass('hidden');
			grid.getDataTable().ajax.reload();
		}
	}
	function doOnChangeFromDate(dom) {
		grid.getDataTable().ajax.reload();
	}
	function doOnChangeToDate(dom) {
		grid.getDataTable().ajax.reload();
	}
</script>
@endsection
