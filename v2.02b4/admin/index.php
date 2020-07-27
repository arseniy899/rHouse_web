<html>
	<? require_once('include.php'); ?>
<?
if(isset($_GET['SYS_PWD']) && $_GET['SYS_PWD'] == 'Happy')
{
	$_SESSION['SYS_SHOW'] = true;
	echo "<script>alert('SYS show');</script>";
}
?>
	<head>
	<title>SmartHouse</title>
	<? require('templates/head.php'); ?>
	<style>
	
	.dashboard table {
		width: 100%;
		max-width: 450px;
		margin: 0 auto;
	}
	.dash-board table, td, th {
		vertical-align: middle;
		padding-right: 5px;
	}
	.dashtable tr,td {
		height: 25px;
		border-bottom: 1px solid #ebebeb;
	}
	.dash-board table {
		border-collapse: separate;
		border-spacing: 0;
		white-space: nowrap;
	}
	.val-field {
		color: gray;
	}
	</style>
	
	</head>
	<body>
		<div class="top-bar">Обслуживание.Главная</div>
		<? require('menu.php'); ?>
		<div id="page">
			<table class="dashboard">
				<?
				$data = array();
				$data['Web_version'] = $CONFIG['version'];
				$data += Hub::getPayload(array(
					"cmd"		=> "system.stat.get"
				));
				foreach($data as $key => $value)
				{
					?>
						<tr><td><?=$key?></td><td class="val-field"><?=$value?></td></tr>
					<?
				}
				?>
			</table>
			<div class="button green"  onclick="proceedLinkBg('api/hub.system.shell.php',{'cmd':'python3 -V'})"> Python ver</div>
			<div class="button red"  onclick="if (window.confirm('Do you really want to reboot?')) proceedLinkBg('api/hub.system.shell.php',{'cmd':'sudo reboot'})">Reboot</div>
			<div class="button red"  onclick="if (window.confirm('Do you really want to power off?')) proceedLinkBg('api/hub.system.shell.php',{'cmd':'sudo poweroff'})">Power Off</div>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

	