@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-project" role="form" class="form-horizontal" action="{{ url('/member/project/?view=save') }}" method="POST">
            	<input type="hidden" name="project[id]" value="{{ $project['id'] or 0 }}">
            	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	            <div class="panel-body">
		            <div class="form-group">
						<label class="control-label col-sm-3">Name</label>
						<div class="col-sm-4">
							<input type="text" class="form-control validate[required]" value="{{ $project['name'] or '' }}" name="project[name]" placeholder="Name">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Description</label>
						<div class="col-sm-4">
							<textarea class="form-control" rows="4" name="project[description]" placeholder="Description">{{ $project['description'] or '' }}</textarea>
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
			$('#frm-project').validationEngine('attach', { promptPosition : "topRight"});	
		});
	});	
</script>
@endsection