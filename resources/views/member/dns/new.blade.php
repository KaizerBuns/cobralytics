@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
			<form id='frm-dns' class="form-horizontal" role="form" action="{{ url('/member/dns/?view=save') }}" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dns[domain_id]" value="{{ $object->id }}">
				<input type="hidden" name="dns[id]" value="0">
				<input type="hidden" name="object_id" value="{{ $object->id }}">
				<div class="panel-body">
					<div class="form-group">
						<label class="control-label col-sm-3">Name (subdomain)</label>
						<div class="col-sm-4">
							<input type="text" name="dns[name]" class="form-control" value="" placeholder="{subdomain}.{{ $object->name }}">
							<p class="help-block">
								Leave blank for no subdomain.<br>
								<strong>Note:</strong> If you're adding a subdomain and wish to track it <a href="/member/source/?view=new">Click Here</a> to add it as seperate domain.
							</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Type</label>
						<div class="col-sm-4">
							<select id="dns-type" name="dns[type]" class="form-control">
								<option value="A">A</option>
								<option value="CNAME">CNAME</option>
								<option value="MX">MX</option>
								<option value="PTR">PTR</option>
								<option value="TXT">TXT</option>
							</select>
						</div>
					</div>
					<div id="div-dns-mx" class="form-group">
						<label class="control-label col-sm-3">MX Prio</label>
						<div class="col-sm-4">
							<input type="text" name="dns[prio]" class="form-control validate[required]" value="" placeholder="Enter a number">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Content</label>
						<div class="col-sm-4">
							<input type="text" name="dns[content]" class="form-control validate[required]" value="" placeholder="Content i.e IP Address/CNAME/MX Record">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">TTL</label>
						<div class="col-sm-4">
							<select id="dns-ttl" name="dns[ttl]" class="form-control">
								<option value="60">1 min</option>
								<option value="300" selected>5 mins</option>
								<option value="1800">30 mins</option>
								<option value="3600">1 hour</option>
								<option value="43200">12 hours</option>
								<option value="86400">24 hours</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Options</label>
						<div class="col-sm-4">
							<div class="checkbox">
								<label>
									<input id="dns-option-rule" type="checkbox" name="dns[add_rule]" class="ace" value='1'/>
									<span class="lbl"> Add as IP Rule (A records only)</span>
								</label> 
							</div>
							<div class="checkbox">
								<label>
									<input id='dns-option-wildcard' type="checkbox" name="dns[add_wildcard]" class="ace" value='1'/>
									<span class="lbl"> Add Wildcard (*.)</span>
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
jQuery(function($){
	$('#frm-dns').validationEngine('attach', { promptPosition : "topLeft"});

	$('#dns-type').change(function() {
		$('#div-dns-mx').hide();
		if($(this).val() == 'MX') {
			$('#div-dns-mx').show();
		}
	});

	$('#dns-type').change();
});
</script>
@endsection