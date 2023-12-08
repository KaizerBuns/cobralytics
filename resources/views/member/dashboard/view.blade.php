@extends('layout')
@section('content')
<div id="cb-dashboard">
	<div class="row">
		<div class="col-lg-6">
			{!! $dashboard_realtime !!}
	    </div>
	    <div class="col-lg-6">
			{!! $dashboard_daily !!}	
	    </div>
	</div>
	<div class="row">
		{!! $dashboard_boxes !!}    
	</div>
	<div class="row">
		<div class="col-lg-6">
			{!! $dashboard_country !!}
		</div>
		<div class="col-lg-6">
			{!! $dashboard_sources !!}
		</div>
	</div>
</div>
@endsection