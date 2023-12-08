@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <form id="frm-source" role="form" class="form-horizontal" action="/member/{{ $source_type }}/?view=save" enctype="multipart/form-data" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="submit" value="1">
			<input type="hidden" name="source[type]" value="{{ $source_type }}">
	            <div class="panel-body">
	            	<div class="form-group">
						<label class="control-label col-sm-3">List</label>
						<div class="col-sm-4">
							<textarea class="form-control validate[required]" rows="4" id="source-list" name="source[list]" placeholder="Enter a list - 1 per line"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Project</label>
						<div class="col-sm-4">
							<select id="project_id" class="form-control validate[required]" name="source[project_id]">
								@foreach($projects as $p)
									<option value="{{$p->id}}" {{ $p->id == $user->get_default_project() ? 'selected' : '' }}>{{ $p->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Help</label>
						<div class="col-sm-4" style="padding-top: 8px;">
							To manage your domains please add <br><br><strong>{{ env('COBRA_NS1') }} <br>{{ env('COBRA_NS2') }}</strong><br><br>as your NS servers to your GoDaddy/NameCheap etc account.
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
			$('#frm-source').validationEngine('attach', { promptPosition : "topRight"});
		});
	});	
</script>
@endsection