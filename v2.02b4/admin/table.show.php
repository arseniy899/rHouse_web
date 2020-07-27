

<html>
	<? require_once('include.php'); ?>
	<head>
		<title>SmartHouse</title>
		<? require('templates/head.php'); ?>
	</head>
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.1/js/dataTables.fixedColumns.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.css">
	<?
		$table = IO::getString('table');
		
		if( empty($table) )
			$table = 'events';
		
		$order = IO::getString('order');
		$edit = IO::getInt('edit');
		if( empty($order) )
			//$order = '`id` DESC';
			$sql = "SELECT * FROM `{$table}` LIMIT 1000";
			else
			$sql = "SELECT * FROM `{$table}` ORDER BY {$order} LIMIT 1000";
		
		$result = mysqli_query($db, $sql);
		$rows=[];
		//exit($sql);
		while ($row = mysqli_fetch_assoc($result))
			$rows[] = $row;
		?>
	<script type="text/javascript">
		function search() 
		{
			// Declare variables
			var input, filter, table, tr, td, i;
			input = document.getElementById("search");
			filter = input.value.toUpperCase();
			table = document.getElementById("table");
			tr = table.getElementsByTagName("tr");

			// Loop through all table rows, and hide those who don't match the search query
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td");
				for (q = 0; q < td.length; q++) {
					if (td[q]) {
						if (td[q].innerHTML.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							return;
						} else
							tr[i].style.display = "none";
					}
				}
			}
		}
		var table;
		$(document).ready(function() {
			// Setup - add a text input to each footer cell

			$('.table tfoot th').each(function() {
				var title = $(this).text();
				if (title.length > 0)
					$(this).html('<input width="100%" type="text" placeholder="' + title + '" />');
			});

			// DataTable
			table = $('.table').DataTable({
				//"scrollY": "76vh",
				"scrollX": true,
				"order": [
					[0, "desc"]
				],
				"pageLength": 150,
				"dom": 'Bfrtip',
				"lengthMenu": [
					[10, 25, 50, -1],
					[10, 25, 50, "All"]
				],
				/*"fixedColumns":   {
					"iLeftColumns": 0,
					"iRightColumns":2,
					"heightMatch": 'auto'
				},*/
				buttons: [
					'copy', 'csv', 'excel', 'pdf', 'print'
				]
			});

			// Apply the search
			table.columns().every(function() {
				var that = this;

				$('input', this.footer()).on('keyup change', function() {
					if (that.search() !== this.value) {
						that
							.search(this.value)
							.draw();
					}
				});
			});
			/*$(table.table().container())
			  .find('div.dataTables_paginate')
			  .css( 'display', table.page.info().pages <= 1 ?
				   'none' :
				   'block'
			  )*/
			$('.table-add').click(function() {

				$("#edf0").html(id);
				var $clone = $('#row0').clone(true).removeClass('hide table-line');
				$TABLE.find('table').append($clone);

				$ro = table.row.add($("<tr id='rowf" + id + "'>" + $clone.html() + "</tr>")[0]);
				table.draw();
				var $row = $("#rowf" + id);
				$row.find('td.id').html(id);
				$row.find('input').html(id);

				$row.find('td.id').attr("id", "edf" + id);
				$row.find('form').attr("id", "f" + id);
				$(".DTFC_Cloned").find('form#f0').attr("id", "f" + id);
				table.order([0, 'asc']);

				id++;

			});
			$('#table').on('click', '.table-remove', function() 
			{
				r = confirm('Delete?');
				if (r) {
					if ($(this).attr("id"))
					{
						var xhttp;
						var self = this;
						xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function()
						{
							if (this.readyState == 4 && this.status == 200) 
							{
								var myArr = JSON.parse(this.responseText).responce;
								if(myArr.error == 0)
								{
									table.row($(self).parents('tr'))
										.remove()
										.draw();
								}
								else
									Toast.showAjaxRes(myArr.error,myArr.desc+"\n"+myArr.message);
							}
						};
						xhttp.open("GET", "api/table.values.delete.php?m=<?=$table;?>&id="+ $(this).attr("id"), true);
						xhttp.send();
					}
				}
			});


			//$('#table').on('click', '.save-all',
			$('.save-all').click(function() {
				for (var i = 1; i < (id); i++)
					submit("f" + i);
			});
			$('.table-up').click(function() {
				var $row = $(this).parents('tr');
				if ($row.index() === 1) return; // Don't go above the header
				$row.prev().before($row.get(0));
			});

			$('.table-down').click(function() {
				var $row = $(this).parents('tr');
				$row.next().after($row.get(0));
			});

			// A few jQuery helpers for exporting only
			jQuery.fn.pop = [].pop;
			jQuery.fn.shift = [].shift;

			$BTN.click(function() {
				var $rows = $TABLE.find('tr:not(:hidden)');
				var headers = [];
				var data = [];

				// Get the headers (add special header logic here)
				$($rows.shift()).find('th:not(:empty)').each(function() {
					headers.push($(this).text().toLowerCase());
				});

				// Turn all existing rows into a loopable array
				$rows.each(function() {
					var $td = $(this).find('td');
					var h = {};

					// Use the headers from earlier to name our hash keys
					headers.forEach(function(header, i) {
						h[header] = $td.eq(i).text();
					});

					data.push(h);
				});

				// Output the result
				$EXPORT.text(JSON.stringify(data));
			});

			$('#table').on('submit', '.form-table', function(event)
				//$(".form").submit(function (event)
				{
					event.preventDefault();
					//var $form = $(this),url = $form.attr('action');
					submit("" + jQuery(this).attr("id"));
				});
			$(".form_new").submit(function(event) {
				/* stop form from submitting normally */


				/* get the action attribute from the <form action=""> element */
				var $form = $(this),
					url = $form.attr('action');

				var $inputs = $("#" + jQuery(this).attr("id") + ' td:input');
				// get an associative array of just the values.
				var values = {};
				$("#row" + jQuery(this).attr("id")).find('input').each(function() {
					var name = jQuery(this).attr("name");
					if (name != null)
						values[name] = $(this).val();
				});

				var $row = $("#row" + jQuery(this).attr("id"));
				//alert(name);
				$("#row" + jQuery(this).attr("id")).find('td').each(function() {
					var name = jQuery(this).attr("name");

					if (name != null)
						values[name] = $(this).html();
				});

				/* Send the data using post with element id name and name2*/
				var posting = $.post(url, values);
				$("div#loadingAlert").css('visibility', 'visible');
				$("div#loadingAlert").css('visibility', 'hidden');
				$("div#successAlert").css('visibility', 'hidden');
				/* Alerts the results */
				posting.done(function(data) {
					//alert(data);
					var obj = jQuery.parseJSON(data);

					//$("div#errorAlert").css('visibility', 'hidden');
					//alert(obj.responce.error);
					if (typeof obj == 'object') {
						//$("#row"+jQuery(this).attr("id")).find('input').val('1');
						//$("#row"+jQuery(this).attr("id")).eq(1).find('td').html(obj.id);
						$("div#successAlert").css('visibility', 'visible');
					} else {
						$("div#errorAlert").css('visibility', 'visible');
						$("span#errorAlertText").html("#Ошибка получения данных");
					}
					//$("#result").empty().append(content);

				});
			});
			$(".form_menu").submit(function(event) {
				/* stop form from submitting normally */
				event.preventDefault();

				/* get the action attribute from the <form action=""> element */
				var $form = $(this),
					url = $form.attr('action');

				var $inputs = $("#" + jQuery(this).attr("id") + ' td:input');
				// get an associative array of just the values.
				var values = {};
				$("#row" + jQuery(this).attr("id")).find('input').each(function() {
					var name = jQuery(this).attr("name");
					if (name != null)
						values[name] = $(this).val();
				});

				var $row = $("#row" + jQuery(this).attr("id"));
				//alert(name);
				$("#row" + jQuery(this).attr("id")).find('td').each(function() {
					var name = jQuery(this).attr("name");

					if (name != null)
						values[name] = $(this).html();
				});

				/* Send the data using post with element id name and name2*/
				var posting = $.post(url, values);
				$("div#loadingAlert").css('visibility', 'visible');
				$("div#loadingAlert").css('visibility', 'hidden');
				$("div#successAlert").css('visibility', 'hidden');
				/* Alerts the results */
				posting.done(function(data) {
					//alert(data);
					var obj = jQuery.parseJSON(data);

					//$("div#errorAlert").css('visibility', 'hidden');
					//alert(obj.responce.error);
					if (typeof obj == 'object') {
						//$("#row"+jQuery(this).attr("id")).find('input').val('1');
						//$("#row"+jQuery(this).attr("id")).eq(1).find('td').html(obj.id);
						$("div#successAlert").css('visibility', 'visible');
					} else {
						$("div#errorAlert").css('visibility', 'visible');
						$("span#errorAlertText").html("#Ошибка получения данных");
					}
					//$("#result").empty().append(content);

				});
			});
		});
		var $TABLE = $('#table');
		var $BTN = $('#export-btn');
		var $EXPORT = $('#export');
		

		function proceedLink(url, id) {
			var values = {};
			values['id'] = id;
			/* Send the data using post with element id name and name2*/
			var posting = $.post(url, values);
			$.ajax({
				type: "POST",
				url: url,
				success: success,
				ContentType: "application/text; charset=utf-8",
				async: false,
				cache: false,
				data: values
			});
			$("div#loadingAlert").css('visibility', 'visible');
			$("div#loadingAlert").css('visibility', 'hidden');
			$("div#successAlert").css('visibility', 'hidden');
			/* Alerts the results */
		};

		function success(data) {
			//alert(data);
			var obj = jQuery.parseJSON(data);

			//$("div#errorAlert").css('visibility', 'hidden');
			//alert(obj.responce.error);
			if (typeof obj == 'object') {
				if (obj.responce.error == 0)
					Toast.showAjaxRes(0);
				else
					Toast.showAjaxRes(obj.responce.error, obj.responce.desc);
			} else
				Toast.showAjaxRes(1, 'Ошибка получения данных');
			//$("#result").empty().append(content);

		}
		

		/* attach a submit handler to the form */
		(function blink() 
		{
			$('div#loadingAlert').fadeOut(800).fadeIn(1000, blink);
		})();
		$.urlParam = function(name) {
			var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
			if (results == null) {
				return null;
			} else {
				return results[1] || 0;
			}
		}
		//$(".input").focusout(
		function success(data)
		{
			//alert(data);
			var obj = jQuery.parseJSON(data);

			//
			//alert("loading hidden");
			if (typeof obj == 'object') {
				if (obj.responce.error == 0) {

					$("div#successAlert").css('visibility', 'visible');

				} else {
					$("div#errorAlert").css('visibility', 'visible');
					$("span#errorAlertText").html("#" + obj.responce.error + ": " + obj.responce.desc);
				}
			} else {
				$("div#errorAlert").css('visibility', 'visible');
				$("span#errorAlertText").html("#Ошибка получения данных");
			}
			$("div#loadingAlert").css('visibility', 'hidden');
			//$("#result").empty().append(content);

		}

		function submit(id) 
		{
			/* stop form from submitting normally */
			event.preventDefault();

			/* get the action attribute from the <form action=""> element */
			var $form = $("#" + id),
				url = $form.attr('action');

			var $inputs = $("#" + id + ' td:input');
			// get an associative array of just the values.
			var values = {};
			$("#row" + id).find('input').each(function() {
				var name = jQuery(this).attr("name");
				if (name != null)
					values[name] = $(this).val();
			});
			$("#row" + id).find('td').each(function() {
				var name = jQuery(this).attr("name");
				if (name != null)
					values[name] = $(this).html();
			});

			/* Send the data using post with element id name and name2*/
			var posting = $.post(url, values);
			
			/* Alerts the results */
			posting.done(function(data) {
				//alert(data);
				var obj = jQuery.parseJSON(data);

				//$("div#errorAlert").css('visibility', 'hidden');
				//alert(obj.responce.error);
				if (typeof obj == 'object') {
					if (obj.responce.error == 0)
						Toast.showAjaxRes(0);
					else {
						Toast.showAjaxRes(obj.responce.error,obj.responce.desc);
					}
				} else {
					Toast.showAjaxRes(1,'Ошибка получения данных');
				}
				//$("#result").empty().append(content);

			});
		}
	</script>
	<style>
		tfoot input {
		width: 100%;
		}
	</style>
	<body onload="">
		<div class="top-bar">Просмотр таблицы '<?=$table;?>'</div>
		<? require('menu.php'); ?>
		<div id="page" style="">
			<?
				$maxId=0;
				if (mysqli_num_rows($result) > 0) 
				{
				?>
			<div id="tables" class="table-editable">
				<table class="table table-striped table-hover " style="border-collapse:collapse;border:solid 0px #CCC;"  id="table" width="99%">
					<thead >
						<tr>
							<?
								foreach(array_keys($rows[0]) as $name)
									echo "<th>{$name}</th>";
								if($edit != 0)
								{
								?>
							<th></th>
							<th></th>
							<?}?>
						</tr>
					</thead>
					<tbody>
						<?php
							
							$id = 0;
							foreach(($rows) as $row)
							{
								//$images = explode(";",$row['images']);
								if(array_key_exists ('id', $row) )
								{
									$id = $row['id'];
									if($maxId<$id)
										$maxId=$id;
								}
								else
									$id++;
							?>
						<tr id="rowf<?echo $id;?>">
							<?
								foreach($row as $key => $value)
								{
									$value = htmlentities($value);
								
									echo "<td contenteditable='".($key!="id" ? "true" : "false")."' name='{$key}'>{$value}</td>";
								}
								if($edit != 0)
								{
								?>
							<td>
								<form class="form-table" id="f<?=$id;?>" method="post" action="api/table.values.save.php?m=<?=$table;?>">
									<button class="" type="submit"><span class="glyphicon glyphicon-floppy-saved"></span></button>
								</form>
							</td>
							<td>
								<span class="table-remove glyphicon glyphicon-remove" id="<?=$id;?>"></span>
							</td>
							<?}?>
						</tr>
						<?php 
							}$maxId++;
							?>
						<!-- This is our clonable table line -->
						<tr class="row-hide" id="row0">
							<td contenteditable="false" class="id" id="edf0" name="id"><?=$maxId;?></td>
							<?
								if(count($row) > 0)
								foreach($row as $key => $value)
								{
									if($key !="id")
									echo "<td contenteditable='true' name='{$key}'></td>";
								}
								if($edit != 0)
								{
								?>
							<td>
								<!--<form class="form" id='fnew' method='post' action='api/table.values.save.php?m=<?=$table;?>'>
									<button class="" type="submit"><span class="glyphicon glyphicon-floppy-saved"></span></button>
								</form>-->
							</td>
							<td>
								<span class="table-remove glyphicon glyphicon-remove"></span>
							</td>
							<?}?>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<?
								if(count($rows) > 0)
									foreach(array_keys($rows[0]) as $name)
										echo "<th>{$name}</th>";
								if($edit != 0)
								{
								?>
							<th></th>
							<th></th>
							<?}?>
						</tr>
					</tfoot>
				</table>
			</div>
				<script type="text/javascript">var id = <?=$maxId;?>;</script>
			<?
				}?>
			<div class="table-add fab-2"><i class="icofont-ui-add"></i></div>
		</div>

		<? require('templates/bottom.php'); ?>
	</body>
</html>

