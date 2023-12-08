<? use App\Helpers\MyHelper;?>
@if($view_type == 'simple')
	<div id="chart-daily" style="height:140px;"></div>
@else
<div class="panel">
	<div class="panel-heading">
		<div class="panel-control"></div>
        <h3 class="panel-title">Visitor Traffic</h3>
    </div>
    <div class="panel-body">
		<div id="chart-daily" style="height:180px;"></div>
    </div>
</div>
@endif
<script>
	$(function(){
		$("#chart-daily").highcharts({
			chart: {
	            type: 'areaspline'
	        },
	        title: {
	            text: ''
	        },
	        subtitle: {
	            text: ''
	        },
	        xAxis: {
				categories: ['<?=MyHelper::implode_on_field("','", $top_stats, 'date')?>'],
				gridLineWidth: 0,
				lineWidth: 0,
   				minorGridLineWidth: 0,
   				lineColor: 'transparent',
				gridLineColor: null
			},
	        yAxis: [{ // left y axis
	        	gridLineWidth: 0,
				lineWidth: 0,
   				minorGridLineWidth: 0,
   				lineColor: 'transparent',
				gridLineColor: null,
				title: {
					text: ''
				},
				labels: {
		       		enabled: false
		    	},
				showFirstLabel: false
			}, { // right y axis
				linkedTo: 0,
				gridLineWidth: 0,
				lineWidth: 0,
   				minorGridLineWidth: 0,
   				lineColor: 'transparent',
				gridLineColor: null,
				opposite: true,
				title: {
					text: null
				},
				labels: {
		       		enabled: false
		    	},
				showFirstLabel: false
			}
			],
	        tooltip: {
				shared: true,
				crosshairs: true
			},
	        series: [{
	            name: 'Visitors',
	            color: '#F5F5F5', //'#0589B7',
	            data: [<?=MyHelper::implode_on_field(",", $top_stats, 'visitors')?>]
	        }, {
	            name: 'Uniques',
	            color: '#E8EBF0', //'#8CC152',
	            data: [<?=MyHelper::implode_on_field(",", $top_stats, 'uniques')?>]
	        }]
	    });
	});
</script>