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
				"table" : "triggers",
				"edit":true,
				"delete":true,
				"add":true,
				colums:
				{
					"unid":
					{
						"name":"Вид устройств",
						"edit":true,
						"type":"unitsDef"
					},
					/*"setid":
					{
						"name":"ID устройства",
						"edit":true
					},*/
					"unit":
					{
						"name":"Устройство",
						"type":"units"
					},
					"type":
					{
						"name":"Условие",
						"type":"condition"
					},
					"value":
					{
						"name":"Значение [a]",
						"type":"number"
					},
					"direct":
					{
						"name":"Направление",
						"type":"direction"
					},
					"scriptName":
					{
						"name":"Скрипт",
						"type":"uscript"
					}
					
				}
			});
			
			/*$('.table-add').click(function(){
				new Editor(container, 
				{
					popup: true, 
					fields: 
					{
						"unid":
						{
							"name":"ID типа",
							"edit":true,
							"type":"unitsDef"
						},
						"setid":
						{
							"name":"ID устройства",
							"edit":true
						},
						"unit":
						{
							"name":"Устройство",
							"edit":true,
							"type":"units"
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
							"name":"Видимость",
							"type":"bool"
						}
					},
					onsubmit: function(values)
					{
						console.log("Tables/OnSubmit/values",values,"row=",rows[values.id]);
						values.name = Editor.unitsDef[values.unid].description;
						$.ajax({
							type: "POST",
							url: tableConfig.saveUrl,
							success: function success(data)
							{
								var resp = jQuery.parseJSON(data).responce;
								Toast.showAjaxRes(resp);
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
			});*/
		});
	</script>
	<body>
		<div class="top-bar">Triggers</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			<!--<div class="table-add fab"><i class="icofont-ui-add"></i></div>-->
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

