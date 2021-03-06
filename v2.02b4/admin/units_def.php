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
				"table" : "units_def",
				"edit": false,
				"add": false,
				"delete": false,
				colums:
				{
					"unid":
					{
						"name":"ID вида",
						"edit":false
					},
					"description":
					{
						"name":"Название (если не задано)"
					},
					"units":
					{
						"name":"Ед. измерения"
					},
					"direction":
					{
						"name":"Направление",
						"type":"select",
						"values":"I,O,IO"
					},
					"possValues":
					{
						"name":"Возможные значение",
						"type":""
					},
					"icon":
					{
						"name":"Иконка (если не задана)",
						"type":"metro-icon"
					}
					
				}
			});
			
		});
	</script>
	<body>
		<div class="top-bar">Units types</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
		
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

