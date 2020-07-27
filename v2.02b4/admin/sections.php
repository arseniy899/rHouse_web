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
				"table" : "units_sections",
				"edit":true,
				"delete":true,
				"add":true,
				colums:
				{
					"id":
					{
						"name":"ID",
						"edit":false
					},
					"name":
					{
						"name":"Название",
						"required":true
					},
					"isDefHidden":
					{
						"name":"Скрыта по-умолчанию",
						"type":"bool"
					}
					
				}
			});
			
		});
	</script>
	<body>
		<div class="top-bar">Sections</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

