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
                    <i class="icon-home"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      @if ( Auth::user()->utype == 1 )
                      <span data-counter="counterup" data-value="{{ $total_store_count }}">{{ $total_store_count }}</span>
                      @endif
                    </div>
                    <div class="desc"> 
                      @if ( Auth::user()->utype == 1 )  
                        <a href="{{ route('admin.storelist') }}" class="white-color" >Stores</a>
                      @else
                        <img src="{{ $store_string }}" class="table-cell-img" style="width: 32px!important;" alt="" srcset="">
                        <a href="{{ route('admin.store.profile') }}" class="white-color" >Store detail</a>
                      @endif
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat dashboard-stat-v2 purple" href="#">
                  <div class="visual">
                    <i class="icon-users"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      <span data-counter="counterup" data-value="{{ $total_user_count }}"></span>
                    </div>
                    <div class="desc">
                      <a href="{{ route('admin.store.userlist') }}" class="white-color" >Users</a>
                    </div>
                  </div>
                </div>
              </div>
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat dashboard-stat-v2 blue" href="#">
                  <div class="visual">
                    <i class="icon-user-following"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      <span data-counter="counterup" data-value="{{ $total_agent_count }}"></span>
                    </div>
                    <div class="desc">
                      <a href="{{ route('admin.store.agentlist') }}" class="white-color" >Agents</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="dashboard-stat dashboard-stat-v2 yellow" href="#">
                  <div class="visual">
                    <i class="icon-bell"></i>
                  </div>
                  <div class="details">
                    <div class="number">
                      <span data-counter="counterup" data-value="{{ $total_notifications_count }}">0</span>
                    </div>
                    <div class="desc">
                      <a href="{{ route('admin.store.notificationlist') }}" class="white-color" >Notifications</a>
                    </div>
                  </div>
                </div>
              </div>
						</div>
					</div>	
          <div class="clearfix"></div>
					<div class="portlet-body">
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                @if ( Auth::user()->utype == 1 )
                <div class="note note-active box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Active</h4>
                  <p class="p-users">{{ count($total_store_info['active']) }}</p>
                </div>
                <div class="note note-suspended box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Inactive</h4>
                  <p class="p-users">{{ count($total_store_info['inactive']) }}</p>
                </div>
                @else
                <div class="note box-wrap full-width" style="height: 42px;">
                  <h4 class="fs-bold" style="position: absolute;right: 0;">Current Status</h4>
                </div>
                <div class="full-width text-right">
                  <a href="{{ route('admin.store.profile') }}" class="btn {{ ( Auth::user()->act == 0 ? 'note note-inactive' : 'green' ) }}" style="padding:12px;">{{ ( Auth::user()->act == 0 ? 'Pending Info' : 'Active' ) }}</a>
                </div>
                
                @endif
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="note note-active box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Active</h4>
                  <p class="p-users">{{ count($total_user_info['active']) }}</p>
                </div>
                <div class="note note-suspended box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Inactive</h4>
                  <p class="p-users">{{ count($total_user_info['inactive']) }}</p>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="note note-active box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Active</h4>
                  <p class="p-users">{{ count($total_agent_info['active']) }}</p>
                </div>
                <div class="note note-suspended box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Inactive</h4>
                  <p class="p-users">{{ count($total_agent_info['inactive']) }}</p>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="note note-active box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Sent</h4>
                  <p class="p-users">{{ count($total_notifications['sent']) }}</p>
                </div>
                <div class="note note-suspended box-wrap">
                  <h4 class="block pa-0 ma-0 fs-bold">Scheduled</h4>
                  <p class="p-users">{{ count($total_notifications['scheduled']) }}</p>
                </div>
              </div>
            </div>
            
          </div>	
          
          <div class="clearfix div-sep"></div>
          
          <div class="portlet box blue ">
            <div class="portlet-title">
              <div class="caption">
                <i class="icon-bell"></i> Store Hours </div>
            </div>
            @if (isset($week_info))
            <div class="portlet-body form-wrapper">
              @if ( \Auth::user()->utype == 1 )
              <form class="form-horizontal">
                <div class="form-group" style="margin-left: 20px;">
                  <label class="col-md-3 control-label fs-bold">Store:</label>
                  <div class="col-md-9">
                    <select id="select_company_list" class="form-control" onchange="doOnCompanyChange(this)">
                      @foreach($store_list as $store)
                        @if ( $store['act'] == 1 )
                        <option value="{{ $store['id'] }}">{{ $store['name']}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>
              </form>						
              @endif
              <form role="form">
                <div id="notification_list_wrap" class="form-body">
                  @if ( \Auth::user()->utype != 1 )
                    @foreach($week_info as $i=>$week)
                    <div class="row">
                      <div class="col-md-3 pr-0">
                        <div class="day-of-week">
                          <button type="button" class="btn full-width note note-{{ ( $i < 6 ) ? 'info' : 'danger' }} mb-1">{{ $week['day_str'] }}</button>
                        </div>
                      </div>
                      <div class="col-md-9 pl-0">
                        @if ($week['is_opens'])
                          <button type="button" class="btn full-width pa-0 mb-1" style="margin-top:-1px;">
                            <div class="alert alert-success ma-0">
                              <strong>Opens:</strong> <span class="span-start">{{ $week['start'] }}</span> ~ <span class="span-end">{{ $week['end'] }}</span>
                            </div>
                          </button>
                        @else
                          <button type="button" class="btn full-width pa-0 mb-1" style="margin-top:-1px;">
                            <div class="alert alert-danger ma-0">
                              <strong>Closed</strong>
                            </div>
                          </button>
                        @endif
                      </div>
                    </div>
                    @endforeach
                  @endif
                </div>
              </form>
            </div>
            @endif
          </div>
				</div>
				
			</div>
		</div>
		<div class="clearfix"></div>
		
	</div>
</div>

<script>
  var g_date = '<?php echo date('Y-m-d'); ?>';
  var is_super = '<?php echo \Auth::user()->utype == 1 ? 1 : 0; ?>';
  var request_url = '/admin/dashboard/week_info/';
  $(document).ready(function() {
    changeLocalTime();
    if (is_super == 1) {
      var init_store = $('#select_company_list').val();
      requestWeekInfo(init_store);
    }    
  });
  function doOnCompanyChange(selectDom) {
    requestWeekInfo(selectDom.value);
  }
  function requestWeekInfo( store_id ) {
    $('#notification_list_wrap').html('');
    $.get(request_url+store_id, function(resp) {
      var week_info = resp.data;
      
      var contentHTML = '';
      for(week_index in week_info) {
        var day_data = week_info[week_index];
        var dayHTML = `<div class="row">
                        <div class="col-md-3 pr-0">
                          <div class="day-of-week">
                            <button type="button" class="btn full-width note note-${ ( week_index < 6 ) ? 'info' : 'danger' } mb-1">${ day_data.day_str }</button>
                          </div>
                        </div>
                        <div class="col-md-9 pl-0">` + 
                          ( ( day_data.is_opens ) ? 
                            `<button type="button" class="btn full-width pa-0 mb-1" style="margin-top:-1px;">
                              <div class="alert alert-success ma-0">
                                <strong>Opens:</strong> <span class="span-start">${ changeTime(day_data.start) }</span> ~ <span class="span-end">${ changeTime(day_data.end ) }</span>
                              </div>
                            </button>`
                          :
                            `<button type="button" class="btn full-width pa-0 mb-1" style="margin-top:-1px;">
                              <div class="alert alert-danger ma-0">
                                <strong>Closed</strong>
                              </div>
                            </button>`
                          ) + 
                        `</div>
                      </div>`;
        contentHTML += dayHTML;
      };
      $('#notification_list_wrap').html(contentHTML);
    });
  }
	function changeLocalTime() {
    $('.span-start').each((i, dom)=>{
      var utc_time = dom.innerHTML;
      dom.innerHTML = changeTime(utc_time);
    });
    $('.span-end').each((i, dom)=>{
      var utc_time = dom.innerHTML;
      dom.innerHTML = changeTime(utc_time);
    });
  }
  function changeTime(utc_time) {
    var date = new Date(`${g_date} ${utc_time} UTC`);
    var local_time = ( date.getHours() < 10 ? `0${date.getHours()}` : date.getHours() ) + ':' + (date.getMinutes() < 10 ? `0${date.getMinutes()}` : date.getMinutes()) + ':00';
    return local_time;
  }
</script>
@endsection
