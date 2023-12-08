<?
use App\Helpers\TableMap;
?>
<div class="row">
	<div class="col-xs-5">
		 <div class="panel">
		 	<!--
            <div class="panel-heading">
                <h3 class="panel-title">Details</h3>
            </div>
            !-->
            <form role="form" class="form-horizontal" action="/member/monitor/?view=update" enctype="multipart/form-data" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="monitor[id]" value="{{ $monitor->id }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-3">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ $monitor->url }}" readonly="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Alert Type</label>
						<div class="col-sm-8">
							<select id="project_id" class="form-control validate[required]" name="monitor[alert]">
								<option value="email" {{ $monitor->alert == 'email' ? 'selected': '' }}>Email</option>
								<option value="sms" {{ $monitor->alert == 'sms' ? 'selected': '' }}>SMS</option>
								<option value="email_sms" {{ $monitor->alert == 'email_sms' ? 'selected': '' }}>SMS & Email</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Alert Email</label>
						<div class="col-sm-8">
							<input type="text" class="form-control validate[required]" value="{{ $monitor->email }}" placeholder="Enter your E-mail Address" name="monitor[email]">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Alert SMS</label>
						<div class="col-sm-8">
							<input type="text" class="form-control validate[required]" value="{{ $monitor->sms }}" placeholder="Enter your Phone Number" name="monitor[sms]">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Created On</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $monitor->created_at) }}" readonly="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Updated On</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $monitor->updated_at) }}" readonly="">
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