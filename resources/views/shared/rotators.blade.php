<div class="panel">
    <div class="panel-heading">
        <div class="panel-control">
        	<button id="btn-new-rotator" title="New {{ $rotator_view_object }}" class="btn btn-default cmd-tip">
				<i class="ace-icon fa fa-pencil-square-o"></i>
			</button>
			<button id="btn-bulk-edit-rotator" title="Bulk Update" class="btn btn-default cmd-tip">
				<i class="ace-icon fa fa-gears"></i>
			</button>
			<button id="btn-bulk-delete-rotator" title="Bulk Delete" class="btn btn-default cmd-tip">
				<i class="ace-icon fa fa-trash"></i>
			</button>
			<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#data-rotator"><i class="fa fa-chevron-down"></i></button>
        </div>
        <h3 class="panel-title">{{ $rotator_view_header }}</h3>
    </div>
    <div id="data-rotator" class="collapse in">
    	<div class="panel-body">
    		{!! $tablemap_rotators !!}
    	</div>
    </div>
</div>
<script>
$(function(){
	//Creatives
	$('#btn-new-rotator').click(function(){
		window.location = "/member/rule/?view=new&type={{ $object->get_type() }}&type_id={{ $object->id }}&rotator=1";
	});

	$('#btn-bulk-edit-rotator').click(function(){
		bulk_rotator_edit();
	});

	$('#btn-bulk-delete-rotator').click(function(){
		bulk_rotator_delete();
	});
});
</script>