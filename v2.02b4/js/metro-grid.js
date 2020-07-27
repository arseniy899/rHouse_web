class Unit
{
	/*id	 		= 0;
	setid	 	= 0;
	unid 		= 0;
	lastValue 	= '';
	lastTime 	= '';
	name 		= '';
	alive		= 0;
	uiShow		= true;
	desc		= '';
	units		= '';
	valueType	= '';
	direction	= '';
	timeout		= '';
	values		= [];*/
	constructor(unid, setid) {
        this.unid = unid;
        this.setid = setid;
    }
	static from(json){
	   return Object.assign(new Unit(), json);
	 }
	static getAllUnits()
	{
		ShowLoading();
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() 
		{
			if (this.readyState == 4)
				DismissLoading();
			if (this.readyState == 4 && this.status == 200)
			{
				var myArr = JSON.parse(this.responseText).responce;
				if(myArr.error == 0)
				{
					Unit.allUnits = {};
					var units = myArr.data.units;
					for (var i = 0; i < units.length; ++i) {
						//console.log(data[i]);
						var obj = Unit.from(units[i]);
						//console.log(obj);
						if(obj.lastValue == undefined|| obj.lastValue == "")
							obj.lastValue = "--";
						if(obj.icon == undefined || obj.icon == "")
							obj.icon = "sensor.png";
						if(obj.units == undefined)
							obj.units = "";
						//array.push(obj);
						Unit.allUnits[obj.id] = obj;
					}
					Unit.sections =  myArr.data.sections;
					GridManager.buildGrid(Unit.allUnits);
				}
				else
					Toast.showAjaxRes(myArr);
			}
		};
		xhttp.open("GET", API_URL+"units.get.all.php", true);
		xhttp.send();   
	}
	mapDataToDOM()
	{
		if(this.dom != undefined && this.dom.item != undefined)
		{
			if(this.dom.value == undefined)
				this.dom.value = document.getElementById("val"+this.id);
			var value = this.dom.value;
			console.log("MapDataToDOM","this:",this, "value",value);
			while (value.firstChild) value.removeChild(value.firstChild);
			
			if(this.direction.includes("I"))
			{
				var vals 		=  this.lastValue.split(';');
				var units 		=  this.units.split(';');
				var possValues 	=  this.possValues.split(';');
				var valueTypes 	=  this.valueType.split(';');
				console.log("DrawingGrid/PossValls",possValues);
				var strFinal = "";
				if(this.possValues == "1")
				{
					var unit = this;
					this.dom.icon.onclick = (function()
					{
						unit.sendValue("1");
					});
					this.dom.iconWrapper.style.width = "65%";
					this.dom.iconWrapper.style.textAlign = "right";
					
				}
				else
				{
					for ( var i = 0; i < vals.length; i++)
					{
						if( i < possValues.length)
						{
							if(possValues[i] == "0,1")
							{
								if(vals[i] == "0")
									vals[i] = "<i class='icofont-close-squared'></i>";
								else
									vals[i] = "<i class='icofont-tick-boxed'></i>";
							}
							else if(possValues[i] == "1,0")
							{
								if(vals[i] == "1")
									vals[i] = "<i class='icofont-close-squared'></i>";
								else
									vals[i] = "<i class='icofont-tick-boxed'></i>";
							}
							
							//else if(valueTypes[i] == "sint")
							//	vals[i] = parseInt(vals[i])+128;
						}
						
						if( i >= units.length)
							strFinal += ","+vals[i];
						else
							strFinal += ","+vals[i]+" "+units[i];
					}
					strFinal = strFinal.replace(',','');
					value.innerHTML = `<span>${strFinal}</span>`;
				}
				
			}
			if(this.direction.includes("O") )
			{
				var possValues 	=  this.possValues.split(';')+"";
				var valueTypes 	=  this.valueType.split(';');
				console.log("GetDom/"+this.name,"possValues=",this.possValues,"lastValue",this.lastValue);
				if(possValues == "0,1")
				{
					var checked = "checked='checked'";
					if(this.lastValue == "0" || this.lastValue == "")
						checked = "";
					var html = `
									<label class="switch">
									  <input type="checkbox" ${checked} id="val${this.id}" onclick='Unit.findUnitById(${this.id}).sendValue(this.checked? "1":"0");'>
									  <span class="slider round"></span>
									</label>`;
					value.innerHTML = html;
					//this.dom.value = value;//document.getElementById("val"+this.id);
				}
				else if(possValues == "1")
				{
					var unit = this;
					this.dom.icon.onclick = (function()
					{
						unit.sendValue("1");
					});
					this.dom.textValue.onclick = (function()
					{
						unit.sendValue("1");
					});
					this.dom.iconWrapper.style.width = "65%";
					this.dom.iconWrapper.style.textAlign = "right";
				}
				else if(possValues == "str")
					value.innerHTML =`<span contenteditable="true">${this.lastValue} ${this.units}</span>`;
				else if(this.possValues != "" &&  this.possValues.includes(':') && this.possValues.split(':').length > 0 )
				{
					var step = "1";
					var min="0";
					var max="1";
					var spl = this.possValues.split(':');
					if(spl.length == 1)
						max = this.possValues;
					else if(spl.length == 2)
					{
						min = spl[0];
						max = spl[1];
					}
					else if(spl.length == 3)
					{
						min = spl[0];
						step = spl[1];
						max = spl[2];
					}
					step = parseInt(step);
					min = parseInt(min);
					max = parseInt(max);
					console.log(spl);
					console.log(min+":"+step+":"+max);
					value.style.position = 'relative';
					value.style.left = '0px';
					value.style.right = '0px';
					value.style.bottom = '0px';
					value.style.left = '0px';
					value.innerHTML =`	
								<div  style="display: table-cell; vertical-align: middle;display:block;align:center;" >
									<span id="val${this.id}" contenteditable="true">${this.lastValue} </span>${this.units}
									<img class='grid-item-back-edit' style='right: 55;top: 10px;' src='${REMOTE_URL}/img/metro-icons/icons8-schedule-96.png' onclick="Unit.findUnitById(${this.id}).scheduleValue(document.getElementById('val${this.id}').innerHTML);" />
									<i onclick="Unit.findUnitById(${this.id}).sendValue(document.getElementById('val${this.id}').innerHTML);" class="glyphicon glyphicon-ok tick-save-value"></i>
								</div>
					`;
					var container = document.createElement("div");
					container.className = 'slider-container';
					value.appendChild(container);
					
					
					
					var input = document.createElement("input");
					input.className = 'js-range-slider';
					input.type = "text";
					input.id = "slider-"+this.id;
					container.appendChild(input);
					input.setAttribute('value',this.lastValue);
					
					var id = this.id;
					var lastValue = this.lastValue;
					var dom = this.dom;
					$(input).ionRangeSlider({
						skin: "round",
						grid: true,
						min: min,
						max: max,
						from: lastValue,
						 onChange: function (data) {
							console.log(data);
							var value = data.from;
							//console.log(value);
							document.getElementById('val'+id).innerHTML  = value;
							dom.valueChangedByUser = true;
						},
						step: step,
						prettify_enabled: true,
						prettify_separator: ","
					});
					executeAsync(function() {
						
					});
					console.log("CreateDom/",this.id,"valDiv",document.getElementById("val"+this.id));
					//this.dom.value = document.getElementById("val"+this.id);

				}
				//value.innerHTML +=`<img class='grid-item-back-edit' style='right: 55;top: 10px;' src='${REMOTE_URL}/img/metro-icons/icons8-schedule-96.png' onclick="Unit.findUnitById(${this.id}).scheduleValue(document.getElementById('val${this.id}').innerHTML);" />`;
			}
			this.dom.lastValueTime.innerHTML = this.lastTime;
		}
	}
	getInfoFRDB(callback)
	{
		var xhttp;
		var self = this;
		ShowLoading();
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() 
		{
			if (this.readyState == 4)
				DismissLoading();
			if (this.readyState == 4 && this.status == 200) 
			{
				var myArr = JSON.parse(this.responseText).responce;
				if(myArr.error == 0)
				{
					var array = [];
					var data = myArr.data;
					self.id 		= data.id;
					self.lastTime 	= data.lastTime;
					self.unid 		= data.unid;
					self.setid 		= data.setid;
					self.lastValue 	= data.lastValue;
					self.lastValue 	= data.lastValue;
					self.desc 		= data.desc;
					self.units 		= data.units;
					self.valueType 	= data.valueType;
					self.direction 	= data.direction;
					self.possValues = data.possValues;
					self.uiShow 	= data.uiShow;
					self.sectId 	= parseInt(data.sectId);
					
					self.name 		= data.name;
					self.color 		= data.color;
					self.icon 		= data.icon;
					//var obj = Unit.from(data);
					console.log(self);
					//obj
					if(self.lastValue == undefined|| self.lastValue == "")
						self.lastValue = "";
					if(self.icon == undefined || self.icon == "")
						self.icon = "sensor.png";
					if(self.units == undefined)
						self.units = "";
					callback(self);
				}
				else
					Toast.showAjaxRes(myArr);
			}
		};
		xhttp.open("GET", API_URL+"units.get.id.php?id="+this.id, true);
		xhttp.send();   
	}
	createDOM(div)
	{
		if(!this.uiShow)
			return '';
		if(this.dom == undefined)
			this.dom = {};
		if(this.dom.item == undefined)
			this.dom.item = div;
		this.dom.valueChangedByUser = false;
		div.className = "grid-item";
		div.style.backgroundColor = "#"+this.color;
		div.title = this.name;
		div.onmouseover = function() { 
			//ShowContent('grid-item-back${this.id}');
		};
		console.log("CreateDom/",self);
		var self = this;
		div.onmouseleave = function() { HideContent(self.dom.back); };
		var front = document.createElement("div");
		front.className = 'grid-item-front';
		div.appendChild(front);
		var textValue = document.createElement("div");
		textValue.className = 'grid-item-value';
		front.appendChild(textValue);
		console.log("Editor/ CreateDom");
		var iconWrapper = document.createElement("div");
		iconWrapper.className = 'grid-item-icon';
		front.insertBefore(iconWrapper, textValue);
		var icon = document.createElement("img");
		icon.src =  this.icon;
		icon.style.width =  "100px";
		this.dom.icon = icon;
		iconWrapper.appendChild(icon);
		this.dom.iconWrapper = iconWrapper;
		var lastValueTime = document.createElement("span");
		lastValueTime.style.right = '10px';
		lastValueTime.style.bottom = '5px';
		lastValueTime.style.align = 'center';
		lastValueTime.style.position = 'absolute';
		lastValueTime.innerHTML = this.lastTime;
		this.dom.lastValueTime = lastValueTime;
		front.appendChild(lastValueTime );
		
		var value = document.createElement("div");
		value.style.display = 'table-cell';
		value.style.verticalAlign = 'middle';
		textValue.appendChild(value);
		this.dom.value = value;
		this.dom.textValue = textValue;
		var isTextWhite = isWhiteContrastColorText(this.color);
		this.mapDataToDOM();
		if(!isTextWhite)
		{
			value.style.color = "#000";
			lastValueTime.style.color = "#000";
			//div.style.border = "1px solid #2c3e50";
		}
		
		var back = document.createElement("div");
		back.className = 'grid-item-back';
		back.style.display = 'none';
		div.appendChild(back);
		this.dom.back = back;
		var title = document.createElement("p");
		title.className = 'grid-item-back-title';
		title.innerHTML = this.name;
		back.appendChild(title);
		
		var unitActions = document.createElement("div");
		unitActions.className = 'grid-item-actions';
		div.appendChild(unitActions);
		var infoIcon = document.createElement("img");
		infoIcon.className = 'grid-item-info';
		infoIcon.src = `${REMOTE_URL}/img/metro-icons/icons8-menu-vertical-filled-50.png`;
		if(!isTextWhite)
			infoIcon.style.webkitFilter = "brightness(0) contrast(300%) invert(0)";
		
		infoIcon.onmouseover = function() { 
			ShowContent(back);
		};
		infoIcon.onclick = function() { 
			ReverseDisplay(back);
		};
		//infoIcon.onmouseleave = function() { HideContent(self.dom.back); };
		unitActions.appendChild(infoIcon);
		
		var editIcon = document.createElement("a");
		editIcon.href = `${REMOTE_URL}/unit.edit.php?id=${this.id}`;
		editIcon.innerHTML = `<img class='grid-item-back-edit' src='${REMOTE_URL}/img/metro-icons/icons8-pencil-drawing-100.png' />`;
		back.appendChild(editIcon);
		
		
		var plotIcon = document.createElement("a");
		plotIcon.href = `${REMOTE_URL}/unit.values.stat.php?id=${this.id}`;
		plotIcon.innerHTML = `<i class="icofont-chart-histogram grid-item-back-plot icofont-2x"></i>`;
		back.appendChild(plotIcon);
		
		
		
	}
	sendValue(value)
	{
		
		var xhttp;
		ShowLoading()
		var self = this;
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() 
		{
			if (this.readyState == 4)
				DismissLoading()
			if (this.readyState == 4 && this.status == 200) 
			{
				var myArr = JSON.parse(this.responseText).responce;
				if(myArr.error == 0)
				{
					var scheduled = false;
					if(typeof myArr.data == "object" && "msg" in myArr.data)
					{
						if(myArr.data["msg"] == "scheduled")
							scheduled = true;
					}
					self.dom.valueChangedByUser = false;
					self.lastValue = value;
					var friendlyValue = value;
					if(self.possValues == "0,1" || self.possValues == "0;1")
						friendlyValue = value=="1" ? "ВКЛ":"ВЫКЛ";
					Lobibox.notify('info', {
						size: 'mini',
						msg: ((scheduled)?"Запланирована установка значения ":"Установлено значение ")+friendlyValue+self.units+" для '"+self.name+"'"
					  });
				}
				else
					Toast.showAjaxRes(myArr);
			}
		};
		xhttp.open("GET", API_URL+"units.set.value.php?id="+this.id+"&value="+value, true);
		xhttp.send();   
		
	}
	scheduleValue(value)
	{
	
		var page = document.body;	
		var background = document.createElement("div");
		background.style.position = "fixed";
		background.style.zIndex = "5";
		background.style.top = "0";
		background.style.left = "0";
		background.style.width = "100%";
		background.style.height = "100%";
		background.style.background = "#000A";
		background.onclick = function(e){
			if (e.target == this)
				background.remove();
		};
		page.appendChild(background);
		
		var timePickerWrp = document.createElement("div");
		timePickerWrp.id = "timepicker-wrapper";
		timePickerWrp.style.position = "fixed";
		timePickerWrp.style.zIndex = "6";
		timePickerWrp.style.top = "10%";
		timePickerWrp.style.left = "0";
		timePickerWrp.style.margin = "auto";
		timePickerWrp.style.right = "0";
		timePickerWrp.style.width = "300px";
		//timePickerWrp.style.height = "345px";
		timePickerWrp.style.background = "#FFF";
		timePickerWrp.onclick = function(){
			//background.remove();
		};
		background.appendChild(timePickerWrp);
		
		var input = document.createElement("input");
		input.type = "hidden";
		input.id = "timepicker-input";
		timePickerWrp.appendChild(input);
		
		var holder = document.createElement("div");
		holder.style.position = "relative";
		//holder.style.height = "350px";
		holder.id = "timepicker-holder";
		timePickerWrp.appendChild(holder);
		
		var dateTimePicker = tail.DateTime("#timepicker-input", 
		{
			today: false,
			startOpen: true,
			stayOpen: true,
			position: "#timepicker-holder",
			dateFormat: "YYYY.mm.dd",
			timeFormat: "HH:ii",
			zeroSeconds: true,
			dateStart: new Date(),
			weekStart: 1,
			timeIncrement: true,
			//timeStepMinutes: 5,
			//timeStepSeconds: 0,
			locale: "ru",
		});
		Unit.dateTimePicker = dateTimePicker;
		
		
		var okBtn = document.createElement("div");
		okBtn.className = "button green";
		okBtn.style.width = "calc( 100% - 10px)";
		timePickerWrp.appendChild(okBtn);
		
		dateTimePicker.on("change", function(){
			var time = input.value;
			okBtn.innerHTML = "Запланировать на <br />"+time;
		});
		dateTimePicker.selectTime();	
			
		var self = this;
		okBtn.onclick = function(){
			var time = input.value+":00";
			var xhttp;
			ShowLoading()
			
			xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4)
					DismissLoading()
				if (this.readyState == 4 && this.status == 200) 
				{
					var myArr = JSON.parse(this.responseText).responce;
					if(myArr.error == 0)
					{
						
						self.lastValue = value;
						var friendlyValue = value;
						self.dom.valueChangedByUser = false;
						if(self.possValues == "0,1" || self.possValues == "0;1")
							friendlyValue = value=="1" ? "ВКЛ":"ВЫКЛ";
						Lobibox.alert('info', {
							msg: "Запланирована установка значения "+friendlyValue+self.units+" для '"+self.name+"' на "+time
						  });
						background.remove();
					}
					Toast.showAjaxRes(myArr);
				}
			};
			xhttp.open("GET", API_URL+"units.schedule.value.php?id="+self.id+"&value="+value+"&date="+time, true);
			xhttp.send();  
		};
		
			
		var cancelBtn = document.createElement("div");
		cancelBtn.className = "button red";
		cancelBtn.innerHTML = "Отменить";
		cancelBtn.style.width = "calc( 100% - 10px)";
		cancelBtn.onclick = function()
		{
			background.remove();
		};
		timePickerWrp.appendChild(cancelBtn);
		
	}
	static findUnitById(id)
	{
		return Unit.allUnits[id];
		for (var i = 0; i < Unit.allUnits.length; ++i)
		{
			if(Unit.allUnits[i].id == id)
				return Unit.allUnits[i];
		}
		return null;
	}
}
class GridManager
{
	
	
	static loadGrid()
	{
		Unit.getAllUnits();
	}
	static buildGrid(arr)
	{
		console.log(arr);
		var page = document.getElementById("page");
		for (var sect in Unit.sections)
		{
			GridManager.createSection(page, sect)
		}
		for (var objProp in arr)
		{
			var div = document.createElement("div");
			var unit = arr[objProp];
			log_i("Grid/Build/","unit.sectId="+unit.sectId);
			log_obj("Grid/Build/Unit",unit);
			unit.createDOM(div);
			Unit.sections[unit.sectId].content.appendChild(div);
			//html += arr[i].getGridItemHtml();
		}
		
		//x.innerHTML = html;
	}
	static createSection(page, sectionId)
	{
		var sectionObj = Unit.sections[sectionId];
		var section = document.createElement("div");
		section.className = 'grid-section';
		page.appendChild(section);
		
		var titleDIV = document.createElement("div");
		titleDIV.className = 'grid-section-title';
		titleDIV.innerHTML = sectionObj.name;
		titleDIV.onclick = function(){ReverseDisplay(content)};
		section.appendChild(titleDIV);
		var content = document.createElement("div");
		section.appendChild(content);
		content.className = 'grid-section-content';
		if(sectionObj.isDefHidden == "1")
			content.style.display = "none";
		sectionObj.content = content;
		return content;
	}
}