<?
use App\Helpers\TableMap;
use App\Helpers\MyHelper;
?>
<div class="panel">
    <div class="panel-heading">
        <div class="panel-control">
            <a class="fa fa-question-circle fa-lg fa-fw unselectable add-tooltip" href="#" data-original-title="<h4 class='text-thin'>Information</h4><p style='width:150px'>This is an information bubble to help the user.</p>" data-html="true" title=""></a>
        </div>
        <h3 class="panel-title">Top Countries</h3>
    </div>

    <div class="panel-body">
      	<div id="world-map" style="height: 300px;"></div>
		<div class="table-responsive">
			<!-- .table - Uses sparkline charts-->
			<table class="table table-striped xsmall-text">
				<tr>
					<th><i class="ace-icon fa fa-caret-right blue"></i>&nbsp;Country</th>
					<th><i class="ace-icon fa fa-caret-right blue"></i>&nbsp;Traffic By Hour</th>
					<th><span class="cmd-tip" title="Uniques"><i class="fa fa-user"></i><span></th>
					<th><span class="cmd-tip" title="Visitors"><i class="fa fa-users"></i></span></th>
					<th><span class="cmd-tip" title="Clicks"><i class="fa fa-hand-o-down"></i></span></th>
					<th><span class="cmd-tip" title="Revenue"><i class="fa fa-usd"></i></span></th>
				</tr>
				<?
				foreach($countries as $country) {
					$c = $country['summary'];
				?>
						<tr>
							<td><a href="#"><?=$c->country?></a></td>
							<td><div class='sparklines'>{!! implode(",", (array)$country['hours']) !!}</div></td>
							<td>{{ TableMap::value_format('number',$c->uniques) }}</td>
							<td>{{ TableMap::value_format('number',$c->visitors) }}</td>
							<td>{{ TableMap::value_format('number',$c->clicks) }}</td>
							<td>{{ TableMap::value_format('money', $c->revenue) }}</td>
						</tr>
				<? } ?>
			</table><!-- /.table -->
		</div>
    </div>
</div>
<?
$tooltip = "{{offset:offset}}: {{value:val}} Visitors";
$data = array();
foreach($countries as $country) {
	$c = $country['summary'];
	$data[] = '"'.$c->country.'" : '.(int)$c->visitors;
}
?>
<script>
jQuery(function($) {
	
	$(function(){
	
		//jvectormap data
	   var visitorsData = {
		  <?=implode(",", $data)?>
	   };

		   //World map by jvectormap
	   $('#world-map').vectorMap({
		   map: 'world_mill_en',
		   backgroundColor: '#F9F9F9', //"#fff", 
		   regionStyle: {
			   initial: {
				   fill: '#E8EBF0', //'#e4e4e4',
				   "fill-opacity": 1,
				   stroke: 'none',
				   "stroke-width": 0,
				   "stroke-opacity": 1
			   }
		   },
		   series: {
			   regions: [{
					   values: visitorsData,
					   scale: ["#3B3E46", "#2A2F35"],//["#3c8dbc", "#2D79A6"], //['#3E5E6B', '#A6BAC2'],
					   normalizeFunction: 'polynomial'
				   }]
		   },
		   onRegionLabelShow: function(e, el, code) {
			   if (typeof visitorsData[code] != "undefined")
				   el.html(el.html() + ': ' + visitorsData[code] + ' people');
		   }
	   });

	   $(".sparklines").sparkline('html', {	
		   width: 'auto', 
		   height: '20',
		   type: 'bar',
		   lineColor: '#00a65a',
		   spotRadius: 3,
		   lineWidth:1,
		   tooltipFormat: '{!! $tooltip !!}',
		   tooltipValueLookups: {
			   'offset': {
				   0: '12AM',
				   1: '1AM',
				   2: '2AM',
				   3: '3AM',
				   4: '4AM',
				   5: '5AM',
				   6: '6AM',
				   7: '7AM',
				   8: '8AM',
				   9: '9AM',
				   10: '10AM',
				   11: '11AM',
				   12: '12PM',
				   13: '1PM',
				   14: '2PM',
				   15: '3PM',
				   16: '4PM',
				   17: '5PM',
				   18: '6PM',
				   19: '7PM',
				   20: '8PM',
				   21: '9PM',
				   22: '10PM',
				   23: '11PM'
			   }
		   }
	   });
	});
});
</script>