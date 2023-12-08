@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$header['desc']?></h3>
            </div>
            <form id="frm-pixel" class="form-horizontal" role="form" action="{{ url('/member/pixel/?view=save') }}" method="POST">
	            <div class="panel-body">	
					<input type="hidden" name="pixel[id]" value="{{ $pixel['id'] or 0}}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="form-group">
						<label class="control-label col-sm-4">Name</label>
						<div class="col-sm-6">
							<input type="text" name="pixel[name]" class="form-control validate[required]" value="{{ $pixel['name'] or '' }}" placeholder="Pixel Name">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Type</label>
						<div class="col-sm-6">
							<select name="pixel[type]" class="form-control validate[required]">
								<option value="image">IMG</option>
								<option value="iframe">IFRAME</option>
								<option value="javascript">JavaScript</option>
								<option value="s2s">Server2Server</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">						 
							Pixel
						</label>	
						<div class="col-sm-6">
							<textarea name="pixel[pixel]" class="form-control validate[required]" style="height:100px"><?= isset($pixel['pixel']) ? stripslashes($pixel['pixel']) : '' ?></textarea>
						</div>	
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">						 
							&nbsp;
						</label>	
						<div class="col-sm-6">
							<div class="checkbox inline">
								<label>
									<input type="checkbox" name="pixel[scope]" value="1" class="ace ace-switch ace-switch-3" <?= isset($pixel['scope']) && $pixel['scope'] ? 'checked' : '' ?>>
									<span class="lbl">&nbsp;&nbsp;Fire on all campaign conversions</span>
								</label>
							</div>
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
			$('#frm-pixel').validationEngine('attach', { promptPosition : "topRight"});				
		});
	});	
</script>
@endsection