@extends('layout')
@section('content')
{!! $dashboard_boxes !!}
<div class="row">
	<div class="col-xs-12">
		<div class="tab-base tab-stacked-left" id="overview-tabs">
			<ul class="nav nav-tabs">
				<li class="{{ ($request->input('tab') == 'project' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","project",$query_string) }}">
						<span>Project</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'campaign' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","campaign",$query_string) }}">
						<span>Campaigns</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'domain' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","domain",$query_string) }}">
						<span>Domains</span>
					</a>
				</li>
				<!--
				<li class="{{ ($request->input('tab') == 'page' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","page",$query_string) }}">
						<span>Pages</span>
					</a>
				</li>
				!-->
				<li class="{{ ($request->input('tab') == 'source' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","source",$query_string) }}">
						<span>Traffic Sources</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'traffic' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","traffic",$query_string) }}">
						<span>Traffic Type</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'offer' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","offer",$query_string) }}">
						<span>Offers</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'lp' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","lp",$query_string) }}">
						<span>LandingPages</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'ref' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","ref",$query_string) }}">
						<span>Referers</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'device' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","device",$query_string) }}">
						<span>Devices</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'country' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","country",$query_string) }}">
						<span>Countries</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'subid' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","subid",$query_string) }}">
						<span>SubIDs</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'day' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","day",$query_string) }}">
						<span>By Date</span>
					</a>
				</li>
				<li class="{{ ($request->input('tab') == 'hour' ? 'active' : '') }}">
					<a href="{{ str_replace("%TAB%","hour",$query_string) }}">
						<span>By Hour</span>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="pull-left">
					<h4>{!! $breadcrumbs !!}</h4>
				</div>
				<div id="reportrange" class="selectbox pull-right">
					<form id="frm-overview" action="/member/report/" method="GET">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="view" value="overview">
						<input type="hidden" id="tab" name="tab" value="{{ $request->input('tab') }}">
						<input type="hidden" name="report[start]" id="report-start" value="{{ $request->input('report')['start'] }}">
						<input type="hidden" name="report[end]" id="report-end" value="{{ $request->input('report')['end'] }}">
						<i class="fa fa-calendar fa-lg"></i>
						<span><?= date("M j, Y", strtotime($request->input('report')['start'])) ?> - <?= date("M j, Y", strtotime($request->input('report')['end'])); ?></span> 
						<b class="caret"></b>
					</form>
				</div>
				<p>
					<div class="row">
						<div class="col-xs-12">
							{!! $dashboard_daily !!}
						</div>
					</div>
				</p>
				<p>{!!  $tablemap !!}</p>
				<div class="pull-right">@include('partials.paginate')</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#reportrange').daterangepicker({
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
					'Last 7 Days': [moment().subtract('days', 6), moment()],
					'Last 30 Days': [moment().subtract('days', 29), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
				},
				startDate: '{{ $request->input('report')['start'] }}',
				endDate: '{{ $request->input('report')['end'] }}',
				format: 'YYYY-MM-DD'
			},
			function(start, end) {
				$('#report-start').val(start.format('YYYY-MM-DD'));
				$('#report-end').val(end.format('YYYY-MM-DD'));
				$('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
				$('#frm-overview').submit();
			}
		);
	});

	var report_tab = function(tab) {
		$('#tab').val(tab);
		$('#frm-overview').submit();
	}
</script>
@endsection	