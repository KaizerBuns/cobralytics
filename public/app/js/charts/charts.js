// REALTIME CHART

var chartRealtime; // global
var requestData = function () {
	$.ajax({
		url: '/ajax/realtime/', 
		success: function(point) {
			var series = chartRealtime.series[0],
				shift = series.data.length > 20; // shift if the series is longer than 20

			// add the point
			chartRealtime.series[0].addPoint(eval(point), true, shift);
			
			// call it again after one second
			setTimeout(requestData, 1000);	
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

	 chartRealtime = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-realtime',
            type: 'area',
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
        		fillColor: "rgba(255,255,255, 0.35)",
        		color: "rgba(255,255,255, 0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
			tickPixelInterval: 150,
			maxZoom: 20 * 1000
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Visitors',
			data: []
		}]
	});	$(window).resize();	
});


// DAILY CHART


var chartDaily; // global
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	 chartDaily = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-daily',
            type: 'area',
            marginRight: 0,
            backGroundColor: "white",
        },
        plotOptions:{
        	series: {
        		fillColor: "rgba(255,255,255, 0.5)",
        		color: "rgba(255,255,255, 0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Daily Traffic',
			data: [7.0, 15.9, 9.5, 6.5, 18.2, 21.5, 18.2, 26.5, 15.3, 18.3, 13.9, 9.6]
		}]
	});	$(window).resize();	
});

// VISITORS TODAY CHART

var chartVisitors; // global
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	 chartVisitors = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-visitor',
            type: 'area',
            marginRight: 0,
            backGroundColor: "white",
        },
        plotOptions:{
        	series: {
        		fillColor: "rgba(255,255,255,0.4)",
        		color: "rgba(255,255,255, 0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Visitors',
			data: [17.0, 13.9, 19.5, 6.5, 18.2, 5.5, 18.2, 26.5, 12.3, 20.3, 11.9, 4.6]
		}]
	});	$(window).resize();	
});
// CLICKS TODAY CHART

var chartClicks; // global
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	 chartClicks = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-clicks',
            type: 'area',
            marginRight: 0,
            backGroundColor: "white",
        },
        plotOptions:{
        	series: {
        		fillColor: "rgba(255,255,255,0.5)",
        		color: "rgba(255,255,255,0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Clicks',
			data: [17.0, 8.9, 19.5, 16.5, 8.2, 11.5, 28.2, 16.5, 12.3, 20.3, 9.9, 4.6]
		}]
	});	$(window).resize();	
});
// CLICK THROUGH RATE CHART

var chartCTR; // global
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	 chartCTR = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-ctr',
            type: 'area',
            marginRight: 0,
            backGroundColor: "white",
        },
        plotOptions:{
        	series: {
        		fillColor: "rgba(200,255,255,0.5)",
        		color: "rgba(200,255,255,0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Click Through Rate',
			data: [17.0, 8.9, 15.5, 16.5, 8.2, 11.5, 18.2, 16.5, 12.3, 10.3, 9.9, 4.6]
		}]
	});	$(window).resize();
});

// UNIQUE VISITS TODAY CHART

var chartUVT; // global
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	 chartUVT = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-uvt',
            type: 'area',
            marginRight: 0,
            backGroundColor: "white",
        },
        plotOptions:{
        	series: {
        		fillColor: "rgba(255,255,255,0.5)",
        		color: "rgba(200,255,255,0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Unique Visits Today',
			data: [4.0, 8.9, 10.5, 10.5, 8.2, 13.5, 8.2, 6.5, 12.3, 10.3, 9.9, 4.6]
		}]
	});	$(window).resize();	
});

// REVENUE TODAY CHART

var chartRevenue; // global
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	 chartRevenue = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-revenue',
            type: 'area',
            marginRight: 0,
            backGroundColor: "white",
        },
        plotOptions:{
        	series: {
        		fillColor: "rgba(255,255,255,0.55)",
        		color: "rgba(255,255,255,0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Revenue Today',
			data: [10.0, 11.9, 10.5, 15.5, 8.2, 17.5, 5.2, 11.5, 12.3, 6.3, 10.9, 11.6]
		}]
	});	$(window).resize();	
});

// SALES TODAY CHART

var chartSales; // global
	
$(function(){

	 Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

	 chartSales = new Highcharts.Chart({
		 chart: {
		 		margin:0,
				spacingBottom: 0,
		        spacingTop: 0,
		        spacingLeft: 0,
		        spacingRight: 0,

		        // Explicitly tell the width and height of a chart
		        width: null,
		        height: null,
		 	renderTo: 'chart-sales',
            type: 'area',
            marginRight: 0,
            backGroundColor: "white",
        },
        plotOptions:{
        	series: {
        		fillColor: "rgba(255,255,255,0.5)",
        		color: "rgba(255,255,255,0)",
        		marker:{
        			enabled: false
        		}
        	}
        },
		title: {
			text: ''
		},
		xAxis: {
			minPadding: 0,
			maxPadding: 0,
			gridLineColor: null,
			type: 'datetime',
		},
		yAxis: {
            gridLineWidth: 0,
            minorGridLineWidth: 0,
			gridLineColor: null,
			minPadding: 0,
			maxPadding: 0,
			title: {
				text: '',
				margin: 0
			}
		},
		credits:{
			enabled:false
		},
		series: [{
			showInLegend:false,
			name: 'Sales Today',
			data: [2.0, 5.9, 7.5, 15.5, 18.2, 17.5, 15.2, 11.5, 12.3, 6.3, 10.9, 16.6]
		}]
	});	$(window).resize();	
});

