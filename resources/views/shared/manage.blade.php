@extends('layout')
@section('content')
<?
$show_project_dropdown = false;
if(preg_match("/(campaign|source|domain|traffic)/",$app['controller_name'])) {
	$show_project_dropdown = true;
}

$show_rule_dropdown = false;
if(preg_match("/(campaign|service)/", $app['controller_name'])) {
	$show_rule_dropdown = true;
}
?>
<div class="row">
	<div class="col-xs-12">
		 <div class="panel">
            <div class="panel-heading">
                <div class="panel-control">
                	@include('partials.paginate')
                </div>
                <h3 class="panel-title">Manage</h3>
            </div>
            <div class="panel-body">
            	<form role="form" class="form-horizontal" action="<?=$_SERVER['REQUEST_URI']?>" method="GET">
            		<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="view" value="{{ $_GET['view'] }}">
					<input type="hidden" name="section" value="{{ $_GET['section'] or '' }}">
					<div class="form-group">
						<label class="col-sm-3 control-label">Search</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" value="{{ $request->input('search')['name'] or '' }}" name="search[name]">
							<?if($app['action_name'] == 'campaign'):?>
								<small class="help-block">Search by Name, Sources, Tracking Domains</small>
							<?endif;?>
						</div>
					</div><!-- /.form group -->
					@if($show_project_dropdown)
					<div class="form-group">
						<label class="control-label col-sm-3">Project</label>
						<div class="col-sm-4">
							<select class="form-control validate[required]" name="search[project_id]">
								<option value="0">All Projects</option>
								@foreach($projects as $p)
									<option value="{{ $p->id }}" {{ $p->id == $user->get_default_project() ? 'selected' : '' }}>{{ $p->name }} {{ ($p->id == $user->get_default_project() ? '(Current)' : '') }}</option>
								@endforeach
							</select>
						</div>
					</div>
					@endif
					@if($show_rule_dropdown)
					<div class="form-group">
						<label class="col-sm-3 control-label">Rules</label>
						<div class="col-sm-4">
							<select class="form-control validate[required]" name="search[show]">
								<option value="0" {{ $request->input('search')['show'] == 0 ? 'selected' : '' }}>Any</option>
								<option value="1" {{ $request->input('search')['show'] == 1 ? 'selected' : '' }}>0 Rules</option>
								<option value="2" {{ $request->input('search')['show'] == 2 ? 'selected' : '' }}>1 or more Rules</option>
							</select>
						</div>
					</div><!-- /.form group -->
					@endif
					<div class="form-group">
						<label class="col-sm-3 control-label">&nbsp;</label>
						<div class="col-sm-4">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</div>	
				</form>
				<hr>
				{!! $tablemap !!}
            </div>
        </div>
	</div>
</div>
@endsection