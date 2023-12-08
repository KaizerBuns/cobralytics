<? use App\Helpers\TableMap; ?>
<div class="row">
	<div class="col-xs-5">
		 <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">Details</h3>
            </div>
            <form role="form" class="form-horizontal" action="/member/{{ $source->get_type() }}/?view=update" enctype="multipart/form-data" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="source[id]" value="{{ $source->id }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-3">Name</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ $source->name }}" readonly="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Created On</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $source->created_at) }}" readonly="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Updated On</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="{{ TableMap::value_format('nice-date-time', $source->updated_at) }}" readonly="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Project</label>
						<div class="col-sm-8">
							<select class="form-control" name="source[project_id]">
								@foreach($projects as $p)
									<option value="{{$p->id}}" {{ $p->id == $source->project_id ? 'selected' : '' }}>{{ $p->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					@if($user->is_enabled('analytics'))
					<!--
					<div class="form-group">
						<label class="control-label col-sm-3">Google Analytics</label>
						<div class="col-sm-8">
							<input name="source[google_analytics]" type="checkbox" class="ace ace-switch ace-switch-3" value="1" {{ $source->google_analytics ? 'checked' : '' }}>
							<p class="help-block">To start retrieving GA Stats add<br><b>{{ env('ANALYTICS_SERVICE_EMAIL') }}</b><br>as read access user</p>
						</div>
					</div>
					!-->
					@endif
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
<?
/*
<script type="text/javascript">
var _cb_source = <?=$this->source->id?>; //your source id
var _cb_subid = ''; //your unique subid
var _cb_host = "//cloud.cobra.software/tracker/";
(function() {
var u=_cb_host;
var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'cobra.js'; s.parentNode.insertBefore(g,s);
})();
</script>
<noscript><img src="//cloud.cobra.software/tracker/pixel.php?s=<?=$this->source->id?>&v=1&subid=" style="border:0;" alt="" /></noscript>
*/
?>