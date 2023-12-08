@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-vertical" class="form-horizontal" role="form" action="{{ url('/member/vertical/?view=save') }}" method="POST">
				<input type="hidden" name="vertical[id]" value="{{ $vertical['id'] or 0}}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-4">Name</label>
						<div class="col-sm-4">
							<input type="text" name="vertical[name]" class="form-control validate[required]" value="{{ $vertical['name'] or '' }}" placeholder="Name">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">						 
							 Internal
						</label>	
						<div class="col-sm-4">
							<label>
								<input class="ace ace-switch ace-switch-3" type="checkbox" name="vertical[internal]" value='1' <?= isset($vertical['internal']) && $vertical['internal'] ? 'checked' : '' ?>>
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
			$('#frm-vertical').validationEngine('attach', { promptPosition : "topRight"});	
		});
	});	
</script>
@endsection