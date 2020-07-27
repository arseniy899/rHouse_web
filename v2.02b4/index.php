<html>
	<? require_once('include.php'); ?>
	
	<head>
	<title>SmartHouse</title>
	<? require('templates/head.php'); ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.0/js/ion.rangeSlider.min.js" lazyload></script>
	<link 	href="//<?=REMOTE_ROOT?>/css/metro-grid.css?version=6" rel="stylesheet" type="text/css" lazyload>
	<script src="//<?=REMOTE_ROOT?>/js/metro-grid.js?version=7" 	type="text/javascript" lazyload></script>
	<link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/tail.datetime.css">
	<script src="//<?=REMOTE_ROOT?>/js/tail.datetime.js"></script>
	
	<script>
		var showNotes = <?=IO::getString('notes') ? 'true' : 'false'?>;
		window.addEventListener("load", function()
		{
			GridManager.loadGrid();
			if(showNotes)
			{
				window.history.pushState("", "", 'index.php');
				Lobibox.alert("info", {msg: `<h2>Что нового в этом выпуске:</h2><object type="text/html" data="NOTES.php" ></object>`});
			}
		});
		
	</script>
	</head>
	<body onload="">
		<div class="top-bar">Управление домом</div>
		<? require('templates/menu.php'); ?>
		<div id="page">
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
	
</html>

