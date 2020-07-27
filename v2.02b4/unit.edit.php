<html>
	<? require_once('include.php'); ?>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	<link 	href="css/metro-grid.css?version=6" rel="stylesheet" type="text/css" lazyload>
	<script src="js/metro-grid.js?version=5" 	type="text/javascript" lazyload></script>
	<script src="js/editor.js?version=5" 	type="text/javascript" lazyload></script>
	
	</head>
	<script type="text/javascript">
	var unit = new Unit('','');
	var editor;
	unit.id=<?=IO::getString('id');?>;
	unit.getInfoFRDB(function(unit)
	{
		
		editor = new Editor("form", 
		{
			popup: false, 
			submitUrl:	API_URL+"units.update.php?id="+unit.id,
			fields: 
			{
				"unid":
				{
					"name":"Тип",
					"edit":false,
					"type":"unitsDef"
				},
				"setid":
				{
					"name":"ID устройства",
					"edit":false
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
				"icon":
				{
					"name":"Иконка",
					"type":"metro-icon"
				},
				"name":
				{
					"name":"Название"
				},
				"uiShow":
				{
					"name":"Видимость в интерфейсе",
					"type":"bool"
				}
			}
		},unit);
		
		
	});
	function deleteUnit()
	{
		if (window.confirm('Вы уверены, что хотите удалить устройство \''+unit.name+'\'?')) 
		{
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					var res = JSON.parse(this.responseText).responce;
					Toast.showAjaxRes(res);
					if(res.error == 0)
						window.open('index.php', '_self');
				}
			};
			xhttp.open("GET", 'api/units.delete.php?id='+unit.id, true);
			xhttp.send();
		}
	}
	</script>
	<body onload="">
		<div class="top-bar">Редактирование карточки устройства</div>
		<? require('templates/menu.php'); ?>
		<div id="page" style="">
			
			<div id="form"></div>
			<div class="button red"  onclick="deleteUnit()">Удалить устройство</div>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

