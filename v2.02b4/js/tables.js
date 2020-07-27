var table;
var container;
var tBody;
var tableData = {};
var tableConfig = {};
var rows = {};
function createTable(mContainer, config)
{
	if(isElementDOM(mContainer))
		container = mContainer;
	else
		container = document.getElementById(mContainer);
	
	tableConfig = config;
	
	var uiColums = [];
	var request = {};
	table = document.createElement('table');
	
	table.className = "table table-striped table-hover";
	container.appendChild(table);
	var thead = document.createElement('thead');
	table.appendChild(thead);
	tBody = document.createElement('tBody');
	table.appendChild(tBody);
	var tr = document.createElement('tr');
	thead.appendChild(tr);
	for (var key in  config.colums)
	{
		var column = config.colums[key];
		var td = document.createElement('th');
		
		td.appendChild(document.createTextNode(column.name))
		tr.appendChild(td)
		switch(column.type)
		{
			case "metro-icon":
				request.icons = true;
			break;
			case "section":
				request.sections = true;
			break;
			case "units":
				request.units = true;
				request.unitsDef = true;
			break;
			case "unitsDef":
				request.unitsDef = true;
			break;
			case "condition":
				request.conditions = true;
			break;
			case "direction":
				request.directions = true;
			break;
			case "scheduleType":
				request.scheduleTypes = true;
			break;
			
		}
	}
	if(config.edit)
	{
		var td = document.createElement('td');
		td.appendChild(document.createTextNode(" "));
		td.style.width = "25px";
		tr.appendChild(td)
	}
	if(config.delete)
	{
		var td = document.createElement('td');
		td.appendChild(document.createTextNode(" "));
		td.style.width = "25px";
		tr.appendChild(td)
	}
	if(config.add)
	{
		table.style.marginBottom = "100px";
		//<div class="table-add fab-2"><i class="icofont-ui-add"></i></div>
		var add = document.createElement('div');
		add.className = "table-add fab";
		add.innerHTML = '<i class="icofont-ui-add"></i>';
		add.onclick = function()
		{
			if(typeof tableConfig.onAdd != "undefined")
			{
				tableConfig.onAdd();
			}
			else
			{
				var editor = new Editor(container, {
					popup: true, 
					fields: tableConfig.colums,
					onsubmit: function(values)
					{
						console.log("Tables/OnSubmit/values",values,"row=",rows[values.id]);
						var url =  tableConfig.saveUrl;
						if(typeof tableConfig.addUrl != "undefined")
							url = tableConfig.addUrl;
						
						$.ajax({
							type: "POST",
							url: url,
							success: function success(data)
							{
								var resp = jQuery.parseJSON(data).responce;
								if(resp.error == 0)
								{
									drawRow(values);
									editor.destroy();
								}
								//rows[values.id].entry = values;
								else
									Toast.showAjaxRes(resp);
							},
							//ContentType : "application/text; charset=utf-8",
							//dataType: "json",
							async: false,
							cache : false,
							data: values
							//data: {'data':JSON.stringify(values)},
						});

						
					}
				});
			}
		};
		container.appendChild(add);
	}
	if(typeof config.url == "undefined")
	{
		if(typeof config.table != "undefined")
			config.url = ADMIN_URL+"table.values.select.php?m="+config.table;
		else
			config.url = ADMIN_URL+"table.values.select.php?m=units_run";
	}
	if(typeof config.saveUrl == "undefined")
	{
		if(typeof config.table != "undefined")
			config.saveUrl = ADMIN_URL+"table.values.save.php?m="+config.table;
		else
			config.saveUrl = ADMIN_URL+"table.values.save.php?m=units_run";
	}
	$.ajax({
		type: "GET",
		url: config.url,
		success: function success(data)
		{
			var resp = jQuery.parseJSON(data).responce;
			console.log("TableLoad/Data",resp);
			if(resp.error == 0)
			{
				Editor.icons = resp.data.icons;
				Editor.sections = resp.data.sections;
				Editor.iconsPath = resp.data.iconsPath;
				Editor.units = resp.data.units;
				Editor.unitsDef = resp.data.unitsDef;
				Editor.conditions = resp.data.conditions;
				Editor.directions = resp.data.directions;
				Editor.scheduleTypes = resp.data.scheduleTypes;
				tableData = resp.data.rows;
				drawTable();
			}
			else
				Toast.showAjaxRes(resp);
		},
		//ContentType : "application/text; charset=utf-8",
		//dataType: "json",
		async: false,
		cache : false,
		data: request
		//data: {'data':JSON.stringify(values)},
	});

}
function getFullPathIcon(icon)
{
	if(icon == '')
		return '';
	for (var i = 0; i < Editor.icons.length; i++)
	{
		var iconF = Editor.icons[i];
		if(iconF.endsWith(icon))
			return iconF;
	}
	return "";
}
function drawTable()
{

	for (var i = 0; i < tableData.length; i++)
	{
		//if(tableData[i].id >= 0 || typeof tableConfig.showSystem != "undefined")
			drawRow(tableData[i]);
	}
}
function redrawRow(id)
{
	var row = rows[id];
	//rows[id].tr.remove();
	while (row.tr.firstChild) 
		row.tr.removeChild(row.tr.firstChild);
	drawRow(row.entry, row.tr);
}
function removeRow(id)
{
	var row = rows[id];
	row.tr.remove();
	
}
function removeRowDom(dom)
{
	dom.remove();
	
}
function drawRow(entry, tr)
{
	/*
	ENTRY:
	{
		"id": "5",
		"unid": "01B8",
		"setid": "0170",
		"lastValue": "400000",
		"needSetValue": "1",
		"lastTime": "2019.06.08 16:24:48",
		"interface": "0",
		"timeAdded": "",
		"name": "Уличный термометр на терассе",
		"alive": "1",
		"sectId": "1",
		"uiShow": "1",
		"color": "FFF",
		"iconCust": "icons8-temperature-outside-100.png"
	}
	*/
	rows[entry.id] = {};
	if(typeof tr == "undefined")
	{
		tr = document.createElement('tr');
		tBody.appendChild(tr);
	}
	rows[entry.id].tr = tr;
	rows[entry.id].entry = entry;
	for (var key in  tableConfig.colums)
	{
		var column = tableConfig.colums[key];
		var td = document.createElement('td');
		//console.log("CreateTbel/","key",key,"Entry",entry,"column=",column);
		if(key == "unit" && column.type == "units")
		{
			createInteractive(td, column,entry.unid, entry.setid);
		}
		else
			createInteractive(td, column,entry[key]);
			
		tr.appendChild(td);
	}
	if(tableConfig.edit)
	{
	
		var td = document.createElement('td');
		var div = document.createElement("div");
		div.innerHTML = '<i class="icofont-ui-edit"></i>';
		td.appendChild(div);
		tr.appendChild(td);
		td.onclick = function()
		{
			var editor = new Editor(container, {
				popup: true, 
				fields: tableConfig.colums,
				onsubmit: function(values)
				{
					//console.log("Tables/OnSubmit/values",values,"row=",rows[values.id]);
					$.ajax({
						type: "POST",
						url: tableConfig.saveUrl,
						success: function success(data)
						{
							var resp = jQuery.parseJSON(data).responce;
							Toast.showAjaxRes(resp);
							if(resp.error == 0)
							{
								rows[values.id].entry = values;
								redrawRow(values.id);
								editor.destroy();
							}
						},
						//ContentType : "application/text; charset=utf-8",
						//dataType: "json",
						async: false,
						cache : false,
						data: values
						//data: {'data':JSON.stringify(values)},
					});

					
				}
			},entry);
		};
	}
	if(tableConfig.delete)
	{
		
		if(typeof tableConfig.deleteUrl == "undefined" && typeof tableConfig.onDelete == "undefined")
		{
			if(typeof tableConfig.table != "undefined")
				tableConfig.deleteUrl = ADMIN_URL+"table.values.delete.php?m="+tableConfig.table;
			else
				tableConfig.deleteUrl = ADMIN_URL+"table.values.delete.php?m=units_run";
		}
		var td = document.createElement('td');
		var div = document.createElement("div");
		div.innerHTML = '<i class="icofont-ui-delete"></i>';
		td.appendChild(div);
		tr.appendChild(td);
		td.onclick = function()
		{
			
			if (window.confirm("Вы уверены, что хотите удалить запись?"))
			{
				if(typeof tableConfig.onDelete != "undefined")
				{
					tableConfig.onDelete(entry, tr);
				}
				else
				{
					$.ajax({
						type: "POST",
						url: tableConfig.deleteUrl,
						success: function success(data)
						{
							var resp = jQuery.parseJSON(data).responce;
							Toast.showAjaxRes(resp);
							if(resp.error == 0)
							{
								removeRow(entry.id);
							}
						},
						//ContentType : "application/text; charset=utf-8",
						//dataType: "json",
						async: false,
						cache : false,
						data: {id:entry.id}
						//data: {'data':JSON.stringify(values)},
					});
				}
			}
		};
		
	}
}
function createInteractive(td, column, value, value2)
{
	switch(column.type)
	{
		case "bool":
			var div = document.createElement("input");
			div.type = "checkbox";
			div.disabled = "disabled";
			if(value != "" && value != "0")
				div.checked = "checked";
			td.appendChild(div);
		break;
		case "metro-icon":
			var div = document.createElement("img");
			if(value != "" && value != "0")
				div.src = Editor.iconsPath+getFullPathIcon(value);
			div.style.width = "25px";
			td.appendChild(div);
		break;
		case "section":
			var div = document.createElement("div");
			div.innerHTML = Editor.sections[parseInt(value)].name;
			td.appendChild(div);
		break;
		case "unitsDef":
				
			var div = document.createElement("div");
			if(typeof Editor.unitsDef[value] != "undefined" )
				div.innerHTML = Editor.unitsDef[value].description;
			else
				div.innerHTML = "";
			td.appendChild(div);
			return div;
		break;
		case "scheduleType":
				
			var div = document.createElement("div");
			div.innerHTML = Editor.scheduleTypes[value];
			td.appendChild(div);
			return div;
		break;
		case "condition":
				
			var div = document.createElement("div");
			div.innerHTML = Editor.conditions[value];
			td.appendChild(div);
			return div;
		break;
		case "direction":
				
			var div = document.createElement("div");
			console.log("DrawRow/Direction",Editor.directions, "value",value);
			div.innerHTML = Editor.directions[value];
			td.appendChild(div);
			return div;
		break;
		case "password":
			
			td.appendChild(document.createTextNode("*******"));
			return div;
		break;
		case "units":
			var div = document.createElement("div");
			var unit = null;
			for(var key in Editor.units)
			{
				var cUnit = Editor.units[key];
				if(cUnit.setid == value && cUnit.setid == value2 && value2 != "")
				{
					//console.log("Table/Draw/Interactive","cUnit",cUnit,"val",value,"val2",value2);
					unit = cUnit;
					break;
				}
			}
			//console.log("Table/Draw/Interactive","units",Editor.units,"value",value,"value2",value2);
			if(unit == null && Editor.unitsDef[value] != undefined && value2 != "" && value2 != "0000")
			{
				var unit = null;
				for (i in Editor.units)
				{
					if(Editor.units[i].unid == value && Editor.units[i].setid == value2)
					{
						unit = Editor.units[i];
						break;
					}
				}
				var div = document.createElement("div");
				if(unit != null)
					div.innerHTML = unit.name;
				td.appendChild(div);
				return div;
			}
			if(unit != null)
				div.innerHTML = unit.name;
			td.appendChild(div);
		break;
		case "color":
			var div = document.createElement("div");
			td.appendChild(div);
			td.style.minWidth =	"120px";
			div.style.position= "relative";
			var text = document.createElement("div");
			text.innerHTML = value;
			div.appendChild(text);
			var picker = document.createElement("div");
			picker.className = "colorPreview";
			picker.style.backgroundColor = "#"+value;
			picker.style.color = "#"+value;
			div.appendChild(picker);
		break;
		default: 
			td.appendChild(document.createTextNode(value));
		break;
	}
}
