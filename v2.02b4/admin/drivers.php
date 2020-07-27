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
	
		function search(query, div)
		{
			$.ajax({
				type: "POST",
				url: "api/hub.driver.available.php",
				success: function success(data)
				{
					var resp = jQuery.parseJSON(data).responce;
					if(resp.error == 0)
					{
						div.innerHTML = "";
						for (var i = 0; i < resp.data.length; i++)
						{
							var driver = resp.data[i];
							(function(driver)
							{
								var row = document.createElement('div');
								var name = document.createElement('div');
								var showHide = document.createElement('div');
								var desc = document.createElement('div');
								var install = document.createElement('div');
								div.appendChild(row);
								row.appendChild(name);
								row.style="margin-left: 10px;border-bottom: 1px solid #CCC;line-height: 46px;margin-right: 10px;padding-left: 25px;";
								
								name.innerHTML = driver.name;
								name.style = "display: inline;left:0px";
								
								install.style="float: right;display: inline;line-height: 15px;border: 1px solid #CCC;margin-top: 9px;height: 30px;margin-right: 15px;border-radius: 9px;";
								install.style.color = "#000";
								install.style.backgroundColor = "#FFF";
								install.className="button";
								install.innerHTML="Установить";
								install.onclick = function(){
									ShowLoading()
									$.ajax({
										type: "POST",
										url: "api/hub.driver.install.php",
										success: function success(data)
										{
											DismissLoading();
											var resp = jQuery.parseJSON(data).responce;
											if(resp.error == 0)
											{
												install.innerHTML="Установлено &nbsp;&#10003;";
												install.style.color = "";
												install.style.backgroundColor = "";
												install.className="button green";
											}
											Toast.showAjaxRes(resp);
										},
										//ContentType : "application/text; charset=utf-8",
										//dataType: "json",
										async: false,
										cache : false,
										data: {name:driver.name, url:driver.url}
										//data: {'data':JSON.stringify(values)},
									});
								};
								
								showHide.style="float: right;display: inline;line-height: 30px;border: 1px solid #CCC;margin-top: 9px;height: 30px;margin-right: 15px;width: 27px;border-radius: 9px;";
								showHide.align="center";
								showHide.innerHTML="<i class='icofont-rounded-expand'></i>";
								showHide.onclick = function(){
									ReverseDisplay(desc);
								};
								desc.innerHTML=driver.desc;
								desc.style="display: block; background: rgb(204, 204, 204) none repeat scroll 0% 0%; padding: 7px; font-family: 'Segoe UI Symbol'; margin: 2px;";
								desc.style.display="none";
								row.appendChild(install);
								row.appendChild(showHide);
								row.appendChild(desc);
							})(driver);
						}
						
					}
					else
						Toast.showAjaxRes(resp);
					
				},
				//ContentType : "application/text; charset=utf-8",
				//dataType: "json",
				async: false,
				cache : false,
				data: {search:query}
				//data: {'data':JSON.stringify(values)},
			});
		}			
		function drawSearch() 
		{
			container = document.body;
			var window = document.createElement('div');
			window.className = "popup";
			container.appendChild(window);
			
			shadow = document.createElement('div');
			shadow.className = "shadow";
			shadow.onclick = function(){
				window.remove();
				shadow.remove();
			};
			
			var close = document.createElement('div');
			close.className = "popup-close";
			close.onclick = function(){
				window.remove();
				shadow.remove();
			};
			var searchResults = document.createElement('div');
			searchResults.style.width = "100%";
			//searchResults.style.minHeight = "300px";
			searchResults.style.border = "1px solid #CCC";
			
			var input = document.createElement("input");
			input.className = "";
			input.type = 'text';
			input.placeholder = 'Начните вводить название...';
			window.appendChild(input);
			input.oninput = function() {
				search(input.value, searchResults);
			};
			container.appendChild(shadow);
			window.appendChild(searchResults);
			//window.appendChild(form);
			window.appendChild(close);
		}
		$(document).ready(function() 
		{	
			var table = createTable('page',
			{
				"url":"api/hub.driver.installed.php",
				"edit":true,
				"delete":true,
				"add":true,
				colums:
				{
					"name":
					{
						"name":"Название",
						"required":true
					},
					"ver":
					{
						"edit":false,
						"name":"Версия"
					}
					
				},
				"onDelete" : function(row, dom)
				{
					console.log("TableDel/Row",row);
					$.ajax({
						type: "POST",
						url: "api/hub.driver.uninstall.php",
						success: function success(data)
						{
							var resp = jQuery.parseJSON(data).responce;
							Toast.showAjaxRes(resp);
							if(resp.error == 0)
							{
								removeRowDom(dom);
							}
						},
						//ContentType : "application/text; charset=utf-8",
						//dataType: "json",
						async: false,
						cache : false,
						data: {name:row.name}
						//data: {'data':JSON.stringify(values)},
					});
				},
				"onAdd" : function(row, dom)
				{
					drawSearch();
				}
			});
			
		});
	</script>
	<body>
		<div class="top-bar">Драйверы</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

