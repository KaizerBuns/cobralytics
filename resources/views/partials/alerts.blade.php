@if(isset($alert)) 
<div class="alert {{ $alert['class'] }}" style='margin: 20px 15px;'>
<i class="fa {{ $alert['icon'] }}"></i>&nbsp;
<button type="button" class="close" data-dismiss="alert">×</button>
	@if(preg_match("/info/", $alert['class'])) 
		<strong>Heads up!</strong><br>
	@elseif(preg_match("/success/", $alert['class'])) 
		<strong>Well done!</strong><br>
	@elseif(preg_match("/warning/", $alert['class']))
		<strong>Warning!</strong><br>
	@elseif(preg_match("/danger/", $alert['class']))
		<strong>Application error!</strong><br>
	@endif
	{{ $alert['text'] }}<br>
</div>
@endif