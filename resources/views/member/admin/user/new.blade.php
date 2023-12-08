@extends('layout')
@section('content')
<div class="panel">
	<div class="panel-heading">
		<h3 class="panel-title">{{ $header['desc'] }}</h3>
	</div>
	<!--HORIZONTAL FORM-->
	<form id="frm-account" name="frm_user" role="form" class="form-horizontal" action="{{ url('/member/admin/?section=user&view=save') }}" enctype="multipart/form-data" method="POST">
	<input type="hidden" name="account[id]" value="{{ $account['id'] or 0 }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label col-sm-3">Name</label>
				<div class="col-sm-4">
					<input type="text" class="form-control validate[required]" value="{{ $account['name'] or ''}}" name="account[name]" placeholder="Full name">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">E-mail</label>
				<div class="col-sm-4">
					<input id="account-email" type="text" class="form-control validate[required,custom[email]]" value="{{ $account['email'] or '' }}" name="account[email]" placeholder = 'E-mail'>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Password</label>
				<div class="col-sm-2">
					<input type="password" class="form-control validate[required,minSize[6]]" value="" id="account-password1" name="account[password]" placeholder="Password" autocomplete="off">
				</div>
				<div class="col-sm-2">
					<input type="password" class="form-control validate[required, equals[account-password1]]" id="account-password2" value="" name="account[confirm_password]" placeholder="Confirm Password" autocomplete="off">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Status</label>
				<div class="col-sm-4">
					<select id="account-status" name="account[status]" class="form-control">
						<option value="active">Active</option>
						<option value="pending">Pending</option>
						<option value="disabled">Disabled</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Account Type</label>
				<div class="col-sm-4">
					<select id="account-user_type" name="account[user_type]" class="form-control">
						<option value="master">Master</option>
						<option value="admin">Admin</option>
						<option value="publisher">Publisher</option>
						<option value="advertiser">Advertiser</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Preferences</label>
				<div class="col-sm-4">
					<div class="checkbox inline">
						<label>
							<input type="checkbox" name="account[pref_show_welcome]" class="ace ace-switch ace-switch-3" value='1' <?=($account['pref_show_welcome'] ? 'checked' : '')?>/>
							<span class="lbl">&nbsp;&nbsp;Show Welcome</span>
						</label>
					</div>
					<div class="checkbox inline">
						<label>
							<input type="checkbox" name="account[pref_alerts]" class="ace ace-switch ace-switch-3" value='1' <?=($account['pref_alerts'] ? 'checked' : '')?>/>
							<span class="lbl">&nbsp;&nbsp;Email Alerts</span>
						</label>
					</div>
					<div class="checkbox inline">
						<select id="account-status" name="account[pref_page_limit]" id="user-pref_page_limit" class="form-control">
							<option value="10" <?=($account['pref_page_limit'] == 10 ? 'selected' : '')?>>10</option>
							<option value="25" <?=($account['pref_page_limit'] == 25 ? 'selected' : '')?>>25</option>
							<option value="50" <?=($account['pref_page_limit'] == 50 ? 'selected' : '')?>>50</option>
							<option value="100" <?=($account['pref_page_limit'] == 100 ? 'selected' : '')?>>100</option>
							<option value="300" <?=($account['pref_page_limit'] == 300 ? 'selected' : '')?>>300</option>
							<option value="500" <?=($account['pref_page_limit'] == 500 ? 'selected' : '')?>>500</option>
						</select>
						<p class="help-block">Report Page Limit</p>
					</div>
					<div class="checkbox inline">
						<input id="account-pref_all_rule" type="text" class="form-control" name="account[pref_all_rule]" placeholder="Type a valid URL with http/https" value="{{ $account['pref_all_rule'] }}" autocomplete=off>
						<p class="help-block">Recommended: Any campaigns that fail to retrieve a rule will be sent to this URL.</p>
					</div>

				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3">Upgrades</label>
				<div class="col-sm-4">
					<div class="checkbox inline">
						<label>
							<input type="checkbox" name="account[enable_campaigns]" value="1" class="ace ace-switch ace-switch-3" <?=isset($account['enable_campaigns']) && $account['enable_campaigns'] ? 'checked' : ''?>>
							<span class="lbl">&nbsp;&nbsp;Enable Campaigns</span>
						</label>
					</div>
					<div class="checkbox inline">
						<label>
							<input type="checkbox" name="account[enable_offers]" value="1" class="ace ace-switch ace-switch-3" <?=isset($account['enable_offers']) && $account['enable_offers'] ? 'checked' : ''?>>
							<span class="lbl">&nbsp;&nbsp;Enable Offers</span>
						</label>
					</div>
					<!--
					<div class="checkbox inline">
						<label>
							<input type="checkbox" name="account[enable_monitors]" value="1" class="ace ace-switch ace-switch-3" <?=isset($account['enable_monitors']) && $account['enable_monitors'] ? 'checked' : ''?>>
							<span class="lbl">&nbsp;&nbsp;Enable Monitors</span>
						</label>
					</div>
					!-->
					<div class="checkbox inline">
						<label>
							<input type="checkbox" name="account[enable_reports]" value="1" class="ace ace-switch ace-switch-3" <?=isset($account['enable_reports']) && $account['enable_reports'] ? 'checked' : ''?>>
							<span class="lbl">&nbsp;&nbsp;Enable Reports</span>
						</label>
					</div>
					<!--
					<div class="checkbox inline">
						<label>
							<input type="checkbox" name="account[enable_analytics]" value="1" class="ace ace-switch ace-switch-3" <?=isset($account['enable_analytics']) && $account['enable_analytics'] ? 'checked' : ''?>>
							<span class="lbl">&nbsp;&nbsp;Enable Analytics</span>
						</label>
					</div>
					!-->
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</form>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#frm-account').validationEngine('attach', { promptPosition : "topRight" });
	<? if(isset($account['id']) && $account['id']) {?>
		$('#account-password1').removeClass('validate[required,minSize[6]]');
		$('#account-password2').removeClass('validate[required, equals[account-password1]]');
		$('#account-password2').addClass('validate[equals[account-password1]]');

		$('#account-password1').val('');
		$('#account-password2').val('');
		$('#account-status').val("{{ $account['status'] }}");
		$('#account-user_type').val("{{ $account['user_type'] }}");
	<? } ?>
});
</script>
@endsection