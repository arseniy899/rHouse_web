<? require_once('include.php'); ?>
<html>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	</head>
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script src="//<?=REMOTE_ROOT?>/js/tables.js" 	lazyload		type="text/javascript"></script>
	<script src="//<?=REMOTE_ROOT?>/js/editor.js" 	lazyload		type="text/javascript"></script>
	<script src="//<?=REMOTE_ROOT?>/js/cronstrue.js" lazyload async=""></script>
	<script src="//<?=REMOTE_ROOT?>/js/later.js" lazyload type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() 
		{	
			createTable('page',
			{
				"table" : "users",
				"edit":true,
				"delete":true,
				"add":true,
				colums:
				{
					"login":
					{
						"name":"Логин"
					},
					"password":
					{
						"name":"Пароль",
						"type":"password"
					},
					"email":
					{
						"name":"E-mail",
					},
					"isAdmin":
					{
						"name":"Администратор?",
						"type":"bool"
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
		<div class="top-bar">Users</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			<!--<div class="table-add fab"><i class="icofont-ui-add"></i></div>-->
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

