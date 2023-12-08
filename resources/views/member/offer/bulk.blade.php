@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">Upload CSV</h3>
            </div>
            <form id="frm-offer" class="form-horizontal" role="form" action="/member/offer/?view=save_bulk" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-4">Upload offers</label>
						<div class="col-sm-6">
							<input type="file" name="file" id="file">
							<p class="help-block">
							<b>Required Fields</b><br>
							offer_name, landing_page, advertiser_name, vertical_name, url, revenue, *country, *region<br>
							<b>Optional Fields</b><br>
							country, region - set to '?' for all
							</p>
						</div>
					</div>
	            </div>
	            <div class="panel-footer text-right">
					<button class="btn btn-info" type="submit">Submit</button>
				</div>
	        </form>
        </div>	
		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">Uploaded Offers</h3>
            </div>
            <div class="panel-body">
            	{!! $tablemap !!}
            </div>
        </div>
	</div>
</div>
@endsection