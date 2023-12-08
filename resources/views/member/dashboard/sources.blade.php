 <?
use App\Helpers\TableMap;
use App\Helpers\MyHelper;
?>
 <div class="panel">
    <div class="panel-heading">
        <div class="panel-control">
            <a class="fa fa-question-circle fa-lg fa-fw unselectable add-tooltip" href="#" data-original-title="<h4 class='text-thin'>Information</h4><p style='width:150px'>This is an information bubble to help the user.</p>" data-html="true" title=""></a>
        </div>
        <h3 class="panel-title">Top Sources</h3>
    </div>

    <div class="panel-body">
    	<table class="table table-striped" style="font-size:11px">
				<thead class="thin-border-bottom">
					<tr>
						<th><i class="ace-icon fa fa-caret-right blue"></i>&nbsp;Source</th>
						<th><i class="ace-icon fa fa-caret-right blue"></i>&nbsp;Traffic</th>
						<th><span class="cmd-tip" title="Uniques"><i class="fa fa-user"></i><span></th>
						<th><span class="cmd-tip" title="Visitors"><i class="fa fa-users"></i></span></th>
						<th><span class="cmd-tip" title="Clicks"><i class="fa fa-hand-o-down"></i></span></th>
						<th><span class="cmd-tip" title="Revenue"><i class="fa fa-usd"></i></span></th>
					</tr>
				</thead>
			<?foreach($sources as $source) {
				$s = $source['summary'];
				?>
			<tr>
				<td><a href="/member/source/?view=view&id=<?=$s->id?>"><?=$s->name?></a></td>
				<td><div class='sparklines'>{!! implode(",", $source['hours']) !!}</div></td>
				<td><?=TableMap::value_format('number', $s->uniques)?></td>
				<td><?=TableMap::value_format('number', $s->visitors)?></td>
				<td><?=TableMap::value_format('number', $s->clicks)?></td>
				<td><?=TableMap::value_format('money', $s->revenue)?></td>
			</tr>
			<?}?>
		</table><!-- /.table -->
    </div>
</div>	