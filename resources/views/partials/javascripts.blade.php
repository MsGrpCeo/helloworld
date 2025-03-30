
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ url('public/admin/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}" type="text/javascript"></script>

<script src="{{ url('public/admin/global/plugins/counterup/jquery.waypoints.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/counterup/jquery.counterup.min.js') }}" type="text/javascript"></script>

<script src="{{ url('public/admin/global/plugins/flot/jquery.flot.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/flot/jquery.flot.resize.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/flot/jquery.flot.categories.min.js') }}" type="text/javascript"></script>

<script src="{{ url('public/admin/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/backstretch/jquery.backstretch.min.js') }}" type="text/javascript"></script>

<script src="{{ url('public/admin/global/plugins/bootstrap-modal/js/bootstrap-modalmanager.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-modal/js/bootstrap-modal.js') }}" type="text/javascript"></script>

<script src="{{ url('public/admin/global/plugins/jcrop/js/jquery.color.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/jcrop/js/jquery.Jcrop.js') }}" type="text/javascript"></script>

<script src="{{ url('public/admin/global/plugins/icheck/icheck.min.js') }}" type="text/javascript"></script>

<script src="{{ url('public/admin/global/plugins/jquery-nestable/jquery.nestable.js') }}" type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('public/admin/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/scripts/loadingoverlay.min.js') }}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('public/admin/pages/scripts/pages.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/pages/scripts/multiselect.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/pages/scripts/date-time-pickers.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/pages/scripts/form-icheck.js') }}" type="text/javascript"></script>

<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ url('public/admin/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/layouts/global/scripts/quick-nav.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->


<script src="{{ url('public/admin/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-markdown/lib/markdown.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js') }}" type="text/javascript"></script>
<script src="{{ url('public/admin/global/plugins/bootstrap-summernote/summernote.min.js') }}" type="text/javascript"></script>


<script src="{{ url('public/js/jquery.printArea.js') }}" type="text/javascript"></script>

<script>
$(function() {
  'use strict'
  // $.get('/admin/get_auth', function(resp) {
  //   if (resp.result == false) {
  //     window.location.reload();
  //   }
  // });
  // console.log(12345)
});
</script>
@yield('javascript')