<?php
require("config.php");
if($_SESSION['lang']){
	$_SESSION['lang'] = str_replace("../", "", $_SESSION['lang']);
	if(file_exists("langs/".$_SESSION['lang'].".php")){
		require("langs/".$_SESSION['lang'].".php");
	}else{
		require("langs/default.php");
	}
}else{
	require("langs/default.php");
}
?>
//<script>

isDOM=document.getElementById 
isOpera=isOpera5=window.opera && isDOM 
isOpera6=isOpera && window.print 
isOpera7=isOpera && document.readyState 
isMSIE=document.all && document.all.item && !isOpera 
isMSIE5=isDOM && isMSIE 
isNetscape4=document.layers 
isMozilla=isDOM && navigator.appName=="Netscape" 
var data;
var http_file_link;
var catalogs;
var new_uploaded_file = false;
function open_dir(val){
	var finded_file = false;
	last_selected_file = "";
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				if(req.responseJS.files){
					data = req.responseJS.files;
					http_file_link = req.responseJS.http_link;
					catalogs = req.responseJS.catalogs;
						var select = '<option value="/">/</option>';
						for(i=0;i<catalogs.length;i++){
							if("/"+val==catalogs[i]){
								select += '<option value="'+catalogs[i]+'" selected>'+catalogs[i]+'</option>';
							}else{
								select += '<option value="'+catalogs[i]+'">'+catalogs[i]+'</option>';
							}
						}
					document.getElementById("catalog_list_td").innerHTML = "<select>"+select+"</select>";
					var table = document.createElement("table");
					table.className = "files_list_table";
					oRow = table.insertRow(-1);
					oRow.className = "title";
					oCell = oRow.insertCell(-1);
					oCell = oRow.insertCell(-1);
					oCell.innerHTML = "<?php echo $getword['name']?>";
					oCell = oRow.insertCell(-1);
					oCell = oRow.insertCell(-1);
					for(i=0;i<data.length;i++){
							
							oRow = table.insertRow(-1);
							oRow.height = 22;
							oRow.onmouseover = function(){
							  this.style.backgroundColor = "#F8F8ED";
							  this.style.color = "black";
							}
							oRow.onmouseout = function(){
							  this.style.backgroundColor = "";
							  this.style.color = "";
							}
							oCell = oRow.insertCell(-1);
							oCell.width = "24";
							oCell.align = "center";
							oCell.innerHTML = "<img src=\"images/icons/"+data[i]['image']+"\" alt=\""+data[i]['ext']+"\">";
							oCell.title = data[i]['ext'];
							oCell = oRow.insertCell(-1);
							oCell.style.cursor = "pointer";
							oCell.innerHTML = data[i]['name'];
							oCell.id = i;
							if(data[i]['image']=="folder.gif"){
								oCell.onclick = function(){
									this.style.backgroundColor = "#3D74A6";
									this.style.color = "white";
									if(val){
										next_dir = val+"/"+this.innerHTML
									}else{
										next_dir = this.innerHTML
									}
									open_dir(next_dir);
								}
							}else{
								oCell.onclick = function(){file_select(this.parentNode.rowIndex);}
								if(new_uploaded_file && new_uploaded_file==data[i]['name']){
									finded_file = oRow.rowIndex;
								}
							}
							oCell = oRow.insertCell(-1);
							oCell.width = "16";
							oCell.align = "center";
							oCell.title = "<?php echo $getword['rename']?>";
							oCell.innerHTML = "<a href=\"\" onclick=\"rename_file(this.parentNode.parentNode.rowIndex);return false;\"><img src=\"images/rename.gif\"></a>";
							oCell = oRow.insertCell(-1);
							oCell.width = "18";
							oCell.align = "center";
							oCell.title = "<?php echo $getword['delete']?>";
							oCell.innerHTML = "<a href=\"\" onclick=\"if(confirm('<?php echo $getword['del_request_part_1']?> &quot;"+data[i]['real']+"&quot; <?php echo $getword['del_request_part_2']?>')){delete_file(this.parentNode.parentNode.rowIndex);}return false;\"><img src=\"images/delete.gif\"></a>";
					}
					ts_makeSortable(table);
					document.getElementById("files_list_div").innerHTML = "";
					document.getElementById("files_list_div").appendChild(table);
				}
				document.getElementById("now_location").value = "/"+val;
				if(req.responseText){alert(req.responseText);}
				if(finded_file){file_select(finded_file);}
				if(new_uploaded_file){new_uploaded_file = false;}
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'get_list', 'dir': val});
}
function up_directory(){
	var now_dir = document.getElementById("now_location");
	var temp_dir = now_dir.value;
	var last_sym = temp_dir.substr((temp_dir.length-1), temp_dir.length);
	if(last_sym=="/"){
		temp_dir = temp_dir.substr(0, (temp_dir.length-1));
	}
	var dir_data = temp_dir.split("/");
	var new_dir = "";
	for(i=0;i<(dir_data.length-1);i++){
		if(new_dir){new_dir += "/";}
		new_dir += dir_data[i];
	}
	open_dir(new_dir);
}
function in_array(val, values){
	var result = false;
	for(i=0;i<values.length;i++){
		if(val==values[i]){
			result = true;
			break;
		}
	}
	if(result){return true;}else{return false;}
}
var last_selected_file;
function file_select(val){
  if(val!=last_selected_file){
	document.getElementById("send_file").getElementsByTagName("input")[0].value = "";
	document.getElementById("send_file").getElementsByTagName("input")[1].value = "";
	document.getElementById("send_file").style.display = 'none';
	document.getElementById("copy_to").style.display = 'none';
	document.getElementById("resizing_place").style.display = 'none';
	var preview = document.getElementById("preview");
	if(last_selected_file){
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		td[1].style.backgroundColor = "";
		td[1].style.color = "";
	}
	var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[val];
	var td = tr.getElementsByTagName("td");
	td[1].style.backgroundColor = "#3D74A6";
	td[1].style.color = "white";
	var file_data = data[td[1].id];
	var td = document.getElementById("bottom_info_table").getElementsByTagName("td");
	td[0].innerHTML = file_data['ext'];
	td[2].innerHTML = file_data['info'];
	td[4].innerHTML = file_data['size'];
	var now_folder = document.getElementById("now_location").value;
	if(now_folder=="/"){now_folder = "";}
	var new_file_url = http_file_link+now_folder+"/"+file_data['real'];
	document.getElementById("selected_file_url").value = new_file_url;
		var actions = "<li><a href=\"\" onclick=\"copy_to(''); return false;\"><?php echo $getword['copy']?></a>";
		actions += "<li><a href=\"\" onclick=\"move_to(''); return false;\"><?php echo $getword['move']?></a>";
		if(in_array(file_data['ext'], "jpg gif bmp png tiff".split(" "))){
			var image_sizes = file_data['info'].split("x");
			var image = document.createElement("img");
			image.src = new_file_url;
			image.width = image_sizes[0];
			image.height = image_sizes[1];
			preview.innerHTML = "";
			preview.appendChild(image);
			actions += "<li><a href=\"\" onclick=\"resizing_place_func(''); return false;\"><?php echo $getword['resize']?></a>";
		}else if(in_array(file_data['ext'], "htm html swf txt xml".split(" "))){
			if(file_data['ext']=="swf"){
				new_file_url = "./?show_flash="+new_file_url;
			}
			var iframe = "<iframe src=\""+new_file_url+"\" frameborder=0 width=100% height=100%>";
			preview.innerHTML = iframe;
		}else if(file_data['ext']=="mp3"){
			var iframe = "<iframe src=\"player.php?song="+new_file_url+"\" frameborder=0 width=100% height=100%>";
			preview.innerHTML = iframe;
		}else{
			var table = document.createElement("table");
			table.className = "no_preview";
			oRow = table.insertRow(-1);
			oCell = oRow.insertCell(-1);
			oCell.innerHTML = "<?php echo $getword['no_preview']?>";
			preview.innerHTML = "";
			preview.appendChild(table);
		}
		<?php
		if($_SESSION['type']=="file"){
		?>
		if(!in_array(file_data['ext'], "zip gz tgz rar 7zip".split(" "))){
			actions += "<li><a href=\"\" onclick=\"to_archive();return false;\"><?php echo $getword['add_to_archive']?></a>";
		}else if(file_data['ext']=="zip"){
			actions += "<li><a href=\"\" onclick=\"from_archive();return false;\"><?php echo $getword['extract_archive']?></a>";
		}
		<?php }?>
		actions += "<li><a href=\"\" onclick=\"document.getElementById('send_file').style.display='';document.getElementById('actions_list').style.display='none';return false;\"><?php echo $getword['send_to_email']?></a>";
		document.getElementById("actions_list").getElementsByTagName("td")[1].innerHTML = "<ul>"+actions+"</ul>";
		document.getElementById("actions_list").style.display = '';
	last_selected_file = val;
  }
}
var resize_image_k;
function resizing_place_func(val){
	if(last_selected_file && !val){
		document.getElementById("resizing_place").style.display = '';
		document.getElementById("actions_list").style.display = 'none';
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var file_data = data[td[1].id];
		var sizes = file_data['info'].split("x");
		document.getElementById("resizing_place").getElementsByTagName("input")[0].value = sizes[0];
		document.getElementById("resizing_place").getElementsByTagName("input")[1].value = sizes[1];
		resize_image_k = sizes[0]/sizes[1];
	}else if(last_selected_file && val=="set_height"){
		var w = document.getElementById("resizing_place").getElementsByTagName("input")[0].value;
		document.getElementById("resizing_place").getElementsByTagName("input")[1].value = Math.round(w / resize_image_k);
	}else if(last_selected_file && val=="resize"){
		document.getElementById("action_menu").style.display = "none";
		document.getElementById("action_upload_form").style.display = "none";
		document.getElementById("uploading_image").style.display = "";
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var file_name = data[td[1].id]['real'];
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		var size_w = document.getElementById("resizing_place").getElementsByTagName("input")[0].value;
		var size_h = document.getElementById("resizing_place").getElementsByTagName("input")[1].value;
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				document.getElementById("resizing_place").style.display = 'none';
				document.getElementById("actions_list").style.display = 'none';
					var preview = document.getElementById("preview");
					var table = document.createElement("table");
					table.className = "no_preview";
					oRow = table.insertRow(-1);
					oCell = oRow.insertCell(-1);
					oCell.innerHTML = "<?php echo $getword['no_preview']?>";
					preview.innerHTML = "";
					preview.appendChild(table);
				document.getElementById("action_menu").style.display = "";
				document.getElementById("action_upload_form").style.display = "none";
				document.getElementById("uploading_image").style.display = "none";
				open_dir(now_dir);
				if(req.responseText){alert(req.responseText);}else{alert("Image resized.");}
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'resize_image', 'dir': now_dir, 'file': file_name, 'width': size_w, 'height': size_h});
	}
}
function copy_to(val){
	if(!val){
		document.getElementById("copy_to").getElementsByTagName("td")[1].innerHTML = "<?php echo $getword['copyng_file']?>";
		document.getElementById("copy_to").getElementsByTagName("td")[2].innerHTML = "<?php echo $getword['copy_to']?>:";
		document.getElementById("copy_to").getElementsByTagName("input")[0].value = "<?php echo $getword['copy_btn']?>";
		document.getElementById("copy_to").getElementsByTagName("input")[0].onclick = function(){
			copy_to(1);
		}
		document.getElementById("copy_to").style.display = '';
		document.getElementById("actions_list").style.display = 'none';
	}else if(last_selected_file){
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var to = document.getElementById("catalog_list_td").getElementsByTagName("select")[0].value;
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				document.getElementById("copy_to").style.display = 'none';
				document.getElementById("actions_list").style.display = 'none';
				open_dir(now_dir);
				if(req.responseText){alert(req.responseText);}else{alert("<?php echo $getword['file_copyed']?>");}
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'copy', 'dir': now_dir, 'file': data[td[1].id]['real'], 'to': to});
	}
}
function move_to(val){
	if(!val){
		document.getElementById("copy_to").getElementsByTagName("td")[1].innerHTML = "<?php echo $getword['moving_file']?>";
		document.getElementById("copy_to").getElementsByTagName("td")[2].innerHTML = "<?php echo $getword['move_to']?>:";
		document.getElementById("copy_to").getElementsByTagName("input")[0].value = "<?php echo $getword['move_btn']?>";
		document.getElementById("copy_to").getElementsByTagName("input")[0].onclick = function(){
			move_to(1);
		}
		document.getElementById("copy_to").style.display = '';
		document.getElementById("actions_list").style.display = 'none';
	}else{
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var to = document.getElementById("catalog_list_td").getElementsByTagName("select")[0].value;
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				document.getElementById("copy_to").style.display = 'none';
				document.getElementById("actions_list").style.display = 'none';
				open_dir(now_dir);
				if(req.responseText){alert(req.responseText);}else{alert("<?php echo $getword['file_moved']?>");}
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'move', 'dir': now_dir, 'file': data[td[1].id]['real'], 'to': to});
		
	}
}

var tmp_name;
function rename_file(val){
	var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[val];
	var td = tr.getElementsByTagName("td");
	tmp_name = td[1].innerHTML;
	var input = document.createElement("input");
	input.type = "text";
	input.value = tmp_name;
	input.onblur = function(){
		if(this.value!=tmp_name && this.value.length>=1){
			update_name(data[this.parentNode.id]['real'], this.value);
			this.parentNode.innerHTML = "<?php echo $getword['checking_name']?>";
		}else{
			this.parentNode.innerHTML = tmp_name;
		}
	}
	input.onkeydown = function(e){
		if(!e){e = event;}
		if(e.keyCode==13){
			if(this.value!=tmp_name && this.value.length>=1){
				update_name(data[this.parentNode.id]['real'], this.value);
				this.parentNode.innerHTML = "<?php echo $getword['checking_name']?>";
			}else{
				this.parentNode.innerHTML = tmp_name;
			}
		}
	}
	td[1].innerHTML = "";
	td[1].appendChild(input);
	input.focus();
	input.select();
}
function update_name(old_name, new_name){
	var now_dir = document.getElementById("now_location").value;
	now_dir = now_dir.substr(1, now_dir.length);
		
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				open_dir(now_dir);
				if(req.responseText){alert(req.responseText);}
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'rename', 'dir': now_dir, 'old_name': old_name, 'new_name': new_name});
	
}
function delete_file(val){
	var preview = document.getElementById("preview");
	var now_dir = document.getElementById("now_location").value;
	now_dir = now_dir.substr(1, now_dir.length);
	var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[val];
	var td = tr.getElementsByTagName("td");
	var req = new JsHttpRequest();
	req.onreadystatechange = function(){
		if(req.readyState == 4){
			var table = document.createElement("table");
			table.className = "no_preview";
			oRow = table.insertRow(-1);
			oCell = oRow.insertCell(-1);
			oCell.innerHTML = "<?php echo $getword['no_preview']?>";
			preview.innerHTML = "";
			preview.appendChild(table);
			document.getElementById("actions_list").style.display = 'none';
			open_dir(now_dir);
			if(req.responseText){alert(req.responseText);}
		}
	}
	req.caching = false;
	req.open('POST', './data.php', true);
	req.send({'user_action': 'delete', 'dir': now_dir, 'name': data[td[1].id]['real']});
}
function create_new_dir(){
	var now_dir = document.getElementById("now_location").value;
	now_dir = now_dir.substr(1, now_dir.length);
	var req = new JsHttpRequest();
	req.onreadystatechange = function(){
		if(req.readyState == 4){
			open_dir(now_dir);
			if(req.responseText){alert(req.responseText);}
		}
	}
	req.caching = false;
	req.open('POST', './data.php', true);
	req.send({'user_action': 'new_dir', 'dir': now_dir});
}
function upload_form(val){
	if(val=="show"){
		document.getElementById("action_menu").style.display = "none";
		document.getElementById("action_upload_form").style.display = "";
	}else{
		document.getElementById("action_menu").style.display = "";
		document.getElementById("action_upload_form").style.display = "none";
	}
}
function upload_file(val){
	if(!val.value){
		alert("<?php echo $getword['no_file_selected']?>");
	}else{
		document.getElementById("action_upload_form").style.display = "none";
		document.getElementById("uploading_image").style.display = "";
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				document.getElementById("action_menu").style.display = "";
				document.getElementById("action_upload_form").style.display = "none";
				document.getElementById("uploading_image").style.display = "none";
				new_uploaded_file = req.responseJS.filename;
				var table = document.createElement("table");
				table.className = "no_preview";
				oRow = table.insertRow(-1);
				oCell = oRow.insertCell(-1);
				oCell.innerHTML = "<?php echo $getword['no_preview']?>";
				preview.innerHTML = "";
				preview.appendChild(table);
				document.getElementById("actions_list").style.display = 'none';
				if(req.responseJS.error){alert(req.responseJS.error);}
				if(req.responseText){alert(req.responseText);}
				setTimeout("open_dir('"+now_dir+"')", "100");
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'upload', 'dir': now_dir, 'file': val});
		
	}
}
function download_file(){
	if(last_selected_file){
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		var preview = document.getElementById("preview");
		var iframe = "<iframe src=\"download.php?dir="+now_dir+"&file="+data[td[1].id]['real']+"\" frameborder=0 width=100% height=100%>";
		preview.innerHTML = iframe;
	}
}
function to_archive(){
	if(last_selected_file){
		document.getElementById("action_menu").style.display = "none";
		document.getElementById("uploading_image").style.display = "";
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				document.getElementById("action_menu").style.display = "";
				document.getElementById("uploading_image").style.display = "none";
				open_dir(now_dir);s
				if(req.responseText){alert(req.responseText);}
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'to_archive', 'dir': now_dir, 'file': data[td[1].id]['real']});	
	}	
}
function from_archive(){
	if(last_selected_file){
		document.getElementById("action_menu").style.display = "none";
		document.getElementById("uploading_image").style.display = "";
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		var req = new JsHttpRequest();
		req.onreadystatechange = function(){
			if(req.readyState == 4){
				document.getElementById("action_menu").style.display = "";
				document.getElementById("uploading_image").style.display = "none";
				open_dir(now_dir);
				if(req.responseText){alert(req.responseText);}
			}
		}
		req.caching = false;
		req.open('POST', './data.php', true);
		req.send({'user_action': 'from_archive', 'dir': now_dir, 'file': data[td[1].id]['real']});
	}
}
function send_file_to_email(){
	if(last_selected_file){
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		var to = document.getElementById("send_file").getElementsByTagName("input")[1];
		var from = document.getElementById("send_file").getElementsByTagName("input")[0];
		if(from.value && to.value){
			document.getElementById("action_menu").style.display = "none";
			document.getElementById("uploading_image").style.display = "";
			
			var req = new JsHttpRequest();
			req.onreadystatechange = function(){
				if(req.readyState == 4){
					open_dir(now_dir);
					document.getElementById("action_menu").style.display = "";
					document.getElementById("uploading_image").style.display = "none";
					if(req.responseJS.error){
						alert(req.responseJS.error);
					}
					document.getElementById("send_file").style.display = 'none';
					to.value = "";
					from.value = "";
					if(req.responseText){alert(req.responseText);}
				}
			}
			req.caching = false;
			req.open('POST', './data.php', true);
			req.send({'user_action': 'send_file', 'dir': now_dir, 'file': data[td[1].id]['real'], 'to': to.value, 'from': from.value});
		}else{
			alert("<?php echo $getword['please_set_send_data']?>");
		}
	}else{
		alert("<?php echo $getword['no_file_selected']?>");
	}
}
function openFile(){
	if(last_selected_file){
		var tr = document.getElementById("files_list_div").getElementsByTagName("tr")[last_selected_file];
		var td = tr.getElementsByTagName("td");
		var file_name = data[td[1].id]['real'];
		var now_dir = document.getElementById("now_location").value;
		now_dir = now_dir.substr(1, now_dir.length);
		if(now_dir){
			file_name = "<?php echo $_SESSION['FTP_EXT']?>files/<?php echo $ajaxel_folder; ?>/"+now_dir+"/"+file_name;
		}else{
			file_name = "<?php echo $_SESSION['FTP_EXT']?>files/<?php echo $ajaxel_folder; ?>/"+file_name;
		}
		<?php if (isset($_GET['to']) && $_GET['to'] && $_GET['win_id']):?>
			<?php if ($_GET['name_id']):?>
				var v=window.parent.VisualEditor_<?php echo htmlspecialchars($_GET['name_id'])?>.editor.getValue();
				var o=window.parent.VisualEditor_<?php echo htmlspecialchars($_GET['name_id'])?>.editor.setValue(v+'\r\n<img src="'+file_name+'" alt="" />');
			<?php else:?>
				window.parent.$('#<?php echo $_GET['to']?>').val(window.parent.$('#<?php echo $_GET['to']?>').val()+'\r\n<img src="'+file_name+'" alt="" />');
			<?php endif;?>
			window.parent.S.A.W.close('<?php echo $_GET['win_id']?>');
			return false;
		<?php endif;?>
		var fileSizes = document.getElementById("bottom_info_table").getElementsByTagName("td")[2].innerHTML.split("x");
		var tmp = [];
		tmp['src'] = file_name;
		tmp['background_image'] = file_name;
		tmp['backgroundimage'] = file_name;
		if(fileSizes[0] && fileSizes[1]){
			tmp['width'] = fileSizes[0];
			tmp['height'] = fileSizes[1];
		}
		var win = tinyMCEPopup.getWindowArg("window");
		var forms = win.document.forms;
		for(var f in forms){
			var elements = forms[f].elements;
			for(var e in elements){
				var elm = elements[e];
				if (elm && typeof elm != "undefined" && elm.name && tmp[elm.name]){
					elm.value = tmp[elm.name];
				}	
			}	
		}
        if (win && win.ImageDialog && win.ImageDialog.getImageData){win.ImageDialog.getImageData();}
        if (win && win.ImageDialog && win.ImageDialog.showPreviewImage){win.ImageDialog.showPreviewImage(file_name);}
		if (win && win.document.forms[0] && win.document.forms[0].elements['href']){win.document.forms[0].elements['href'].value = file_name;}
		tinyMCEPopup.close();
	}else{
		alert("<?php echo $getword['no_file_selected']?>");
	}
}
//</script>