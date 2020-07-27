class Editor
{
	
	constructor(mContainer, config, data)
	{
		this.config = config;
		if(typeof this.config.popup != "undefined" && this.config.popup)
			this.container = document.body;
		else if(isElementDOM(mContainer))
			this.container = mContainer;
		else
			this.container = document.getElementById(mContainer);
		
		this.data = data;
		this.fields = {};
		var editor = this;
		var request = {};
		var needLoad = false;
		for (var key in  config.fields)
		{
			var field = config.fields[key];
			switch(field.type)
			{
				case "metro-icon":
					request.icons = true;
					if(typeof Editor.icons == "undefined")
						needLoad = true;
				break;
				case "section":
					request.sections = true;
					if(typeof Editor.sections == "undefined")
						needLoad = true;
				break;
				case "units":
					request.units = true;
					request.unitsDef = true;
					if(typeof Editor.units == "undefined")
						needLoad = true;
				break;
				case "unitsDef":
					request.unitsDef = true;
					if(typeof Editor.unitsDef == "undefined")
						needLoad = true;
				break;
				case "condition":
					request.conditions = true;
					if(typeof Editor.conditions == "undefined")
						needLoad = true;
				break;
				case "direction":
					request.directions = true;
					if(typeof Editor.directions == "undefined")
						needLoad = true;
				break;
				case "uscript":
					request.uscripts = true;
					if(typeof Editor.uscripts == "undefined")
						needLoad = true;
				break;
				case "scheduleTypes":
				case "scheduleType":
					request.scheduleTypes = true;
					if(typeof Editor.scheduleTypes == "undefined")
						needLoad = true;
				break;
				
			}
		}
		if(needLoad)
		{
			if(typeof config.url == "undefined")
			{
				if(typeof config.table != "undefined")
					config.url = ADMIN_URL+"table.values.select.php?m="+config.table;
				else
					config.url = ADMIN_URL+"table.values.select.php";
			}
			$.ajax({
				type: "GET",
				url: config.url,
				success: function success(data)
				{
					var resp = jQuery.parseJSON(data).responce;
					if(resp.error == 0)
					{
						Editor.icons = resp.data.icons;
						Editor.sections = resp.data.sections;
						Editor.iconsPath = resp.data.iconsPath;
						Editor.units = resp.data.units;
						Editor.unitsDef = resp.data.unitsDef;
						Editor.conditions = resp.data.conditions;
						Editor.directions = resp.data.directions;
						Editor.uscripts = resp.data.uscripts;
						Editor.scheduleTypes = resp.data.scheduleTypes;
						editor.draw();
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
		else
			this.draw();
	}
	destroy()
	{
		this.window.remove();
		this.shadow.remove();
	}
	draw()
	{
		var form = document.createElement('div');
		form.className = "form";
		
		if(typeof this.config.popup != "undefined" && this.config.popup)
		{
			this.window = document.createElement('div');
			this.window.className = "popup";
			this.container.appendChild(this.window);
			
			this.shadow = document.createElement('div');
			this.shadow.className = "shadow";
			var editor = this;
			this.shadow.onclick = function(){editor.destroy()};
			
			var close = document.createElement('div');
			close.className = "popup-close";
			close.onclick = function(){editor.destroy()};
			
			
			this.container.appendChild(this.shadow);
			this.window.appendChild(form);
			this.window.appendChild(close);
			
		}
		else
			this.container.appendChild(form);

		console.log("Editor/Data",this.data);
		for (var key in this.config.fields)
		{
			var field = this.config.fields[key];
			//console.log("Editor/Field",field);
			var fieldWrapper = document.createElement('div');
			fieldWrapper.className = 'form-item-wrapper';
			var label = document.createElement("label");
			label.for = key;
			label.innerHTML = field.name;
			fieldWrapper.appendChild(label);
			
			this.fields[key] = {};
			var value = "";
			if(key == "unit" && field.type == "units" && typeof this.data != "undefined")
			{
				var dom = this.createField(fieldWrapper, key, field,this.data.unid, this.data.setid);
			}
			else
			{
				if(typeof this.data != "undefined")
					value = this.data[key];
				var dom = this.createField(fieldWrapper, key, field,value);
			}
			this.fields[key].dom = dom;
			this.fields[key].config = field;
			
			form.appendChild(fieldWrapper);
		}
		
		var fieldWrapper = document.createElement('div');
		fieldWrapper.className = 'form-item-wrapper';
		var submit = document.createElement("div");
		submit.className = "button green";
		submit.innerHTML = "Сохранить";
		submit.style.width = "100%";
		var editor = this;
		submit.onclick = function(){editor.submit()};
		fieldWrapper.appendChild(submit);
		form.appendChild(fieldWrapper);
	}
	submit()
	{
		var editor = this;
		var values = {};
		if(this.data != undefined)
			values.id = this.data.id;
		var notAllRequiredSatisfied = false;
		console.log("FieldS Dump",this.fields);
		for (var id in this.fields)
		{
			var field = this.fields[id];
			var fieldVal = "";
			if(typeof field.value != "undefined")// && field.value != "")
				fieldVal = field.value;
			else 
				fieldVal = field.dom.value;
			var addField = true;
			console.log("FieldDump",field);
			if(typeof field.config.required != "undefined" && field.config.required && fieldVal == "")
			{
				
				Lobibox.notify('error', {size: 'mini',delay: 7000, msg: `Не заполнено поле '${field.config.name}'`});
				notAllRequiredSatisfied = true;
			}
			if(id == "unit")// 
			{
				if(fieldVal != "0000")
				{
					var spl = fieldVal.split(";");
					console.log("Editor/Submit","splVal",spl,"value",values[id]);
					values.unid = spl[0];
					values.setid = spl[1];
				}
				else
				{
					//values.unid = "";
					values.setid = "";
				}
			}
			if(typeof field.config.edit != "undefined" && !field.config.edit && !id.includes("id"))
				addField = false;
			if(id == "unit")
				addField = false;
			if(id.includes("password") && fieldVal.length == 0)
				addField = false;
			if(addField)
				values[id] = fieldVal;
		}
		if(notAllRequiredSatisfied)
			return;	
		console.log("Editor/Submit/values",values,"fields: ", this.fields);
		if(typeof this.config.onsubmit != "undefined")
		{
			this.config.onsubmit(values);
			//this.destroy();
		}
		if(typeof this.config.submitUrl != "undefined")
		{
			$.ajax({
				type: "POST",
				url: this.config.submitUrl,
				success: function success(data)
				{
					var resp = jQuery.parseJSON(data).responce;
					if(resp.error == 0 && typeof editor.config.popup != "undefined" && editor.config.popup)
						editor.destroy();
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
		
	}
	static getFullPathIcon(icon)
	{
		for (var i = 0; i < Editor.icons.length; i++)
		{
			var iconF = Editor.icons[i];
			if(iconF.endsWith(icon))
				return iconF;
		}
	}
	createField(fieldWrapper, id, fieldConf, value, value2)
	{
		var editor = this;
		switch(fieldConf.type)
		{
			case "bool":
				var input = document.createElement("input");
				input.className = "editor";
				input.type = "checkbox";
				if(value == "")
					value = "0";
				input.onclick = function(){editor.fields[id].value = input.checked? "1":"0";}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					input.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					input.required = fieldConf.required;
				if(value != "" && value != "0")
					input.checked = "checked";
				editor.fields[id].value = value;
				fieldWrapper.appendChild(input);
				return input;
			break;
			case "metro-icon":
				
				var select  = document.createElement("select");
				select.className = "image-picker show-html";
				select.value = value;
				console.log("Editor/Draw/Field/Icon/value",value);
				for (var i = 0; i < Editor.icons.length; i++)
				{
					var iconF = Editor.iconsPath+Editor.icons[i];
					var iconName = iconF.substring(iconF.lastIndexOf('/') + 1);
					var option  = document.createElement("option");
					
					option.value = iconName;
					option.innerHTML = iconName;
					option.setAttribute('data-img-alt', iconName);
					option.setAttribute('data-img-src', iconF);
					
					if(iconName == value)
						select.prepend(option);
					else
						select.appendChild(option);
				}
				fieldWrapper.appendChild(select);
				var editor = this;
				$(select).val(value);
				$(select).imagepicker({
					hide_select : true,
					show_label  : false,
					selected : function(selectM, picker, option, event){
						
						console.log("IconPicker/select",select,"picker",picker,"option",option);
						var url = select.options[select.selectedIndex].value;
						editor.fields[id].value = url.substring(url.lastIndexOf('/') + 1);
					}
				});
				return select;
				
			break;
			case "section":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				for (var sectId in Editor.sections)
				{
					var option  = document.createElement("option");
					option.innerHTML = Editor.sections[sectId].name;
					option.value = sectId;
					if(sectId == value)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					select.required = fieldConf.required;
				console.log("EDitor/Draw/Section/","Editor.sections = ",Editor.sections, ";value = ", value);
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "condition":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				for (var condition in Editor.conditions)
				{
					var option  = document.createElement("option");
					option.innerHTML = Editor.conditions[condition];
					option.value = condition;
					if(condition == value)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "scheduleType":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				console.log("EDitor/Draw/Section/","Editor.scheduleTypes = ",Editor.scheduleTypes, ";value = ", value);
				for (var type in Editor.scheduleTypes)
				{
					var option  = document.createElement("option");
					option.innerHTML = Editor.scheduleTypes[type];
					option.value = type;
					if(type == value)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					select.required = fieldConf.required;
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "select":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				var vals = fieldConf.values.split(",");
				for (var ind in vals)
				{
					var option  = document.createElement("option");
					option.innerHTML = vals[ind];
					option.value = vals[ind];
					if(vals[ind] == value)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					select.required = fieldConf.required;
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "direction":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				for (var id in Editor.directions)
				{
					var option  = document.createElement("option");
					option.innerHTML = Editor.directions[id];
					option.value = id;
					if(id == value)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					select.required = fieldConf.required;
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "uscript":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				console.log("EDitor/Draw/uscript/","Editor.uscripts = ",Editor.uscripts, ";value = '"+value+"'");
				for (var id in Editor.uscripts)
				{
					var option  = document.createElement("option");
					if(Editor.uscripts[id].startsWith('/')) {
						Editor.uscripts[id] = Editor.uscripts[id].substr(1, Editor.uscripts[id].length);
					}
					option.innerHTML = Editor.uscripts[id];
					option.value = Editor.uscripts[id];
					//console.log("EDitor/Draw/uscript/","id = '"+id+"';value = '"+value+"'");
					if(option.value == value || ("/"+option.value) == value)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					select.required = fieldConf.required;
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "unitsDef":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				for (var id in Editor.unitsDef)
				{
					var option  = document.createElement("option");
					var undDef = Editor.unitsDef[id];
					option.innerHTML = undDef.unid+" | "+undDef.description;
					option.value = undDef.unid;
					if(undDef.unid == value)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					select.required = fieldConf.required;
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "cronTime":
				
				var div  = document.createElement("div");
				div.className = "cron-wrapper";
				
				this.createCronField(div, value, id);
				fieldWrapper.appendChild(div);
				return div;
			break;
			case "units":
				
				var select  = document.createElement("select");
				select.className = "custom-select";
				var option  = document.createElement("option");
				option.innerHTML ="None";
				option.value = "0000";
				select.appendChild(option);
				
				for (var id in Editor.units)
				{
					var option  = document.createElement("option");
					var unit = Editor.units[id];
					option.innerHTML = unit.unid+" "+unit.setid+" | "+unit.name;
					option.value = unit.unid+";"+unit.setid;
					if(unit.unid == value && value2 == unit.setid)
						option.selected = "selected";
					select.appendChild(option);
				}
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					select.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					select.required = fieldConf.required;
				fieldWrapper.appendChild(select);
				return select;
			break;
			case "color":
				var div = document.createElement("div");
				div.id = "color-pick-"+id;
				div.className = 'colorPickSelector';
				fieldWrapper.appendChild(div);
				var editor = this;
				if(typeof value == "undefined" || value == "")
					value = "2ecc71";
				if(typeof fieldConf.edit == "undefined" || fieldConf.edit)
				
					
					$(div).colorPick({
						'initialColor': '#'+value,
						'allowRecent': true,
						'recentMax': 5,
						'allowCustomColor':true,
						'onColorSelected': function() {
							this.element.css({'backgroundColor': this.color, 'color': this.color});
							editor.fields[id].value = this.color.replace("#","");
							//document.getElementById("color").value = this.color.replace("#","");
						}
					});
				return div;
			break;
			case "password": 
				var input = document.createElement("input");
				input.className = "";
				input.type = 'password';
				input.name = id;
				input.value = "";
				this.fields[id].dom = input;
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					input.disabled = "disabled";
				fieldWrapper.appendChild(input);
				if(typeof fieldConf.required != "undefined")
					input.required = fieldConf.required;
				
				return input;
			break;
			default: 
				var input = document.createElement("input");
				input.className = "";
				input.type = 'text';
				input.name = id;
				input.value = value;
				this.fields[id].dom = input;
				if(typeof fieldConf.edit != "undefined" && !fieldConf.edit)
					input.disabled = "disabled";
				if(typeof fieldConf.required != "undefined")
					input.required = fieldConf.required;
				fieldWrapper.appendChild(input);
				return input;
			break;
		}
	}
	createCronField(div, value, id)
	{
		this.cronResText = document.createElement("div");
		this.cronResText.className = "center";
		div.appendChild(this.cronResText);
		this.cronResNext = document.createElement("div");
		this.cronResNext.className = "center";
		div.appendChild(this.cronResNext);
		var editor = this;
		this.cronInput = document.createElement("input");
		div.appendChild(this.cronInput);
		this.fields[id] = this.cronInput;
				
		this.cronInput.id = "cronInput";
		this.cronInput.value = value;
		var explain = document.createElement("div");
		explain.className = "part-explanation";
		explain.innerHTML = `
			<div><span class="clickable">minute</span></div>
			<div><span class="clickable">hour</span></div>
			<div><span class="clickable">day</span><br>(month)</div>
			<div><span class="clickable">month</span></div>
			<div><span class="clickable">day</span><br>(week)</div>`
		div.appendChild(explain);
		
		/*div.innerHTML +=  `
		<div class="part-explanation">
			
			
		</div>
		<table class="center">
			<tbody>
				<tr>
					<th>*</th>
					<td>any value</td>
				</tr>
				<tr>
					<th>,</th>
					<td>value list separator</td>
				</tr>
				<tr>
					<th>-</th>
					<td>range of values</td>
				</tr>
				<tr>
					<th>/</th>
					<td>step values</td>
				</tr>
			</tbody>
			<tbody style="">
				<tr>
					<th>@yearly</th>
					<td>(non-standard)</td>
				</tr>
				<tr>
					<th>@annually</th>
					<td>(non-standard)</td>
				</tr>
				<tr>
					<th>@monthly</th>
					<td>(non-standard)</td>
				</tr>
				<tr>
					<th>@weekly</th>
					<td>(non-standard)</td>
				</tr>
				<tr>
					<th>@daily</th>
					<td>(non-standard)</td>
				</tr>
				<tr>
					<th>@hourly</th>
					<td>(non-standard)</td>
				</tr>
				<tr>
					<th>@reboot</th>
					<td>(non-standard)</td>
				</tr>
			</tbody>
		</table>
		`;*/
		this.cronInput.onchange = function(){editor.cronTextChanged()};
		this.cronInput.onkeyup = function(){editor.cronTextChanged();};
		if(value != "")
			this.cronTextChanged();
	}
	cronTextChanged()
	{
		var resText = this.cronResText;
		var resNext = this.cronResNext;
		var field = this.cronInput;
		console.log("CronText/Change",'value',field.value,'cronResText',this.cronResText);
		
		var sched = later.parse.text(field.value);
		// calculate the next 5 occurrences using local time
		later.date.localTime();
		var sched = later.parse.cron(field.value);
		later.date.localTime();
		var results = later.schedule(sched).next(1);

		console.log(results);
		if(field.value.split(" ").length > 3)
			var desc = cronstrue.toString(field.value,{ locale: "ru" });
		else
			var desc = field.value;
		console.log(desc);
		 
		resText.innerHTML  = "<i>"+desc+"</i>";
		var d = results;
		var datestring = d.getDate().pad(2)  + "-" + (d.getMonth()+1).pad(2) + "-" + d.getFullYear() + " " +
		d.getHours().pad(2) + ":" + d.getMinutes().pad(2)+":" + d.getSeconds().pad(2);
		resNext.innerHTML  = "next at "+datestring+"";
	}
}
