<html>
	<? require_once('include.php'); ?>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	<script>
		function show()
		{
			Lobibox.notify('info', {
				size: 'mini',
				rounded: true,
				position: 'bottom right',
				
				msg: 'Lorem ipsum dolor sit amet hears farmer indemnity inherent.'
			  });
		}
		</script>
	</head>
	<body onload="show()">
		<div onclick="show()">SHOW</div>
		<div class="lobibox-notify-wrapper bottom right"></div>
		
		<? require('templates/bottom.php'); ?>
	</body>
</html>

