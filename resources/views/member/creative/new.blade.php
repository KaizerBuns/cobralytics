@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-creative" class="form-horizontal" role="form" action="{{ url('/member/creative/?view=save') }}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="creative[id]" value="{{ $creative['id'] or 0 }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
	            <div class="panel-body">
					<div class="form-group">
						<label class="control-label col-sm-4">Type</label>
						<div class="col-sm-4">
							<select name="creative[type]" class="form-control" id="creative-type">
								<option value="image" selected="selected">Image</option>
								<option value="template">Template</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Upload</label>
						<div class="col-sm-4">
							<input type="file" name="fileupload[]" multiple="multiple">
							<p class="help-block">JPEG, GIF, PNG, ZIP</p>
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
			$('#frm-creative').validationEngine('attach', { promptPosition : "topRight"});
			$('#creative-type').val("{{ $creative['type'] or 'image'}}");
		});
	});	
</script>
@endsection
