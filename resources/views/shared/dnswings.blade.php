@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
		 <div class="panel">
            <div class="panel-heading">
                <div class="panel-control">
                	@include('partials.paginate')
                </div>
                <h3 class="panel-title">Manage</h3>
            </div>
            <div class="panel-body">
            	<form role="form" class="form-horizontal" action="<?=$_SERVER['REQUEST_URI']?>" method="GET">
            		<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="view" value="{{ $_GET['view'] }}">
					<input type="hidden" name="section" value="{{ $_GET['section'] or '' }}">
					<input type="hidden" id="search-date_start" name="search[date_start]" value="{{ $_REQUEST['search']['date_start'] or '' }}">
					<input type="hidden" id="search-date_end" name="search[date_end]" value="{{ $_REQUEST['search']['date_end'] or '' }}">
					<div class="form-group">
						<label class="col-sm-3 control-label">Expires Range</label>
						<div class="col-sm-4">
							<div id="searchdate" class="selectbox">
								<i class="fa fa-calendar fa-lg"></i>
								<span><?php echo date("M j, Y", strtotime($_REQUEST['search']['date_start'])); ?> - <?php echo date("M j, Y", strtotime($_REQUEST['search']['date_end'])); ?></span> 
								<b class="caret"></b>
							</div>
						</div>
					</div><!-- /.form group -->
					<div class="form-group">
						<label class="col-sm-3 control-label">Search</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="{{ $request->input('search')['name'] or '' }}" name="search[name]">
						</div>
					</div><!-- /.form group -->
					<div class="form-group">
						<label class="control-label col-sm-3">Type</label>
						<div class="col-sm-4">
							<select class="form-control validate[required]" name="search[type]">
								<option value="">Any</option>
								<option value="domain" {{ $request->input('search')['type'] == 'domain' ? 'selected' : '' }}>Domain</option>
								<option value="ns" {{ $request->input('search')['type'] == 'ns' ? 'selected' : '' }}>Nameserver</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">&nbsp;</label>
						<div class="col-sm-4">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</div>	
				</form>
				<hr>
				{!! $tablemap !!}
            </div>
        </div>
	</div>
</div>
<script type="text/javascript">
jQuery(function($){
	$(function() {
		$('#searchdate').daterangepicker({
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
					'Last 7 Days': [moment().subtract('days', 6), moment()],
					'Last 30 Days': [moment().subtract('days', 29), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
				},
				startDate: '<?=$_REQUEST['search']['date_start']?>',
				endDate: '<?=$_REQUEST['search']['date_end']?>',
				format: 'YYYY-MM-DD'
			},
			function(start, end) {
				$('#search-date_start').val(start.format('YYYY-MM-DD'));
				$('#search-date_end').val(end.format('YYYY-MM-DD'));
				$('#searchdate span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
			}
		);
	});
});	
</script>
@endsection