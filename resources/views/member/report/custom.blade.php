@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
		 <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">Custom Report</h3>
            </div>
            <form role="form" class="form-horizontal" action="/member/report/" method="GET">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="submit" value="1">
			<input type="hidden" name="view" value="custom">
			<input type="hidden" id="report-start" name="report[start]" value="<?=$_REQUEST['report']['start']?>">
			<input type="hidden" id="report-end" name="report[end]" value="<?=$_REQUEST['report']['end']?>">
            	<div class="panel-body">
					<div class="row">
						<div class="col-xs-4">
							<div class="form-group">
								<label class="col-xs-3">Date range</label>
								<div class="col-xs-6">
									<div id="reportrange" class="selectbox">
										<i class="fa fa-calendar fa-lg"></i>
										<span><?php echo date("M j, Y", strtotime($_REQUEST['report']['start'])); ?> - <?php echo date("M j, Y", strtotime($_REQUEST['report']['end'])); ?></span> 
										<b class="caret"></b>
									</div>
								</div>
							</div><!-- /.form group -->
							<div class="form-group">
								<label class="col-xs-3">Campaign</label>
								<div class="col-xs-7">
									<input type="hidden" id="report-campaign_id" class="form-control" name="report[campaign_id]">
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3">Offers/LP</label>
								<div class="col-xs-7">
									<input type="hidden" id="report-offer_id" class="form-control" name="report[offer_id]">
								</div>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label class="col-xs-4">Traffic Source</label>
								<div class="col-xs-7">
									<input type="hidden" id="report-source_id" class="form-control" name="report[source_id]">
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-4">Domain</label>
								<div class="col-xs-7">
									<input type="hidden" id="report-domain_id" class="form-control" name="report[domain_id]">
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-4">Traffic Type</label>
								<div class="col-xs-7">
									<select id="report-traffic_type" name="report[traffic_type][]" class="form-control report-select2" multiple="">
										<option value="">All</option>
										<option value="Search Organic">Search Organic</option>
										<option value="Direct">Direct</option>
										<option value="Referral">Referral</option>
										<option value="Social">Social</option>
										<option value="Campaign">Campaign</option>
										<option value="Ad">Ad</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label class="col-xs-3">Country</label>
								<div class="col-xs-7">
									<select id="report-country" name="report[country][]" class="form-control report-select2" multiple="">
										<?foreach($countries as $key => $value) {?>
											<option value='<?=$key?>'><?=$value?></option>
										<?}?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3">Group By</label>
								<div class="col-xs-7">
									<div class="row">
										<div class="col-xs-6">
											<div class="checkbox">
												<label>
													<input id="report-group_date" type="checkbox" name="report[groupby][date]" class="ace" value='1'/>
													<span class="lbl"> Date</span>
												</label> 
											</div>
											<div class="checkbox">
												<label>
													<input id='report-group_campaign' type="checkbox" name="report[groupby][campaign]" class="ace" value='1'/>
													<span class="lbl"> Campaign</span>
												</label> 
											</div>		
											<div class="checkbox">
												<label>
													<input id='report-group_offer' type="checkbox" name="report[groupby][offer]" class="ace" value='1'/>
													<span class="lbl"> Offer/LPs</span>
												</label> 
											</div>
											<div class="checkbox">
												<label>
													<input id='report-group_source' type="checkbox" name="report[groupby][source]" class="ace" value='1'/>
													<span class="lbl"> Source</span>
												</label> 
											</div>
											<div class="checkbox">
												<label>
													<input id='report-group_domain' type="checkbox" name="report[groupby][domain]" class="ace" value='1'/>
													<span class="lbl"> Domain</span>
												</label> 
											</div>
										</div>
										<div class="col-xs-6">
											<div class="checkbox">
												<label>
													<input id='report-group_traffic_type' type="checkbox" name="report[groupby][traffic_type]" class="ace" value='1'/>
													<span class="lbl"> Traffic Type</span>
												</label> 
											</div>
											<div class="checkbox">
												<label>
													<input id='report-group_devices' type="checkbox" name="report[groupby][devices]" class="ace" value='1'/>
													<span class="lbl"> Devices</span>
												</label> 
											</div>
											<div class="checkbox">
												<label>
													<input id='report-group_referers' type="checkbox" name="report[groupby][referrers]" class="ace" value='1'/>
													<span class="lbl"> Referers</span>
												</label> 
											</div>
											<div class="checkbox">
												<label>
													<input id='report-group_subids' type="checkbox" name="report[groupby][subids]" class="ace" value='1'/>
													<span class="lbl"> SubIDs</span>
												</label> 
											</div>
											<div class="checkbox">
												<label>
													<input id='report-group_country' type="checkbox" name="report[groupby][country]" class="ace" value='1'/>
													<span class="lbl"> Country</span>
												</label> 
											</div>		
										</div>	
									</div>
								</div>
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
<div class="row">
	<div class="col-xs-12">
		 <div class="panel">
            <div class="panel-heading">
                <div class="panel-control">                	
                	@include('partials.paginate')
                </div>
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <div class="panel-body">
				{!! $tablemap !!}
            </div>
        </div>
	</div>
</div>
<script>
jQuery(function($){
	$(function() {
		$('#reportrange').daterangepicker({
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
					'Last 7 Days': [moment().subtract('days', 6), moment()],
					'Last 30 Days': [moment().subtract('days', 29), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
				},
				startDate: '<?=$_REQUEST['report']['start']?>',
				endDate: '<?=$_REQUEST['report']['end']?>',
				format: 'YYYY-MM-DD'
			},
			function(start, end) {
				$('#report-start').val(start.format('YYYY-MM-DD'));
				$('#report-end').val(end.format('YYYY-MM-DD'));
				$('#reportrange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
			}
		);

		<?
		if($request->input('submit')){
			foreach ($request->input('report') as $key => $value) {
				if(is_array($value)) {
					echo "$('#report-{$key}').val(['".implode("','", $value)."']);\r\n";
				} else {
					echo "$('#report-{$key}').val('{$value}');\r\n";
					if($value == 1) {
						echo "$('#report-{$key}').attr('checked', true);\r\n";
					}
				}
			}

			//echo "$('#reportrange').data('daterangepicker').setStartDate('{$this->input->post('report')['start_date']}');\r\n";
			//echo "$('#reportrange').data('daterangepicker').setEndDate('{$this->input->post('report')['end_date']}');\r\n";
		}
		?>

		$('.report-select2').select2({ placeholder: "Select one"});

		$("#report-domain_id").select2({
			placeholder: "Select one",
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

		$("#report-source_id").select2({
			placeholder: "Select one",
			minimumInputLength: 3,
			width:'copy',
			multiple:true,
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				url: "/ajax/search_source/?type=traffic",
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

		$("#report-campaign_id").select2({
			placeholder: "Select one",
			minimumInputLength: 3,
			width:'copy',
			multiple:true,
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				url: "/ajax/search_rule_types/?type=campaign",
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

		$("#report-offer_id").select2({
			placeholder: "Select one",
			minimumInputLength: 3,
			width:'copy',
			multiple:true,
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				url: "/ajax/search_rule_types/?type=offer",
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


	});
});
</script>
@endsection
