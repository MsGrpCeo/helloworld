<script>
	var interests = <?php echo json_encode($interests) ?>;
	var interest_list = <?php echo json_encode($interest_list) ?>;
</script>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title bold">User Detail</h4>
</div>
<div class="modal-body" style="padding-top: 0!important;">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light bordered">
				
				<div class="portlet-body form">
					<form id="frm_input" method="post" action="{{ route('admin.users.save') }}" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" name="user_id" value="{{ $user_id }}">
						<input type="hidden" name="hidden_email" value="{{ $email }}">
						<div class="form-group row">
							<div class="col-md-4">
								<label class="fs-bold">Photo</label>
								<!-- <input type="file" class="form-control" name="photo" id="photo" placeholder=""  accept="image/*">
								<div class="crop_wrapper" style="margin-top: 20px; text-align: center;">
									<img id="uploaded_image" src="{{ ( $photo_url == '' ) ? '' : $photo_url }}" alt="" srcset="" width="150px">
								</div> -->

								<div id="photoCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
									<ol id="photoCarousel_ol" class="carousel-indicators">
									</ol>

									<div id="photoCarousel_div" class="carousel-inner">
									</div>

									<!-- Left and right controls -->
									<a class="left carousel-control" href="#photoCarousel" data-slide="prev">
										<span class="glyphicon glyphicon-chevron-left"></span>
										<span class="sr-only">Previous</span>
									</a>
									<a class="right carousel-control" href="#photoCarousel" data-slide="next">
										<span class="glyphicon glyphicon-chevron-right"></span>
										<span class="sr-only">Next</span>
									</a>
								</div>
								<div class="full-width text-center" style="margin-top: 20px;">
									<button type="button" class="btn sbold red" onclick="doOnDeleteImage()">
										<i class="glyphicon glyphicon-trash"></i>
										<span style="padding-left: 10px;">Delete</span>
									</button>
								</div>

								<div class="form-group" style="margin-top: 30px;">
									<div class="">
										<label class="fs-bold">Interests </label>
										<input type="text" class="form-control bg-white" style="border: none;" name="interests" placeholder="" value="" readonly/>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-6">
										<label class="fs-bold">Member Since </label>
										<p class="">{{ $member_since }}</p>
									</div>
									<div class="col-md-6">
										<label class="fs-bold">Recency </label>
										<p class="">{{ $recency }}</p>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-6">
										<label class="fs-bold">Platform </label>
										<p class="">{{ $platform }}</p>
									</div>
									<div class="col-md-6">
										<label class="fs-bold">Version </label>
										<p class="">{{ $version }}</p>
									</div>
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group row">
									<div class="col-md-6">
										<label class="fs-bold">Email </label>
										<input type="email" class="form-control" name="email" autocomplete="new-password" value="{{ $email }}" required onkeydown="doOnRestore()">
										<span id="email_error" class="red hidden">This email already exists.</span>
									</div>									
									<div class="col-md-6">
										<label class="fs-bold">Password </label>
										<input type="password" class="form-control" name="password" autocomplete="new-password" value="" />
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-3">
										<label class="fs-bold">Name </label>
										<input type="text" class="form-control" name="name" value="{{ $name }}" required>
									</div>
									<div class="col-md-3">
										<label class="fs-bold">Gender </label>
										<select name="gender" class="form-control">
											@foreach($gender_list as $gender_value=>$gender_label)
											<option value="{{ $gender_value }}" {{ $gender == $gender_value ? 'selected' : '' }}>{{ $gender_label }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-3">
										<label class="fs-bold">Phone </label>
										<input type="text" class="form-control" name="phone" value="{{ $phone }}">
									</div>
									<div class="col-md-3">
										<label class="fs-bold">Date of Birth</label>
										<div class="input-group date form_datetime bs-datetime">
											<input type="text" size="16" class="form-control" name="dob" value="{{ $dob }}" readonly style="background: #ffffff;">
											<span class="input-group-addon"><button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button></span>
										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-4">
										<label class="fs-bold">Occupation </label>
										<input type="text" class="form-control" name="occupation" value="{{ $occupation }}">
									</div>
									<div class="col-md-8">
										<label class="fs-bold">Address </label>
										<input type="text" class="form-control" name="address" value="{{ $address }}" >
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-4">
										<label class="fs-bold">Personality1 </label>
										<input type="text" class="form-control" name="personality1" value="{{ $personality1 }}">
									</div>
									<div class="col-md-4">
										<label class="fs-bold">Personality2 </label>
										<input type="text" class="form-control" name="personality2" value="{{ $personality2 }}">
									</div>
									<div class="col-md-4">
										<label class="fs-bold">Personality3 </label>
										<input type="text" class="form-control" name="personality3" value="{{ $personality3 }}">
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-4">
										<label class="fs-bold">Ghost Count </label>
										<input type="text" class="form-control" name="count_ghost" value="{{ $count_ghost }}">
									</div>
									<div class="col-md-4">
										<label class="fs-bold">Swipe Count </label>
										<input type="text" class="form-control" name="swipe_count" value="{{ $swipe_count }}">
									</div>
									<div class="col-md-4">
										<label class="fs-bold">Bonus Swipe Count </label>
										<input type="text" class="form-control" name="swipe_count2" value="{{ $swipe_count2 }}">
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-12">
										<label class="fs-bold">About Me </label>
										<textarea name="about_me" rows="7" class="form-control">{{ $about_me }}</textarea>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-md-4">
										<label class="fs-bold">Search Age Min </label>
										<input type="text" class="form-control" name="search_age_min" value="{{ $search_age_min }}">
									</div>
									<div class="col-md-4">
										<label class="fs-bold">Search Age Max </label>
										<input type="text" class="form-control" name="search_age_max" value="{{ $search_age_max }}">
									</div>
									<div class="col-md-4">
										<label class="fs-bold">DAM </label>
										<input type="text" class="form-control" name="dam" value="{{ $dam }}">
									</div>
								</div>
								<div class="form-group">
									<label>Account Status</label>
									<br/>
									<div class="mt-radio" style="margin-top: 6px; padding-left: 0;display: flex;">
										<label class="mt-radio block-dom rd-danger">
											<input type="radio" class="rd-status" name="act" value="inactive" {{ $act == 0 ? 'checked' : '' }}> Inactive
											<span></span>
										</label> &nbsp;&nbsp;&nbsp;
										<div style="display: block;">
											<label class="mt-radio block-dom rd-green">
												<input type="radio" class="rd-status" name="act" value="active" {{ $act == 1 ? 'checked' : '' }}> Active
												<span></span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<br>
						<div class="text-right">
							<button type="button" class="btn default" data-dismiss="modal">Cancel</button>
							<button type="button" name="btn_submit" class="btn btn-default green" >Save</button>
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
	.both-bordered {
		border-left: 1px solid #a0a0a0;
		border-right: 1px solid #a0a0a0;
	}
	.bootstrap-tagsinput .tag [data-role="remove"]:after {
		content: "";
		padding: 0px 2px;
	}
	.bootstrap-tagsinput .tag [data-role="remove"] {
    margin-left: 0;
	}
	.bootstrap-tagsinput {
		border: none;
	}
	.carousel-indicators {
    bottom: 0px;
    margin-bottom: 5px;
	}
	.carousel-caption {
    position: absolute;
    z-index: 100;
    padding: 0;
	}
	.carousel-img-item {
		display: block;
		width:100%!important;
		height:400px!important;
		object-fit: cover!important;
		object-position: center!important;
	}
</style>
<script>
	var flag = true;
	var tagCategory;

	function doOnKeyUp() {
		var _firstName = $('input[name="firstname"]').val();
		var _lastName = $('input[name="lastname"]').val();
		$('input[name="display_name"]').val(_firstName+' '+_lastName);
	}

	function doOnCheckEmail() {
		var email = $('input[name="email"]').val();
		if ( $('input[name="user_id"]').val() == 'NULL' && email == $('input[name="hidden_email"]').val() ) {
			alert("You should enter email");
			return;
		}
		url = "/admin/users/checkemail/" + email;
		$('button[name="btn_submit"]').attr('disabled', true);
		$.get(url, function(resp) {
			if ( resp.status != 'none' ) {
				$('input[name="email"]').addClass('red-border');
				$('#email_error').removeClass('hidden');
				$('button[name="btn_submit"]').attr('disabled', true);
			}
			else {
				$('button[name="btn_submit"]').attr('disabled', false);
			}
		});
	}
	function doOnRestore() {
		$('input[name="email"]').removeClass('red-border');
		$('#email_error').addClass('hidden');
		$('button[name="btn_submit"]').attr('disabled', false);
	}
	function doOnCheckPassword() {
		var password = $('input[name="password"]').val();
		if ( password == $('input[name="confirm_password"]').val() ) return;
		$('input[name="confirm_password"]').addClass('red-border');
		$('input[name="password"]').addClass('red-border');
		$('#password_error').removeClass('hidden');
	}
	function doOnRestorePassword() {
		$('input[name="password"]').removeClass('red-border');
		$('input[name="confirm_password"]').removeClass('red-border');
		$('#password_error').addClass('hidden');
	}

	$(document).ready(function() {

		$(".form_datetime").datepicker({
			autoclose: true,
			isRTL: App.isRTL(),
			format: "yyyy-mm-dd",
			fontAwesome: true,
			pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left")
		});

		tagCategory = $('input[name="interests"]');
		tagCategory.tagsinput({ itemValue: 'value', itemText: 'text', tagClass: 'bg-dark', interactive: false });
		if ( interests.length > 0 ) {
			for( i=0;i<interests.length;i++ ) {
				tagCategory.tagsinput('add', { value: interest_list[interests[i]], text: interest_list[interests[i]] });
			}
		}
		$('.bootstrap-tagsinput input').prop('readonly', true);
		
		$('button[name="btn_submit"]').click(function() {
			if ( $('input[name="name"]').val() == "" ) {
				alert("You should enter user name");
				// $('button[name="btn_submit"]').attr('disabled', true);
				return;
			}
			var email = $('input[name="email"]').val();
			if ( email == $('input[name="hidden_email"]').val() && $('input[name="user_id"]').val() == "NULL" ) {
				alert("You should enter user email");
				$('input[name="email"]').addClass('red-border');
				// $('#email_error').removeClass('hidden');
				// $('button[name="btn_submit"]').attr('disabled', true);
				return;
			}
			if ( $('input[name="user_id"]').val() == 'NULL' && $('input[name="password"]').val() == "" ) {
				alert("You should enter user password");
				// $('button[name="btn_submit"]').attr('disabled', true);
				return;
			}
			if ( $('input[name="user_id"]').val() == 'NULL' && $('input[name="confirm_password"]').val() == "" ) {
				alert("You should enter user confirm password");
				// $('button[name="btn_submit"]').attr('disabled', true);
				return;
			}
			
			url = "/admin/users/checkemail/" + email + '?user_id=' + $('input[name="user_id"]').val();
			$.get(url, function(resp) {
				if ( resp.status != 'none' ) {
					$('input[name="email"]').addClass('red-border');
					$('#email_error').removeClass('hidden');
					$('button[name="btn_submit"]').attr('disabled', true);
				}
				else {
					$('button[name="btn_submit"]').attr('disabled', false);
					
					$('#frm_input').submit();
				}
			});
			
			
			
		});
		

		$("input[name='photo']").on('change', function(){
			var reader = new FileReader();
			reader.onload = function (event) {
				var file = event.target;
				$('#uploaded_image').attr('src', file.result);
			}
			reader.readAsDataURL(this.files[0]);

		});
    
		fetchPhotoList();
	});

	function fetchPhotoList() {
		$.get('/admin/users/photo_list/'+$('input[name="user_id"]').val(), function(photo_list) {
			console.log(photo_list);
			var olHTMLs = [];
			var imgHTMLs = [];
			if (photo_list.length > 0) {
				for(var i=0;i<photo_list.length;i++) {
					olHTMLs.push(`<li data-target="#photoCarousel" data-slide-to="${i}" class="${i==0 ? 'active' : ''}"></li>`);
					imgHTMLs.push(`<div class="item ${i==0 ? 'active' : ''}">
											<img src="${photo_list[i].url}" filename="${photo_list[i].filename}" class="carousel-img-item">
										</div>`);
				}
			}
			$('#photoCarousel_ol').html(olHTMLs.join(''));
			$('#photoCarousel_div').html(imgHTMLs.join(''));
		});
	}
	function doOnDeleteImage() {
		var filename = $('#photoCarousel_div > .active > img').attr('filename');
		if (filename == '') return;
		var confirmResp = confirm("Are you sure you want to delete this photo?");
		if (confirmResp) {
			$.get('/admin/users/remove_photo/'+$('input[name="user_id"]').val()+'/'+filename, function(resp) {
				fetchPhotoList();
			});
		}		
	}
	
	function getDateFromTimestamp(fromTimestamp) {
    var date_;
    if ( fromTimestamp.toString().length > 10 ) {
      date_ = new Date(fromTimestamp);
    }
    else {
      date_ = new Date(fromTimestamp*1000);
    }
    
    var mm = date_.getMonth() + 1; // getMonth() is zero-based
    var dd = date_.getDate();
    var hh = date_.getHours() < 10 ? "0" + date_.getHours() : date_.getHours();
    var min = date_.getMinutes() < 10 ? "0" + date_.getMinutes() : date_.getMinutes();
    var ss = date_.getSeconds() < 10 ? "0" + date_.getSeconds() : date_.getSeconds();

    return `${(dd>9 ? '' : '0') + dd}-${(mm>9 ? '' : '0') + mm}-${date_.getFullYear()} ${hh}:${min}:${ss}`;
  }
</script>