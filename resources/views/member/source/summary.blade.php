@extends('layout')
@section('content')
<div class="row">
	<div class="col-xs-12">
 		<div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $header['desc'] }}</h3>
            </div>
            <div class="panel-body">
				<table class="table table-striped table-hover table-condensed">
					<tbody>
						<tr>
							<th>Name</th>
							<th>Status</th>
						</tr>
						@foreach($results as $r)
						<tr>
							<td><?=$r['name']?></td>
							<td><span class="label label-{{ ($r['status'] == 'Success' ? 'success' : 'danger') }}">{{ $r['status'] }}</span></td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>	
</div>
@endsection