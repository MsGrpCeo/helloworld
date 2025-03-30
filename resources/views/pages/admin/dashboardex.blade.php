@extends('layouts.app')

@section('content')
<style>
  .store-title {
    font-size: 32px;
    font-weight: bold;
    color: #3e4e4e;
  }
</style>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<!-- BEGIN CONTENT BODY -->
	<div class="page-content">
		<!-- BEGIN DASHBOARD STATS 1-->
		<div class="row">
			<div class="col-md-12">
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet light bordered dashboard">
					<div class="portlet-title">
						<div class="caption font-dark">
							<span class="caption-subject bold uppercase hidden"> Dashboard </span>
						</div>
					</div>
					<div class="portlet-body">
						<div class="row">
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat dashboard-stat-v2 green" href="#">
                  <div class="visual">
                    <i class="fa fa-users"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      <span data-counter="counterup" data-value="{{ $sales_agents }}"></span>
                    </div>
                    <div class="desc"> 
                      <a href="{{ route('admin.agents.list') }}" class="white-color" >Sales Agents</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat dashboard-stat-v2 purple" href="#">
                  <div class="visual">
                    <i class="fa fa-send"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      <span data-counter="counterup" data-value="{{ $contacts }}"></span>
                    </div>
                    <div class="desc">
                      <a href="{{ route('admin.contact.list') }}" class="white-color" >Contacts</a>
                    </div>
                  </div>
                </div>
              </div>
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat dashboard-stat-v2 blue" href="#">
                  <div class="visual">
                    <i class="fa fa-truck"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      <span data-counter="counterup" data-value="{{ $equipments }}"></span>
                    </div>
                    <div class="desc">
                      <a href="{{ route('admin.equipment.list') }}" class="white-color" >Equipment Types</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat dashboard-stat-v2 yellow" href="#">
                  <div class="visual">
                    <i class="fa fa-file-text"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      <span data-counter="counterup" data-value="{{ $forms }}"></span>
                    </div>
                    <div class="desc">
                      <a href="{{ route('admin.forms.list') }}" class="white-color" >Forms Submitted</a>
                    </div>
                  </div>
                </div>
              </div>
						</div>
					</div>	
          <div class="clearfix"></div>
					<div class="portlet-body">
            <div class="row">
            </div>
            
          </div>	
          
          <div class="clearfix div-sep"></div>
          
				</div>
				
			</div>
		</div>
		<div class="clearfix"></div>
		
	</div>
</div>

@endsection
