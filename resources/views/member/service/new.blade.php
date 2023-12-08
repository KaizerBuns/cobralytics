@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
		 <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-service" class="form-horizontal" role="form" action="{{ url('/member/service/?view=save') }}" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-4">Name</label>
						<div class="col-sm-4">
							<input type="text" name="service[name]" class="form-control validate[required]" value="" placeholder="Service name">
						</div>
					</div>
					{{--
						<div class="form-group">
						<label class="control-label col-sm-4">Offer Group</label>
						<div class="col-sm-4">
							<select class="form-control cobra-select2" name="service[offer_group]">
								<option value="">Select one</option>
								@foreach($offer_groups as $g)
									<option value="{{ $g->group_name }}">{{ $g->group_name }} ( {{ $g->total_offers }} Offers)</option>
								@endforeach
							</select>
							<p class="help-block">Add offers to service as rules</p>
						</div>
					</div>
					--}}
					<div class="form-group">
						<label class="control-label col-sm-4">
							 Monetizer Service
						</label>
						<div class="col-sm-4">
							<label>
								<input class="ace ace-switch ace-switch-3" type="checkbox" name="service[is_monetizer]" value='1'>
								<span class="lbl"></span>
							</label>
						</div>
					</div>
	            </div>
	            <div class="panel-footer text-right">
					<button type="submit" class="btn btn-info">Submit</button>
				</div>
			</form>
        </div>
	</div>
</div>
<script>
	jQuery(function($) {
		$(function() {
			$('#frm-service').validationEngine('attach', { promptPosition : "topRight"});
		});
	});
</script>
@endsection