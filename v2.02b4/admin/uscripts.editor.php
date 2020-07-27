

<? require_once('include.php'); ?>
<?
?>
<html>
	<head>
	<title>SmartHouse</title>
	<? require('templates/head.php'); ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.5/ace.js" lazyload></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.5/ext-language_tools.js" lazyload></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.5/mode-python.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.5/snippets/python.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.5/snippets/python.js"></script>
	<link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/jsTree/style.min.css" />
	<script src="//<?=REMOTE_ROOT?>/js/jsTree/jstree.js"></script>
	<script src="//<?=REMOTE_ROOT?>/js/jsTree/jstree.contextmenu.js"></script>
	<style type="text/css" media="screen">
		#editor { 
			/*position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;*/
			height: calc(100vh - 140px);
			overflow:hidden;
			min-height: 100vh;
		}
		#editor-top-name {
			right: 150px;
			left: 0px;
			width: auto;
			float: left;
		}
		#fileNav-wrapper
		{
			/*position:absolute;*/
			top: 40px;
			right: 10px;
			height: calc(100vh - 140px);
			background-color: #FFF;
			float:right;
		}
		#fileNav
		{
			/*position:absolute;*/
			float:right;
			top: 0px;
			right: 0px;
			height: 100%;
			min-width: 150px;
			border: 1px solid;
			background-color: #FFF;
		}
		#fileNav-show {
			position: absolute;
			top: calc(50% - 25px);
			height: 50px;
			right: 5px;
			width: 17px;
			border: 1px solid;
			background-color: #FFF;
			font-size: 20px;
			line-height: 50px;
			z-index: 10;
		}
		#editor-top,#editor-top-name-file
		{
			height: 30px;
			width: 100%;
			background-color: #2c3e50;
			color: #FFF;
			font-size: 20px;
			line-height: 30px;
			padding-left: 7px;
		}
		#editor-top-buttons {
			position: unset;
			margin-right: 59px;
			/* width: 50px; */
			float: right;
			top: 0;
			line-height: 30px;
		}
		#fileNav .folder 
		{
			background: url('//<?=REMOTE_ROOT?>/css/jsTree/color.png') right bottom no-repeat;
		}
		.vakata-context, .vakata-context ul {
			z-index: 10;
		}
	</style>
	<script>
		var lastPosition = 0;
		var objectPrefix = "";
		var fileNav;
		var editor;
		var editor_top_name;
		var editor_top_name_file;
		var curPath = '';
		var treeNodes = {};
		var dndNode = {};
		var dndPathOld = '';
		var needToSave = false;
		var ignoreChange = false;
		window.onload = function()
		{
			
			var langTools = ace.require("ace/ext/language_tools");
			editor = ace.edit("editor");
			editor.session.setMode("ace/mode/python");
			//editor.setTheme("ace/theme/xcode");
			// enable autocompletion and snippets
			editor.setOptions({
				enableBasicAutocompletion: true,
				enableSnippets: true,
				enableLiveAutocompletion: true
			});
			editor.commands.addCommand({
				name: "dotCommand1",
				bindKey: { win: ".", mac: "." },
				exec: function () {
					var pos = editor.selection.getCursor();
					var session = editor.session;

					var curLine = (session.getDocument().getLine(pos.row)).trim();
					var curTokens = curLine.slice(0, pos.column).split(/\s+/);
					var curCmd = curTokens[0];
					if (!curCmd) return;
					var lastToken = curTokens[curTokens.length - 1];

					editor.insert(".");                
					objectPrefix = lastToken;
					 editor.execCommand("startAutocomplete");
				}
			});
			editor.commands.addCommand({
				name: 'save',
				bindKey: {win: "Ctrl-S", "mac": "Cmd-S"},
				exec: function(editor) {
					editorSaveFile();
				}
			});
			editor.commands.addCommand({
				name: 'run',
				bindKey: {win: "Ctrl-Shift-U", "mac": "Cmd-Shift-U"},
				exec: function(editor) {
					editorExecuteFile();
				}
			});
			editor.commands.addCommand({
				name: "replace",
				bindKey: {win: "Ctrl-R", mac: "Command-Option-F"},
				exec: function(editor) {
					require("ace/config").loadModule("ace/ext/searchbox", function(e) {
						 e.Search(editor, true)  
						 // take care of keybinding inside searchbox           
						 // this is too hacky :(             
						 var kb = editor.searchBox.$searchBarKb
						 command = kb.commandKeyBinding["ctrl-h"]
						 if (command && command.bindKey.indexOf("Ctrl-R") == -1) {
							 command.bindKey += "|Ctrl-R"
							 kb.addCommand(command)
						 }
					 });
				}
			});
			
			var staticWordCompleter = 
			{
				getCompletions: function(editor, session, pos, prefix, callback) 
				{
					console.log("prefix:"+prefix);
					objectComplete(pos, callback);
				}
			}
			langTools.setCompleters([/*langTools.snippetCompleter,*/ langTools.textCompleter]);
			langTools.addCompleter(staticWordCompleter);
			
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) {
					var ajaxRes = JSON.parse(this.responseText).responce;
					if(ajaxRes.error == 0)
					{
						var array = [];
						var data = ajaxRes.data;
						$('#fileNav').jstree(
						{
							'core' : {
								'data' :  data,
								'check_callback' : function(o, n, p, i, m) {
									if(m && m.dnd && m.pos !== 'i') { return false; }
									if(o === "move_node" || o === "copy_node") {
										if(this.get_node(n).parent === this.get_node(p).id) { return false; }
									}
									return true;
								},
								'themes' : {
									'responsive' : true,
									'variant' : 'small',
									'stripes' : true
								}
							},
							'sort' : function(a, b) {
								return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
							},
							'contextmenu' : {
								'select_node': false,
								'items' : function(node) 
								{
									var tmp = $.jstree.defaults.contextmenu.items();
									delete tmp.create.action;
									tmp.create.label = "New";
									tmp.create.submenu = {
										"create_folder" : {
											"separator_after"	: true,
											"label"				: "Folder",
											"action"			: function (data) {
												var inst = $.jstree.reference(data.reference),
													obj = inst.get_node(data.reference);
												inst.create_node(obj, { type : "default", "text":"NewFolder"}, "last", function (new_node) {
													setTimeout(function () { inst.edit(new_node); },0);
												});
											}
										},
										"create_file" : {
											"label"				: "File",
											"action"			: function (data) {
												var inst = $.jstree.reference(data.reference),
													obj = inst.get_node(data.reference);
												inst.create_node(obj, { type : "file",'icon' : 'file',"text":"NewFile.py" }, "last", function (new_node) {
													setTimeout(function () { inst.edit(new_node); },0);
												});
											}
										}
									};
									if(this.get_type(node) === "file") {
										delete tmp.create;
									}
									if(node.text == "root") {
										log_obj("ContMenu/Create/Node",node);
										log_obj("ContMenu/Create/tmp",tmp);
										delete tmp.remove;
									}
									delete tmp.ccp;
									return tmp;
								}
							},
							'types' : {
								'default' : { 'icon' : 'folder' },
								'file' : { 'valid_children' : [], 'icon' : 'file' }
							},
							'unique' : {
								'duplicate' : function (name, counter) {
									return name + ' ' + counter;
								}
							},
							'plugins' : ['state','dnd','sort','types','contextmenu','unique']
						})
						.on('delete_node.jstree', function (e, data) 
						{
							if (confirm("Delete '"+data.node.text+"'?"))
							{
								var path = getPathOfNode(data.node);
								if(data.node.type == "file")
								{
									$.get('api/ueditor.files.delete.file.php', { 'path' : path })
										.done(function (d) {
											var ajaxRes = JSON.parse(d).responce;
											Toast.showAjaxRes(ajaxRes);
										})
										.fail(function (d) {
											data.instance.refresh();
										});
								}
								else
								{
									$.get('api/ueditor.files.delete.dir.php', { 'path' : path })
										.done(function (d) {
											var ajaxRes = JSON.parse(d).responce;
											Toast.showAjaxRes(ajaxRes);
										})
										.fail(function (d) {
											data.instance.refresh();
										});
								}
							}
						})
						.on('create_node.jstree', function (e, data) 
						{
							var newPath = getPathOfNode(data.node);
							log_obj("Create/Node",data.node);
							var path = getPathOfNode(data.node);
							if(data.node.type == "file")
								path = path.replace(data.node.text,"");
							var nodeName = data.node.text;
							log_i("Create/Node",path);
							$.get('api/ueditor.files.create.php', { 'path' : path, 'name' : nodeName })
								.done(function (d) {
									var ajaxRes = JSON.parse(d).responce;
									//Toast.showAjaxRes(ajaxRes);
								})
								.fail(function (d) {
									Toast.showAjaxRes(1,"ERROR #"+d.status);
								});
						})
						.on('rename_node.jstree', function (e, data) {
							log_obj("Rename/Node",data.node);
							if(data.node.text.includes("__init__") || data.node.text == "SYS")
							{
								data.node.text =data.node.original.text;
								fileNav.rename_node(data.node,data.node.original.text);
								return;
							}
							if(data.node.type == "file" && !data.node.text.includes(".py"))
							{
								data.node.text +=".py";
								fileNav.rename_node(data.node,data.node.text);
								return;
							}
							var newPath = getPathOfNode(data.node);
							var oldPath = getPathOfNode(data.node).replace(data.node.text,"")+data.node.original.text;
							
							log_i("Rename/Node",oldPath+"->"+newPath);
							$.get('api/ueditor.files.rename.php', { 'old' : oldPath, 'new' : newPath })
								.done(function (d) {
									var ajaxRes = JSON.parse(d).responce;
									Toast.showAjaxRes(ajaxRes);
									if(curPath == oldPath && newPath.includes(".py"))
									{
										curPath = newPath;
										editor_top_name_file.innerHTML = newPath;
									}
								})
								.fail(function () {
									Toast.showAjaxRes(1,"ERROR #"+this.status);
								});
						})
						.on('move_node.jstree', function (e, data) {
							log_obj("Move/Node",data.node);
							
							var newPath = getPathOfNode(data.node);
							log_i("Move/Path",dndPathOld+"->"+newPath);
							$.get('api/ueditor.files.move.php', { 'old' : dndPathOld, 'new' : newPath })
								.done(function (d) {
									var ajaxRes = JSON.parse(d).responce;
									Toast.showAjaxRes(ajaxRes);
									if(curPath == dndPathOld && newPath.includes(".py"))
									{
										curPath = newPath;
										editor_top_name_file.innerHTML = newPath;
									}
									dndPathOld = "";
								})
								.fail(function () {
									data.instance.refresh();
									Toast.showAjaxRes(1,"ERROR #"+this.status);
								});
						})
						.on('copy_node.jstree', function (e, data) {
							$.get('?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent })
								.done(function (d) {
									//data.instance.load_node(data.parent);
									data.instance.refresh();
								})
								.fail(function () {
									data.instance.refresh();
								});
						})
						.on('changed.jstree', function (e, data) {
							if(data.selected.length) {
								path = getPathOfData(data);
								editorOpenFile(path);
								//alert('The selected node is: ' + data.instance.get_node(data.selected[0]).text);
							}
						});
						fileNav = $('#fileNav').jstree(true);
					}
					else
						Toast.showAjaxRes(ajaxRes);
				}
			};
			editor.setShowInvisibles(true);
			editor.setOption("useSoftTabs",false)
			editor.on('input', function() { // async and batched
				if (!ignoreChange) setEditorNeedSave(true);
				ignoreChange = false;
			});
			editor.on('change', function() { // sync for each change
				if (!ignoreChange) 
					setEditorNeedSave(true);
			});
			$(document).on('dnd_start.vakata', function (data, element, helper, event) {
				/*log_obj("DND/Start/data",data);
				log_obj("DND/Start/helper",helper);
				log_obj("DND/Start/event",event);*/
				dndNode = fileNav.get_node(element.element.id);
				dndPathOld = getPathOfNode(dndNode);
				/*log_obj("DND/Start/element",element);
				log_obj("DND/Start/node",node);*/
			});
			xhttp.open("POST", "api/ueditor.list.all.php", true);
			xhttp.send();
			editor.setOptions({readOnly: true});
			editor_top_name = document.getElementById("editor-top-name");
			editor_top_name_file = document.getElementById("editor-top-name-file");
			editor_top_name_file.addEventListener("keypress", function(e) {
				if(e.keyCode == 13)
				{
					var newPath = editor_top_name_file.innerHTML;
					var oldPath = curPath;
					if(!newPath.includes(".py"))
						newPathtext +=".py";
					editorSaveFile();
					$.get('api/ueditor.files.rename.php', { 'old' : oldPath, 'new' : newPath })
						.done(function (d) {
							var ajaxRes = JSON.parse(d).responce;
							Toast.showAjaxRes(ajaxRes);
							if(curPath == oldPath && newPath.includes(".py"))
							{
								curPath = newPath;
								editor_top_name_file.innerHTML = newPath;
								setEditorNeedSave(false);
							}
						})
						.fail(function () {
							Toast.showAjaxRes(1,"ERROR #"+this.status);
						});
				}
				//else
					//setEditorNeedSave(true);
				//log_i("Top/File/Name/keyDown/code",e.keyCode);
			});
		}
		function setEditorNeedSave(need)
		{
			if(need && !needToSave)
				editor_top_name.innerHTML +="*";
			else if(!need && needToSave)
			{
				var htm = editor_top_name.innerHTML;
				editor_top_name.innerHTML =htm.substr(0,htm.length-1);
			}
			needToSave = need;//editor.session.getUndoManager().isClean()
		}
		function getPathOfData(data)
		{
			var node = data.instance.get_node(data.selected[0]);
			//log_obj("SelectNode/e",node);
			return data.instance.get_path(node, '/');
		}
		function getPathOfNode(node)
		{
			return fileNav.get_path(node, '/');
		}
		function editorOpenFile(path)
		{
			if(!path.includes(".py"))
				return;
			if(needToSave && confirm("File not saved. Save changes before closing file?"))
				editorSaveFile();
			var xhttp = new XMLHttpRequest();
			ShowLoading();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4)
				{
					DismissLoading();
					if(this.status == 200)
					{
						editor.setOptions({readOnly: false});
						ignoreChange = true;
						editor.setValue(this.responseText, 1) // moves cursor to the start
						
						editor_top_name_file.innerHTML = path;
						editor.getSession().setUndoManager(new ace.UndoManager());
						if(curPath != path)
							Hide('fileNav');
						curPath = path;
						//ignoreChange = false;
					}
					else
						Toast.showAjaxRes(1,"ERROR #"+this.status);
				}
			};
			xhttp.open("POST", "api/ueditor.files.get.php", true);
			xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xhttp.send("path="+path);
		}
		var isGoingToExecute = false;
		function editorExecuteFile()
		{
			isGoingToExecute = true;
			editorSaveFile();
			ShowLoading();
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4)
				{
					isGoingToExecute = false;
					DismissLoading();
					var ajaxRes = JSON.parse(this.responseText).responce;
					if(ajaxRes.error ==0)
						Lobibox.notify('success', {
						size: 'mini',
						delay: 15000,
						msg: "Скрипт успешно выполнен.<br />Нажмите для дополнительной информации",
						onClick: function()
						{
							Lobibox.alert("info", {msg: ajaxRes.data});
						},
					  });
					else
						Toast.showAjaxRes(ajaxRes);
				}
			};
			xhttp.open("POST", "api/ueditor.files.execute.php", true);
			xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xhttp.send("path="+curPath);
		}
		function editorSaveFile()
		{
			ShowLoading();
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4)
				{
					if(!isGoingToExecute)
						DismissLoading();
					var ajaxRes = JSON.parse(this.responseText).responce;
					if(ajaxRes.error ==0)
						setEditorNeedSave(false);
					else
						Toast.showAjaxRes(ajaxRes);
				}
			};
			var body = curPath+"\n"+editor.getValue();
			xhttp.open("POST", "api/ueditor.files.save.php", true);
			xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhttp.setRequestHeader("Content-Length", body.length);
			xhttp.setRequestHeader("Connection", "close");
			xhttp.send(body);
		}
		function objectComplete(pos, callback)
		{
			if(pos != lastPosition)
				objectPrefix = "";
			var meta = "";
			var wordList = [];
			console.log("objectPrefix"+objectPrefix);
			switch(objectPrefix)
			{
				default:
					wordList = ["device", "value","Device"];
					meta = "input";
				break;
				case "device":
					wordList = ["setValue($0)", "sendHeartBeat()"];
					meta = "device";
				break;
				case "Device":
					wordList = ["findDeviceById($0)", "findDeviceByUnId($0)"];
					meta = "device";
				break;
			}
			
			callback(null, wordList.map(function(word) {
				return {
					caption: word,
					snippet: word,
					meta: meta,
					score:100,
					completer: {
						insert: function(editor, data) {
							var insertedValue = data.value;
							if(insertedValue === objectPrefix) {
								objectPrefix = "";
							}
							//editor.insertMa(insertedValue);
						}
					}
				};
			}));
			
		}
	</script>
	</head>
	<body>
		<div class="top-bar">UserScripts Editor</div>
		<? require('menu.php'); ?>
		
		<div id="page" style="">
			<div id="editor-top">
				<div id="editor-top-name">File: <span id="editor-top-name-file" contenteditable="true"></span></div>
				<div id="editor-top-buttons" onclick="editorExecuteFile()"><i class="icofont-ui-play" style="float: right;" ></i></div>
				<div id="editor-top-buttons" onclick="editorSaveFile()"><i class="icofont-save" style="float: right;"></i></div>
			</div>
			<div id="fileNav-wrapper">
				<div id="fileNav"></div>
				<div id="fileNav-show" onclick="ReverseDisplay('fileNav');"><i class="icofont-rounded-left"></i></div>
			</div>
			<div id="editor">#To start editing scripts, select or create new one on the right in the explorer.</div>
		</div>
		<? require('templates/bottom.php'); ?>
	</body>
</html>

