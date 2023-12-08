<? 
use App\Helpers\MyHelper;
$chart_id = rand(0, 10000);
?>
@if($view_type == 'simple')
	<div id="chart-daily" style="height:140px;"></div>
@else
<div class="panel">
	<div class="panel-heading">
		<div class="panel-control"></div>
        <h3 class="panel-title">DNS Requests</h3>
    </div>
    <div class="panel-body">
		<div id="chart-daily-{{ $chart_id }}" style="height:180px;"></div>
    </div>
</div>
@endif
<script>
	$(function(){
		$("#chart-daily-{{ $chart_id }}").highcharts({
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
	            name: 'DNS Requests',
	            color: '#F5F5F5', //'#0589B7',
	            data: [<?=MyHelper::implode_on_field(",", $top_stats, 'total_requests')?>]
	        }]
	    });
	});
</script>