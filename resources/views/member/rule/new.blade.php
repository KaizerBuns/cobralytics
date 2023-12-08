@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">{{ $header['desc'] }}</h3>
			</div>
			<form id='frm-rule' class="form-horizontal" role="form" action="{{ url('/member/rule/?view=save') }}" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="rule-id" name="rule[id]" value="{{ $rule->id or 0 }}">
			<input type="hidden" id="rule-rule_type" name="rule[rule_type]" value="{{ $object->get_type() }}">
			<input type="hidden" id="rule-rule_type_id" name="rule[rule_type_id]" value="{{ $object->id or 0}}">
			<input type="hidden" id="rule-bulk_update" name="rule[bulk_update]" value="{{ $bulk or '' }}">
			<input type="hidden" id="rule-bulk_update_ids" name="rule[bulk_update_ids]" value="{{ $bulk_ids or '' }}">
			<input type="hidden" id="rule-rotator" name="rule[rotator]" value="{{ $rotator or 0 }}">
				<div class="panel-body">
					<div class="form-group" id="div-rule-type">
						<label class="control-label col-sm-3">Type</label>
						<div class="col-sm-4">
							<select id="rule-type" name="rule[type]" class="form-control validate[required]">
								<option value="">Select One</option>
								@if($rotator || $rule->rotator)
									<option value="redirect">Redirect</option>
									<option value="landingpage">Landing Page</option>
									<!--<option value="creative">Creative</option>!-->
								@else
									<option value="redirect">Redirect</option>
									<option value="landingpage">Landing Page</option>
									<option value="offer">Offer</option>
									<option value="service">Service</option>
									<!--<option value="creative">Creative</option>!-->
									<option value="html">HTML Page (In Progress)</option>
									<option value="sale">Sale Page (In Progress)</option>
									<option value="ip">IP Forwarding</option>
								@endif
							</select>
						</div>
					</div>
					<div class="form-group" id="div-rule-name">
						<label class="control-label col-sm-3">Name</label>
						<div class="col-sm-4">
							<input type="text" id="rule-name" name="rule[name]" class="form-control validate[required]" value="{{ $rule->name or '' }}" placeholder="Enter a Name" maxlength="25">
						</div>
					</div>
					<div class="form-group" id="div-rule-key">
						<label class="control-label col-sm-3">Rotator Key</label>
						<div class="col-sm-4">
							<input type="text" id="rule-key" name="rule[rule_key]" class="form-control" value="{{ $rule->rule_key or '' }}" placeholder="Enter a key - optional" maxlength="25">
						</div>
					</div>
					<div id="div-rule-redirect">
						<div class="form-group" id="div-type-redirect">
							<label class="control-label col-sm-3">URL</label>
							<div class="col-sm-4">
								<input type="text" id="rule-url" name="rule[url]" class="form-control validate[required]" value="{{ $rule->url or ''}}" placeholder="">
								<p class="help-block">
									<b>Dynamic parameters</b><br>
									{CLICKID} - The generated CLICKID by Cobralytics.com<br>
									{SUBID} - The subid that was sent in from the campaign tracking link
								</p>
							</div>
						</div>
						<div class="form-group" id="div-type-oc">
							<label class="control-label col-sm-3" id="div-type-oc-label"></label>
							<div class="col-sm-4">
								<input type="hidden" id="rule-oc" name="rule[oc]" value="" class="form-control validate[required]"/>
							</div>
						</div>
						<div class="form-group" id="div-type-redirect-options">
							<label class="control-label col-sm-3">Options</label>
							<div class="row">
								<div class="col-sm-2">
									<div class="checkbox inline">
										<label class="form-checkbox form-normal form-primary form-text active">
											<input id="rule-secure" type="checkbox" name="rule[secure]" value='1'/>
											Secure HTTPs
										</label>
									</div>
									<div class="checkbox inline">
										<label class="form-checkbox form-normal form-primary form-text active">
											<input id='rule-framed' type="checkbox" name="rule[framed]" value='1'/>
											IFrame
										</label>
									</div>
									<div class="checkbox inline">
										<label class="form-checkbox form-normal form-primary form-text active">
											<input id="rule-path_forwarding" type="checkbox" name="rule[path_forwarding]" value='1'/>
											Path Forwarding
										</label>
									</div>
								</div>
								<div class="col-xs-2">
									<div class="checkbox inline">
										<label class="form-checkbox form-normal form-primary form-text active">
											<input id="rule-qstring_forwarding" type="checkbox" name="rule[qstring_forwarding]" value='1'/>
											QueryString Forwarding
										</label>
									</div>
									<div class="checkbox inline">
										<label class="form-checkbox form-normal form-primary form-text active">
											<input id="rule-hide_referrer" type="checkbox" name="rule[hide_referrer]" value='1'/>
											Hide Referrer
										</label>
									</div>
									<div class="checkbox inline">
										<label class="form-checkbox form-normal form-primary form-text active">
											<input id="rule-skip_tracking_url_append" type="checkbox" name="rule[skip_tracking_url_append]" value='1'/>
											Skip Tracking URL Append
										</label>
									</div>
									<div class="checkbox inline" id="div-type-redirect-options-offoption">
										<hr>
										<label class="form-checkbox form-normal form-primary form-text active cmd-tip" title="Select an option and check this to turn off">
											<input id="rule-hide_referrer" type="checkbox" name="rule[option_off]" value='1'/>
											Option Off
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id='div-rule-iframe'>
						<div class="form-group">
							<label class="control-label col-sm-3">Page Title</label>
							<div class="col-sm-4">
								<input type="text" id="rule-page_title" name="rule[page_title]" class="form-control" value="" placeholder="Page Title">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Meta Keywords</label>
							<div class="col-sm-4">
								<input type="text" id="rule-meta_keywords" name="rule[meta_keywords]" class="form-control" value="" placeholder="Meta Keywords">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Meta Description</label>
							<div class="col-sm-4">
								<input type="text" id="rule-meta_desc" name="rule[meta_desc]" class="form-control" value="" placeholder="Meta Description">
							</div>
						</div>
					</div>
					<div id="div-rule-ipforwarding">
						<div class="form-group">
							<label class="control-label col-sm-3">IP Forwarding</label>
							<div class="col-sm-4">
								<input type="text" id="rule-ip_address" name="rule[ip_address]" class="form-control" value="" placeholder="0.0.0.0" maxlength="15">
								<p class="help-block">Enter a valid IP Address</p>
								<p class="text-red"><i class='fa fa-warning'></i> Setting an IP Address will set other rules to Pixel Redirects.</p>
							</div>
						</div>
					</div>
					<div id="div-rule-html">
						<div class="form-group">
							<label class="control-label col-sm-3">HTML Page</label>
							<div class="col-sm-4">
								<select id="rule-html_id" name="rule[html_id]" class="form-control validate[required]">
									<option value="0">Select One</option>
								</select>
								<p class="help-block"><a href='#' target="_new">Click here</a> to create custom HTML pages</p>
							</div>
						</div>
					</div>
					<div id="div-rule-sale">
						<div class="form-group">
							<label class="control-label col-sm-3">Sale Page</label>
							<div class="col-sm-4">
								<select id="rule-sale_id" name="rule[sale_id]" class="form-control validate[required]">
									<option value="0">Select One</option>
								</select>
								<p class="help-block">Select one of our custom For Sale Pages</p>
							</div>
						</div>
					</div>
					<div id="div-rule-country-weight">
						<div class="form-group">
							<label class="control-label col-sm-3">Country</label>
							<div class="col-sm-4">
								<select id="rule-country" name="rule[country]" class="form-control validate[required]">
								@if($bulk)
									<option value='nochange'>No Change (Country/Region/City)</option>
								@endif
								@foreach($rule_countries as $key => $value)
									<option value='{{ $key }}'>{{ $value }}</option>
								@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Region</label>
							<div class="col-sm-4">
								<select id="rule-region" name="rule[region]" class="form-control validate[required]"></select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">City</label>
							<div class="col-sm-4">
								<select id="rule-city" name="rule[city]" class="form-control validate[required]"></select>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-sm-3">Agent</label>
							<div class="col-sm-4">
								<select id="rule-agent" name="rule[agent]" class="form-control validate[required]">
									@foreach($rule_agents as $key => $value)
										<option value='{{ $key }}'>{{ $value }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Weight</label>
							<div class="col-sm-4">
								<input id="rule-weight" type="text" name="rule[weight]" class="form-control validate[required]" value="" maxlength="3">
								<p class="help-block">Enter a value between 0 and 100</p>
							</div>
						</div>
						@if($object->has_iprule())
						<p class="red"><i class='fa fa-warning'></i> You currently have an IP Address service active. New rules added will be set to Pixel Redirects.</p>
						@endif
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="submit" class="btn btn-sm btn-primary">
						<i class="ace-icon fa fa-check"></i>
						Submit
					</button>
				</div>
			</form>
        </div>
    </div>
</div>
<script>
$(function(){
	$('#frm-rule').validationEngine('attach', { promptPosition : "topRight"});
	$('#rule-type').change(function() {
		$('#div-rule-redirect').show();
		$('#div-rule-ipforwarding').hide();
		$('#div-rule-html').hide();
		$('#div-rule-sale').hide();
		$('#div-rule-country-weight').show();
		$('#div-type-oc').hide();
		$('#div-type-redirect').hide();
		$('#div-type-redirect-options').hide();
		$('#div-rule-name').hide();

		if($(this).val() == 'redirect' || $(this).val() == 'landingpage') {
			$('#div-rule-redirect').show();
			$('#div-type-redirect').show();
			$('#div-type-redirect-options').show();
			$('#div-rule-name').show();
		} else if ($(this).val() == 'campaign' || $(this).val() == 'service' || $(this).val() == 'offer' || $(this).val() == 'creative') {
			//$('#rule-oc').select2('data', null);
			$('#div-type-oc').show();
		} else if($(this).val() == 'ip') {
			$('#div-rule-ipforwarding').show();
			$('#div-rule-country-weight').hide();
		} else if($(this).val() == 'html') {
			$('#div-rule-html').show();
		} else if($(this).val() == 'sale') {
			$('#div-rule-sale').show();
		} else if ($('#rule-bulk_update').val() == 1) {
			$('#div-type-redirect-options').show();
		}
	});

	$('#rule-framed').click(function(){
		if($(this).prop('checked')) {
			$('#div-rule-iframe').show();
		} else {
			$('#div-rule-iframe').hide();
		}
	});

	$('#div-rule-iframe').hide();

	$("#rule-oc").select2({
		placeholder: "Search",
		minimumInputLength: 3,
		width:'copy',
		multiple:false,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: "/ajax/search_rule_types/",
			dataType: 'json',
			data: function (term, page) {
				return {
					type: $('#rule-type').val(),
					name: term
				};
			},
			results: function (data, page) {
				// parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter remote JSON data

				//alert(data[0].id);
				return {results: data};
			}
		}
	}).on("select2-highlight", function(e) {
		if($('#rule-type').val() == 'creative') {
			//e.val
			//e.choice.text;
			$.ajax({
				url: '/ajax/get_image/',
				data: 'id=' + e.val,
				type: 'GET',
				async: false,
				dataType: 'html',
				success: function(data) {
					$('#div-type-oc').tooltipster('content', data);
				}
			});
     	}
    });

	$('#rule-country').change(function() {
		$.ajax({
			url: '/ajax/search_regions/',
			data: 'country=' + $(this).val(),
			type: 'GET',
			async: false,
			dataType: 'json',
			success: function(data) {
				var options = $("#rule-region");
				options.empty();
				$.each(data, function(k, value) {
					options.append('<option value=' + value.id + '>' + value.text + '</option>');
				});
			}
		});
	});

	$('#rule-region').change(function() {
		$.ajax({
			url: '/ajax/search_cities/',
			data: 'country='+$('#rule-country').val() +'&region=' + $(this).val(),
			type: 'GET',
			async: false,
			dataType: 'json',
			success: function(data) {
				var options = $("#rule-city");
				options.empty();
				$.each(data, function(k, value) {
					options.append('<option value=' + value.id + '>' + value.text + '</option>');
				});
			}
		});
	});

	Select2ValidateFix("rule-oc");

	@if($rule->id)
		edit_rule({{ $rule->id }});
	@else
		@if($bulk)
			new_rule(2);
		@else
			new_rule(1)
		@endif
	@endif

	$('#rule-type').change();

	$('#div-type-oc').tooltipster({contentAsHTML: true});
});

function new_rule(type) {

	$('#rule-country').select2("destroy");
	$('#rule-region').select2("destroy");
	$('#rule-city').select2("destroy");
	$('#rule-agent').select2("destroy");

	$('#frm-rule').validationEngine('attach', { promptPosition : "topLeft"});
	$('#frm-rule').validationEngine('hideAll');
	$('#frm-rule')[0].reset();
	$('#modal-title').html("New {{ $view_object }}");
	$('#div-rule-name').show();
	$('#div-type-redirect').hide();
	$('#div-type-redirect-options').hide();
	$('#div-type-redirect-options-offoption').hide();

	//bulk edit
	if(type == 2) {
		$('#div-rule-type').hide();
		$('#div-rule-name').hide();
		$('#div-type-redirect').show();
		$('#div-type-redirect-options').show();
		$('#div-type-redirect-options-offoption').show();
		$('#frm-rule').validationEngine('detach');
	}

	$('#rule-country').change();
	$('#rule-region').change();

	$('#rule-country').select2();
	$('#rule-region').select2();
	$('#rule-city').select2();
	$('#rule-agent').select2();

	Select2ValidateFix('rule-country');
	Select2ValidateFix('rule-region');
	Select2ValidateFix('rule-city');
	Select2ValidateFix('rule-agent');
}

function edit_rule(rule_id) {
	$('#frm-rule')[0].reset();
	new_rule(1);

	var controller_name = "{{ $app['controller_name'] }}";
	var type = "{{ $object->get_type() }}";

	$.ajax({
		url: '/member/rule/',
		data: 'view=json&id=' + rule_id,
		type: 'GET',
		async: false,
		dataType: 'json',
		success: function(data) {

			$.each(data, function (i, v) {
				if(i == 'framed' && v == 1) {
					$('#rule-' + i).click();

				} else {
					$('#rule-' + i).prop('checked', false);
					if(v == 1) {
						$('#rule-' + i).prop('checked', true);
					}
					$('#rule-' + i).val(v);
					if(i == 'type') {
						$('#rule-' + i).change();
					} else if(i == 'country') {
						$('#rule-country').val(data.country);
						$('#rule-country').change();

					} else if(i == 'region') {
						$('#rule-region').val(data.region);
						$('#rule-region').change();
						$('#rule-city').val(data.city);
					}
				}
			});

			if(data.type == 'offer') {
				$("#rule-oc").select2("data", {id: data.offer_id, text: data.offer.name});
			} else if(data.type == 'campaign') {
				$("#rule-oc").select2("data",  {id: data.campaign_id, text: data.campaign.name});
			} else if(data.type == 'service') {
				$("#rule-oc").select2("data",  {id: data.service_id, text: data.service.name});
			} else if(data.type == 'creative') {
				$("#rule-oc").select2("data",  {id: data.creative_id, text: data.creative.name});
			}

			$('#rule-country').select2();
			$('#rule-region').select2();
			$('#rule-city').select2();
			$('#rule-agent').select2();

			Select2ValidateFix('rule-country');
			Select2ValidateFix('rule-region');
			Select2ValidateFix('rule-city');
			Select2ValidateFix('rule-agent');
		}
	});
}
</script>
@endsection