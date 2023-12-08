<? use App\Helpers\TableMap; ?>
<div class="row">
	<div class="col-xs-5">
		 <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">Details</h3>
            </div>
            <form id="frm-service" class="form-horizontal widget-form" role="form" action="{{ url('/member/service/?view=save') }}" enctype="multipart/form-data" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="service[id]" value="{{ $service['id'] or 0 }}">

	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-xs-4">Name</label>
						<div class="col-sm-6">
							<input type="text" class="form-control validate[required]" name="service[name]" value="{{ $service['name'] or ''}}">
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
						<label class="control-label col-xs-4">Created On</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $service['created_at']) }}" readonly="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-4">Updated On</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $service['updated_at']) }}" readonly="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-xs-4">
							Monetizer
						</label>
						<div class="col-sm-6">
							<label>
								<input class="ace ace-switch ace-switch-3" type="checkbox" name="service[is_monetizer]" value='1' <?=isset($service->is_monetizer) && $service->is_monetizer ? 'checked' : ''?>>
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
	 <div class="col-xs-7">

	</div>
</div>
<script>
	$(function(){
		$('#frm-service').validationEngine('attach', { promptPosition : "topLeft"});
	});
</script>