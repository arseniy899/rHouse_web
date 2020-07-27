<? require_once('include.php'); ?>
<html>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	</head>
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script src="//<?=REMOTE_ROOT?>/js/tables.js" 	lazyload		type="text/javascript"></script>
	<script src="//<?=REMOTE_ROOT?>/js/editor.js" 	lazyload		type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() 
		{	
			createTable('page',
			{
				"table" : "units_run",
				"edit":true,
				"delete":true,
				"add":false,
				saveUrl		: API_URL+"units.update.php",
				addUrl 		: API_URL+"units.create.php",
				deleteUrl 	: API_URL+"units.delete.php",
				
				colums:
				{
					"unid":
					{
						"name":"ID вида",
						"edit":false
					},
					"setid":
					{
						"name":"ID устройства",
						"edit":false
					},
					"name":
					{
						"name":"Название"
					},
					"sectId":
					{
						"name":"Раздел",
						"type":"section"
					},
					"color":
					{
						"name":"Цвет ячейки",
						"type":"color"
					},
					"iconCust":
					{
						"name":"Иконка",
						"type":"metro-icon"
					},
					"uiShow":
					{
						"name":"Отображать в интерфейсе",
						"type":"bool"
					}
					
				}
			});
			
			$('.table-add').click(function(){
				var editor = new Editor(container, 
				{
					popup: true, 
					fields: 
					{
						"unid":
						{
							"name":"Тип",
							"edit":true,
							"type":"unitsDef"
						},
						"setid":
						{
							"name":"ID устройства",
							"edit":true
						},
						"sectId":
						{
							"name":"Раздел",
							"type":"section"
						},
						"color":
						{
							"name":"Цвет ячейки",
							"type":"color"
						},
						"iconCust":
						{
							"name":"Иконка",
							"type":"metro-icon"
						},
						"uiShow":
						{
							"name":"Отображать в интерфейсе",
							"type":"bool"
						}
					},
					onsubmit: function(values)
					{
						console.log("Tables/OnSubmit/values",values,"row=",rows[values.id]);
						values.name = Editor.unitsDef[values.unid].description;
						$.ajax({
							type: "POST",
							url: API_URL+"units.create.php",
							success: function success(data)
							{
								var resp = jQuery.parseJSON(data).responce;
								Toast.showAjaxRes(resp);
								if(resp.error == 0)
									editor.destroy();
								drawRow(values);
							},
							//ContentType : "application/text; charset=utf-8",
							//dataType: "json",
							async: false,
							cache : false,
							data: values
							//data: {'data':JSON.stringify(values)},
						});
					}
				});
			});
		});
	</script>
	<body>
		<div class="top-bar">Устройства</div>
		<? require('templates/menu.php'); ?>
		
		<div id="page" style="">
			<div class="table-add fab"><i class="icofont-ui-add"></i></div>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

