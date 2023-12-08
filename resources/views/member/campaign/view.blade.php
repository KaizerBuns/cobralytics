@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="tab-base">
			<ul id="cb-tab-menu" class="nav nav-tabs">
				<li class="active">
					<a aria-expanded="true" data-toggle="tab" href="#tab-1">Dashboard</a>
				</li>
				<li>
					<a aria-expanded="true" data-toggle="tab" href="#tab-2">Manage</a>
				</li>
				<li>
					<a aria-expanded="true" data-toggle="tab" href="#tab-3">Tracking</a>
				</li>
				<li>
					<a aria-expanded="true" data-toggle="tab" href="#tab-4">Edit</a>
				</li>
				<li>
					<a href="/member/report/?view=overview&tab=campaign&limit=100&page=1&filter[campaign]={{ $campaign->id }}"><i class="fa fa-bar-chart-o"></i> View in Reports</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="tab-1" class="tab-pane active">
					<div class="row">
						<div class="col-lg-12">
							{!! $dashboard_boxes !!}
					    </div>
					    <div class="col-lg-12">
							{!! $dashboard_daily !!}
					    </div>
					</div>
				</div>
				<div id="tab-2" class="tab-pane">
					@include('shared.rules')
					@if(!$campaign->is_redirect()) 
						@include('shared.rotators')
					@endif
				</div>
				<div id="tab-3" class="tab-pane">
					@include('member.campaign.tracking')
				</div>
				<div id="tab-4" class="tab-pane">
					@include('member.campaign.form')
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
	$('#cb-tab-menu a[href="#tab-2"]').tab('show');
});
</script>
@endsection