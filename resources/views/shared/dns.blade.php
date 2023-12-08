<div class="panel">
    <div class="panel-heading">
        <div class="panel-control">
        	<button id="btn-new-dns" title="New Rule" class="btn btn-default cmd-tip" data-toggle="modal">
				<i class="ace-icon fa fa-pencil-square-o"></i>
			</button>
			<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#data-dns"><i class="fa fa-chevron-down"></i></button>
        </div>
        <h3 class="panel-title">DNS Records</h3>
    </div>
    <div id="data-dns" class="collapse in">
    	<div class="panel-body">
    		{!! $tablemap_dns !!}
    	</div>
    </div>
</div>
<script>
$(function(){
	$('#btn-new-dns').click(function(){
		window.location = "/member/dns/?view=new&object_id={{ $object->id }}";
	});
});
</script>