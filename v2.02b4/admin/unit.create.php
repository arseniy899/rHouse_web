<html>
	<? require_once('include.php'); ?>
	<head>
	<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	</head>
	<script>
		$( document ).ready(function() {
			
		
			$(".colorPickSelector").colorPick({
				'initialColor': '#27AE60',
				'allowRecent': true,
				'recentMax': 5,
				'onColorSelected': function() {
					this.element.css({'backgroundColor': this.color, 'color': this.color});
					document.getElementById("color").value = this.color.replace("#","");
				}
			});
		});

		//$('select').val(unit.icon);

		/*$("select").imagepicker({
			hide_select : true,
			show_label  : false,
			selected : function(select, picker, option, event){
				$('#icon').val( $("select").data('picker').selected_values()) ;
			}
		});*/
	</script>
	<body onload="">
		<div class="top-bar">Обслуживание. Добавление нового устройства</div>
		<? require('menu.php'); ?>
		<div id="page" style="">
			<form id="form" class="form no-reload" action="api/units.create.php">
				<!--<div class='form-item-wrapper' id="icons-wrapper">-->
				
				<div class='form-item-wrapper'><label for='unid'>UNIT iD</label>
				<select id='unid' class="">
					<?
					$units = Unit::getUnitsDefs();
					$i = 1;
					foreach($units as $row)
					{
						
						?>
							<option value="<?=$row['unid'];?>">  <?=$row['unid'].": ".$row['description'];?>  </option>
						<?
						$i++;
					}
					
					?>
				</select>
				</div>
				<div class='form-item-wrapper'><label for='intf'># Interface</label>
				<input class="" type='number' id='intf' value='14'></div>
				<div class='form-item-wrapper'><label for='intf'>UI show</label>
				<input class="" type='text' id='uiShow' value='1'></div>
				<div class='form-item-wrapper'><label for='color'>Color</label>
				<input class="short" type='text' id='color' value=''>
				<div class='colorPickSelector'></div>
				
				</div>
				
				
				
				 
				<!--<select class="image-picker show-html ">
					<?
					/*$icons = Misc::getDirFiles('/img/metro-icons');
					$i = 1;
					foreach($icons as $name)
					{
						$name = substr($name, 1);
						$fname = substr($name, strrpos($name, '/') + 1);
						//echo $fname.'<br />';
						?>
							<option data-img-src="<?=$name;?>" data-img-class="first" data-img-alt="<?=$name;?>" value="<?=$fname;?>">  <?=$fname;?>  </option>
						<?
						$i++;
					}*/
					
					?>
				
				 
				</select>-->
				
				
				
				
				<input type="submit" value="Сохранить">
				</div>
			</form>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

