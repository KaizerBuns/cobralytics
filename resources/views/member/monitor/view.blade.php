@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="tab-base">
			<ul class="nav nav-tabs">
				<li class="active">
					<a aria-expanded="true" data-toggle="tab" href="#tab-1">Details</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="tab-1" class="tab-pane fade active in">
					@include('member.monitor.details')
				</div>
			</div>
		</div>
	</div>
</div>
@endsection