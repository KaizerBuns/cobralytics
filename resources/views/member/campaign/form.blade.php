<div class="row">
	<div class="col-xs-7">
		<div class="panel">
			<div class="panel-heading">
					<h3 class="panel-title">{{ $campaign->id > 0 ? 'Details' : $header['desc']  }}</h3>
			</div>
			<form id="frm-campaign" name="frm_campaign" role="form" class="form-horizontal" action="/member/campaign/?view=save" enctype="multipart/form-data" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="campaign[id]" value="{{ $campaign->id or 0}}">
				<div class="panel-body">
				<!-- form start -->
					<div class="form-group">
						<label class="control-label col-sm-3">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control validate[required]" value="{{ $campaign->name or ''}}" name="campaign[name]">
						</div>
					</div>
					@if($user->is_admin())
					<div class="form-group">
						<label class="control-label col-sm-3">Project</label>
						<div class="col-sm-8">
							<select class="form-control" id="campaign-project" name="campaign[project_id]">
								@foreach($projects as $p)
									<option value="{{ $p->id }}" {{ $p->id == $campaign->project_id ? 'selected' : '' }}>{{ $p->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					@endif
					<div class="form-group">
						<label class="control-label col-sm-3">Traffic Source</label>
						<div class="col-sm-8">
							<input type="hidden" id="SourceID" name="campaign[source_id]" value="{{ $campaign->source_id or 0}}" class="form-control validate[required]"/>
							<p class="help-block">Examples: Default Traffic Source</p>
						</div>
					</div>
					<!--
					<div class="form-group">
						<label class="control-label col-sm-3">Ad Medium</label>
						<div class="col-sm-8">
						<input type="text" class="form-control" value="{{ $campaign->medium or ''}}" name="campaign[medium]">
						<p class="help-block">Examples: PPC, E-mail, Coupon</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Ad Content</label>
						<div class="col-sm-8">
							<select name="campaign[content]" id="campaign-content" class="form-control">
								<option value="redirect">Redirect</option>

								<option value="banner">Banner</option>
								<option value="template">Template</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Ad Type</label>
						<div class="col-sm-8">
							<select name="campaign[type]" id="campaign-type" class="form-control">
								<option value="cpc">CPC</option>
								<option value="cpm">CPM</option>
								<option value="cpa">CPA</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Ad Cost</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ $campaign->cost or 0}}" name="campaign[cost]">
							<p class="help-block">Track the cost associated with this ad</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Ad Revenue</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ $campaign->revenue or 0}}" name="campaign[revenue]">
							<p class="help-block">Track the revenue associated with this ad</p>
						</div>
					</div>
					!-->
					<div class="form-group">
						<label class="control-label col-sm-3">Tracking Domains</label>
						<div class="col-sm-8">
							<input type="hidden" id="DomainID" name="campaign[domain_id]" value="{!! $campaign->key_domains or ''!!}" class="form-control validate[required]"/>
							<p class="help-block">Examples: A domain in your account to use as tracking links</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">3rd party pixels</label>
						<div class="col-sm-8">
							<input type="hidden" id="PixelID" name="campaign[pixel_id]" value="{!! $campaign->key_pixels or ''!!}" class="form-control"/>
							<p class="help-block">Examples: A domain in your account to use as tracking links</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Tracking URL Append</label>
						<div class="col-sm-4">
							<input type="text" id="campaign-tracking-append-url" name="campaign[tracking_url_append]" value="{{ $campaign->tracking_url_append or ''}}" class="form-control"/>
							<p class="help-block">
								Append a tracking url to the end of the destination URL
							</p>
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

	$('#frm-campaign').validationEngine('attach', { promptPosition : "topRight"});

	$("#SourceID").select2({
		placeholder: "Add tracking source",
		minimumInputLength: 3,
		maximumSelectionSize: 1,
		width:'copy',
		multiple:true,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: "/ajax/search_source/?type=traffic&add=true",
			dataType: 'json',
			data: function (term, page) {
				return {
					name: term
				};
			},
			results: function (data, page) {
				// parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter remote JSON data
				return {results: data};
			}
		}
	});

	$("#DomainID").select2({
		placeholder: "Add tracking domain",
		minimumInputLength: 3,
		width:'copy',
		multiple:true,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: "/ajax/search_source/?type=domain",
			dataType: 'json',
			data: function (term, page) {
				return {
					name: term
				};
			},
			results: function (data, page) {
				// parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter remote JSON data
				return {results: data};
			}
		}
	});

	$("#PixelID").select2({
		placeholder: "Add 3rd party pixel",
		minimumInputLength: 3,
		width:'copy',
		multiple:true,
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: "/ajax/search_pixel/",
			dataType: 'json',
			data: function (term, page) {
				return {
					name: term
				};
			},
			results: function (data, page) {
				// parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter remote JSON data
				return {results: data};
			}
		}
	});

	$('#campaign-type').select2({
		width:'100'
	});

	$('#campaign-project').select2({
		width:'300'
	});

	<?if(isset($campaign->id) && $campaign->id) { ?>

		if($("#SourceID").val() != 0) {
			$.ajax("/ajax/search_source/?type=traffic", {
				data: { id: $("#SourceID").val() },
				dataType: "json"
			}).done(function(data) {
				$("#SourceID").select2("data", data);
			});
		}

		if($("#DomainID").val() != 0) {
			$.ajax("/ajax/search_source/?type=domain", {
				data: { id: $("#DomainID").val() },
				dataType: "json"
			}).done(function(data) {
				$("#DomainID").select2("data", data);
			});
		}

		if($("#PixelID").val() != 0) {
			$.ajax("/ajax/search_pixel/", {
				data: { id: $("#PixelID").val() },
				dataType: "json"
			}).done(function(data) {
				$("#PixelID").select2("data", data);
			});
		}

		$('#campaign-content').val("{{ $campaign->content or 'redirect' }}");
	<? } ?>

	$('#btn-campaign').click(function(){
		$("form[name='frm_campaign']").submit();
	});

	$('#campaign-content').select2({
		width:'100'
	});

	Select2ValidateFix('SourceID');
	Select2ValidateFix('DomainID');
	Select2ValidateFix('PixelID');
});
</script>