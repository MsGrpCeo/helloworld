@extends('layouts.app')

@section('content')
<script src="{{ url('public/js/TweenMax.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/js/Draggable.min.js') }}" type="text/javascript"></script>
<div class="page-content-wrapper">
	<div class="page-content">
		<div class="row">
			<div class="col-md-12">
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<span class="caption-subject bold"> Equipment Sample Images</span>
						</div>
						<div class="actions">
							<div class="btn-group">
								<a class="btn sbold blue" href="{{ route('admin.equipment.list') }}" style="margin-left: 10px;">Equipment List</a>
								<a id="add_banner" class="btn sbold modal-trigger green hidden" data-url="{{ route('admin.equipment.edit', 'null') }}" style="margin-left: 10px;" data-toggle="modal"> Add
									<i class="fa fa-plus"></i>
								</a>
								<a class="btn sbold modal-trigger blue hidden" href="{{ route('admin.equipment.download_csv') }}" style="margin-left: 10px;"> Download CSV
									<i class="fa fa-download"></i>
								</a>
								<form id="frm_input" class="" method="post" action="" enctype="multipart/form-data" autocomplete="off">
									@csrf
									<input type="hidden" name="equipment_id" value="{{ $equipment_id }}">
									<div class="">
										<label class="fs-bold full-width div-between">
											<span></span>
											<span class="btn green fileinput-button btn-sm">
												<i class="fa fa-plus"></i>
												<span> Add New Sample Photos </span>
												<input type="file" name="file_sample_images[]" id="file_sample_images" multiple="multiple" accept="image/*"> 
											</span>
										</label>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="portlet-body">
						<!-- <form id="frm_input" class="horizontal" method="post" action="{{ route('admin.equipment.upload_sample_images') }}" enctype="multipart/form-data" autocomplete="off">
							@csrf
							<input type="hidden" name="equipment_id" value="{{ $equipment_id }}">
							<div class="form-group">
								<label class="fs-bold full-width div-between">
									<span></span>
									<span class="btn green fileinput-button btn-sm">
										<i class="fa fa-plus"></i>
										<span> Select Images </span>
										<input type="file" name="file_sample_images[]" id="file_sample_images" multiple="multiple" accept="image/*"> 
									</span>
								</label>
							</div>
						</form> -->
						
						<div class="card-block">
							<div class="row" id="sortable">
							</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.modal {
		width: 40vw;
		left: 30vw;
		margin: auto;
	}
	#frm_input {
		display: inline-block;
		margin-left: 10px;
	}
	.image-title {
		height: 20px;
	}
	.div-square-rect {
		height: 0; 
		width: 100%;
		padding-bottom: 100%;
	}
	.img-sample-image {
		width: 100%;
		height: auto;
		object-fit: cover;
		margin: auto 0;
	}


	.stretch-card>.card {
		width: 100%;
		min-width: 100%
	}

	.flex {
		-webkit-box-flex: 1;
		-ms-flex: 1 1 auto;
		flex: 1 1 auto
	}

	@media (max-width:991.98px) {
		.padding {
			padding: 1.5rem
		}
	}

	@media (max-width:767.98px) {
		.padding {
			padding: 1rem
		}
	}

	.padding {
		padding: 3rem !important
	}

	.card-sub {
		cursor: move;
		border: none;
		margin: 5px 0;
		-webkit-box-shadow: 0 0 1px 2px rgba(0, 0, 0, 0.05), 0 -2px 1px -2px rgba(0, 0, 0, 0.04), 0 0 0 -1px rgba(0, 0, 0, 0.05);
		box-shadow: 0 0 1px 2px rgba(0, 0, 0, 0.05), 0 -2px 1px -2px rgba(0, 0, 0, 0.04), 0 0 0 -1px rgba(0, 0, 0, 0.05)
	}

	.card-img-top {
		width: 100%;
		height:180px;
		border-top-left-radius: calc(.25rem - 1px);
		border-top-right-radius: calc(.25rem - 1px)
	}

	.card-block {
		padding: 1.25rem;
		background-color: #fff !important
	}
	.card-text, .card-title {
		padding: 0 10px;
	}
	.div-center-image {
		background-size: contain;
		background-repeat: no-repeat;
		background-position: center;
	}
</style>

<script>


	$(document).ready(function() {

		$("#file_sample_images").on('change', function(){
			// var reader = new FileReader();
			// reader.onload = function (event) {
			// 	var file = event.target;
			// 	$('#uploaded_image').attr('src', file.result);
			// }
			// reader.readAsDataURL(this.files[0]);
			// $('#frm_input').submit();
			var photos = $(`#file_sample_images`).prop('files');
			console.log(photos);
			var formData = new FormData();

			// add assoc key values, this will be posts values
			formData.append( '_token', $('input[name="_token"]').val() ); 
			formData.append( `equipment_id`, $('input[name="equipment_id"]').val() );
			// formData.append( `file_sample_images[]`, photos );
			let TotalFiles = photos.length; //Total files
			let files = $(`#file_sample_images`)[0];
			for (let i = 0; i < TotalFiles; i++) {
				formData.append('files' + i, files.files[i]);
			}
			formData.append('TotalFiles', TotalFiles);
			// console.log(formData);
			// return;
			$('#p_detail').html('Uploading photos');
			$('#modal_progress').modal('show');
			$.ajax({
				type: "POST",
				url: `{{route('admin.equipment.upload_sample_images')}}`,
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success : function(data) {
					fetchSampleImageList();
					$('#modal_progress').modal('hide');
					$('#p_detail').html('Data processing');					
				}
			});
		});

		fetchSampleImageList();

		
		
		// init();
	});

	function fetchSampleImageList() {
		var equipment_id = $('input[name="equipment_id"]').val();
		$.get('/admin/equipment/fetch_sample_image_list/'+equipment_id, function(resp) {
			var htmls = [];
			if (resp.images.length > 0) {
				for(var i=0;i<resp.images.length;i++) {
					var image = resp.images[i];
					htmls.push(`<div class="col-xs-12 col-sm-4 col-md-3" id="${image.id}">
												<div class="card-sub full-bordered">
													<div class="div-square-rect div-center-image" style="background-image: url(${ image.sample_url});">
														<!-- <img src="${ image.sample_url}" class="card-img-top img-fluid"> -->
													</div>
													<div class="card-title div-between">
														<span>${ image.sample_name == '' ? 'No Name' : image.sample_name }</span>
														<button type="button" class="btn sbold green btn-xs" onclick="doOnChangeSampleName(${image.id})">
														<i class="icon-note"></i></button>
													</div>
													<div class="card-text hidden">${ image.date_created }</div>
													<div class="text-center" style="margin-bottom: 10px;">
														<button type="button" class="btn sbold red" onclick="doOnRemoveSampleImage(${ image.id })">Remove</button>
													</div>
												</div>
											</div>`);
				}

			}
			$('#sortable').html(htmls.join(''));

			$("#sortable").sortable({
				// revert       : true,
				// connectWith  : ".sortable",
				update         : function(event,ui){ 
					var sortedIDs = $("#sortable").sortable( "toArray" );
					console.log(sortedIDs);
					$.get('/admin/equipment/sort_sample_images?ids='+sortedIDs.join(','), function(resp) {
						console.log(resp)
					});
				}
    	});
			$("#sortable").disableSelection();
		});
	}

	function doOnChangeSampleName(sample_id) {
		var name = prompt("Enter the Image title", "");
		if (name != null) {
			$.get(`/admin/equipment/change_image_title/${sample_id}/${name}`, function(resp){
				fetchSampleImageList();
			});
		}
	}

	function doOnRemoveSampleImage(sample_id) {
		var resp = confirm('Are you sure you remove this image?');
		if (resp) {
			$.get(`/admin/equipment/remove_sample_image/${sample_id}`, function (resp) {
				fetchSampleImageList();
			});
		}
	}
	




</script>
@endsection