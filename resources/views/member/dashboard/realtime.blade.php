<?
use App\Helpers\TableMap;
use App\Helpers\MyHelper;
?>
<div class="panel">
	<div class="panel-heading">
		<div class="panel-control"></div>
        <h3 class="panel-title">Network Load</h3>
    </div>
    <div class="panel-body">
		<div id="chart-realtime" style="height:180px;"></div>       
    </div>
</div>
<script>
var chart; // global
var requestData = function () {
	$.ajax({
		url: '/ajax/realtime/', 
		success: function(point) {
			var series = chart.series[0],
				shift = series.data.length > 20; // shift if the series is longer than 20

			// add the point
			chart.series[0].addPoint(eval(point), true, shift);
			
			// call it again after 60 seconds
			setTimeout(requestData, 10000);	
		},
		cache: false
	});
}
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	chart = new Highcharts.Chart({
		 chart: {
		 	renderTo: 'chart-realtime',
            type: 'areaspline',
        	animation: {
                duration: 999,   // As close to interval as possible.
                easing: 'linear'  // Important: this makes it smooth
            },
            marginRight: 0,
            backGroundColor: "white",
			events: {
                load: requestData()
            },
        },
        plotOptions:{
        	series: {
        		fillColor: '#E8EBF0', //"#3bb5e8",
        		color: '#E8EBF0', //"#3bb5e8",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			lineWidth: 0,
   			minorGridLineWidth: 0,
   			lineColor: 'transparent',
			gridLineColor: null,
			type: 'datetime',
			tickPixelInterval: 150,
			maxZoom: 20 * 1000
		},
		yAxis: {
			lineWidth: 1,
   			minorGridLineWidth: 0,
		    lineColor: 'transparent',
			gridLineColor: null,
			minPadding: 0.2,
			maxPadding: 0.2,
			title: {
				text: '',
				margin: 80
			},
			labels: {
		       enabled: false
		    },
		},
		series: [{
			name: 'Traffic',
			data: []
		}]
	});		
});
</script>