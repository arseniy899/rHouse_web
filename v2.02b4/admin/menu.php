<div id="menu">
	<a href="index.php">Главная</a>
	<!--<a href="unit.edit.php">Карточка устройства</a>-->
	<a href="unit.create.php">Добавить устройство</a>
	<a href="db.php">Database connection</a>
	<a href="uscripts.editor.php">Редактор сценариев</a>
	<a href="wifi.php">WiFi connection</a>
	<a href="hub.php">Hub settings</a>
	<a href="units.php">Устройства</a>
	<a href="drivers.php">Драйверы</a>
	<a href="triggers.php">Триггеры</a>
	<a href="sections.php">Разделы</a>
	<a href="schedule.php">Расписание</a>
	<a href="users.php">Пользователи</a>
	<a href="units_def.php">Виды устройств</a>
	<a href="table.show.php?table=events&order=`id` DESC">События</a>
	<a href="table.show.php?table=alerts&order=`id` DESC">Предупреждения</a>
	<a href="update.install.php">Проверить обновления</a>
</div> 
<div class="menu_icon"  >
	<i class="icofont-navigation-menu" onclick="ReverseDisplay('menu');"></i>
	<a href="//<?=REMOTE_ROOT?>" style="color: red;padding-left:25px;"><i class="icofont-fix-tools icofont-1x"></i></a>
</div>
<div class="logout_icon" ><i class="icofont-logout" onclick="logout()"></i></div>
