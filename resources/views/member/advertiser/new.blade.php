@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-advertiser" class="form-horizontal" role="form" action="{{ url('/member/advertiser/?view=save') }}" method="POST">
			<input type="hidden" name="advertiser[id]" value="{{ $advertiser['id'] or 0 }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-4">Name</label>
						<div class="col-sm-4">
							<input type="text" name="advertiser[name]" class="form-control validate[required]" value="{{ $advertiser['name'] or '' }}" placeholder="Name">
						</div>
					</div>
					<!--
					<div class="form-group">
						<label class="control-label col-sm-4">Platform</label>
						<div class="col-sm-4">
							<select name="advertiser[platform]" class="form-control" id="advertiser-platform">
								<option value="">None</option>
								<option value="cake">Cake</option>
								<option value="hasoffers">HasOffers</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Account</label>
						<div class="col-sm-4">
							<p> Fill in the necessary account information for Cake/HasOffers</p>
							<table class="table">
								<tr>
									<td>PubID</td>
									<td><input type="text" name="advertiser[platform_pubid]" value="{{ $advertiser['platform_pubid'] or '' }}"></td>
								</tr>
								<tr>
									<td>API Key</td>
									<td><input type="text" name="advertiser[platform_api]" value="{{ $advertiser['platform_api'] or '' }}"></td>
								</tr>
								<tr>
									<td>Username</td>
									<td><input type="text" name="advertiser[platform_acct]" value="{{ $advertiser['platform_acct'] or '' }}"></td>
								</tr>
								<tr>
									<td>Password</td>
									<td><input type="text" name="advertiser[platform_pass]" value="{{ $advertiser['platform_pass'] or '' }}"></td>
								</tr>
							</table>
						</div>
					</div>
					!-->
					<div class="form-group">
						<label class="control-label col-sm-4">
							 Internal
						</label>
						<div class="col-sm-4">
							<label>

								<input class="ace ace-switch ace-switch-3" type="checkbox" name="advertiser[internal]" value='1' <?=isset($advertiser['internal']) && $advertiser['internal'] ? 'checked' : ''?>>
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
			$('#frm-advertiser').validationEngine('attach', { promptPosition : "topRight"});
			$('#advertiser-platform').val("{{ $advertiser['platform'] or ''}}");
		});
	});
</script>
@endsection