<ol class="breadcrumb">
    <li><i class="ace-icon fa fa-home home-icon"></i></li>
    <li class="active">{{$my_project ? $my_project['name'] : 'Lets Start!'}}</li>
    @if(isset($header))
    	<li>{{ ucfirst($header['title']) }}</li>
    	<li>{{ ucfirst($header['desc']) }}</li>
    @else
    	<li>{{ $app['action_name'] }}</li>
   	@endif
</ol>

