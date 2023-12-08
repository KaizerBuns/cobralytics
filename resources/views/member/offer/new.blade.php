@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-offer" class="form-horizontal" role="form" action="{{ url('/member/offer/?view=save') }}" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="offer[id]" value="{{ $offer->id or 0 }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-4">Name</label>
						<div class="col-sm-4">
							<input type="text" name="offer[name]" class="form-control validate[required]" value="{{ $offer->name or '' }}" placeholder="Offer Name">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">
							Vertical
						</label>
						<div class="col-sm-4">
							<select class="form-control cobra-select2" name="offer[vertical_id]">
								@foreach($verticals as $v)
									<option value="{{ $v->id }}">{{ $v->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">
							Advertiser
						</label>
						<div class="col-sm-4">
							<select class="form-control cobra-select2" name="offer[advertiser_id]">
								@foreach($advertisers as $v)
									<option value="{{ $v->id }}">{{ $v->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Group</label>
						<div class="col-sm-4">
							<input type="text" name="offer[group_name]" class="form-control validate[required]" value="{{ $offer->group_name or ''}}" placeholder="Offer Group">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">
							Revenue
						</label>
						<div class="col-sm-4">
							<input type="text" name="offer[revenue]" class="form-control validate[required]" value="{{ $offer->revenue or ''}}" placeholder="0.00">
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
			$('#frm-offer').validationEngine('attach', { promptPosition : "topRight"});
		});
	});
</script>
@endsection