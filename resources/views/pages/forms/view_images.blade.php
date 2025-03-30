<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title bold">Form Submitted Images</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light bordered">
				
				<div class="portlet-body form">
					<form id="frm_input" method="post" action="" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" name="form_id" value="{{ $form_id }}">
						<div class="form-group">
							<label class="fs-bold"></label>
						</div>
						<div class="form-group row" id="div_image_list">

						</div>
						<br>
						<div class="text-right">
							<button type="button" class="btn default" data-dismiss="modal">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.modal {
		width: 70vw;
		left: 15vw;
		margin: auto;
	}
	#div_image_list {
		min-height: 50vh;
	}
	.image-thumb-wrap {
		/* background-color: var(--gray-semi-color); */
		border: 2px solid var(--gray-semi-color);;
		position: relative;
	}
	.z-bottom-1 {
		z-index: 1;
	}
	.sample-thumbnail {
		width: 35%;
		padding-bottom: 35%;
		position: absolute;
		top: 0;
		left: 0;
		/* border: 2px solid var(--gray-semi-color); */
		z-index: 10;
		border-radius: 5px!important;
		box-shadow: 5px 5px 5px #8b888899;
	}
	.image-bottom-wrap {
		height: 30px;
	}
	.span-sample-name {
		width: calc(100% - 30px);
		padding-top: 5px;
    padding-left: 3px;
		display: block;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
</style>
<script>
	var flag = true;
	$(document).ready(function() {
		fetchImageList();

	});
	function fetchImageList() {
		var form_id = $('input[name="form_id"]').val();
		$.get('/admin/forms/fetch_images/'+form_id, function(resp) {
			var htmls = [];
			if (resp.images.length > 0) {
				for(var i=0;i<resp.images.length;i++) {
					var image = resp.images[i];
					htmls.push(`<div class="col-md-3" style="padding: 4px;">
												<div class="full-bordered image-thumb-wrap">
													<div class="fit-square z-bottom-1" id="div_photo_${image.id}" style="background-image: url(${image.photo_url});"></div>
													<div class="fit-square sample-thumbnail" style="background-image: url(${image.sample_url}"></div>
													<div class="image-bottom-wrap div-between" style="margin-top: 4px;">
														<span class="span-sample-name" title="${image.sample_name}">${image.sample_name}</span>
														<span class="btn green fileinput-button btn-sm" title="Update Photo">
															<i class="fa fa-pencil"></i>
															<input type="file" data-name="file_update_image" photo-id="${image.id}" onchange="doOnChangeImage(this)" accept="image/*"> 
														</span>
													</div>
												</div>
											</div>`);
				}
			}
			$('#div_image_list').html(htmls.join(''));
		});
	}
	function doOnChangeImage(dom) {
		var photo_id = $(dom).attr('photo-id');
		var reader = new FileReader();
		reader.onload = function (event) {
			var file = event.target;
			$(`#div_photo_${photo_id}`).css('background-image', `url(${file.result})`);
			
			var formData = new FormData();
			formData.append( '_token', $('input[name="_token"]').val() ); 
			formData.append( `photo_id`, photo_id );
			formData.append( `photo`, dom.files[0] );
			$('#modal_progress').modal('show');
			$.ajax({
				type: "POST",
				url: `{{route('admin.forms.change_equip_image')}}`,
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success : function(data) {
					fetchImageList();
					$('#modal_progress').modal('hide');
				}
			});
		}
		reader.readAsDataURL(dom.files[0]);
	}
</script>