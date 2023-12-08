<div class="panel">
    <div class="panel-heading">
        <div class="panel-control">
        	<button id="btn-new-rule" title="New {{ $view_object }}" class="btn btn-default cmd-tip">
				<i class="ace-icon fa fa-pencil-square-o"></i>
			</button>
			<button id="btn-bulk-edit-rule" title="Bulk Update" class="btn btn-default cmd-tip">
				<i class="ace-icon fa fa-gears"></i>
			</button>
			<button id="btn-bulk-delete-rule" title="Bulk Delete" class="btn btn-default cmd-tip">
				<i class="ace-icon fa fa-trash"></i>
			</button>
			<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#data-rules"><i class="fa fa-chevron-down"></i></button>
        </div>
        <h3 class="panel-title">{{ $view_header }}</h3>
    </div>
    <div id="data-rules" class="collapse in">
    	<div class="panel-body">
    		{!! $tablemap_rules !!}
    	</div>
    </div>
</div>
<script>
$(function(){
	//Rules
	$('#btn-new-rule').click(function(){
		window.location = "/member/rule/?view=new&type={{ $object->get_type() }}&type_id={{ $object->id }}";
	});

	$('#btn-bulk-edit-rule').click(function(){
		bulk_edit();
	});

	$('#btn-bulk-delete-rule').click(function(){
		bulk_delete();
	});
});
</script>
@include('partials.common-js')