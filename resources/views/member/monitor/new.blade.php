@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-monitor" role="form" class="form-horizontal" action="/member/monitor/?view=save" enctype="multipart/form-data" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="submit" value="1">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-3">List</label>
						<div class="col-sm-4">
							<textarea class="form-control validate[required]" rows="4" id="monitor-list" name="monitor[list]" placeholder="Enter a list - 1 per line"></textarea>
							<p class="help-block">URL, Domain, IP</p>
						</div>
					</div>
		            <div class="form-group">
						<label class="control-label col-sm-3">Alert Type</label>
						<div class="col-sm-4">
							<select id="project_id" class="form-control validate[required]" name="monitor[alert]">
								<option value="email">Email</option>
								<option value="sms">SMS</option>
								<option value="email_sms">SMS & Email</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Alert Email</label>
						<div class="col-sm-4">
							<input type="text" class="form-control validate[required]" value="{{ $user->email }}" placeholder="Enter your E-mail Address" name="monitor[email]">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Alert SMS</label>
						<div class="col-sm-4">
							<input type="text" class="form-control validate[required]" value="" placeholder="Enter your Phone Number" name="monitor[sms]">
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
			$('#frm-monitor').validationEngine('attach', { promptPosition : "topRight"});
		});
	});	
</script>
@endsection