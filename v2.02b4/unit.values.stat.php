<html>
	<? require_once('include.php'); ?>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	<style>
	table {
		border-collapse: collapse;
		width: 100%;
		
	}

	th, td {
		text-align: left;
		padding: 8px;
	}

	tr:nth-child(even){background-color: #f2f2f2}

	th {
		
	}
	</style>
	<script type="text/javascript" src="//<?=REMOTE_ROOT?>/js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="//<?=REMOTE_ROOT?>/js/amcharts/serial.js"></script>
	
	
	<link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/calendar.css" lazyload>
	<script src="//<?=REMOTE_ROOT?>/js/calendar.js" lazyload></script>
	<script>
		var chart;
		function getValues(from, to)
		{
			DismissLoading();
			ShowLoading();
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4)
					DismissLoading();
				if (this.readyState == 4 && this.status == 200) 
				{
					var ajaxRes = JSON.parse(this.responseText).responce;
					if(ajaxRes.error == 0)
					{
						var array = [];
						var data = ajaxRes.data;
						
						
						if(data.values == false)
						{
							
							Show("errorMSG");
							document.getElementById('errorMSG').innerHTML = "Нет данных на выбранный период";
						}
						else
						{
							
							Hide("errorMSG");
							document.getElementById('errorMSG').innerHTML = "";
							if( (data.unit.valueType.includes("int") || data.unit.valueType.includes("float") ) && (!data.unit.possValues.includes(",") || data.unit.possValues.split(",").length == 2) )
							{
								plotValues(data);
								Show("chart-wrapper");
								Hide("table-wrapper");
							}
							else
							{
								Show("table-wrapper");
								Hide("chart-wrapper");
								writeValuesTable(data);
							}
						}
					}
					else
						Toast.showAjaxRes(ajaxRes);
				}
			};
			xhttp.open("POST", API_URL+"units.get.values.php", true);
			xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xhttp.send(window.location.search.substr(1)+"&from="+formatDateServ(from)+"&to="+formatDateServ(to));
			
		}
		function plotValues(data)
		{
			var ctx = document.getElementById('plot');
			var countSeries = 0;
			var values = data.values;
			var valueTypes = data.unit.valueType.split(';');
			for (var i = 0; i < values.length; i++) 
			{
				var value = values[i];
				var splitt = value.value.split(',');
				value.date = value.timeStamp
				for(var q = 0; q < splitt.length; q++)
				{
					var subVal = parseFloat(splitt[q]);
					//if(valueTypes[q] == "sint")
					//	subVal += 128;
					value["val"+q] = subVal;
				}
				countSeries = splitt.length;
			}
			var graphs = [];
			for (var i = 0; i < countSeries; i++) 
			{
				graphs[i] = {
					"id": "g"+(i+1),
					"bullet": "round",
					"bulletBorderAlpha": 1,
					"bulletColor": "#FFFFFF",
					"bulletSize": 5,
					"hideBulletsCount": 50,
					"lineThickness": 2,
					"title": "red line",
					"useLineColorForBulletBorder": true,
					"valueField": "val"+i
				};
			}
			log_obj("Plot/Datasets",values);
			
			/*var myChart = new Chart(ctx, {
				type: 'line',
				data: {
					datasets:datasets
				},
				options: {
					scales: {
						xAxes: [{
							type: 'time',
							time: {
								unit: 'month'
							},
							position: 'bottom'
						}]
					}
				}
			});*/
			

			//chart.addListener("rendered", zoomChart);
			//chart.zoomToIndexes(0, 20);
			//zoomChart();
			chart.graphs = graphs;
			chart.dataProvider = values;
			chart.validateData();
		}
		
		function writeValuesTable(values)
		{
			values = values.values;
			var tbdy = document.getElementById('table-values');
			for (var i = 0; i < values.length; i++) 
			{
				var value = values[i];
				var tr = document.createElement('tr');

				var tdDate = document.createElement('td');
				tdDate.appendChild(document.createTextNode(value.timeStamp));
				tr.appendChild(tdDate);
				var tdVal = document.createElement('td');
				tdVal.innerHtml = value.value;
				tdVal.className = 'val-field';
				tdVal.appendChild(document.createTextNode(value.value));
				
				tr.appendChild(tdVal);

				tbdy.appendChild(tr);
			}
		}
		function start()
		{
			chart = AmCharts.makeChart("chartdiv", {
				"type": "serial",
				"theme": "frozen",
				"pathToImages": "http://www.amcharts.com/lib/3/images/",
				"dataDateFormat": "YYYY.MM.DD HH:NN:SS",
				"valueAxes": [{
					"id":"v1",
					"axisAlpha": 0,
					"position": "left"
				}],
				"graphs": [{
					"id": "g1",
					"bullet": "round",
					"bulletBorderAlpha": 1,
					"bulletColor": "#FFFFFF",
					"bulletSize": 5,
					"hideBulletsCount": 50,
					"lineThickness": 2,
					"title": "red line",
					"useLineColorForBulletBorder": true,
					"valueField": "value"
				}],
				"chartScrollbar": {
					"graph": "g1",
					"scrollbarHeight": 30
				},
				"chartCursor": {
					"cursorPosition": "mouse",
					"pan": true,
					 "valueLineEnabled":true,
					 "valueLineBalloonEnabled":true,
					 "categoryBalloonDateFormat": "MMM-DD HH:NN"
				},
				"categoryField": "date",
				"categoryAxis": {
					"parseDates": true,
					"dashLength": 1,
					"minorGridEnabled": true,
					"position": "bottom",
					"minPeriod": "10ss",
				},
				"timeUnit": "minute",
				"count": 5,
				//"tooltipText" : "{dateX.formatDate('M-d HH:NN:ss')}: {valueY.formatNumber('#.00')}",
				"dataProvider": [],
				"xAxes": [{
					"type": "DateAxis",
					"tooltipDateFormat": "yyyy-MM-dd"
				  }]
			});
			//chart.tooltipText = "{dateX.formatDate('M-d HH:NN:ss')}: {valueY.formatNumber('#.00')}";
			var to = new Date();
			var from = new Date();
			from.setDate(from.getDate() - 14);

			var calendar = $('#calendar');
			calendar.html(formatDate(from)+"-"+formatDate(to));
			getValues(from, to);
			var instance = pickmeup('#calendar',
			{
				// date in the center of rendered calenda
				//	number/object/string
				current: new Date,
				// selected date
				date: new Date,
				// default date
				// sets to false to leave empty
				default_date: new Date,
				// appends the date picker to an element or triggers by an event
				
				// the first day
				// 0 - Sunday, 1 - Monday
				first_day : 1,
					
				// prev/next symbols
				prev: '&#9664;',
				next: '&#9654;',

				// single/multiple/range
				mode: 'range',

				// enables/disables year/month/day selection
				select_year: true,
				select_month: true,
				select_day: true,

				// days/months/years
				view: 'days',

				// the number of calendars to render
				calendars: 2,

				// date format
				format: 'd-m-Y',

				// title format
				title_format: 'B, Y',

				// top/right/bottom/left/function
				position : 'bottom',

				// additional class(es)
				class_name: '',

				// custom separator
				separator: ' - ',

				// hides the date picker after selection
				hide_on_select: false,

				// min/max dates
				//	number/object/string
				min: null,
				max: new Date,

				// executed for each day element rendering, takes date argument, allows to select, disable or add class to element
				render: function () {},

				// current local
				locale: 'en',

				// local strings
				locales : {
				en : {
					days		: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
					daysShort	: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
					daysMin		: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
					months		: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
					monthsShort : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
				}
				}
			});
			instance.set_date([from,to]);
			calendar.bind('pickmeup-change', function (e) 
			{
				if(e.detail.date.length == 2 && formatDate(e.detail.date[0]) != formatDate(e.detail.date[1]))
				{
					/*console.log(e.detail.formatted_date); 
					console.log(e.detail.date);**/
					//log_obj("Calendar/Picked",e);
					calendar.html(formatDate(e.detail.date[0])+"-"+formatDate(e.detail.date[1]));
					getValues(e.detail.date[0], e.detail.date[1]);
					instance.hide();
				}
			});
		}
	</script>
	</head>
	<body onload="start()">
		<div class="top-bar">История устройства</div>
		<? require('templates/menu.php'); ?>
		<div id="page" style="">
			Период: <i id="calendar" class="button"></i>
			<div id="errorMSG" class="alert alert-secondary">Загрузка данных ....</div>
			<div id="chart-wrapper" style="display:none"><div id="chartdiv" style="height:500px;"></div></div>
			<div id="table-wrapper" style="display:none">
				<table>
				<thead>
					<tr>
						<th>Дата/Время</th>
						<th>Значение (Значения)</th>
				</tr>
				</thead>
				<tbody id="table-values">
				</tbody>
				</table>
			</div>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

