<?
use App\Helpers\TableMap;
use App\Helpers\MyHelper;
?>

@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
		 <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
    		<form id="frm-account" class="form-horizontal" action="/member/account/?view=save" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="user[id]" value="{{ $user['id'] }}">
	            <div class="panel-body">
	            	<div class="col-xs-6">
						<div class="form-group">
							<label class="col-xs-3">Name</label>
							<div class="col-xs-8">
								<input class="form-control" id="user-first-name" name="user[name]" type="text" placeholder='First name' value="{{ $user['name'] }}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3">Email</label>
							<div class="col-xs-8">
								<input class="form-control" id="user-email" name="user[email]" type="text" value="{{ $user['email'] }}" placeholder="E-mail address" readonly="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-11">
								<hr>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3">Address</label>
							<div class="col-xs-8">
								<input class="form-control" id="user-address" name="user[address]" type="text" value="{{ $user['address'] }}" placeholder='Address'/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3">Address 2</label>
							<div class="col-xs-8">
								<input class="form-control" id="user-address2" name="user[address2]" type="text" value="{{ $user['address2'] }}" placeholder="Address 2" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3">City</label>
							<div class="col-xs-8">
								<input class="form-control" id="user-city" name="user[city]" type="text" value="{{ $user['city'] }}" placeholder="City" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-3">State/Province</label>
							<div class="col-xs-8">
								<select class="form-control" id="user-state" name="user[state]">
									<?=MyHelper::makeStateDropDown($user['country'], $user['state'])?>
								</select>
								<input class="form-control" id="user-input-state" name="user[state]" style="display:none" type="text" value="{{ $user->state }}" disabled>
							</div>
						</div>					
						<div class="form-group">
							<label class="col-xs-3">Country</label>
							<div class="col-xs-8">
								<select class="form-control" id="user-country" name="user[country]" ><?=MyHelper::makeCountryDropDown($user['country'])?></select>
							</div>
						</div>
					</div>	
					<div class="col-xs-6">
							<!-- form start -->
						
						<div class="form-group">
							<label class="col-xs-4">Password</label>
							<div class="col-xs-6">
								<input id="user-password" type="password" class="form-control" name="user[password]" autocomplete=off>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4">Confirm Password</label>
							<div class="col-xs-6">
								<input id="user-confirm-password" type="password" class="form-control validate[equals[user-password]]" name="user[confirm_password]" autocomplete=off>
							</div>
						</div>					
						<div class="form-group">
							<label class="col-xs-4">Preferences</label>
							<div class="col-xs-6">
								<div class="checkbox inline">
									<label>
										<input type="checkbox" name="user[pref_show_welcome]" class="ace ace-switch ace-switch-3" value='1' <?=($user['pref_show_welcome'] ? 'checked' : '')?>/>
										<span class="lbl">&nbsp;&nbsp;Show Welcome</span>
									</label>
								</div>
								<div class="checkbox inline">
									<label>
										<input type="checkbox" name="user[pref_alerts]" class="ace ace-switch ace-switch-3" value='1' <?=($user['pref_alerts'] ? 'checked' : '')?>/>
										<span class="lbl">&nbsp;&nbsp;Email Alerts</span>
									</label>
								</div>
								<div class="checkbox inline">
									<select name="user[pref_page_limit]" id="user-pref_page_limit" class="form-control">
										<option value="10" {{ ($user['pref_page_limit'] == 10 ? 'selected' : '') }}>10</option>
										<option value="25" {{ ($user['pref_page_limit'] == 25 ? 'selected' : '') }}>25</option>
										<option value="50" {{ ($user['pref_page_limit'] == 50 ? 'selected' : '') }}>50</option>
										<option value="100" {{ ($user['pref_page_limit'] == 100 ? 'selected' : '') }}>100</option>
										<option value="300" {{ ($user['pref_page_limit'] == 300 ? 'selected' : '') }}>300</option>
										<option value="500" {{ ($user['pref_page_limit'] == 500 ? 'selected' : '') }}>500</option>
									</select>
									<p class="help-block">Report Page Limit</p>
								</div>
								<div class="checkbox inline">
									<input id="user-pref_all_rule" type="text" class="form-control" name="user[pref_all_rule]" placeholder="Type a valid URL with http/https" value="{{ $user['pref_all_rule'] }}" autocomplete=off>
									<p class="help-block">Recommended: Any campaigns that fail to retrieve a rule will be sent to this URL.</p>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-group">
							<label class="col-xs-4">Created On</label>
							<div class="col-xs-6">
								<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $user['created_at']) }}" readonly="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4">Updated On</label>
							<div class="col-xs-6">
								<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $user['updated_at']) }}" readonly="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4">Last Login</label>
							<div class="col-xs-6">
								<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $user['last_login']) }}" readonly="">
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
<script type="text/javascript">
$(function(){

	$('#frm-account').validationEngine('attach', { promptPosition : "topRight" });	

	var can_states = '{!! MyHelper::makeStateDropDown('CA','') !!}';
	var usa_states = '{!! MyHelper::makeStateDropDown('US','') !!}';


	$('#user-country').change(function(event) {
		$('#user-input-state').hide();
		$('#user-state').html('');

		if($(this).val() != 'US' && $(this).val() != 'CA')
		{
			$('#user-state').hide();
			$('#user-input-state').show();			
 			$("#user-input-state").removeAttr("disabled");
		} else {
			$('#user-state').show();
			$('#user-input-state').hide();			
			$("#user-input-state").attr("disabled","disabled");

			if($(this).val() == 'CA') {
				$('#user-state').html(can_states);
			} else if($(this).val() == 'US') {
				$('#user-state').html(usa_states);
			}
		}
	});	
	
	
	<?if(!$user->state){?>
	$('#user-country').change();
	<?}?>
});
</script>
@endsection
