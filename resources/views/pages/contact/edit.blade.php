<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title bold">Contact Detail</h4>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light bordered">
				
				<div class="portlet-body form">
					<form id="frm_input" method="post" action="{{ route('admin.contact.save') }}" enctype="multipart/form-data" autocomplete="0">
						@csrf
						<input type="hidden" name="user_id" value="{{ $user_id }}">
						<div class="form-group">
							<label class="fs-bold">Name</label>
							<input type="text" class="form-control" name="name" value="{{ $name }}" required/>
						</div>
						<div class="form-group">
							<label class="fs-bold">Email</label>
							<input type="text" class="form-control" name="email" value="{{ $email }}" required/>
						</div>
						<div class="form-group">
							<label class="fs-bold">Phone</label>
							<input type="text" class="form-control" name="phone" value="{{ $phone }}" required/>
						</div>
						
						<br>
						<div class="text-right">
							<button type="button" class="btn default" data-dismiss="modal">Close</button>
							<button type="submit" name="btn_submit" class="btn btn-default green" >Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
	.modal {
		width: 60vw;
		left: 20vw;
		margin: auto;
	}
</style>