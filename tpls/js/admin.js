/* ----------------------------------------------- 
	Admin javascript library.
	Ajaxel CMS 7.00
	http://ajaxel.com
   ----------------------------------------------- */
S.A = {
	console:false,console_max:50
	,prepend: '#center-area', admin_tag: 'e'
	,log:function(str){
		if (!this.console) this.console=$('<div>').addClass('a-console').css({color:'#000'}).attr('id','a-console').appendTo(this.prepend);	
		$('<div>').html(str).prependTo(this.console);
		var i=0;this.console.children().each(function(){i++;if(i>this.console_max)$(this).remove()});
	}
	,Conf:{
		temp:[],
		open_in_popup: false,
		open_inline: true,
		visual: true,
		lang: ''
	}
	,O:{}
	,Lang:{}
	,W:{	// Window
		wins:[],win_id:0,callback_func:false,vars:[],temp_win:false,
		name:'',title:'',url:'',id:0,name_id:0,upload:'',multi:false,prefix:'',userid:0,version:1,zIndex:400,reload:false
		,dock_height:24
		,toggleFullScreen:function(fn){
			$('#center-area').css({
				height:$(window).height()-5,
				width:$(window).width()-4			
			});
			if (fn) fn($(window).height());
			$(window).unbind('resize').resize(function(){
				S.A.W.toggleFullScreen(fn);
			});
		}
		,load:function(vars){
			this.vars[vars.name_id] = vars;
			$('input.a-date').unbind('datepicker').datepicker({
				dateFormat: 'dd/mm/yy'
			}).css({cursor:'pointer'});
		}
		,callback:function(win_id, func) {
			
			if (func) this.callback_func = func;
			//	setTimeout(function(){
				eval('if (typeof('+win_id+')==\'object\'&&typeof('+win_id+'.load)==\'function\'){try{'+win_id+'.load()}catch(e){alert(e.message)}}');
			//},200);
			if ($.fn.button) $('.a-button').button();
			this.wins[win_id].win.find('.a-win_status').mouseover(function(){
				S.A.W.status(win_id,$(this).attr('alt'));
			}).mouseout(function(){
				S.A.W.status(win_id);
			});
			setTimeout(function(){
				S.A.W.scroll(false,win_id);
				/*
				var oi=S.A.W.wins[win_id].win.find('input:first');
				oi.focus().val(oi.val()).focus();
				setTimeout(function(){
					oi.focus();
				},1000);
				*/
			},200);
			
		}
		,status:function(win_id, msg) {
			if (!msg) msg=this.wins[win_id].status;
			this.wins[win_id].status=$('#a-status_'+win_id).html();
			$('#a-status_'+win_id).html(msg);	
		}
		,filePath:function(win_id, file, th, global) {
			
			if (file.indexOf('/')!==-1) {
				return file;
			}
			if (this.vars[win_id].id>0) {
				if (this.vars[win_id].name=='grid') {
					var path = this.vars[win_id].name+'_'+this.vars[win_id].module;
				} else {
					var path = this.vars[win_id].name;	
				}

				//if (!th) th = 'th1';
				var r=S.C.FTP_EXT+S.A.Conf.dir_files+(!global?this.vars[win_id].prefix+'/':'')+path+'/'+this.vars[win_id].id+'/'+(th?th+'/':'')+file;
			} else {
				var r=S.C.FTP_EXT+S.A.Conf.dir_files+(!global?this.vars[win_id].prefix+'/temp/':'')+this.vars[win_id].userid+'/'+(th?th+'/':'')+file;	
			}
			
			return r;
		}
		,delPhoto:function(win_id, file, fn, obj, name, action){
			if (typeof(name)!='undefined') name = '';
			if (file) {
				var files=file;
				if (files.indexOf('|')!==-1) {
					var ex=files.split('|'),j=[],e;
					for (i in ex) {
						e=ex[i].split('/');
						j.push((parseInt(i)+1)+'. '+e[e.length-1]);
					}
					files='\n'+j.join('\n');
				} else {
					var f=files.split('/');
					files=' \''+f[f.length-1]+'\'';	
				}
			} else {
				files = '';	
			}
			if (confirm(S.A.Lang.image_dc+files+'?')) {
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+this.vars[win_id].name+'&id='+this.vars[win_id].id, {
					'get': (action ? action : 'delete_image'),
					'name': name,
					'file': file
				},function(data){
					if (name) {
						if (fn)fn(data.response);
					}
					else if (file) {
						if (fn) fn(data.response);
						if (obj) {
							$(obj).parent().parent().hide('slide',{}, 300, function(){$(this).remove()});
						}
					} else {
						S.A.W.dialog(data, false, function(){
							if (fn)fn(data.response);
						});
					}
				});
			}
		}
		,addMainPhoto:function(win_id, file, name, th) {
			if (!file) {
				$('#a-w-'+name+'_'+win_id).html('')
			} else {
				S.A.W.reload = true;
				/*
				if (file.indexOf('/')!==-1) {
					file = file.replace('/temp/'+this.vars[win_id].userid+'/','/temp/'+this.vars[win_id].userid+'/th1/');
				}
				*/
				var html = '<a href="'+file+'" target="_blank" class="a-thumb"><img src="'+file.replace('/th1/','/'+(th?th:'th3')+'/')+'?n='+Math.random()+'"></a>';
				setTimeout(function(){
					$('#a-w-'+name+'_'+win_id).html(html);
					S.A.I.load();
				},1000);
			}	
		}
		,browseServer:function(path, action) {
			if (CKFinder===undefined) return false; 
			var finder = new CKFinder();
			finder.basePath = '../';
			finder.startupPath = path;
			finder.selectActionFunction = S.A.W.setFileField;
			finder.selectActionData = action || false;
			// finder.selectThumbnailActionFunction = S.A.W.showThumbnails;
			finder.popup();
		}
		,codebb:function(id, val){
			S.A.L.global('codebb', {text: val}, function(data){
				$('#'+id).html(data.text);
			});
		}
		,setFileField:function(fileUrl, data) {
			if (data['selectActionData']) $('#'+data['selectActionData']).val(fileUrl);
			else S.G.alert(fileUrl);
		}
		/*
		,showThumbnails:function(fileUrl,data) {
			return false;
			// this = CKFinderAPI
			var sFileName = this.getSelectedFile().name;
			document.getElementById( 'thumbnails' ).innerHTML +=
					'<div class="thumb">' +
						'<img src="' + fileUrl + '" />' +
						'<div class="caption">' +
							'<a href="' + data["fileUrl"] + '" target="_blank">' + sFileName + '</a> (' + data["fileSize"] + 'KB)' +
						'</div>' +
					'</div>';
		
			document.getElementById( 'preview' ).style.display = "";
			// It is not required to return any value.
			// When false is returned, CKFinder will not close automatically.
			return false;
		}
		*/
		,mail_template:function(id,obj_subject,obj_body){
			if (obj_subject&&!S.A.Conf.mail_template_subject) S.A.Conf.mail_template_subject=$(obj_subject).val();
			if (id) {
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=email&tab=mailtpl',{
					get: 'action',
					a: 'mail_template',
					id: id
				}, function(data) {
					if (obj_subject) obj_subject.val(data.subject);
					if (obj_body) obj_body.html(data.body);
				});
			} else {
				if (obj_subject) obj_subject.val('');
				if (obj_body) obj_body.html('');
			}
		}
		
		// For catalogue
		,delOption:function(obj) {
			$(obj).parent().parent().find('td').animate({opacity:0.1, height:5}, 500, function(){$(obj).parent().parent().remove()});
		}
		,copyOption:function(obj) {
			var tr=$(obj).parent().parent(),tr2 = tr.clone();
			tr2.insertAfter(tr);
		}
		,addOption:function(obj) {
			var tr=$(obj).parent().parent(),tr2 = tr.clone();
			tr2.find('.a-option').val('');
			tr2.insertAfter(tr);
		}
		,addModelOption:function(win_id, obj, name) {
			var o=$('#a-options_'+name+'_'+win_id);
			var total = parseInt($('#a-total_models_'+name+'_'+win_id).val())+1;
			$('#a-total_models_'+name+'_'+win_id).val(total);
			var w=parseInt(98/total);
			if (!o.find('.a-option_value').length) return;
			o.find('.a-option_value').each(function(){
				var i=$(this);
				i.css({width: w+'%'});
				if (i.hasClass('a-option_first')) {
					var c=i.clone().removeClass('a-option_first');
					i.after(c);
				}
			});
		}
		,delModelOption:function(win_id, obj, name) {
			var o=$('#a-options_'+name+'_'+win_id);
			var total = parseInt($('#a-total_models_'+name+'_'+win_id).val())-1;
			if (total<1) return false;
			$('#a-total_models_'+name+'_'+win_id).val(total);
			var w=parseInt(96/total);
			if (!o.find('.a-option_value').length) return;
			o.find('.a-option_value').parent().each(function(){
				var i=$(this).find('.a-option_value');
				i.css({width: w+'%'});
				i.last().remove();
			});
		}
		,saveOptionGroup:function(win_id, fm, c, obj, vars) {
			S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+this.vars[win_id].name+'&id='+this.vars[win_id].id, {
				_t: S.C._T,
				get: 'save_option_group',
				vars: vars,
				post: S.A.P.data(fm.find(c))
			},function(data){
				var o=$(obj).parent().find('select');
				S.A.F.options(o,data,vars.group);
			});	
		}
		,selectGroup:function(win_id, obj,vars) {
			S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+this.vars[win_id].name+'&id='+this.vars[win_id].id, {
				_t: S.C._T,
				get: 'select_group',
				vars: vars,
				name: obj.value
			},function(data){
				eval('window_'+win_id).setOptions(data.opts,vars.option_group,vars.max_add,vars.type,data.group,obj.value);
				$('.a-button').button();
			});	
		}
		//\\ end for catalogue
		
		,setMainPhoto:function(win_id, file, obj, isFiles){
			if (file=='undefined') return alert('File name is undefined');
			S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+this.vars[win_id].name+'&id='+this.vars[win_id].id, {
				_t: S.C._T,
				get: 'set_main_photo',
				file: file
			},function(data){
				if (isFiles) {
					$(obj).parent().parent().parent().parent().parent().parent().parent().parent().find('li').removeClass('a-main_photo');
					$('#a-img_'+isFiles+'').addClass('a-main_photo');	
				} else {
					$(obj).parent().parent().parent().find('.a-photo').removeClass('a-main_photo');
					$(obj).parent().parent().addClass('a-main_photo');
				}
			});
		}
		,uploadify_one:function(win_id, name, fileExt, fileDesc, action, global) {
			
			var uploaded=0;
			$('#a-'+name+'_'+win_id).unbind('uploadify').uploadify({
				wmode      		: 'transparent',
				uploader		: S.C.FTP_EXT+'tpls/js/uploadify/uploadify.swf',
				script			: S.C.HTTP_EXT+'?upload='+Conf.URL_KEY_ADMIN+'.'+S.A.W.vars[win_id].name+'.'+S.A.W.vars[win_id].id,
				cancelImg		: S.C.FTP_EXT+'tpls/img/cancel.png',
				expressInstall	: S.C.FTP_EXT+'tpls/js/uploadify/expressInstall.swf',
				folder			: S.A.W.vars[win_id].upload/*+((name&&name!='main_photo')?'/'+name:'')*/,
				queueID			: 'a-w-upload_progress',
				auto			: true,
				multi			: false,
				fileExt			: fileExt,
				fileDesc		: fileDesc,
				buttonImg		: S.C.FTP_EXT+'tpls/img/upload.png',
				width			: 150,
				height			: 39,
				simUploadLimit	: 1,
				queueSizeLimit	: 500,
				onComplete: function(e, queueID, fileObj, response, data) {
					if (response.substring(0,1)!='1') {
						try{
							eval('var d='+response+';');
							S.A.W.dialog(d);
						}catch(e){
							alert(response);	
						}
						S.A.FU.div.hide();
					} else {
						var file = S.A.W.filePath(win_id, response.substring(2), 'th2', global);
						eval('window_'+win_id+'.setPhoto(\''+file+'\',\''+name+'\');');
						uploaded++;
					}
				},
				onError: function(e, ID, fileObj, errorObj) {
					S.G.alert(errorObj);
					S.A.FU.div.delay(1000).hide();
				},
				onAllComplete: function() {
					if(!uploaded) {
						S.A.FU.div.delay(1000).hide();
						S.A.W.unblock();
						return false;
					}
					S.A.FU.div.effect('blind', {}, 300, function(){
						S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+S.A.W.vars[win_id].name+'&id='+S.A.W.vars[win_id].id,{
							get: (action?action:'save_image')
						});
					});
				},
				onSelect: function() {
					S.A.FU.div.show('slow');
				}
			});
		}
		,images:[]
		,uploadify:function(win_id, name, fileExt, fileDesc, action, global, fn){
			var uploaded = 0;
			$('#a-'+name+'_'+win_id).unbind('uploadify').uploadify({
				wmode      		: 'transparent',
				uploader		: S.C.FTP_EXT+'tpls/js/uploadify/uploadify.swf',
				script			: S.C.HTTP_EXT+'?upload='+Conf.URL_KEY_ADMIN+'.'+S.A.W.vars[win_id].name+'.'+S.A.W.vars[win_id].id,
				cancelImg		: S.C.FTP_EXT+'tpls/img/cancel.png',
				expressInstall	: S.C.FTP_EXT+'tpls/js/uploadify/expressInstall.swf',
				folder			: S.A.W.vars[win_id].upload,
				queueID			: 'a-w-upload_progress',
				auto			: true,
				multi			: true,
				fileExt			: fileExt,
				fileDesc		: fileDesc,
				
				buttonImg		: S.C.FTP_EXT+'tpls/img/upload.png',
				width			: 150,
				height			: 39,
				
				simUploadLimit	: 3,
				queueSizeLimit	: 500,
				onComplete: function(e, queueID, fileObj, response, data) {
					if (response.substring(0,1)!='1' && response.substring(1,2)!='1') {
						if (response.substring(0,1)!='{') alert(response);
						else {
							try{
								eval('var d='+response+';');
								S.A.W.dialog(d);
							}catch(e){
								alert(response);
							}
						}
						S.A.FU.div.hide();
					} else {
						uploaded++;
					}
				},
				onError: function(e, ID, fileObj, errorObj) {
					S.G.alert(errorObj);
					S.A.FU.div.delay(1000).hide();
				},
				onAllComplete: function() {
					if(!uploaded) {
						S.A.FU.div.delay(1000).hide();
						S.A.W.unblock();
						return false;
					}
					S.A.FU.div.effect('blind', {}, 300, function(){
						S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+S.A.W.vars[win_id].name+'&id='+S.A.W.vars[win_id].id,{
							get: (action?action:'save_images'),
							type: name
						}, function(data) {
							if (data&&!data.halt) {
								if (fn) fn(data, name);
								else if (typeof(data)=='object') {
									if (data.images) {
										S.A.images=data.images;
										eval('window_'+win_id+'.setImages(S.A.images,\''+name+'\');');
										S.A.images=false;
									} else {
										S.A.W.dialog(data);
									}
								}
								else {
									alert(data);	
								}
							}
						});
					});
				},
				onSelect: function() {
					uploaded = 0;
					S.A.FU.div.show('slow');
				}
			});	
		}
		,toggle_images:function(win_id){
			if ($('#a-files_'+win_id).hasClass('a-images_info')) {
				$('#a-files_'+win_id).removeClass('a-images_info');
			} else {
				$('#a-files_'+win_id).addClass('a-images_info');
			}
		}
		,cur_images:{}, sel_images_div:'', images_files:[]
		,selectable_images:function(win_id, name, fn) {
			this.images_files=[];
			var r=[];
			$('#a-'+name+'s_'+win_id).unbind('selectable').selectable({
				start: function(){
					r=[];
				},
				stop: function(){
					S.A.W.sel_images_div='#a-'+name+'s_'+win_id;
					var t=[], s, e, src;
					$('.ui-selected', this).each(function(){
						s=$(this).children(0).children(0).attr('src');
						if (s) {
							r.push(s);e=s.split('/');src=e[e.length-1];t.push(src);S.A.W.images_files.push(src);
						}
					});
					if (r.length) {
						if (fn) fn(r.join('|'));
						/*
						else S.G.confirm(t.join('<br />'),'Are you sure to delete selected images?', function(){
							S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+S.A.W.vars[win_id].name+'&id='+S.A.W.vars[win_id].id, {
								get: 'delete_images',
								arr: t
							}, function(data) {
								$('#a-'+name+'s_'+win_id).find('.ui-selected').hide('slow');
							});
						}, function(){
							$('#a-'+name+'s_'+win_id).find('.ui-selected').removeClass('ui-selected');
						});
						*/
					}
				}
			});
			
			$(window).keydown(function(){
				if(S.E.keyCode==46&&S.A.W.images_files.length) {
					S.A.W.delPhoto(win_id,S.A.W.images_files.join('|'), S.A.W.hideImages);
					S.A.W.images_files=[];
				}
			});
			
			this.context_images(win_id,name);
		}
		,context_images:function(win_id, name) {
			$(document.body).unbind('click').bind('click', function(){
				S.A.X.close();
			});
			$('#a-'+name+'s_'+win_id).find('img').bind('contextmenu', function(e){
				
				S.E.context=true;
				S.A.W.cur_images=this;
				var src = $(S.A.W.cur_images).data('file') ? $(S.A.W.cur_images).data('file') : $(S.A.W.cur_images).attr('src');
				var images = S.A.W.images_files.join('|') || src;
				var c=[
					{
						label: S.A.Lang.preview,
						icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/fileview-preview.png',
						func: 'S.A.I.show($(S.A.W.cur_images).parent());'
					}
					,{
						label: S.A.Lang.set_as_main,
						icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/flag-blue.png',
						func: 'S.A.W.setMainPhoto(\''+win_id+'\',\''+src+'\', $(S.A.W.cur_images));'
					}
					,{
						label: S.A.Lang.del,
						icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit-delete.png',
						func: 'S.A.W.delPhoto(\''+win_id+'\',\''+images+'\', S.A.W.hideImages);'
					}
					,{
						sep: true,
						label: S.A.Lang.properties,
						func: 'S.A.W.info_image(S.A.W.cur_images);'
					}
				];
				S.A.X.context(e, c, $(this));
				S.A.E.hl($(this), true);
				return false;
			}).parent().bind('dblclick', function(){
				S.A.I.show($(this));
			});
		}
		,hideImages:function(){
			if (S.A.W.sel_images_div) {
				$(S.A.W.sel_images_div+' .ui-selected').hide('slow');
			} else {
				$(S.A.W.cur_images).parent().parent().hide('slow');
			}
		}
		,info_image:function(obj){
			var src = $(obj).attr('src');
			S.G.alert(S.A.Lang.thumbnail+': '+src+'<br>'+S.A.Lang.original+': '+src.replace(/\/th([0-9]+)\//,'/th1/')+'');
		}
		,sortable_images:function(win_id, name) {
			$('#a-'+name+'s_'+win_id).unbind('sortable').sortable({
				containment: 'parent',
				items: '>.a-sortable',
				opacity: (name=='file'?null:0.7),
				update: function(e, ui) {
					var arr = '',ul = $(this),i = 1;
					var _j = ul.attr('id').substring(8);
					ul.find('li').each(function(){
						var o=$(this);
						var j=o.attr('id').substring(6);
						arr += (j?j:_j)+'-'+i+'|';
						i++;
					});
					i = 1;
					ul.find('li').each(function(){
						var o=$(this);
						var j=o.attr('id').substring(win_id.length+6);
						if (j!=i) o.attr('id', 'file_'+win_id+'_'+i);
						i++;
					});
					ul.sortable('refresh');
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+S.A.W.vars[win_id].name+'&id='+S.A.W.vars[win_id].id, {
						get: 'sort_images',
						sort: arr
					}, function(data) {
						/*
						var o,h,img,c;
						for (i in data) {
							o=$('#file_'+S.A.W.name_id+'_'+data[i][0]);
							var img=o.find('.show-photo img');
							c = img.attr('class');
							eval('var a='+c+';');
							img.attr('src', S.A.conf.DIR_FILES+S.A.W.name+'/'+S.A.W.id+'/th3/'+data[i][1]);
						}
						*/
					});
				},
				tolerance: 'pointer',
				forceHelperSize: true,
				forcePlaceholderSize: true
			});	
		}
		,editors:[]
		,editor:function(e) {
			
			var settings = {
				height: e.height,
				language: e.lang,
				save_onsavecallback: function(){S.A.E.save(oid)},
				save_oncancelcallback: function(){S.A.E.cancel(oid)},
				plugins: S.A.D.get('plugins_2'),
				theme_advanced_buttons1: (e.simple?S.A.D.get('buttons_1_3'):S.A.D.get('buttons_1_2')),
				theme_advanced_buttons2: (e.simple?S.A.D.get('buttons_2_1'):S.A.D.get('buttons_2_2')),
				theme_advanced_toolbar_location: 'top'
			}
			
			settings = $.extend({},S.A.D.get(S.A.Conf.editor),settings);
			$(e.element).unbind('tinymce').tinymce(settings);
			if (!this.editors[this.win_id]) this.editors[this.win_id]=[];
			this.editors[this.win_id].push(e.element);
			
			/*
			$(tinyMCE.getInstanceById(e.element).getWin()).bind('mousewheel', function(event, delta) {
				return false;
			});
			*/
		}
		,tab:[],tab_index:[],went:[],refresh:false
		,tabs:function(win_id,open) {
			this.tab[win_id]=$('#a-tabs_'+win_id).tabs({
				collapsible: false,
				heightStyle: 'content',
				enable:function(e, ui){
					if (!S.A.W.went[win_id]) S.A.W.tab_index[win_id]=ui.index;
				}
			}).show();
			if (open) {
				var ex=win_id.split('_');
				id=parseInt(ex[ex.length-1]);
				if (!isNaN(id)&&id>0) {
					this.tab[win_id].tabs('option','active',open!==true?1:open);
				}
			}
		}
		,dialog:function(data, url, fn, fn2) {
			if (!url) url = S.A.L.url;
			if (data&&data.text) {
				S.G.message(data.text,data.type,false,data.focus,0,(data.delay?data.delay:1500),function() {
					S.A.W._dialog(url,data,fn,fn2);
				});	
			} else {
				S.A.W._dialog(url,data,fn,fn2);
			}
		}
		,_dialog:function(url,data,fn, fn2){
			if (!data) data={};
			if (!fn&&data.close) {
				this.close('window_'+data.close);
			}
			if (!fn&&(this.reload||data.reload)&&!this.refresh) {
				if (S.A.L.name&&$('#'+S.A.L.name+'-find').val()) {
					S.A.L.find(S.A.L.name,S.A.L.name+'-find','',true,true);
				}
				else if (S.A.L.tab) {
					
					/*
					if (S.A.L.cur_url.indexOf('&tab=')>0) {
						S.A.L.tab.tabs('option','url',S.A.L.tab_index,S.C.HTTP_EXT+S.A.L.cur_url);
					}
					*/
					
					if (S.A.L.name&&S.A.L.tab_name&&$('#'+S.A.L.name+'-find_'+S.A.L.tab_name).val()) {
						
						S.A.L.find(S.A.L.name,S.A.L.name+'-find_'+S.A.L.tab_name,S.A.L.tab_name,true);
					} else {
						S.A.L.get(S.A.L.cur_url,false,S.A.L.tab_name);
					}
				}
				else if (window.opener&&S.A.Conf.open_in_popup&&window.location.href.indexOf('&popup=1')!==-1) {
					if (window.opener.location.hash&&window.opener.location.hash.substring(1,Conf.URL_KEY_ADMIN.length+3)=='?'+Conf.URL_KEY_ADMIN+'=') {
						window.opener.setTimeout("S.A.L.get('"+window.opener.location.hash.substring(1)+(data.url?data.url:'')+"',false,false,false,false,true)",200);
					} else {
						window.opener.location.href=window.opener.location.href;
					}
					window.close();
				}
				else {
					if (window.location.hash) url=window.location.hash.substring(1);
					S.A.L.get(url+(data.url?data.url:''));
				}
			}
			else if (this.refresh && data.reload) {
				if (window.location.hash) {
					url=window.location.hash.substring(1);
					S.A.L.get(url+(data.url?data.url:''));
				} else {
					location.href=location.href;	
				}
			}
			if (data.js) {
				eval(data.js);
			}
			if (fn) fn();
			if (fn2) fn2();
			this.reload = false;
			this.refresh = false;	
		}
		,save: function(url, fm, obj, fn){
			S.A.E.kill(true);
			if ($(obj).hasClass('a-button')) $(obj).button('disable');
			S.G.no_err=false;
			S.A.L.json(url, $(fm).serialize(), function(data){
				S.A.W.dialog(data, false, fn, function(){
					try{$(obj).button('enable')}catch(e){}
				});
			});
		}
		,maximized:function(win_id){
			S.A.W.wins[win_id].win.draggable('destroy');
			eval('if (typeof('+win_id+')==\'object\'&&typeof('+win_id+'.resize)==\'function\'){'+win_id+'.resize(\''+win_id+'\', true);}');
		}
		,minimized:function(win_id){
			S.A.W.wins[win_id].win.draggable(S.A.W.draggable(win_id));
			eval('if (typeof('+win_id+')==\'object\'&&typeof('+win_id+'.resize)==\'function\'){'+win_id+'.resize(\''+win_id+'\', false);}');
		}
		,maximize:function(win_id){
			eval('if (typeof('+win_id+')==\'object\'&&typeof('+win_id+'.maximize)==\'function\'){'+win_id+'.maximize(\''+win_id+'\');}else{S.A.W._maximize(\''+win_id+'\')}');
		}
		,_maximize:function(win_id) {
			var o=this.wins[win_id];
			if ($('.mceEditor',o.win).length || ($('.a-textarea').length && $('.a-textarea').eq(0).attr('id'))) return this._maximize_max(win_id);
			var name_id=win_id.replace('window_','');
			if (o.maximized) {
				o.win.children(0).css({
					width:o.w2,
					height: (o.h2?o.h2:null)
				}, function(){
					o.maximized=false;	
				}).find('.a-window-title').css({
					width:o.w2-150
				});
				o.win.css({
					width:o.w2,
					height: o.h2,
					left: o.l2
				}, function(){
					o.maximized=false;
				});
				o.w1=o.w2;o.h1=o.h2;
				o.maximized=false;
				this.minimized(win_id);
			} else {
				o.h2=parseInt(o.win.children(0).height());
				var w=$(window).width()-100;
				o.win.children(0).css({
					width:w
				});
				$(this).find('.a-window-title').css({
					width:w-150
				});
				o.win.css({
					width:w,
					left: 50
				});
				o.w1=w;o.h1=o.h2;
				o.maximized=true;
				this.maximized(win_id);
			}
		}
		,max_vars:{}
		,_maximize_max:function(win_id) {
			var name_id=win_id.replace('window_','');
			var tabs=$('#a-tabs_'+name_id).length;
			var o=this.wins[win_id];
			if (o.maximized) {
				o.win.children(0).css({
					width:o.w2,
					height: (o.h2?o.h2:null)
				}).find('div.a-window-title').css({
					width:o.w2-150
				});
				o.win.css({
					width:o.w2,
					height: (o.h2?o.h2:null),
					left: o.l2,
					top: o.t1
				});
				for (i in o.editor_height) {
					$('#'+i.replace('_parent','_ifr')).css('height',o.editor_height[i]);
				}
				if (tabs) {
					o.win.find('div.a-tab').css({
						height:o.tab_height
					});
				}
				o.w1=o.w2;o.h1=o.h2;
				o.maximized=false;
				this.minimized(win_id);
			} else {
				if (this.no_close==win_id) return false;
				o.h2=parseInt(o.win.children(0).height());
				var w=$(window).width()-100;
				var h=$(window).height()-40;
				o.win.children(0).css({
					height:h,
					width:w
				});
				o.win.css({
					width:w,
					top:20,
					left: 50
				});
				o.w1=w;o.h1=h;
				$(this).find('div.a-window-title').css({
					width:w-150
				});
				o.editor_height = [];
				if (tabs) {
					o.tab_height=o.win.find('div.a-tab').css('height');
					o.win.find('div.a-tab').css({
						height: h-75
					}).each(function(){
						var e=$(this).find('.mceEditor');
						if (e.length==1) {
							var id=e.attr('id');							
							var e=$('#'+id.replace('_parent','_ifr'));
							o.editor_height[id]=parseInt(e.css('height'));
							var oh = o.h2-o.editor_height[id];
							var eh = h-oh;
							e.css('height',eh);
						}
					});
				} else {
					var e=o.win.find('.mceEditor');
					if (e.length==1) {
						var id=e.attr('id');
						var e=$('#'+id.replace('_parent','_ifr'));
						o.editor_height[id]=parseInt(e.css('height'));
						var oh = o.h2-o.editor_height[id];
						var eh = h-oh;
						e.css('height',eh);
					}
					else if (e.length==0) {
						if ($('.a-textarea').length==1) {
							var e=$('.a-textarea');
							if (e.attr('id')) {
								o.editor_height[e.attr('id')]=parseInt(e.css('height'));
								var oh = o.h2-o.editor_height[e.attr('id')];
								var eh = h-oh;
								e.css('height',eh);
							}
						}
					}
				}
				o.maximized=true;
				this.maximized(win_id);
			}
		}
		,active:function() {
			var m=0;
			for (win_id in this.wins) {
				
			}
		}
		
		,dock:function(win_id, fromDock) {
			if (fromDock||this.wins[win_id].docked) {
				this.wins[win_id].win.show();
				this.wins[win_id].dock.addClass('ui-state-active');
				if (!fromDock) this.wins[win_id].docked=false;
				this.wins[win_id].win.css('z-index',S.A.W.zIndex++);
				this.wins[win_id].z=S.A.W.zIndex;
				this.win_id=win_id;
				this.wins[win_id].docked=false;
			} else {
				this.wins[win_id].dock.removeClass('ui-state-active');
				this.wins[win_id].win.hide();
				this.wins[win_id].docked=true;
			}
			if (this.total_docked()==this.total()) this.scroll(true,win_id);
			else this.scroll(false,win_id);
		}
		
		/*
		,_dock:function(obj,win_id,img_normal,img_active) {
			var o=this.wins[win_id];
			var speed = 400;
			$(obj).children(0).attr('src',(o.docked?img_normal:img_active));
			if (o.docked) {
				o.win.animate({
					top:o.t2+this.dock_height+$(window).scrollTop(),
					left:o.l2
				}, speed, function(){
					o.win.animate({
						top:o.t2
					},speed);
					o.win.children(0).animate({
						height:o.h2,
						width:o.w2
					}, speed, function(){
						$('#'+win_id+' .a-window-top .a-window-buttons').show();	
						$('#'+win_id+' .a-window-top').unbind('click').dblclick(function(){
							S.A.W.maximize(win_id);
						});
						S.A.W.dockSort();
						S.A.W.whileMoving(o.win, false);
					});
				});
				o.docked = false;
				this.block();
			} else {
				$('#a-window_closeall').show('highlight');
				S.A.W.whileMoving(o.win, true);
				var of=$(window).offset();
				o.h2=o.win.children(0).height();
				$('#'+win_id+' .a-window-top .a-window-buttons').hide();
				o.win.animate({
					height: this.dock_height,
					width: 250,
					left: o.l2+o.w2/2-125,
					top: o.t2+this.dock_height
				//	top: o.t2+o.h2/2-27
				}, speed);
				o.win.children(0).animate({
					height: this.dock_height,
					width: 250
				}, speed, function() {
					$('#'+win_id+' .a-window-top').unbind('dblclick');
					var nd=S.A.W.nextDock();
					o.win.animate({
						top: nd[0],
						left: nd[1]
					}, speed,  'swing', function() {
						$('#'+win_id+' .a-window-top').click(function(){
							S.A.W.dock(obj,win_id,img_normal,img_active);
						});
						o.docked = true;
						S.A.W.dockSort();
						S.A.W.scroll(true,win_id);
					});
				});
				this.unblock();
			}
		}
		,dockSort:function(){
			var add = 0, left = 0,all = 0, top = 10, width = parseInt($(window).width()) - 200, row = 1;
			for (id in this.wins){
				if (this.wins[id].docked) {
					row = (Math.floor(all / width));
					if (row==0) top = 10;
					else top = row * 29 + 10;
					left = add;
					if (left > width) {
						add = 0;
						left = 0;
					}
					this.wins[id].win.animate({
						left: left+25,
						top: top
					}, 400);
					add+=250;
					all+=250;
				}
			}
		}
		,nextDock:function(){
			var left=0, top=0, width = $(window).width() - 200, row = 1, top = 10;
			for (id in this.wins){
				if (this.wins[id].docked) {
					left += 250;
				}
			}
			row = (Math.floor(left / width));
			if (row==0) top = 10;
			else top = row * 29 + 10;
			left = left - width * row + 25;
			return [top, left+5];
		}
		*/
		,blocked: false
		,block:function(){
			return;
			/*
			if (this.blocked) return;
			if ($.blockUI) {
				$.blockUI({
					overlayCSS: {
						cursor: 'default',
						backgroundColor: '#000',
						opacity: .3
					},
					fadeIn: 500,
					fadeOut: 200,
					message: false
				});
			}
			this.blocked = true;
			
			*/
		}
		,unblock:function(){
			return;
			/*
			if (!this.blocked) return;
			if ($.unblockUI) $.unblockUI();
			this.blocked = false;
			*/
		}
		,total:function(){
			var total = 0;
			for (id in this.wins){
				total++;
			}
			return total;
		}
		,total_docked:function(){
			var total = 0;
			for (id in this.wins){
				if (this.wins[id].docked) total++;
			}
			return total;
		}
		
		,popup:function(url,w,h,s,b,html){if(s){s='yes'}else{s='no'}if(!w){w=800}if(!h){h=600}var c=[$(window).width(),$(window).height()];var top=(c[1]-h)/2;var left=(c[0]-w)/2;var win=window.open(url,url.toString().replace(/[^A-Za-z0-9]/gi,''),'directories='+(b?'yes':'no')+',status='+(b?'yes':'no')+',location=no,scrollbars='+s+',resizable=yes,width='+w+',height='+h+',top='+top+',left='+left);if(html)win.document.writeln(html);win.focus();return win}
		/*
		,pop:function(win_id,url,post){
			S.A.E.kill(true);
			var w,h;
			if (win_id) {
				w=S.A.W.wins[win_id].win.children(0).width()+17;
				h=S.A.W.wins[win_id].win.children(0).height();
			} else {
				w=800;
				h=500;
			}
			if (url) {
				url = '/?window'+(url.replace(/\?/,'&'));
			} else {
				url=S.A.W.wins[win_id].url;	
			}
			var win=this.popup(url+'&popup=1',w,h,true);
			if (win_id) {
				this.close(win_id);
			}
			return false;
		}
		*/
		,empty:function(data,url){
			if (!data) {
				alert(S.A.Lang.window_data_empty+' '+url);
				if ($.unblockUI) $.unblockUI();
				return true;
			}
			else if (data.substring(0,2)=='{"') {
				var r='';
				eval('r='+data);
				S.G.msg(r, function(){
					if ($.unblockUI) $.unblockUI();	
				});
				alert(r);
				return true;
			}
			return false;
		}
		,go:function(url, win_id, post, obj){
			if (!S.A.W.wins[win_id]) return this.open(url);
			if (post) post._t=S.C._T;
			$.ajax({
				cache: false,
				dataType: 'html',
				url: S.C.HTTP_EXT+'?window'+url.replace(/\?/,'&'),
				data: post,
				type: (post?'post':'get'),
				success: function(data){
					S.A.W.went[win_id]=true;
					S.A.W.wins[win_id].win.find('.a-window-contents').html(data);
					S.A.W.callback(win_id, function(){
						$('#'+win_id+' .a-window-top').dblclick(function(){
							S.A.W.maximize(win_id);
						});							
					});
					if (S.A.W.tab&&S.A.W.tab_index[win_id]){
						S.A.W.tab[win_id].tabs('option','active',S.A.W.tab_index[win_id]);	
					}
					S.A.W.went[win_id]=false;
				}
			});
		}
		,randAnim:function() {
			var effects=['explode','pulsate','scale','shake','size','slide','clip','bounce'];
			var r = this.shuffle(effects);
			return r[0];
		}
		,shuffle:function(o) {
			for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
			return o;
		}
		,open:function(url, post, obj, no_block) {

			/*
			if (S.E.ctrlKey||S.A.Conf.open_in_popup) {
				S.E.ctrlKey=false;
				return this.pop(false,url);
			}
			*/
			url = S.C.HTTP_EXT+'?window'+url.replace(/\?/,'&').replace('&&','&');
			if (obj) {
				if ($(obj).attr('title') && $(obj).attr('title').length>1) {
					t=$(obj).attr('title');	
				}
				else if ($(obj).text().length>1) {
					t=$(obj).html();
				}
				else {
					t=url;	
				}
				$('#a-area .a-sel').removeClass('a-sel');
				$(obj).closest('tr').addClass('a-sel');
			}
			else {
				t=url;
			}
			
			
			
			S.A.Conf.open_inline = false;
			found = false;
			
			if (!S.A.Conf.open_inline) {
				for (win_id in S.A.W.wins) {
					if (S.A.W.wins[win_id].url==url) {
						/*
						S.A.W.wins[win_id].win.css('z-index',S.A.W.zIndex++).find('.a-window-wrapper').hide().show(this.randAnim());
						return false;
						*/
						found = S.A.W.wins[win_id];
					}
				}
				
				if (!found) {
					var total = this.total();
					if (!total&&!no_block) this.block();
					var title='',t='';
					var width=$(window).width();
					var height=$(window).height();
					var data = '<div class="a-abs a-window-wrapper ui-dialog ui-widget ui-widget-content ui-corner-all" style="width:570px"><div class="a-window-top ui-dialog-titlebar ui-widget-header ui-corner-top ui-helper-clearfix"><div class="a-window-title"><span class="ui-dialog-title" style="width:440px;height:19px;overflow:hidden;white-space:nowrap">'+t+'</span></div><div class="a-window-buttons"><a href="javascript:;" class="a-window_min"></a><a href="javascript:;" class="a-window_resize"></a><a href="javascript:;" onclick="S.A.W.temp_win.remove();S.A.W.temp_win=false" class="a-window_close"></a></div></div><div class="a-window-contents ui-dialog-content ui-widget-content"><div style="padding:30px 0;text-align:center"><img src="'+S.C.FTP_EXT+'tpls/img/loading/loading6.gif" /></div></div></div>';
					if (this.temp_win) {
						this.temp_win.remove();
						this.temp_win=false;
					}
					this.temp_win=$('<div>').addClass('a-window a-abs').css({
						position: 'fixed',
						left: width/2-250+total*16,
						top: 200+total*16,
						zIndex:S.A.W.zIndex,
						width:470,
						height:200
					}).show();
					S.G.html(this.temp_win,data);
					this.temp_win.appendTo(S.A.prepend);
				}
			}
			S.G.loader=false;
			if (!post) post = {};
			post._t=S.C._T;
			$.ajax({
				cache: false,
				dataType: 'html',
				url: url,
				type: 'POST',
				data: post,
				success: function(data){
					if (!found && S.A.W.empty(data,url)) {
						if (S.A.W.temp_win) {
							S.A.W.temp_win.remove();
							S.A.W.temp_win=false;
						}
						return false;
					}
					var pos = data.indexOf('<window>');
					if (pos===-1) {
						S.G.alert(data,'Cannot open this window.');
						if (S.A.W.temp_win) {
							S.A.W.temp_win.remove();
							S.A.W.temp_win=false;
						}
						return false;	
					}
					var s=data.substring(pos);
					s=s.substring(8,s.indexOf('</window>')).split('|');
					var name_id=s[0],w2=s[1],h2=600,w1=15,h1=15,win_id='window_'+s[0];
					if (!title) title=s[2];
					data = '<div class="a-abs a-window-wrapper ui-dialog ui-widget ui-widget-content ui-corner-all" id="window_'+name_id+'" style="width:'+w2+'px"><div class="a-window-top ui-dialog-titlebar ui-widget-header ui-corner-top ui-helper-clearfix" id="a-window-top_'+name_id+'"><div class="ui-dialog-title a-window-title" style="width:'+(w2-160)+'px;">'+title+'</div><div class="a-window-buttons">'+(!S.A.Conf.open_inline?'<a href="javascript:;" onclick="S.A.W.dock(\'window_'+name_id+'\');" class="a-window_min"></a><a href="javascript:;" onclick="S.A.W.maximize(\'window_'+name_id+'\')" class="a-window_resize"></a>':'')+'<a href="javascript:;" onclick="S.A.W.close(\'window_'+name_id+'\')" class="a-window_close"></a></div></div><div class="a-window-contents ui-dialog-content ui-widget-content">'+data+'</div></div>';
					
					var found_then = false;
					if (!found) {
						if (win_id in S.A.W.wins) {
							if (!confirm('Are you sure to renew previous window?')) {
								if (S.A.W.temp_win) {
									S.A.W.temp_win.remove();
									S.A.W.temp_win=false;
								}
								return false;	
							}
							found_then = true;
						}
					}
					
					if (S.A.Conf.open_inline) {
						S.G.html('a-area',data);
					}
					else if (found) {
						S.G.html(found.win.empty(),data);
					}
					else if (found_then) {
						if (S.A.W.temp_win) {
							S.A.W.temp_win.remove();
							S.A.W.temp_win=false;
						}
						S.G.html(S.A.W.wins[win_id].win.empty(),data);
					}
					else {
			
						if (obj) var p = $(obj).position(),t1=p.top,l1=p.left;
						else var t1=0,l1=0;
		
						var t2=parseInt(height/2-h2/2-100),l2=parseInt(width/2-w2/2);
						if (t2<40) t2 = 40;
						l2 += total * 16;t2 += total * 16;
						var win = $('<div>').addClass('a-window a-abs').css({
							visibility:'hidden',position:'fixed',top:t1,left:l1,top:t2,left:l2,width:w2,
							zIndex: S.A.W.zIndex++
						});
						S.A.W.win_id = win_id;
						
						S.G.html(win,data);						
						win.appendTo(document.body).mousedown(function(){
							$(this).css('z-index',S.A.W.zIndex++);
							S.A.W.wins[win_id].z=S.A.W.zIndex;
							S.A.W.win_id=win_id;
						});
						
						S.A.W.temp_win.remove();
						S.A.W.temp_win=false;
						if (!$('#a-dock').length) {
							$('<div>').attr('id','a-dock').appendTo(document.body);	
						}
						var dock=$('<div/>').addClass('a-dock_item ui-dialog-titlebar ui-widget-header ui-corner-all ui-state-default ui-state-active').html(title).appendTo($('#a-dock')).mouseover(function(){
							$(this).addClass('ui-state-hover');
						}).mouseout(function(){
							$(this).removeClass('ui-state-hover');
						}).click(function(){
							S.A.W.dock(win_id,true);
						});
						/*
						dock.find('a').click(function(){
							return false;
						});
						*/
	
						setTimeout(function(){
							if (parseInt(height)-10<parseInt(win.children(0).height())) {
								h2=height - 20;
								t2=20;
								win.css({top:t2,height:h2}).children(0).children(0).next().css({height:h2-40, overflowY:'auto'});
							}
						}, 400);
						
						S.A.W.wins[win_id]={url:url,win:win,dock:dock,w1:w1,w2:w2,h1:h1,h2:h2,t1:t1,t2:t2,l1:l1,l2:l2,maximized:false,docked:false,z:S.A.W.zIndex};
						var h2=win.find('.a-window-wrapper').height();
						if (!h2 || h2<50) h2=500;
						h1=h2;
						t1=height/2-h2/2+total * 16;
						S.A.W.wins[win_id].t1=(t1>60?t1-60:60);
						S.A.W.wins[win_id].win.css({
							visibility: '',	top:S.A.W.wins[win_id].t1
						}).draggable(S.A.W.draggable(win_id));
					}
					S.A.W.callback(win_id, function(){
						$('#'+win_id+' .a-window-top').dblclick(function(){
							S.A.W.maximize(win_id);
						});
						var o=S.A.W.wins[win_id].win.find('input:not(.a-date):first');
						if (o.length) {
							setTimeout(function(){
								o.focus().val(o.val()).focus();
							},100);
						}
					});
					
					
					S.G.loader=true;
				}
			})
		}
		,draggable:function(win_id) {
			var ret = {
				containment: 'window',
				handle: '#'+win_id+' .a-window-top .a-window-title',
				stop: function(e, ui){
					var l=ui.position.left;
					var o=S.A.W.wins[win_id];
					o.l2=ui.position.left;
					o.t2=ui.position.top;
					if (!o.docked) S.A.W.whileMoving($(this), false);
				},
				start: function(e, ui){
					S.A.W.whileMoving($(this), true);
				}
			};
			return ret;
		}
		,whileMoving:function(o, start) {
			if (start) {
				o.find('.a-window-contents').css({
					visibility: 'hidden'
				}).parent().css({
					opacity: 0.5
				});	
			} else {
				o.find('.a-window-contents').css({
					visibility: ''
				}).parent().css({
					opacity: ''
				});
			}
		}
		,scrollT:false
		,scroll:function(dis,win_id) {
			
			/*
			$('#'+win_id).bind('mousewheel', function(event, delta) {
				return false;
			});
			*/
			if (dis) {
				/*$(window).unbind('mousewheel');*/
				this.scrollT = false;
				window.onscroll=function() { return null }
			} else {
				/*
				$(window).bind('mousewheel', function(event, delta) {
					return false;
				});
				*/
				
				if (this.scrollT===false) this.scrollT=$(window).scrollTop();
				window.onscroll=function() {window.scrollTo(0,S.A.W.scrollT)}
			}
			
		}
		,no_close:false
		,close:function(win_id) {
			
			//$(document.activeElement).blur();
			//S.A.E.kill(true);
			//$('.a-w-upload_progress').hide();
			if (!win_id || S.E.ctrlKey) {
				S.E.ctrlKey=false;
				for (id in this.wins){
					this.wins[id].win.remove();
					this.wins[id].dock.remove();
					delete this.wins[id];
				}
				this.scroll(true);
				this.unblock();
			} else {
				if (!this.wins[win_id]) {
					win_id = S.A.W.win_id;
					if (!this.wins[win_id]) {
						return false;
					}
				}
				if (this.no_close==win_id) return;
				if (this.wins[win_id].pop) {
					return window.close();
				}
				
				var del=0;
				if (this.editors&&this.editors[win_id]) {
					for (j in this.editors[win_id]) {
						if (this.editors[win_id][j]) {
							$(this.editors[win_id][j]).tinymce().remove();
							del++;
							delete this.editors[win_id][j];
						}
					}
				}
				
				
				
				
				// nasty bug in IE with TinyMCE 3.5.10, sorry! Without that is unable to click to input field when you close the window that has tinymce WYSIWYG initialized.
				// Focus is still in tinymce somewhere, and JS crash when that iframe is removed but focus leaves.
				// tried to create invisible field, but didnt work :(((
				// IE focus() problem [discovered]
				// tinyMCE is not a part of Ajaxel CMS, we cant change that thing.
				// Also there is a tinyMCE problem with cross domains. Why all Ajaxel scripts are working but not tinymce, are they sooo STUPID?! They want to be? LOL!
				// Many google results for that. Check that if don't believe.
				
				
				this.wins[win_id].win.remove();
				this.wins[win_id].dock.remove();
				delete this.wins[win_id];
				this.scroll(true);
				this.unblock();
				
				
				/*
				if (($.browser.msie || ($.browser.mozilla && $.browser.version=='11.0'))&&del) {
					input = $('input:first');
					//input.blur();
				}
				*/
				var input = false;
				// Keep removing focus for crushy result!
				// Bad solution is in so that if you are at the bottom of page and you close window that contains tinemce editor, 
				// focus will be moved to first found input field and you will be scrolled to the top for no reason. Not good. IE only.
				if (input) {
					setTimeout(function(){
						input.blur().val(input.val()).focus();
					},100);
				}
			}
		}
		,alerts:0
		,alert:function(m,c,f,t){
			if (!m) {
				if ($.unblockUI) $.unblockUI();
				$('#a-alert').remove();
				if (S.A.F.addOptFunc) S.A.F.addOptFunc(false, true);
				return false
			}
			var _t=$(window).height()/2-50;
			var _l=$(window).width()/2-100;
			var id = this.alerts++;
			
			var o=$('<div id="a-alert" class="ui-widget ui-widget-content ui-corner-all" style="z-index:15000;position:fixed;top:'+_t+'px;left:'+_l+'px;">'+(t?'<div style="cursor:move;font-weight:bold;color:#555;font-size:12px;padding:1px 4px" id="alert_'+id+'"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/dialog-close.png" style="float:right;cursor:pointer;cursor:hand" alt="'+S.A.Lang.close+'" onclick="S.A.W.alert()" />'+t+'</div>':'')+m+'</div>');
			var ended = false;
			o.appendTo(S.A.prepend).css({
				position: 'fixed',
				display:'none',
				cursor:'default'
			}).resizable({
				minHeight: 50,
				minWidth: 200
			}).show();
			if (!t) {
				o.draggable();	
			} else {
				o.draggable({
					handle: '#alert_'+id
				});
			}
			if ($.blockUI) {
				$.blockUI({
					overlayCSS: {
						cursor: 'default',backgroundColor: '#dedede',opacity: .5
					},
					fadeIn: 500,fadeOut: 200,message: false, bindEvents: false
				});
				S.A.W.blocked=true;
				$('.blockOverlay').click(function(){
					S.A.W.alert();
				});
			}
			if (f){f()}
			ended = true;
			if(!c) {
				o.css({'z-index':10000}).click(function(){
					if(ended) {
						if (!S.A.W.total()) S.A.W.unblock();
						$(this).remove();
					}
				})
			}
		}
	}
	,L:{ // List
		temp:{},vars:{},
		fade: '', // fade speed or false to disable
		name:'', title:'', lang:'', url:'', cur_url:'', get_url:'', sorting:false, hint_obj:false, hint_tim:0, tab:false, tab_cur:0, tab_name:'',tab_load:0, went:false,tab_index:0,ready_was:false,no_hash:false
		,tim:0, tim2:0, prev_search: ''
		,load:function(vars){
			this.name=vars.name;
			this.title=vars.title;
			this.url=vars.url;
			this.vars=vars;
			this.tab_name=vars.tab;
			if ($('#a-btn_'+this.name).length) {
				$('#a-menus button, #a-btn_main').removeClass('ui-state-highlight');
				$('#a-btn_'+this.name).addClass('ui-state-highlight');
			}
		}
		,get:function(url,div,tab,o,el,nohash,fn) {
			if (!url) return false;
			if(!div){div='center-area'}
			if (this.fade) $('#'+div).fadeOut(this.fade);
			if (url.substr(0,1)=='/') url=url.substr(1);
			if (!tab&&!nohash&&!S.A.L.no_hash) {
				window.location.hash=url;
				S.G.U.hash=window.location.hash;
			}
			this.cur_url=url+(tab?'&load':'');
			if (tab) {
				var index=this.tabIndex(tab);
				if (this.tab_index!=index) {
					this.tab.tabs('option','url',index,S.C.HTTP_EXT+this.cur_url);
					this.tab.tabs('option','active',index);
					return false;
				}
			}
			$.ajax({
				type: 'get',
				url: S.C.HTTP_EXT+this.cur_url,
				success: function(data) {
					if (!tab) S.A.L.tab=false;
					if (typeof(data)=='object') return S.G.msg(data);
					S.A.L.sent(data,tab,div,fn,o,el);
					S.A.L.no_hash=false;
				}
			});
		}
		,post:function(url,post,div,tab,fn) {
			if (!url) return false;
			if(!div){div='center-area'}
			if (this.fade) $('#'+div).fadeOut(this.fade);
			this.cur_url=url+(tab?'&load':'');
			$.ajax({
				type: 'post',
				url: S.C.HTTP_EXT+this.cur_url,
				data: S.G.data(post),
				success: function(data) {
					if (typeof(data)=='object') return S.G.msg(data);
					S.A.L.sent(data,tab,div,fn);
				}
			});
		}
		,tabIndex:function(tab) {
			var i=0,index=this.tab_index;
			if (this.tab) {
				this.tab.find('ul.ui-tabs-nav>li>a').each(function(){
					var h=$(this).attr('href');
					if (h&&(h.indexOf('/tab-'+tab+'/')>0||h.indexOf('&tab='+tab+'&')>0)) {
						index=i;
					}
					i++;
				});
			}
			return index;
		}
		,no_scroll:false
		,sent:function(data,tab,div,fn,o,el){
			if (tab&&this.tab&&this.tab_load) {
				S.G.html(this.tab_load.id,data);
				//this.tab.tabs('option','active',this.tab_load.index());
			} else {
				this.went=true;
				$('#'+div).replaceWith($(data));
				S.G.ready(true);
				if (this.tab&&this.tab_cur&&this.tab_cur.index){
					this.tab.tabs('option','active',this.tab_cur.index);
				}
				this.went=false;
				if (o) this.hover(o,el);
			}
			if (!this.no_scroll) {
				$('html, body').animate({
					scrollTop: 0
				}, 'fast', function(){
					
				});
			}
			if(fn) fn();
			this.no_scroll=false;
		}

		/*
		,get_cur_url:function(){
			if (this.cur_url) {
				return this.cur_url;	
			} else {
				return window.location.href.substring(window.location.href.indexOf('?'));
			}
		}
		*/
		,hover:function(o,el) {
			$(el).removeClass('ui-state-active ui-state-hover ui-state-focus').bind('mouseout', function(){
				$(this).removeClass('ui-state-hover');
			});
			$(o).addClass('ui-state-active').unbind('mouseout');
		}
		,tabs:function(id) {
			this.tab_load=0;
			this.tab_cur=0;

			this.tab=$('#'+id).tabs({
				collapsible: false,
				cache: false,
				//fx: {opacity:'toggle', speed:100},
				activate: function(e, ui){
					S.G.U.hash=window.location.hash;
					var hash=window.location.hash.substring(1);
					if (hash.length==1) hash='';
					if (hash.indexOf('#')!==-1) hash=hash.substring(0,hash.indexOf('#'));
					window.location.hash=hash+'#'+ui.newTab.index();
					S.A.L.tab_load=ui.newPanel;
					S.A.L.tab_load.id=S.A.L.tab_load.attr('id');
					S.A.L.tab_index=parseInt(ui.newTab.index());
					S.A.L.cur_url=hash;
					
					S.A.L.cur_url=ui.newTab.find('a').attr('href');

					
				},
				load: function(e, ui){
					S.G.U.hash=window.location.hash;
					S.A.L.tab_load=ui.panel;
					S.A.L.tab_load.id=S.A.L.tab_load.attr('id');
					S.A.L.tab_index=parseInt(ui.tab.index());
					S.A.L.cur_url=ui.tab.find('a').attr('href');
					if (S.A.L.cur_url && S.A.L.cur_url.substring(0,S.C.HTTP_EXT.length)==S.C.HTTP_EXT) {
						S.A.L.cur_url=S.A.L.cur_url.substring(S.C.HTTP_EXT.length);
					}
				},
				stop:function() {
					S.G.U.hash=window.location.hash;
				},
				show: function(e, ui){
					/*
					if (!S.A.L.went) S.A.L.tab_cur=ui;
					if (S.A.L.tab_load.index()!==S.A.L.tab_cur.index()) {
						S.A.L.tab_load=0;	
					}
					S.A.L.tab_index=ui.index();
					*/
				},
				beforeActivate:function(e, ui) {
					/*
					alert(ui.newTab.html());
					alert(ui.newTab.prop('id'));
					S.A.L.tab_index.id=ui.newTab.prop('id');
					*/
				},
				beforeLoad:function(e, ui) {
					//ui.ajaxSettings 
				}
			}).show();
			
			S.G.U.hash=window.location.hash;
			var hash=window.location.hash.substring(1);
			if (hash.indexOf('#')!==-1) hash=hash.substring(hash.indexOf('#')+1);
			hash = parseInt(hash);
			if (!isNaN(hash) && hash>0 && this.tab_index!=hash) {
				/*if (this.cur_url) this.tab.tabs('url',hash,S.C.HTTP_EXT+this.cur_url);*/
				this.tab.tabs('option','active',hash);
			}
		}
		,tabgo:function(url, index, id) {
			if (id) {
				var t=$(id);	
			} else {
				var t=this.tab;
			}
			this.tab.tabs('option','active',index);
		}
		,json:function(url, post, func) {
			url = S.C.HTTP_EXT+'?&json'+url.replace(/\?/,'&');
			if (post) post._t=S.C._T;
			$.ajax({
				cache: false,
				dataType: 'json',
				url: url,
				type: (post?'POST':'GET'),
				data: (post?post:null),
				success: function(data) {
					if (func) {
						if (func===true) {
							S.A.W.dialog(data);
						} else {
							func(data);
						}
					}
				}
			});	
		}
		,hint:function(obj, descr, hide){
			if (hide) {
				$('table.a-hint').remove();
				return false;
			}
			clearTimeout(this.hint_tim);
			if (this.sorting) return false;
			this.hint_tim = setTimeout(function(){
				var o = $(obj),p=o.offset();
				if (!p.top) return false;
				var h = '<div class="a-hint_caption">'+o.text()+'</div><div class="a-hint_descr">'+descr+'</div>';
				S.A.L.hint_obj=$('<div>').css({
					width: 300,
					position:'absolute',
					top: p.top+20,
					left: p.left+20,
					display:'none'
				}).append($('<table>').addClass('a-hint').append($('<tr>')).append($('<td>').addClass('a-hint').html(h))).appendTo(S.A.prepend).slideDown('fast').mouseover(function(){
					$(this).fadeOut();
				});
			}, 400);
		}
		,nav:function(url, obj) {
			window.open(url);
		}
		,ready:function(html, run) {

			/*$('.a-list').find('.a-search').addClass('ui-widget ui-widget-content');*/
			if (run&&!this.ready_was) {
				this.ready_one();
				this.ready_was = true;	
			}
			if (!run) {
				this.prev_search = '';
				if (this.name) {
					if (html) S.G.html(this.name+'-content',html);
					if (!this.no_scroll) {
						S.A.L.tim2=setTimeout(function(){
							var o=$('#'+S.A.L.name+'-find');
							o.focus();o.val(o.val());o.focus();
						}, 200);
					}
				}
				if ($.fn.button) {
					$('.a-button').button().css('visibility','');
				}
				S.A.I.load();
				if (this.title) $(document).attr('title',this.title);
				this.datepicker();
				/*
				if ($('#a-buttons_'+this.name).length) {
					this.float_save($('#a-buttons_'+this.name));
				}
				*/
			}
			if (S.A.L.tab) {
				if (S.A.L.title) {
					$('#a-area .a-h1 .a-l').html(S.A.L.vars.caption);	
				}
			}
			
			/*
			if (this.name) {
				var b=$('#a-buttons_'+S.A.L.name);
				
				b.css({
					position:'fixed',
					zIndex:10,
					width: b.width(),
					top: $(window).height()-40
				});
			}
			*/
		}
		,float_save:function(o){
			var of=o.offset(),h=$(window).height();
			if (of.top>h) {
				o.css({
					position: 'fixed',
					bottom: 0,
					width: o.parent().width()-10
				});	
			}
			
		}
		,datepicker:function(){
			$('input.a-datepicker').unbind('datepicker').datepicker({
				numberOfMonths: 3,
				dateFormat: 'dd/mm/yy',
				onSelect: function(){
					if ($(this).hasClass('a-datepicker_from')) {
						if (!$(this).val()) $(this).next().val('');
						var date = $(this).datepicker("getDate");
						$(this).next().datepicker('option', 'minDate', date).trigger('focus');
					}
					else if ($(this).hasClass('a-datepicker_to')) {

					}
				}
			}).css({cursor:'pointer'});	
		}
		,isScrolledIntoView:function(e) {
			var t = $(window).scrollTop();
			var b = t + $(window).height();
			var et = e.offset().top;
			var eb = et + e.height();
			return eb >= t && et <= b && eb <= b && et >= t;
		}
		,ready_one:function(){
			var h = '<table><tr>';
			h += '<td><button type="button" onclick="S.A.L.website(this)" class="a-button a-button_x">'+S.A.Lang.website+'</button></td>';
			if (S.A.Conf.crm){
				h += '<td><button type="button" onclick="S.A.L.website(this,\'?crm\')" class="a-button a-button_x">CRM</button></td>';
			}
			h += '</tr></table>';
			this.switcher(h);
		}
		,switcher:function(h,noop){
			
			var s=$('<div>').attr('id','a-switcher').addClass('a-switcher').html(h).css({
				position: 'fixed',
				zIndex: 10000,
				bottom: 4,
				left: 5
			}).appendTo(document.body);
			if (!noop) {
				s.css({opacity:0.4}).mouseover(function(){
					$(this).stop().animate({
						opacity: 1
					});
				}).mouseout(function(){
					$(this).stop().animate({
						opacity: 0.4
					});
				});
			}
			if ($.fn.button) {
				$('.a-button',s).button().addClass('ui-state-default');	
			}
		}
		,website:function(obj,name){
			window.location.replace(S.C.HTTP_EXT+(name?name:''));
			$(obj).button('disable');
			$(document.body).fadeOut();
		}
		,ui:function(v, arr) {
			var s=arr[v];
			if (!s) return false;
			$('#s-title_admin').html(s[1]);
			$('#s-theme_admin').attr('href',S.C.FTP_EXT+'tpls/css/ui/'+s[0]+'/'+S.A.Conf.jquery_css);
		}
		,act:function(url, a, obj, img) {
			var o=$(obj).children(0);
			var p=o.attr('alt');
			S.A.L.json(url, {
				get: 'action',
				a: 'act',
				id: a.id
			},function(data){
				if (o.attr('alt')) {
					data.reload = false;
					o.attr('alt',o.attr('src'));
					o.attr('src',p);
				} else data.reload=true;
				S.A.W.dialog(data);
			});
		}
		,excel:function(sql,table,msg,db_name) {
			$('<div>').html('Are you sure to export '+db_name+'.'+table+'?<br /><br />'+(msg.length>30?msg:sql)).addClass('g-alert').appendTo(S.A.prepend).dialog({
				title: 'Export SQL query',
				draggable: true,
				resizable: false,
				width: 410,
				zIndex: 4000,
				modal: true,
				height: 'auto',
				close: function(ev, ui) {
					$(this).remove()
				},
				buttons: {
					'Cancel': function() {
						$(this).dialog('close');
					},
					'XLS': function() {
						S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&p='+S.A.L.vars.page,{
							get:'action',
							a:'excel',
							db_name:db_name,
							sql:sql,
							table:table
						},true);
						$(this).remove();
					},
					'CSV': function() {
						S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&p='+S.A.L.vars.page,{
							get:'action',
							a:'csv',
							db_name:db_name,
							sql:sql,
							table:table
						},true);
						$(this).remove();
					},
					'SQL': function() {
						S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&p='+S.A.L.vars.page,{
							get:'action',
							a:'sql',
							db_name:db_name,
							sql:sql,
							table:table
						},true);
						$(this).remove();
					}
				}
			});	
			
			/*
			S.G.confirm('Are you sure to export:<br /><br />'+msg,'Exporting to excel',function(){
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&p='+S.A.L.vars.page,{
					get:'action',
					a:'excel',
					sql:sql,
					table:table
				},true);
			}, function() {
				
			});
			*/
		}
		,autocomplete:function(o,name,table,fn) {
			o.autocomplete({
				source: function(request, response) {
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global', {
						get:'action',
						a:name,
						table:table,
						term: request.term
					}, function(data) {
						if (!data.length) {
							o.val('');
							$('#'+S.A.L.name+'-'+name).val('');
							$('#'+S.A.L.name+'-'+name+'_id').val('');
							response();
						} else {
							response(data);	
						}
					});
				}
				,position:{ 
					my: "left top",
					at: "left bottom",
					collision: "none" 
				} 
				,focus: function(event, ui) {
					o.val(ui.item.label);
					$('#'+S.A.L.name+'-'+name+'_id').val(ui.item.value);
					return false;			
				}
				,select: function(event, ui) {
					o.val(ui.item.label);
					$('#'+S.A.L.name+'-'+name+'_id').val(ui.item.value);
					if (fn) fn(ui.item.value);
					return false;
				}
			});
			o.keyup(function(){
				if (!$(this).val()) {
					o.val('');
					$('#'+S.A.L.name+'-'+name+'_id').val('');
				}
			});	
		}
		,user:function(o, table, fn) {
			this.autocomplete(o,'user_search',table,function(value){
				if (fn) fn(value);
				else $('#'+S.A.L.name+'-search').submit();
				return false;
			});
		}
		,find:function(ku,n,tab,fast,samePage){
			clearTimeout(this.tim);
			clearTimeout(this.tim2);
			this.tab_name=tab;
			var v=$('#'+n),time=1000;
			if (fast) time=0;
			this.tim=setTimeout(function(){
				var f = v.val();
				var ao = $('#'+n.replace('find','ao_search')).val();
				var df = $('#'+n.replace('find','df_search')).val();
				var dt = $('#'+n.replace('find','dt_search')).val();
				
				if (v.length&&!fast&&!f&&!S.A.L.prev_search) {
					v.focus();
					o.disabled=false;
					return false;
				}
				if (v.length&&!fast&&f==S.A.L.prev_search) {
					clearTimeout(S.A.L.tim);
					return false;
				}
				if (!S.A.L.tab) tab=false;
				var post = S.A.P.data($('#'+S.A.L.name+'-search'));
				post = $.extend(post,{f:f,ao:ao,df:df,dt:dt});
				S.A.L.post(S.A.L.url+(tab?'&tab='+tab:'')+(!samePage?'&p=0':''),post,false,tab,function(){
					if (v.length) {
						S.A.L.tim2=setTimeout(function(){
							S.A.L.prev_search=f;
							if (ku) {
								var o=$('#'+ku+'-find'+(tab?'_'+tab:''));
								o.focus();o.val(o.val());o.focus();
							}
							else v.focus();v.val(v.val());v.focus();
						}, 200);
					}
				});
			}, time);
			return false;
		}
		/*
		,google_type:'web'
		,google_find:function(name, div, button, type) {
			if (!type) type = this.google_type;
			this.google_type=type;
			if (button) {
				S.A.L.prev_search=''
			} else {
				
			}
			var o=$('#'+name+'-find');
			var val = o.val();
			if (!val) {
				v.focus();
				o.disabled=false;
				return false;
			}
			if (val==S.A.L.prev_search) {
				return false;
			}
			S.A.L.prev_search=val;
			$('#a-'+div+'_hide').hide();
			this.google_search(false, val, div, type);
			return false;
		}
		,google_search:function(settings, val, div, type){
			var config = {
				siteURL		: document.domain=='alx'?'ajaxel.com':document.domain,
				searchSite	: true,
				append		: false,
				perPage		: 8,
				page		: 0,
				attempts	: 4,
				type		: type ? type : 'web'
			}
			if ($('#a-'+div+'_all').is(':checked')) config.siteURL = '';
			settings = $.extend({},config,settings);
			settings.term = settings.term || val;
			if(settings.searchSite){
				settings.term = settings.siteURL ? 'site:'+settings.siteURL+' '+settings.term : settings.term;
			}
			var url = 'https://ajax.googleapis.com/ajax/services/search/'+settings.type+'?v=1.0&callback=?';
			var rd = $('#a-'+div+'_show');
			$('html, body').stop();
			$.getJSON(url,{q:settings.term,rsz:settings.perPage,start:settings.page*settings.perPage},function(r){
				if (!r || !r.responseData) {
					$('#a-'+div+'_hide').show();
					return alert('Google returned no more results');
				}
				$('#'+div+'_more').parent().remove();
				var results = r.responseData.results;
				if(results.length){
					var pc = $('<div>',{className:'a-google_find'});
					for(var i=0;i<results.length;i++){
						pc.append(S.A.L.google_result(results[i],settings) + '');
					}
					if(!settings.append){
						rd.show().empty();
					}
					pc.append('<div class="a-google_clear"></div>').appendTo(rd);
					var cursor = r.responseData.cursor;
					if (cursor.estimatedResultCount > (settings.page+1)*settings.perPage){
						$('<button>',{id:div+'_more'}).addClass('a-google_more ui-button').button().html('More...').appendTo($('<div class="a-google_more">').appendTo(rd)).click(function(){
							S.A.L.google_search({append:true,page:settings.page+1}, val, div, type);
							$(this).hide();
						});
						if (settings.page<=settings.attempts-1) {
							S.A.L.google_search({append:true,page:settings.page+1}, val, div, type);
						}
					}
					S.A.I.load();
				} else {
					rd.show().empty();
					$('<div>',{className:'a-not_found',html:'No results were found!'}).hide().appendTo(rd).show();	
				}
			});
		}
		,google_video:function(t, url, title) {
			var w=800, h=600;
			if (t!='YouTube'&&url.indexOf('www.youtube.com/watch')) t='YouTube';
			switch (t) {
				case 'YouTube':
					$('#a-youtube').remove();
					$(function () { 
						setTimeout(function () { 
							if (typeof __flash__removeCallback != "undefined") { 
								__flash__removeCallback = __flash__removeCallback__replace; 
							} else { 
								setTimeout(arguments.callee, 50); 
							} 
						}, 50); 
					});
					function __flash__removeCallback__replace(instance, name) { 
						if(instance != null) instance[name] = null; 
					}
					
					var code = url.substr(url.indexOf('%3D')+3);
					code = code.substr(0,code.indexOf('&'));
					if (!code) code = url.substr(url.indexOf('?v=')+3);
					var html = '<div class="ui-widget ui-widget-header" style="padding:2px 4px;border-bottom:1px solid #555;cursor:move;font-weight:bold;font-size:12px;"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/dialog-close.png" style="float:right;cursor:pointer;cursor:hand" alt="'+S.A.Lang.close+'" onclick="$(\'#a-youtube\').remove();" />'+S.A.P.js2(title)+'</div><div><iframe width="100%" height="'+h+'" src="http://www.youtube.com/embed/'+code+'/?autoplay=1&lang='+(S.A.Conf.lang?S.A.Conf.lang:'en')+'" frameborder="0"></iframe></div>';
					$('<div>').attr('id','a-youtube').addClass('a-alert ui-widget ui-widget-content ui-corner-all').css({
						width:w,height:h,overflow:'hidden',zIndex:300,
						top: Math.abs($(window).height()/2-h/2)-40,left: Math.abs($(window).width()/2-w/2),position: 'fixed'
					}).html(html).draggable({
						drag: function(){
							$(this).css({opacity:0.2});$(this).find('iframe').hide();	
						},
						stop: function(){
							$(this).css({opacity:1});$(this).find('iframe').show();
						}
					}).appendTo(S.A.prepend);
					return false;
				break;
				default:
					window.open(url);
				break;
			}
		}
		,google_result:function(r,settings){
			var ret = '';
			switch(r.GsearchResultClass){
				case 'GimageSearch':
					html = '<title>['+r.width+'x'+r.height+'] '+r.originalContextUrl+'</title><body style=&quot;text-align:center;padding:0;margin:0;background:#3A3A3A url(/'+S.C.FTP_EXT+'tpls/img/loading/loading49.gif) center no-repeat;width:'+r.width+'px;height:'+r.height+'px;&quot;><img src=&quot;'+r.unescapedUrl+'&quot; /></body>';
					var w=$(window).width();
					var h=$(window).height();

					ret =
						'<div class="a-google_result a-image_result"><div>'+
						'<a href="'+r.unescapedUrl+'" class="a-thumb" rel="google_images" title="'+r.titleNoFormatting+'" class="a-google_pic" style="width:'+r.tbWidth+'px;height:'+r.tbHeight+'px;">'+
						'<img src="'+r.tbUrl+'" width="'+r.tbWidth+'" height="'+r.tbHeight+'" title="'+r.width+'x'+r.height+'" /></a></div>'+
						'<a href="'+r.originalContextUrl+'" target="_blank" class="a-google_publisher">'+r.visibleUrl+'</a>'+
						'</div>'
					;
				break;
				case 'GvideoSearch':
					ret =
						'<div class="a-google_result a-video_result"><div>'+
						'<a target="_blank" href="'+r.url+'" onclick="S.A.L.google_video(\''+S.A.P.js(r.videoType)+'\', \''+S.A.P.js(r.url)+'\', \''+S.A.P.js(r.title)+'\'); return false;" title="'+S.A.P.js2(r.titleNoFormatting)+'" class="a-google_pic">'+
						'<img src="'+r.tbUrl+'" width="100%" /></a>'+
						'<div title="'+r.rating+'" style="width:'+(22.2*r.rating)+'px;height:3px;background:#CCCC00"></div></div>'+
						'<span style="font-size:11px;font-family:Arial" class="a-date">'+S.A.P.secondsToTime(r.duration)+'</span> <a href="'+r.url+'" target="_blank" class="a-google_publisher">'+r.title+'</a>'+
						'</div>'
					;
				break;
				case 'GnewsSearch':
					ret =
						'<div class="a-google_result a-web_result">'+
						'<a href="'+r.unescapedUrl+'" target="_blank" class="a-google_title">'+r.title+'</a>'+
						'<div class="a-google_content">'+r.content+'</div>'+
						'<div class="a-google_publisher"><a href="'+r.unescapedUrl+'" target="_blank" class="a-google_publisher">'+r.publisher+'</a></div>'+
						'</div>'
					;
				break;
				default:
					ret =
						'<div class="a-google_result a-web_result">'+
						'<a href="'+r.unescapedUrl+'" target="_blank" class="a-google_title">'+r.title+'</a>'+
						(r.content?'<div class="a-google_content">'+r.content+'</div>':'')+
						(!settings.siteURL?'<div class="a-google_publisher"><a href="'+r.unescapedUrl+'" class="a-google_publisher" target="_blank">'+r.visibleUrl+'</a> (<a href="'+r.cacheUrl+'" target="_blank">cache</a>)</div>':'')+
						'</div>'
					;
				break;
			}
			return ret;
		}
		*/
		,global:function(action, post, fn) {
			S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a='+action, post, function(data){
				if (fn) fn(data);
				else S.A.W.dialog(data, '', fn);
			});
		}
		,chart_data:{}
		,chart_win:function(type,id,params) {
			this.chart(type, id, function(h, data) {
				S.A.L.chart_data.obj=$('<div>').appendTo(S.A.prepend).html(data.filter+h).dialog({
					title: data.title,
					width: 700,
					resizable: false
				});
			}, params);
			S.A.L.datepicker();
		}
		,chart_build:function(obj) {
			if (S.A.L.chart_data.obj) S.A.L.chart_data.obj.remove();
			this.chart_win('c_cnt',escape($('#'+obj).val()));
		}
		,chart_date:function(v) {
			this.chart(this.chart_data.type, this.chart_data.id, function(h, data) {
				S.A.L.chart_data.obj.html(data.filter+h);
			}, $.extend(this.chart_data.params,{date_type:v}));
		}
		,chart_type:function(v) {
			this.chart(this.chart_data.type, this.chart_data.id, function(h, data) {
				S.A.L.chart_data.obj.html(data.filter+h);
			}, $.extend(this.chart_data.params,{t:v}));
		}
		,chart_main:function(type, id){
			this.chart(type, id, function(h, data) {
				S.A.L.chart_data.obj=$('#a-main_chart_div').html(data.filter+h);
			});
		}
		,chart:function(type, id, fn, params){
			this.chart_data.type=type;
			this.chart_data.id=id;
			this.chart_data.params=params;
			this.global('main_chart',$.extend({t:type,id:id},params),function(data){
				if (!data.swf) data.swf='Line';
				var swf = S.C.FTP_EXT+'tpls/js/charts/'+data.swf+'.swf';
				var width = data.width?data.width:'100%';
				var height = data.height?data.height:290;
				var h = 30;
				var id = 'a-main_chart';
				var debug = data.debug ? 1 : 0;
				if (!data.params) data.params = 'caption="Web Statistics" bgColor="406181, 6DA5DB" bgAlpha="100" baseFontColor="000000" toolTipFontColor="222222" canvasBgAlpha="0" canvasBorderColor="FFFFFF" divLineColor="f9f9f9" divLineAlpha="20" lineColor="BBDA00" anchorRadius="3" anchorBgColor="BBDA00" anchorBorderColor="FFFFFF" anchorBorderThickness="0" showValues="0" showAlternateHGridColor="1" alternateHGridColor="406181" shadowAlpha="40" numberSuffix="" toolTipBgColor="406181" toolTipBorderColor="406181" alternateHGridAlpha="5"';				
				data.params += 'hoverCapBorderColor="114B78" hoverCapBgColor="E7EFF6" plotGradientColor="DCE6F9" plotFillAngle="90" plotFillColor="1D8BD1" plotfillalpha="80"';
				var xml = '<chart '+data.params+'>'+data.xml+'</chart>';
				var vars = '&chartWidth='+width+'&chartHeight='+height+'&debugMode='+debug;
				if (!xml) vars += '&dataURL='+data.url
				else vars += '&dataXML='+escape(xml);
				var h = '<div style="overflow:hidden;height:'+(height-h)+'px"><div style="position:relative;top:-'+h+'px"><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="'+width+'" height="'+height+'" id="a-main_chart"><param name="allowScriptAccess" value="always" /><param name="movie" value="'+swf+'"/><param name="FlashVars" value="'+vars+'" /><param name="quality" value="high" /><embed src="'+swf+'" FlashVars="'+vars+'" quality="high" width="'+width+'" height="'+height+'" name="'+id+'" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object></div></div>';
				if (fn) return fn(h, data); else S.G.html('a-main_chart_div',h);
			});
		}
		,del:function(a, obj, conf, fn) {
			if (conf) {
				if (conf===1) {
					S.G.confirm('Are you sure to delete'+((a&&a.title)?' '+a.title:'')+'?','Please confirm', function(){
						S.A.L.json(S.A.L.url, $.extend(a,{
							'get': 'action',
							'a': 'delete',
							'id': a.id,
							'tab': a.tab
						}),function(data){
							if (fn) if (fn===true||fn===1) $(obj).parent().parent().fadeOut(); else fn();
							else S.A.W.dialog(data);
						});	
					});	
				} else {
					if (confirm('Are you sure to delete'+((a&&a.title)?' '+a.title:'')+'?')) {
						S.A.L.json(S.A.L.url, $.extend(a,{
							'get': 'action',
							'a': 'delete',
							'id': a.id,
							'tab': a.tab
						}),function(data){
							if (fn) if (fn===true||fn===1) $(obj).parent().parent().fadeOut(); else fn();
							else S.A.W.dialog(data);
						});	
					}
				}
			} else {
				if (fn) if (fn===true||fn===1) $(obj).parent().parent().remove(); else fn();
				S.A.L.json(S.A.L.url, {
					'get': 'action',
					'a': 'delete',
					'id': a.id,
					'tab': a.tab
				}, function(data){
					if (!fn) S.A.W.dialog(data);	
				});
			}
		}
		,del_file:function(file, file_path, url, obj, folder, fn) {
			if (confirm('Are you sure to delete this '+(folder?'folder /'+file+'?\n\nWarning!!! All sub folders and files inside this folder will be deleted!':'file /'+file+'?')+'')) {
				S.A.L.json(S.A.L.url+url, {
					'get': 'action',
					'a': 'delete',
					'file_path': file_path,
					'file': file
				},function(data){
					S.A.W.dialog(data);
					if (data.ok) {
						$(obj).parent().parent().fadeOut(function(){
							$(this).remove();	
						});
					}
				});
			}
		}
		,rename_file_obj:false,rename_file_obj_to:false
		,rename_file:function(file, file_path, url, obj_to, obj, folder, thumb_look) {
			if (!obj) {
				obj=this.rename_file_obj;
				obj_to=this.rename_file_obj_to;
				var new_file = $('#'+obj_to.attr('id')+'_input').val(),name;
				S.A.L.json(S.A.L.url+url, {
					'get': 'action',
					'a': 'rename',
					'file_path': file_path,
					'old_file': file,
					'new_file': new_file
				},function(data){
					if (data.ok) {
						var a=data.a,i=0;
						var folder_url = '', h = '';
						eval(data.row_to_eval);
						$('#'+obj_to.attr('id')+'_table').parent().parent().html(h);
					} else {
						S.A.W.dialog(data);
					}
					$('#'+id+'_input').blur();
				});
				return;
			}

			this.rename_file_close();
			this.rename_file_obj = obj;
			this.rename_file_obj_to = obj_to;
			var id=obj_to.attr('id');
			if (folder) {
				name=file;
			} else {
				name=file.split('.').slice(0,-1).join('.');
			}
			if (!name) name = file;
			var html = '<table cellspacing="0" cellpadding="0" class="a-l a-action_buttons"><tr><td><input type="text" id="'+id+'_input" class="a-input" value="'+name+'" style="width:'+(thumb_look?80:220)+'px" onkeyup="if (S.E.keyCode==13){S.A.L.rename_file(\''+file+'\', \''+file_path+'\', \''+url+'\', false, false, \''+folder+'\'); this.blur();return false}" /></td><td><a href="javascript:;" onclick="S.A.L.rename_file(\''+file+'\', \''+file_path+'\', \''+url+'\', false, false, \''+folder+'\')"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/document-save.png" alt="Save" /></a></td><td><a href="javascript:;" onclick="S.A.L.rename_file_close();"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/mail-delete.png" alt="'+S.A.Lang.save+'" /></a></td></tr></table>';
			obj_to.hide().parent().append($('<div>').attr('id',id+'_table').attr('class','a-rename_file').html(html));
			$('#'+id+'_input').focus().val($('#'+id+'_input').val()).focus();
		}
		,rename_file_close:function() {
			$('.a-rename_file').prev().show().next().remove();
		}
		,del_stat:function(id, obj) {
			if (confirm(S.A.Lang.visit_dc)) {
				this.json('?'+Conf.URL_KEY_ADMIN+'=stats&tab=who&a=delete', {id:id}, function(){
					$(obj).parent().parent().remove();
				});
			}
		}
		,del_block:function(id, ip, obj, double) {
			if (confirm(S.A.Lang.ip_dc+': '+ip+'?')) {
				this.json('?'+Conf.URL_KEY_ADMIN+'=stats&tab=db&a=delete_ip', {id:id}, function(){
					if (double) {
						$(obj).parent().parent().next().remove();
					}
					$(obj).parent().parent().remove();
				});
			}
		}
		,del_db:function(t,k,obj) {
			if (confirm(S.A.Lang.db_dc+': '+k+'?')) {
				this.json('?'+Conf.URL_KEY_ADMIN+'=stats&tab=db&a=delete', {k:k,t:t}, function(){
					$(obj).parent().parent().remove();
				});
			}
		}
		,block:function(ip, obj, tab) {
			if (confirm(S.A.Lang.ip_bc+': '+ip+'?')) {
				this.json('?'+Conf.URL_KEY_ADMIN+'=stats&tab=who&a=block_toggle', {ip:ip}, function(){
					if (tab&&S.A.L.tab) S.A.L.tab.tabs('load',tab);
					else S.A.L.get(S.A.L.url);
				});
			}
		}
		,unblock:function(ip, obj, tab) {
			if (confirm(S.A.Lang.ip_uc+': '+ip+'?')) {
				this.json('?'+Conf.URL_KEY_ADMIN+'=stats&tab=who&a=block_toggle', {ip:ip}, function(){
					if (tab&&S.A.L.tab) S.A.L.tab.tabs('load',tab);
					else S.A.L.get(S.A.L.url);
				});
			}
		}
		,sdel:function(el,o){
			if (o) {
				if (o.checked) {
					$(el).find('td').not('.a-c').attr('disabled','disabled');
				} else {
					$(el).find('td').not('.a-c').removeAttr('disabled');
				}
			} else {
				if ($(el).attr('disabled')=='disabled') {
					$(el).removeAttr('disabled');
				} else {
					$(el).attr('disabled','disabled');
				}
			}
		}
		,show:function(el){
			var d=$(el).children(0).children(0);
			var o=d.children(0).children(0);
			if (d.is(':hidden')) {
				d.show();
			} else {
				d.hide();
			}
		}
		,expanded: false
		,expand:function(el,obj,we,wc) {
			$(obj).html('');
			if (this.expanded) {
				if ($(el+':visible').length) {
					$(el+':visible').hide();
					S.A.L.expanded = false;
					$(obj).html(we);
					/*					
					$(el+':visible').hide('blind', {}, 200, function(){
						S.A.L.expanded = false;
						$(obj).html(we);														  
					});
					*/
				} else {
					S.A.L.expanded = false;
					$(obj).html(we);		
				}
			} else {
				if ($(el+':hidden').length) {
					$(el+':hidden').show();
					S.A.L.expanded = true;
					$(obj).html(wc);
					/*
					$(el+':hidden').show('blind', {}, 400, function(){
						S.A.L.expanded = true;
						$(obj).html(wc);														  
					});
					*/
				} else {
					S.A.L.expanded = true;
					$(obj).html(wc);
				}
			}
		}
		,chk:function(btns, fn, fn2){
			$('#'+this.name+'-content .a_chk_'+this.name+', #a_chk_'+this.name+'').click(function(){
				if (btns) {
					var i=$('#'+S.A.L.name+'-content .a_chk_'+S.A.L.name+':checked, #a_chk_'+S.A.L.name+':checked').length;
					if (i>0) {
						btns.slideDown('fast');
						if (fn) fn();
					} else {
						btns.slideUp();
						if (fn) fn2();
					}
				}
				if (this.checked) {
					S.A.L.blink($(this).parent().parent().addClass('ui-selected'),10,'ui-selected');
				} else {
					$(this).parent().parent().removeClass('ui-selected');	
				}
			});	
		}
		,selectable:function(cl, fn) {
			if (!cl) cl = 'a_chk_'+this.name;
			$('#'+this.name+'-content table.a-list-one>tbody.a-tbody').selectable({
				filter: 'tr',
				distance: 5,
				stop: function(e, ui) {
					i = 0;
					$('.ui-selected', this).each(function(){
						var c=$(this).find('.'+cl);
						if (S.E.shiftKey) {
							$(this).removeClass('ui-selected');
							c.removeAttr('checked');
						} else {
							$(this).addClass('ui-selected');
							c.attr('checked','checked');
						}
						/*
						var o=$(this);
						setTimeout(function(){
							o.removeClass('ui-selected');
						},i*10);
						i++;
						*/
					});
					if (!S.E.shiftKey) {
						S.A.L.blink($('.ui-selected', this),20,'ui-selected');
					}
					
					
					if (fn) {
						fn($('.ui-selected', this).length>0 ? 1 : 0);
					}
					setTimeout(function(){S.E.shiftKey=false},100);
				}
			});	
		}
		,blink:function(o,i,c){
			setTimeout(function(){
				o.removeClass(c);
			},200);
			return false;
			/*
			setTimeout(function(){
				o.removeClass(c);
				setTimeout(function(){
					o.addClass(c);
					setTimeout(function(){
						o.removeClass(c);
						setTimeout(function(){
							o.addClass(c);
							setTimeout(function(){
								o.removeClass(c);
							},i);
						},3*i);
					},3*i);
				},6*i);
			},6*i);
			*/
		}
		,sortable_sub:function() {
			if (!$('#'+this.name+'-content table.a-sublist tbody.a-sortable').length) return false;
			$('#'+this.name+'-content table.a-sublist tbody.a-sortable').sortable({
				axis: 'y',
				containment: 'parent',
				cursor: 'move',
				start: function(e,ui){
					S.A.L.sorting=true;
					ui.item.find('td').addClass('a-sorting').removeClass('a-first');
				},
				stop: function(e,ui){
					S.A.L.sorting=false;
					ui.item.find('td').removeClass('a-sorting').show('highlight',{},1200);
				},
				helper: function(e,tr){
					tr.parent().parent().css({borderSpacing:0});
					tr.children().each(function() {					
						$(this).width($(this).width());
					});  
					return tr;
				},
				update: function(e, ui) {
					var arr='';
					$(this).find('tr').each(function(){
						var o=$(this);
						var f=o.find('td:first');
						if (!this.rowIndex) {
							f.addClass('a-first');
						}
						f.text(this.rowIndex+1);
						var id=o.attr('id').substring(4);
						arr += id+'-'+(this.rowIndex+1)+'|';
					});	
					$(this).sortable('refresh');
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+S.A.L.name, {
						get: 'action',
						a: 'sort',
						sort: arr
					}, function(data) {
					});
				},
				tolerance: 'pointer',
				forceHelperSize: true,
				forcePlaceholderSize: true
			});
			$('#'+this.name+'-content table.a-list').disableSelection();	
		}
		,sortable:function(){
			if (!$('#'+this.name+'-content .a-sortable_'+this.name).length) {
				return false;
			}
			$('#'+this.name+'-content .a-sortable_'+this.name).sortable({
				axis: 'y',
				containment: 'parent',
				cursor: 'move',
				zIndex:3000,
				items: '>tr.a-sortable',
				start: function(e, ui) {
					S.A.L.sorting=true;
					id=ui.item.attr('id');
					_id = 'a-cc-'+id.substring(4);
					var _o=$('#'+_id);
					if (_o.length) {
						ui.item.append(_o);
					}
					ui.item.find('td').addClass('a-sorting');
				},
				helper: function(e,tr){
					tr.parent().parent().css({borderSpacing:0});
					tr.children().each(function() {					
						$(this).width($(this).width());
					});  
					return tr;
				},
				stop:function(e, ui) {
					S.A.L.sorting=false;
					id=ui.item.attr('id');
					_id = 'a-cc-'+id.substring(4);
					var _o=$('#'+_id);
					ui.item.find('td').removeClass('a-sorting');
					if (_o.length) {
						o=ui.item.find('#'+_id);
						o.remove();
						ui.item.after(o);
						S.A.L.sortable_sub();
					}
				},
				update: function(e, ui) {
					var arr='';
					$(this).find('tr.a-sortable').each(function(){
						var o=$(this);
						var id=o.attr('id').substring(4);
						arr += id+'-'+(this.rowIndex+1)+'|';
					});	
					$(this).sortable('refresh');
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+S.A.L.name+(S.A.L.tab?'&tab='+S.A.L.tab:''), {
						get: 'action',
						a: 'sort',
						sort: arr
					}, function(data) {
						//S.G.msg(data);
					});
				},
				tolerance: 'pointer',
				forceHelperSize: true,
				forcePlaceholderSize: true
			});	
		}
		,sortable_tree:function(){
			$('#'+this.name+'-content .a-tree').sortable({
				axis: 'y',
				cursor: 'move',
				zIndex: 3000,
				containment: 'parent',
				placeholder: 'ui-state-highlight',
		
				update: function(e, ui) {
					var arr='';
					var i=0;
					$(this).find('li').each(function(){
						var o=$(this);
						var id=o.attr('id').substring(4);
						arr += id+'-'+(i++)+'|';
					});	
					$(this).sortable('refresh');
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+S.A.L.name, {
						get: 'action',
						a: 'sort',
						sort: arr
					}, function(data) {
						
					});
				},
				tolerance: 'pointer',
				forceHelperSize: true,
				forcePlaceholderSize: true
			}).disableSelection();
		}
		,C:{
			checked:[],
			init:function(){
				var fn = false;
				$('#a_email_mail_div').selectable({
					filter: 'tr.a-row',
					stop: function(){
						S.A.L.C.checked=[];
						$('.ui-selected', this).each(function(){
							S.A.L.C.checked.push($(this).attr('id').substring(7));
						});
						if (S.A.L.C.checked.length){
							$('#a_email_mail_del').button('enable');
							$('#a_email_mail_move').removeAttr('disabled');
						} else {
							$('#a_email_mail_del').button('disable');
							$('#a_email_mail_move').attr('disabled','disabled');
						}
					}
				});
			}
			,del:function(fn,folder){
				var r = this.checked;
				if (r.length) {
					if (fn) fn(r.join('|'));
					if (confirm('Are you sure to delete '+(r.length>1?'these '+r.length+' mail letters':'this mail letter')+'?')) {
						S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=email&tab=mail', {
							get: 'action',
							a: 'delete',
							folder: folder,
							arr: r.join('|')
						}, function(data) {
							for (i in r) $('#a-mail_'+r[i]).remove();
							if (!$('#a_email_mail_div .a-row').length) {
								S.A.L.get('?'+Conf.URL_KEY_ADMIN+'=email&tab=mail', false, 'mail')
							}
							S.A.L.C.checked=[];
							$('#a_email_mail_del').button('disable');
							$('#a_email_mail_move').attr('disabled','disabled');
						});
					} else {
						$('#a_email_mail_div').find('.ui-selected').removeClass('ui-selected');
					};
				} else {
					S.G.alert('Please use drag-and-drop with mouse to select the letters you want them to be deleted','No letters were selected');
				}
			}
			,history_label:false, history_open:false
			,history:function(obj, slide, opp){
				if (!obj) {
					this.history_label=false;
					this.history_open=false;
					return;	
				}
				if(!this.history_label) this.history_label=$(obj).html();
				if (this.history_open) {
					this.history_open=false;
					$(obj).html(this.history_label);
					slide.hide();
				} else {
					this.history_open=true;
					$(obj).html('<span class="ui-button-text">'+opp+'</span>');
					slide.show().css({width:200});
				}
			}
			,move:function(obj,folder){
				var s=$(obj);
				if (folder){
					S.A.L.post('?'+Conf.URL_KEY_ADMIN+'=email&tab=mail', {
						get: 'action',
						a: 'move',
						to: folder,
						arr: this.checked.join('|')
					}, false, 'mail', function() {
						S.A.C.checked=[];
						s.val('');
					});	
					return;	
				}
				var folder = s.val();
				var r = this.checked;
				if (r.length) {
					if (folder=='new') {
						S.A.F.addOpt(s, [S.A.Lang.new_folder,S.A.Lang.and_move], false, function(folder){
							if (!folder) {
								s.val('');
								return false;
							}
							else if (folder.toUpperCase()=='SENT' || folder.toLowerCase()=='new') {
								S.G.alert('Folder name canot be called as '+folder+'','Conflict detected');
								s.val('');
							}
							else {
								S.A.L.C.move(obj,folder);
							}
						});
					}
					else if (folder) {
						S.A.L.C.move(obj,folder);
					} else {
						s.val('');
					}
				} else {
					s.val('');
				}
			}
		}
	}
	,F:{ // form
		addOptFunc:0,addOptObj:false
		,addOpt:function(o, t, callme, fn) {
			if (callme) {
				o=this.addOptObj;
				var v=$('#a-addOpt').val();
				if (v){o.append($('<option/>').attr('value',v).text(v).attr('selected','selected'))}
				else{o.val('');o.find('option').select();}
				$('#a-alert').remove();
				if ($.unblockUI) $.unblockUI();
				if (this.addOptFunc) this.addOptFunc(v);
			}
			else {
				this.addOptObj=$(o);
				if (fn) {this.addOptFunc = function(a, b, c, d){fn(a, b, c, d)}} else this.addOptFunc=false;
				var h = '<table width="100%"><tr><th style="padding:10px 0px 4px 10px;">'+t[0]+':</th></tr><tr><td style="padding:2px 10px 10px 10px;white-space:nowrap"><input type="text" id="a-addOpt" class="a-input" value="" /> <button type="button" class="a-button" onclick="S.A.F.addOpt(false,0,true)">'+t[1]+'</button></th></tr></table>';
				S.A.W.alert(h, true, function(close){
					if (close) S.A.F.addOpt(false,0,true);
					else
					$('#a-addOpt').keydown(function(e){
						if(e.keyCode==13){
							S.A.F.addOpt(false,0,true);
							return false;
						}
					}).focus();
				});
				$('#a-alert').find('.a-button').button();
			}
		}
		,fill:function(obj,arr,sel,single) {
			if (single) {
				obj.find('option').remove().end();
				if(single) obj.append($('<option value="" style="color:#ccc">'+single+'</option>'));
				var use_sel = sel!==false;
				if (use_sel) var sel_is_arr = (sel&&typeof(sel)=='object'?true:false),s='';
				for (i in arr){
					s = '';
					if (use_sel) {
						if (sel_is_arr) {
							for (j in sel) if(sel[j]==i) s = ' selected="selected"';	
						} else {
							if (i==sel) s = ' selected="selected"';
						}
					}
					obj.append($('<option value="'+i+'"'+s+'>'+arr[i]+'</option>'));
				}
			} else {
				obj.find('li').remove().end();
				var name=$(obj).attr('rel'),li;
				for (i in arr) {
					c = false;
					for (j in sel) if(sel[j]==i) c = true;
					if (!name) return;
					n=name.replace(/[/g,'').replace(/]/g,'');
					li=$('<li />').html('<div><input type="checkbox" onclick="if(this.checked)this.parentNode.parentNode.className=\'checked\';else this.parentNode.parentNode.className=\'\'" name="'+name+'" value="'+i+'"'+(c?' checked="checked"':'')+' /> '+arr[i]+'</div>');
					if (c) li.attr('class','checked');
					li.mousedown(function(){
						if(this.childNodes[0].childNodes[0].checked){
							this.className='';
							this.childNodes[0].childNodes[0].checked=false;
						} else {
							this.childNodes[0].childNodes[0].checked=true;
							this.className='checked';
						}
					}).find('input').mouseup(function(){
						if(this.checked){
							this.parentNode.parentNode.className='';
							this.checked=false;
						} else {
							this.checked=true;
							this.parentNode.parentNode.className='checked';
						}
					});
					$(obj).append(li);
				}
			}
		}
		,options:function(o,data,s) {
			o.find('option,optgroup').remove().end();
			o.append($('<option>').attr('value','').text(''));
			for (i in data) {
				if ($.isArray(data[i])) {
					var p=$('<optgroup>').attr('label',i);
					for (j in data[i]) {
						p.append($('<option>').attr('value',data[i][j]).attr('selected',(data[i][j]==s?true:false)).text(data[i][j]));		
					}
					o.append(p);
				} else {
					o.append($('<option>').attr('value',data[i]).attr('selected',(data[i]==s?true:false)).text(data[i]));
				}
			}
			o.show('highlight');	
		}
		,fillOptions:function(id, url, post, keys, fn) {
			var s=$('#'+id).show();
			s.attr('disabled','disabled');
			S.A.L.json(url, post, function(data){
				s.removeAttr('disabled');
				S.A.F.addOptions(s, data, keys);
				if (fn) fn();
			})
		}
		,addOptions:function(s, data, keys) {
			if (!data) return false;
			s.find('option').remove().end();
			if (data[0]&&data[0].label) {
				for (i in data){
					var c={};
					if (data[i].label.substring(0,4)=='    ') {c={color:'#666'};data[i].label=data[i].label.substring(4)}
					s.append($("<option></option>").css(c).attr('value',(keys?data[i].id:data[i].label)).text(data[i].label));
				}				
			} else {
				for (id in data){
					var c={};
					if (data[id]&&data[id].substring(0,4)=='    ') {c={color:'#666'};data[id]=data[id].substring(4)}
					s.append($("<option></option>").css(c).attr('value',(keys?id:data[id])).text(data[id]));
				}
			}
		}
	}
	
	,M:{ // Menu, Users, Email
		id:0
		,page:function(id, obj, post) {
			this.id = id;
			S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=content&mid='+id, post, obj);
		}
		,edit:function(id, obj, post) {
			if (typeof(id)=='object') {
				this.id = id.id;
				post=$.extend(post,id);
			} else {
				this.id = id;	
			}
			
			S.A.W.open(S.A.L.url+'&id='+this.id, post, obj);
		}
		,del:function(a, obj) {
			if (confirm('Are you sure to delete?')) {
				S.A.L.json(S.A.L.url, {
					'get': 'action',
					'a': 'delete',
					'id': a.id
				},function(data){
					S.A.W.dialog(data);
				});
			}
		}
		,copy:function(id, obj) {
			if (confirm('Are you sure to duplicate this entry?')) {
				S.A.L.json(S.A.L.url, {
					'get': 'action',
					'a': 'copy',
					'id': id
				},function(data){
					S.A.W.dialog(data);
				});
			}
		}
		,add:function(obj) {
			this.id = 0;
			S.A.W.open(S.A.L.url+'&new=true', 'new', obj);
		}
		,send:function(id, obj, post){
			S.A.W.open(S.A.L.url+'&id='+id+'&send=true', post, obj);
		}
		,email_div:false
		,sendEmails:function(data){
			if (data.complete) {
				S.G.hideLoader();
				if ($.unblockUI) $.unblockUI();
				/*S.A.W.unblock();*/
				if (this.email_div) {
					if (S.A.L.tab) S.A.L.tab.tabs('load',S.A.L.tab_index);
					this.close();
					this.email_div.remove();
					this.email_div=false;
				}
			}
			else if (data.halt) {
				if (S.A.L.tab) S.A.L.tab.tabs('load',S.A.L.tab_index);
				this.close();
				this.email_div.remove();
				this.email_div=false;
				return false;	
			}
			else if (data.begin) {
				S.G.loader=false;
				S.A.M.dialog(data,function(){
					S.A.L.json(S.A.L.url+'&send=true', {
						'get': 'action',
						'a': 'send_next'
					}, function(data){
						S.A.M.sendEmails(data);
					});
				});
				S.A.W.close();
				if ($.blockUI) {
					$.blockUI({
						overlayCSS: {
							cursor: 'default',backgroundColor: '#222',opacity: .5
						},
						fadeIn: 500,fadeOut: 200,message: false, bindEvents: false
					});
				}
			} else {
				S.G.loader=false;
				S.A.L.json(S.A.L.url+'&send=true', {
					get: 'action',
					a: (data.end?'send_complete':'send_next')
				}, function(data){
					if (!S.A.M.email_div) {
						data.percent=0;
						S.A.M.dialog({title:'Continuation has been started',descr:'Just hold a second..'},function(){
							S.A.L.json(S.A.L.url+'&send=true', {
								'get': 'action',
								'a': (data.end?'send_complete':'send_next')
							}, function(data){
								S.A.M.sendEmails(data);
							});
						});
					}
					var o = S.A.M.email_div;
					o.find('.a-title').html(data.title);
					o.find('.a-progress').stop().animate({
						width: data.percent+'%'
					});
					o.find('.a-descr').html(data.descr);
					setTimeout(function(){
						S.A.M.sendEmails(data);
					}, data.delay);
				});
			}
		}
		,dialog:function(data, fn){
			
			$('#center-area').fadeOut();
			if(this.email_div) return;
			this.email_div = $('<div>').addClass('a-massemail ui-corner-all').css({
				position:'fixed',
				width:400,
				top:'25%',
				left:$(document.body).width()/2-200,
				display:'none',
				zIndex: 5000
			}).appendTo(document.body).show('fade', {}, fn).append('<div class="a-title">'+data.title+'</div><div class="a-progress_wrapper"><div class="a-progress" style="width:'+data.percent+'%"></div></div><div class="a-descr">'+data.descr+'</div><div class="a-cancel" style="text-align:center;padding:10px 0"><button type="button" class="a-button a-button_s" onclick="if(confirm(\'Are you sure to pause?\')){$(document.body).fadeOut();location.href=\'/'+S.A.L.url+'\'}">Pause..</button>').draggable();	
			$('.a-button').button();
		}
		,close:function(){
			$('#center-area').fadeIn();
			S.A.W.scroll(true);
			/*
			if (this.email_div) {
				this.email_div.remove();
				this.email_div=false;
			}
			*/
		}
	}
	
	,C:{ // Content
		id:0
		,page:function(module, id, obj, post) {
			this.id = module+'_'+id;
			S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=content&m='+module+'&conid='+id, post, obj);
		}
		,edit:function(module, id, obj, post) {
			this.id = module+'_'+id;
			S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=content'+(module?'_'+module:'')+'&id='+id, post, obj);
		}
		,del:function(module, a, obj) {
			if (module) {
				if (confirm(S.A.Lang.dc+' '+module+' ("'+a.title+'")?')) {
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=content_'+module, {
						'get': 'action',
						'a': 'delete',
						'id': a.id
					},function(data){
						data.reload = false;
						$(obj).parent().parent().remove();
						S.A.W.dialog(data);
					});
				}
			} else {
				if (confirm(S.A.Lang.content_dc+' ("'+a.title+'") '+S.A.Lang.subc+'?')) {
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=content', {
						'get': 'action',
						'a': 'delete',
						'id': a.id
					},function(data){
						data.reload = false;
						if ($(obj).parent().parent().next().children(0).children().hasClass('a-sublist')) {
							$(obj).parent().parent().next().remove();
						}
						$(obj).parent().parent().remove();
						S.A.W.dialog(data);
					});
				}
			}
		}
		,add:function(obj) {
			this.id = 0;
			S.A.W.open(S.A.L.url, 'new', obj);
		}
		,open:function(conid, module) {
			S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=content_'+module+'&conid='+conid);
		}
	}
	,FU:{ // flash upload
		div:0
		,init:function(h){
			if (this.div) {
				this.div.css({height:h});
				return false;
			}
			this.div=$('<div class="a-upload_progress fileQueue" style="display:none" id="a-w-upload_progress" ondblclick="$(this).hide()"></div>').css({
				position: 'fixed',
				top: parseInt($(window).height()/2-h/2),
				left: parseInt($(window).width()/2-350/2),
				width: 350,
				height: h,
				overflow: 'auto',
				display: 'none',
				zIndex: 5000
			}).draggable().appendTo(S.A.prepend);	
		}
		,upload:function(set) {
			if ($('#a-file_'+set.name).length) {
				this.init(set.height);
				var uploaded=0;
				var errors=[];
				$('#a-file_'+set.name).unbind('uploadify').uploadify({
					wmode			: 'transparent',
					uploader		: S.C.HTTP_EXT+'tpls/js/uploadify/uploadify.swf',
					script			: S.C.HTTP_EXT+'?upload='+Conf.URL_KEY_ADMIN+'.'+set.name+'.'+set.id+'.'+set.upload,
					cancelImg		: S.C.HTTP_EXT+'tpls/img/cancel.png',
					expressInstall	: S.C.HTTP_EXT+'tpls/js/uploadify/expressInstall.swf',
					folder			: set.upload,
					queueID			: 'a-w-upload_progress',
					auto			: true,
					multi			: false,
					simUploadLimit	: set.sim,
					queueSizeLimit	: set.limit,
					buttonImg		: set.buttonImg,
					width			: set.b_width,
					height			: set.b_height,
					fileExt			: set.fileExt,
					fileDesc		: set.fileDesc,
					onComplete: function(e, queueID, fileObj, response, data) {
						uploaded++;
					},
					onError			 : function(e, ID, fileObj, errorObj) {
						alert(e);
					},
					onAllComplete	 : function() {
						if(!uploaded) {
							S.A.FU.div.hide();
							return false;
						}
						S.A.FU.div.effect('blind', {}, 300, function(){
							if (set.func) {
								(set.func)();
							}
							else if (S.A.L.tab) {
								S.A.L.tab.tabs('load',S.A.L.tab_index);
							}
							else {
								S.A.L.get(S.A.L.url);
							}
							$(this).hide();
						});
					},
					onSelect		: function() {
						S.A.FU.div.show('blind');
					}
				});
			}
			if ($('#a-file_'+set.name+'2').length) {
				if ($('#a-file_'+set.name+'2').fileupload) {
					S.A.FU.uploadfile(set);
				} else {
					S.G.addJS(S.C.FTP_EXT+'tpls/js/fileupload/jquery.iframe-transport.js');
					S.G.addJS(S.C.FTP_EXT+'tpls/js/fileupload/jquery.fileupload.js', function(){
						S.A.FU.uploadfile(set);
					});
				}
			}
		}
		,uploadfile_fail: false
		,uploadfile:function(set){
			$('#a-file_'+set.name+'2').die().fileupload({
				url: S.C.HTTP_EXT+'?upload='+Conf.URL_KEY_ADMIN+'.'+set.name+'.'+set.id,
				dataType: 'html',
				maxChunkSize: 2000000,
				formData: {
					folder:set.upload,
					_t: S.C._T,
				},
				add: function (e, data) {
					var uploadFile = data.files[0];
					if (set.regex && !set.regex.test(uploadFile.name)) {
						if (set.error) alert(set.error);
						else alert('Please upload one of the folowed: '+(set.regex));
					} else {
						data.submit();
					}
				},
				start:function(e,data){
					S.A.L.uploadfile_fail=false;
					$('#progress').show();
					$('#a-filediv_'+set.name).hide();
				},
				complete:function(e,data){
					return;
				},
				done: function (e, data) {
					$('#progress .progress-title').html('Uploaded!');
					$('#a-filediv_'+set.name).show();
					$('#progress').fadeOut(function(){
						if (S.A.L.uploadfile_fail) return;
						if (set.func) {
							(set.func)();
						}
						else if (S.A.L.tab) {
							S.A.L.tab.tabs('load',S.A.L.tab_index);
						}
						else S.A.L.get(S.A.L.url);
					});
				},
				progress: function(e,data){
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('#progress .progress-bar').stop().animate({width:progress+'%'},'fast').html(progress+'%');
					$('#progress .progress-title').html('Uploading '+(data.files&&data.files[0].name?data.files[0].name+' ':''));
				},
				progressall: function (e, data) {
					
				},
				
				maxRetries: 100,
				retryTimeout: 500,
				fail: function (e, data) {
					S.A.L.uploadfile_fail=true;
					if (!data || !$(this).data) return;
					var fu = $(this).data('blueimp-fileupload') || $(this).data('fileupload'),
					retries = $(this).data('retries') || 0,
					retry = function () {
						$.getJSON(S.C.HTTP_EXT+'?upload='+Conf.URL_KEY_ADMIN+'.'+set.name+'.'+set.id, {file: data.files[0].name,folder:set.upload}).done(function (result) {
							var file = result.file;
							data.uploadedBytes = file && file.size;
							data.data = null;
							data.submit();
						}).fail(function () {
							fu._trigger('fail', e, data);
						});
					};
					if (data.errorThrown !== 'abort' &&	data.uploadedBytes < data.files[0].size && retries < fu.options.maxRetries) {
						retries += 1;
						$(this).data('retries', retries);
						window.setTimeout(retry, retries * fu.options.retryTimeout);
						return;
					}
					$(this).removeData('retries');
					$.blueimp.fileupload.prototype.options.fail.call(this, e, data);
				}
			});
		}
	}
	,S:{ // settings
		init:function(){
			
		}
		,columns:function(id,v,tab){
			S.A.F.fillOptions(id,'?'+Conf.URL_KEY_ADMIN+'='+S.A.L.name+(tab?'&tab='+tab:''),{
				get:'columns',
				table:v
			}, false)	
		}	
	}
	,T:{ // template editor
		search:function(url) {
			alert('Not yet ready');	
		}
	}
	,X:{ // contex menus
	/*
		var c=[{
			label: 'Close window',
			icon: '/tpls/img/oxygen/16x16/actions/application-exit.png',
			func: 'S.A.win.close(\''+id+'\');'
		}];
	*/
		opened:false
		,init:function(){
			$(document.body).bind('click', function(){
				S.A.X.close();
			});	
		}
		,context:function(e, x, obj) {
			this.close();
			var c=$('<div />').attr('id','a-context').addClass('a-context_shadow').css({
				position: 'absolute',
				zIndex: S.A.W.zIndex+10,
				top: e.pageY+10,
				left: e.pageX-80
			}).prependTo(document.body);
			var sep=0,j=0;
			var h='<div class="a-context">';
			for (i in x) {
				if (!x[i]) continue;
				if (!x[i].label) continue;
				if(x[i].sep) {
					h+='<div class="a-separator"></div>';
					sep++;
				}
				h+='<div class="a-item" onmousedown="this.className=\'a-item-down\'" onmouseout="this.className=\'a-item\'">';
				h+='<div class="a-inner" style="'+(x[i].icon?'background-image:url(\''+x[i].icon+'\')':'')+'" onclick="S.A.X.close();'+x[i].func+'">'+x[i].label+'</div></div>';
				j++;
			}
			h+='</div>';
			
			c.html(h).show();
			this.opened=true;
			return false;
		}
		,close:function(){
			if (!this.opened) $('#a-context').remove();
			setTimeout(function(){
				S.A.X.opened=false;
			},100);
			S.A.E.hl(0,0,1);
		}
	}
	// Visual
	,V:{
		switched:false, inited:false, element:0, type:'',visual_id:'',clicked:false,dblclicked:false,title:'',files:[]
		,init:function(run, files){
			
			if (this.inited) return false;
			this.inited=true;
			var h = '<table class="ui-dialog-header" style="background:none"><tr>';
			if (!run) {
				h += '<td><button type="button" onclick="S.A.V.admin(this)" class="a-button a-button_x">'+S.A.Lang.admin+'</button></td>';
				/*if (S.A.Conf.crm){
					h += '<td><button type="button" onclick="S.A.L.website(this,\'crm\')" class="a-button a-button_x">CRM</button></td>';
				}*/
			}
			if (S.A.Conf.visual) {
				if (run) {
					h += '<td><button type="button" id="a-visual_commit" style="display:none" onclick="S.A.V.commit(this)" class="a-button a-button_x">'+S.A.Lang.commit+'</button></td>';
					h += '<td><button type="button" id="a-visual_discard" style="display:none" onclick="S.A.V.discard(this)" class="a-button a-button_x">'+S.A.Lang.discard+'</button></td>';
				}
				h += '<td><button type="button" onclick="S.A.V.sw(this)" class="a-button a-button-s a-button_x">'+(run?S.A.Lang.edit:S.A.Lang.visual)+'</button></td>';
			}
			h += '</tr></table>';
			S.A.L.switcher(h,run);
			if (run) {
				this.switched = true;
				this.load();
				this.put_files(files);
			}
		}
		,files_table:function(){
			var html = '<ul>';
			for (i in this.files) {
				html += '<li>'+this.files[i].f.replace('_visual_','')+' ('+this.files[i].c+' change'+(this.files[i].c>1?'s':'')+')</li>';
			}
			html += '</ul>';
			return html;
		}
		,discard:function(obj){
			S.G.confirm('All changes in: '+this.files_table()+' will be lost.<br /><br />These files will just be re-generated from original template files.<br /><br />Press `OK` to restore to original look.<br />Press `Cancel` to close this window.','Are you sure to discard changes?', function(){
				if ($.fn.button) $(obj).button('disable');
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a=visual_discard',false,function(data){
					S.G.msg(data, function(){
						if (!data.error) $(document.body).fadeOut();
						if ($.fn.button) $(obj).button('enable');
					});
				})
			}, function(){
				if ($.fn.button) $(obj).button('enable');	
			});
		}
		,commit:function(obj){
			S.G.confirm('Once you press this button, all changes in: '+this.files_table()+' will be commited!<br /><br />These temporary files will overwrite original files and you will no longer be able to restore these templates. You are doing this at your own risk!<br /><br />Press `OK` to commit.<br />Press `Cancel` to close this window.','Are you sure to commit changes?', function(){
				if ($.fn.button) $(obj).button('disable');
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a=visual_commit',false,function(data){
					S.G.msg(data, function(){
						if (!data.error) $(document.body).fadeOut();
						$(obj).button('enable');
					});
				})
			}, function(){
				if ($.fn.button) $(obj).button('enable');
			});
		}
		,admin:function(obj){
			window.location.replace(S.C.HTTP_EXT+'?'+Conf.URL_KEY_ADMIN);
			$(document.body).fadeOut();
			if ($.fn.button) $(obj).button('disable');
		}
		,logout:function(obj){
			window.location.replace(S.C.HTTP_EXT+'logout');
			$(document.body).fadeOut();
			if ($.fn.button) $(obj).button('disable');
		}
		,sw:function(obj){
			S.A.L.global('session',{key:'visual',value:!this.switched}, function(){
				window.location.href=window.location.href;	
			});
			if ($.fn.button) $(obj).button('disable');
			$(document.body).fadeOut();
			
		}
		,put_files:function(files){
			this.files=files;
			var l = 0;
			for (i in this.files) l++;
			if (l) {
				$('#a-visual_commit').show('highlight');
				$('#a-visual_discard').show('highlight');
			}
		}
		,hasClass:function(o,target,c) {
			if (target) {
				if (target.hasClass(c)) {
					this.type = c.replace('_dummy','');	
					return target;
				}
				if (target.parent().hasClass(c)) {
					this.type = c.replace('_dummy','');	
					return target.parent();
				}
				if (target.parent().parent().hasClass(c)) {
					this.type = c.replace('_dummy','');	
					return target.parent().parent();
				}
			}
			else if (o.hasClass(c)) {
				this.type = c.replace('_dummy','');	
				return o;
			}
			else if (o.children(0).hasClass(c)) {
				this.type = c.replace('_dummy','');	
				return o.children(0);
			}
			else if (o.children(0).children(0).hasClass(c)) {
				this.type = c.replace('_dummy','');	
				return o.children(0).children(0);
			}
			else return false;
		}
		,target:function(o,target){
			this.type='';
			var r = this.hasClass(o,target,'a-edit_dummy');
			if (r) return r;
			var r = this.hasClass(o,false,'a-lang_dummy');
			if (r) return r;
			return o;
		}
		,load:function(){			
			$('visual').mouseover(function(e){
				var o=$(this);
				if ($('#a-context').length || o.parent()[0].tagName=='BODY' || o.parent().parent()[0].tagName=='BODY' || o.parent().parent().parent()[0].tagName=='BODY' || S.A.V.F.moving_ok) return false;
				if (S.A.V.F.element.length) S.A.V.F.element.removeClass('a-visual_selected');
				S.A.V.F.element = S.A.V.target($(this),$(e.target));
				switch (S.A.V.type) {
					case 'a-lang':
						S.A.V.title=S.A.V.F.element.attr('title');S.A.V.F.element.attr('title','');
						S.A.V.F.element.addClass('a-visual_hover_lang');
					break;
					case 'a-edit':
						S.A.V.F.element.addClass('a-visual_hover_edit');
					break;
					default:
						S.A.V.F.element.addClass('a-visual_hover');
					break;	
				}
				S.A.V.visual_id = o.attr('id');
				return false;
			}).mouseout(function(e){
				S.A.V.dblclicked=false;S.A.V.clicked=false;
				if (!S.A.V.F.element) return false;
				/*S.A.V.F.element.sortable('destroy');*/
				switch (S.A.V.type) {
					case 'a-lang':
						S.A.V.F.element.attr('title',S.A.V.title);
						S.A.V.F.element.removeClass('a-visual_hover_lang');
					break;
					case 'a-edit':
						S.A.V.F.element.removeClass('a-visual_hover_edit');
					break;
					default:
						S.A.V.F.element.removeClass('a-visual_hover');
					break;	
				}
				return false;
			}).bind('contextmenu',function(e){
				S.E.context=true;
				S.A.V.visual_id=$(this).attr('id');
				if (S.A.V.F.moving) {
					$('#a-context').remove();
					S.A.V.F.click();
					S.A.V.clicked=true;	
					return false;
				}
				if (S.A.V.F.element.length) S.A.V.F.element.removeClass('a-visual_selected');
				if ($('#a-context').length) {
					S.A.X.close();
					S.A.V.F.element = S.A.V.target($(this));
				}
				if (!S.A.V.F.element) return null;
				if (S.A.V.type=='a-lang') S.A.V.F.element.attr('title',S.A.V.title);
				S.A.V.F.element.addClass('a-visual_selected');
				var c=[];
				c.push({
					sep: c.length ? true : false,
					label: S.A.Lang.e_thtml,
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit.png',
					func: 'S.A.V.F.html_edit()'
				});
				c.push({
					label: S.A.Lang.p_thtml,
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/zoom-best-fit.png',
					func: 'S.A.V.F.html()'
				});
				c.push({
					label: S.A.Lang.p_ghtml,
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/ark-view.png',
					func: 'S.A.V.F.html(true)'
				});
				
				if (!S.A.V.F.element.hasClass('a-visual_nomove') && S.A.V.type!='a-edit'&&S.A.V.type!='a-snippet') {
					c.push({
						sep: true,
						label: S.A.Lang.m_ebefore+'..',
						icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/go-top.png',
						func: 'S.A.V.F.move(false, true)'
					});
					c.push({
						label: S.A.Lang.m_eafter+'..',
						icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/go-bottom.png',
						func: 'S.A.V.F.move()'
					});
				}
				c.push({
					sep: true,
					label: S.A.Lang.a_ea+'..',
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/go-previous.png',
					func: 'S.A.V.F.add(true)'
				});
				c.push({
					label: S.A.Lang.a_eb+'..',
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/go-next.png',
					func: 'S.A.V.F.add()'
				});
				if (!S.A.V.F.element.hasClass('a-visual_nomove')) {
					c.push({
						sep: true,
						label: S.A.Lang.d_e+'..',
						icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit-delete.png',
						func: 'S.A.V.F.del()'
					});
				}
				switch (S.A.V.type) {
					case 'a-lang':
						c.push({
							sep: true,
							label: S.A.Lang.qe_lang,
							icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit.png',
							func: 'S.A.V.F.quick_edit()'
						});
					break;
					case 'a-edit':
						c.push({
							sep: true,
							label: S.A.Lang.qe_entry,
							icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit.png',
							func: 'S.A.V.F.quick_edit()'
						});
						c.push({
							label:  S.A.Lang.fe_entry,
							icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/apps/kwrite.png',
							func: 'S.A.V.F.full_edit()'
						});
					break;
					case 'a-snippet':
						c.push({
							sep: true,
							label: S.A.Lang.e_snippet,
							icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit.png',
							func: 'S.A.V.F.quick_edit()'
						});
					break;
				}
				S.A.X.context(e, c, this.element);				
				return false;
			}).bind('dblclick', function(){
				if (S.A.V.dblclicked||!S.A.V.F.element.length||!S.A.V.type) return false;
				S.A.V.F.element.removeClass('a-visual_selected');
				if ($('#a-context').length) {
					S.A.X.close();
					S.A.V.F.element = S.A.V.target($(e.target));
				}
				if (!S.A.V.F.element) return null;
				S.A.V.F.element.addClass('a-visual_selected');
				S.A.V.F.quick_edit();
				S.A.V.dblclicked=true;
			}).bind('click',function(){
				if (S.A.V.clicked||S.A.V.dblclicked) return null;
				$('#a-context').remove();
				S.A.V.visual_id=$(this).attr('id');
				S.A.V.F.click();
				S.A.V.clicked=true;
				if (S.A.V.F.moving) return false;
			});
		}
		,F:{
			element:false,moving:false,moving_ok:false,move_before:false
			,html_edit:function(){
				S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=global&a=visual_edit&visual_id='+S.A.V.visual_id);
			}
			,html:function(gen){
				if (!S.A.V.visual_id) {
					return false;
				}
				if (gen) {
					if (!$('#'+S.A.V.visual_id).length) {
						return alert(S.A.V.visual_id+' id is missing!');
					}
					var html = S.G.html(S.A.V.visual_id);
					S.G.alert(S.A.P.html(html),S.A.Lang.html_gp,false,true);
				} else {
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a=visual_html',{
						visual_id: S.A.V.visual_id
					},function(data){
						if (data.text) {
							return S.G.msg(data);
						}
						if (!data.html) {
							return S.G.alert('Data is missing, something went wrong..');
						}
						S.G.alert(S.A.P.html(data.html),(data.file?S.A.Lang.html_pf+': '+data.file.replace('_visual_',''):'HTML preview error')+(data.line?' (line: '+data.line+')':''),false,true);
					});
				}
			}
			,dummy_obj:false
			,click:function(){
				if (this.moving && S.A.V.visual_id) {
					this.moving_ok = true;
					$('#'+this.moving).addClass('a-visual_moving'); // green, from
					$('#'+S.A.V.visual_id).addClass('a-visual_selected'); // orange, to
					S.G.confirm(S.A.Lang.move_t1,S.A.Lang.move_s1,function(){
						S.A.V.F.dummy_obj=$('<visual>').html($('#'+S.A.V.F.moving).html()).hide().addClass('a-visual_moving');
						if (S.A.V.F.move_before) {
							$('#'+S.A.V.visual_id).before(S.A.V.F.dummy_obj);
						} else {
							$('#'+S.A.V.visual_id).after(S.A.V.F.dummy_obj);
						}
						S.A.V.F.dummy_obj.show('highlight');
						$('#'+S.A.V.F.moving).hide();
						
						S.G.confirm(S.A.Lang.move_t2,S.A.Lang.move_s2,function(){
							$('#'+S.A.V.F.moving).removeClass('a-visual_hover').removeClass('a-visual_hover_lang').removeClass('a-visual_hover_edit');
							$('#'+S.A.V.visual_id).removeClass('a-visual_hover').removeClass('a-visual_hover_lang').removeClass('a-visual_hover_edit');
							S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a=visual_move',{
								from: S.A.V.F.moving,
								to: S.A.V.visual_id,
								before: S.A.V.F.move_before
							},function(data){
								if (data.error) {
									$('#'+S.A.V.F.moving).show();
									alert(data.error);
								} else {
									var obj=$('<visual>').html($('#'+data.from).html());
									if (S.A.V.F.move_before) {
										$('#'+data.to).before(obj);
									} else {
										$('#'+data.to).after(obj);
									}
									$('#'+data.from).remove();
									obj.attr('id',data.to_new);
									S.A.V.put_files(data.files);
								}
								S.A.V.F.dummy_obj.remove();
								S.A.V.F.move(true);
							});
						}, function(){
							$('#'+S.A.V.F.moving).show('highlight');
							S.A.V.F.dummy_obj.remove();
							S.A.V.F.move(true);
						});
					}, function(){
						S.A.V.F.move(true);
					});
				}
			}
			,move:function(cancel, before){
				if (cancel) {
					$('#'+S.A.V.F.moving).removeClass('a-visual_moving');
					$('#'+S.A.V.visual_id).removeClass('a-visual_selected');
					S.A.V.F.moving = false;
					S.A.V.F.moving_ok = false;	
				} else {
					this.move_before=before;
					this.moving = S.A.V.visual_id;
				}
			}
			,del:function(){
				S.G.confirm(S.A.Lang.ec_del+'<br /><br />'+S.A.P.html($('#'+S.A.V.visual_id).html().substring(0,200))+'..',S.A.Lang.ect_del,function(){
					$('#'+S.A.V.visual_id).removeClass('a-visual_hover').removeClass('a-visual_hover_lang').removeClass('a-visual_hover_edit');
					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a=visual_delete',{
						visual_id: S.A.V.visual_id
					},function(data){
						if (data.error) {
							S.G.alert(data.error,'Error!');
						} else {
							$('#'+data.from).remove();
							S.A.V.put_files(data.files);
						}
					});
				});
			}
			,add:function(above){
				S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=global&a=visual_add&above='+(above?'1':'0')+'&visual_add='+S.A.V.visual_id);
			}
			,quick_edit:function(){
				if (S.A.V.type=='a-lang'&&S.A.V.title) S.A.V.F.element.attr('title',S.A.V.title);
				S.A.E.dblclick(false, S.A.V.F.element.get(0));	
			}
			,full_edit:function(){
				var c = S.A.V.F.element.attr('class');
				eval('var r='+c.substring(c.indexOf('['),c.indexOf(']')+1));
				if (!r) return false;
				var eid = S.A.V.F.element.attr('id');
				var ex = eid.split('_');
				if (r[1].indexOf(':')!==-1) {
					r[1] = r[1].split(':');
					var module = r[1][1];
					r[1] = r[1][0];
				} else {
					var ex = r[1].split('_');
					var module = ex[1];
				}
				S.A.E.r[eid]=r;
				S.A.E.full_edit(eid,r[1],r[2],module);
			}
		}
	}
	// Drag and drop li-s
	,B:{
		/*
		updated:false, from:{}, cur:{}, to:[], destroyed: false
		,getArguments:function(id) {
			eval('var data='+$('#a-arguments_'+id).html()+';');
			return data;
		}
		,getPrevNext:function(el,is_update){
			var ret={}, id, data;
			if (el.prev().hasClass('a-div')) {
				id=el.prev().attr('id').substring(6);
				if (!id) return false;
				data=this.getArguments(id);
				if (data) ret.prev = {id: id,file:data.file}
			}
			else if (el.next().hasClass('a-div')) {
				id=el.next().attr('id').substring(6);
				if (!id) return false;
				data=this.getArguments(id);
				if (data) ret.next = {id: id,file:data.file}
			}
			else if (el.parent().hasClass('a-block')) {
				id=el.parent().attr('id').substring(8);
				if (!id) return false;
				data=this.getArguments(id);
				if (data) ret.prev = {id: id,file:data.file,none:true}
			}
			return ret;
		}
		,*/init:function(){
			return;
			/*
			if (this.destroyed) {
				$('.a-block_dummy').removeClass('a-block_dummy').addClass('a-block');
				this.destroyed = false;	
			}
			
			$('.a-block').sortable({
				connectWith: '.a-block',
				snap: true,
				revert: true,
				stop:function(e,ui){
					S.A.B.updated=false
				}
				,start:function(e,ui){
					var id=ui.item.attr('id').substring(6);
					if (!id) return false;
					var data=S.A.B.getArguments(id);
					S.A.B.cur={
						id: id,
						file: data.file
					}
				}
				,update:function(e,ui){
					if (S.A.B.updated) return null;
					S.A.B.updated=true;
					S.A.B.to=S.A.B.getPrevNext(ui.item,true);
					var data={
						current: S.A.B.cur,
						to: S.A.B.to,
						ul: ui.item.parent().attr('id').substring(8)
					}

					S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a=design',data,function(data){
						S.G.msg(data);
					});
				}
			}).disableSelection();
			*/
		}
	}
	// Edit
	,E:{
		o:[], index: 7001, templates:[], template:'',highlight:false,r:[],vars:[],destroyed:false
		,init:function(vars,no_cookie){
			$(document).bind('keydown', 'CTRL+S', function(e){
				if (S.E.ctrlKey&&S.E.keyCode==83) {
					e.preventDefault();
					return false;
				}
			});
			this.template=vars.template;
			this.templates=vars.templates;
			if (!this.template) {
				alert('Cannot initialize Edit/Visual:\n template name is missing!');
				return false;	
			}
			S.A.X.init();
			if (vars.visual) {
				S.A.V.init(true, vars.visual_files);
				return false;	
			}
			S.A.B.init();
			S.A.V.init();
			
			$('button>'+S.A.admin_tag+'.a-lang').each(function(){
				var o=$(this);
				o.parent().attr('class', o.attr('class')+' '+o.parent().attr('class')).attr('id', o.attr('id')).attr('alt', o.attr('alt')).html(o.html());
			});
			
			
			$(''+S.A.admin_tag+'.a-snippet').live('contextmenu', function(e){
				S.E.context=true;
				var eid = $(this).attr('id');
				var c = $(this).attr('class');
				eval('var r='+c.substring(c.indexOf('['),c.indexOf(']')+1));
				if (!r) return false;
				var c=[{
					label: 'Edit snippet',
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/apps/konsole.png',
					func: 'S.A.E.full_edit(\''+eid+'\',\''+r[1]+'\',\''+r[2]+'\');'
				}];
				S.A.X.context(e, c, $(this));
				S.A.E.hl($(this), true);
				return false;
			});
			$(''+S.A.admin_tag+'.a-lang, button.a-lang').live('contextmenu', function(e){
				S.E.context=true;
				S.A.E.dblclick(e, this);
				return false;
			}).live('dblclick', function(e){
				S.A.E.dblclick(e, this);
				return false;
			});
			$(''+S.A.admin_tag+'.a-edit').live('contextmenu', function(e){
				S.E.context=true;
				/*S.A.E.hov.hide();*/
				S.A.E.context(e,this);
				return false;
			}).live('dblclick', function(e){
				/*S.A.E.hov.hide();*/
				S.A.E.dblclick(e, this);
				return false;
			})
		}
		,hov:{}
		,context:function(e,obj){
			if (S.A.E.highlight) return;
			var eid = $(obj).attr('id');
			var ex = eid.split('_');
			var wrapper_index = 'a-edit_wrapper_'+ex[1];
			var r=this.ret(eid, obj);
			if (!r) return false;
			
			var c=[
				{
					label: S.A.Lang.f_e,
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/apps/kwrite.png',
					func: 'S.A.E.full_edit(\''+eid+'\',\''+r[1]+'\',\''+r[2]+'\',\''+r[9]+'\');'
				}
				,{
					label: (r[3]==4?false:S.A.Lang.q_e),
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit.png',
					func: 'S.A.E.quick_edit(\''+eid+'\',\''+r[0]+'\',\''+r[1]+'\',\''+r[2]+'\',\''+r[3]+'\',\''+r[9]+'\',\'\',\''+r[7]+'\');'
				}
				,{
					label: (r[4]?S.A.Lang.pd_e:false),
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/tool2.png',
					func: 'S.A.E.link(\''+eid+'\',\''+r[1]+'\','+r[4]+','+r[5]+',\''+r[9]+'\',4);'
				}
				,{
					sep: true,
					label: ((r[1]=='entries'||r[1]=='menu')?S.A.Lang.n_em:false),
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/list-add.png',
					func: 'S.A.E.link(\''+eid+'\',\''+r[1]+'\',0,'+r[5]+',\''+r[9]+'\',5);'
				}
				,{
					sep: true,
					label: (r[1]=='grid'?S.A.Lang.n_et+' '+r[9]+'':''),
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/list-add.png',
					func: 'S.A.E.link(\''+eid+'\',\''+r[1]+'\','+r[4]+','+r[5]+',\''+r[9]+'\',6);'
				}
				,{
					label: ((!r[4]&&r[1]!='grid')?S.A.Lang.n_pm:false),
					func: 'S.A.E.link(\''+eid+'\',\'content\',0,'+r[5]+',\''+r[9]+'\',3);'
				}
				,{
					label: (r[4]?S.A.Lang.n_psm:false),
					func: 'S.A.E.link(\''+eid+'\',\''+r[1]+'\','+r[4]+','+r[5]+',\''+r[9]+'\',3);'
				}
				,{
					label: ((r[4]&&r[1]!='grid')?S.A.Lang.a_nmp:false),
					func: 'S.A.E.link(\''+eid+'\',\''+r[1]+'\','+r[4]+','+r[5]+',\''+r[9]+'\',2);'
				}
				,{
					label: ((!r[4]&&r[1]=='menu')?S.A.Lang.a_nm:false),
					func: 'S.A.E.full_edit(\''+eid+'\',\''+r[1]+'\',0,\''+r[9]+'\');'
				}
				,{
					sep: true,
					label: 'Undo to previous version',
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit-undo.png',
					func: 'S.A.E.undo(\''+eid+'\',\''+r[1]+'\',\''+r[2]+'\',\''+wrapper_index+'\',\''+r[9]+'\');'
				}
				,{
					label: S.A.Lang.del,
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit-delete.png',
					func: 'S.A.E.del(\''+r[1]+'\',\''+r[2]+'\',\''+r[9]+'\');'
				}
				,{
					label: S.A.Lang.dc,
					icon: S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/flag-red.png',
					func: 'S.A.E.act(\''+eid+'\',\''+r[1]+'\',\''+r[2]+'\',\''+wrapper_index+'\',\''+r[9]+'\');'
				}
				
			];
			S.A.X.context(e, c, $(obj));
			S.A.E.hl($(obj), true);	
		}
		,ret:function(eid, obj) {
			var c = $(obj).attr('class');
			if (c.length<7 || c.indexOf('[')===-1) {
				alert('Error. Element does not contain arguments in class attribute. Attempt to call using custom `'+c+'` class.');
				return false;
			}
			var r=false;
			eval('r='+c.substring(c.indexOf('['),c.indexOf(']')+1));
			if (!r) return false;
			if (r[3]==4) return;
			if (r[1].indexOf(':')!==-1) {
				r[1] = r[1].split(':');
				r[9] = r[1][1];
				r[1] = r[1][0];
			} else {
				var ex = r[1].split('_');
				if (ex[1]) {
					r[9] = ex[1];
					r[1] = ex[0];
				}
			}
			if (!r[2]) {
				if (r[1]=='lang' && $(obj).attr('title')) {
					r[2] = S.A.P.js($(obj).attr('title'));
				} else {
					return alert('ID is missing');
				}
			}
			if (r[1]=='lang'||r[1]=='vars')r[7]=1;
			this.r[eid]=r;

			return r;
		}
		,dblclick:function(e, obj) {
			if (this.highlight) return;
			var eid = $(obj).attr('id');
			var r=this.ret(eid, obj);
			S.A.E.quick_edit(eid,r[0],r[1],r[2],r[3],r[9],((r[3]==6||r[3]==7)?$(obj).attr('alt'):0),r[7]);	
		}
		,hl:function(o, on, all) {
			if (all) {
				o=$(''+S.A.admin_tag+'.a-edit, '+S.A.admin_tag+'.a-snippet');
			}
			if (on) {
				o.css({
					color: 'red'
				});
			} else {
				o.css({
					color: ''
				});
			}
		}
		,params_url:function(){
			return (S.A.Conf.lang?'&l='+S.A.Conf.lang:'')+'&tpl='+this.template;
		}
		,prop:function(o,table,id,wrapper_index) {
			alert('Sooner..');	
		}
		,act:function(o,table,id,active,wrapper_index,module) {
			S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+table+'&id='+id+'&exact'+this.params_url()+(module?'&m='+module:''), {
				get: 'action',
				a: 'act',
				id: id
			}, function(){
				window.location.href=window.location.href+window.location.hash;
			});
		}
		,reverting:false
		,undo:function(o,table,id,active,wrapper_index,module) {
			S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&exact'+this.params_url()+(module?'&m='+module:''), {
				get: 'action',
				a: 'undo',
				table: table,
				column: S.A.E.r[o][0],
				id: id
			}, function(data){
				if (data.error) alert(data.error);
				else if (!data.html) alert('No previous versions avalable!');
				else {
					var oldhtml=$('#'+o).html();
					$('#'+o).css({'visibility':''}).html(data.html);
					S.G.confirm('<table><tr><td>Title:</td><td>'+data.title+'</td></tr><tr><td>Date saved:</td><td>'+data.added+'</td></tr><tr><td>By:</td><td>'+data.user+'</td></tr><tr><td>Table ID column:</td><td>'+table+' '+id+' '+S.A.E.r[o][0]+'</td></tr></table>', 'Are you sure to revert to this state?', function(){
						S.A.E.revert(o, data.id, data.quick);
					}, function(){
						$('#'+o).html(oldhtml);
					});
				}
			});
		}
		,revert:function(o, logid, quick){
			$('#'+o).fadeOut();
			S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=log&exact'+this.params_url(), {
				get: 'action',
				a: 'save',
				id: logid
			}, function(data){
				$('#'+o).fadeIn();
				if (!quick) window.location.href=window.location.href;
			});
		}
		,link:function(o,table,conid,menuid,module,type) {
			S.A.W.refresh = true;
			switch (type) {
				case 1: // to the same content
					S.A.W.open('?'+Conf.URL_KEY_ADMIN+'='+table+this.params_url()+'&conid='+conid+'&m='+module+'&id=new');
				break;
				case 2: // another module to the same content
					S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=content'+this.params_url()+'&conid='+conid+'&m='+module+'&add=1&new=true');
				break;
				case 3: // another content to the same menu
					if (table=='entries') {
						S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=entries'+this.params_url()+'&mid='+menuid+'&add=1');
					} else {
						S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=content'+this.params_url()+'&mid='+menuid+'&m='+module+'&add=1');	
					}
				break;
				case 4: // another content to the same menu
					// http://alx/?window&'+Conf.URL_KEY_ADMIN+'=content&id=5
					S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=content'+this.params_url()+'&id='+conid+(module?'&m='+module:''));
				break;
				case 5: // new entry to this menu
					// http://alx/?window&'+Conf.URL_KEY_ADMIN+'=content&id=5
					S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=entries&id=new&mid='+menuid+this.params_url());
				break;
				case 6:
					S.A.W.open('?'+Conf.URL_KEY_ADMIN+'=grid&m='+module+'&id=new'+this.params_url());
				break;
			}
		}
		,del:function(table,id,module) {
			if (confirm(S.A.Lang.entry_dc)) {
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'='+table+this.params_url()+'&exact=true&id='+id+(module?'&m='+module:''), {
					get: 'action',
					a: 'delete'
				}, function(data){
					window.location.href=window.location.href;
				});
			}
		}
		,kill_div:false
		,kill:function(cancel){
			return;
			/*
			if (cancel) {
				if (this.kill_div) {
					this.kill_div.remove();
					this.kill_div=false;
				}
			} else {
				this.kill_div=$('<img>').attr('src',S.C.FTP_EXT+'tpls/img/1x1.gif').css({
					position:'fixed',
					width:$(window).width(),
					height:$(window).height(),
					top:0,left:0,right:0,bottom:0,zIndex:1
				}).prependTo(document.body);
			}
			*/
		}
		,getStyleObject:function(dom){
			dom=dom.get(0);
			var style;
			var returns = {};
			if (window.getComputedStyle){
				var camelize = function(a,b){
					return b.toUpperCase();
				};
				style = window.getComputedStyle(dom, null);
				for (var i=0;i<style.length;i++){
					var prop = style[i];
					var camel = prop.replace(/\-([a-z])/g, camelize);
					var val = style.getPropertyValue(prop);
					returns[camel] = val;
				}
				return returns;
			}
			if (dom.currentStyle){
				style = dom.currentStyle;
				for(var prop in style){
					returns[prop] = style[prop];
				}
				return returns;
			}
			return $(dom).css();
		}
		,full_edit:function(o,table,id,m) {
			this.kill();
			S.A.W.refresh = true;
			if (table=='content' && m) table=table+'_'+m;
			S.A.W.open('?'+Conf.URL_KEY_ADMIN+'='+table+this.params_url()+'&id='+id+(m?'&m='+m:'')+'&exact=true');
		}
		,quick_edit:function(o,column,table,id,type,module,value,langed) {
			/*
			for (eid in this.o){
				if (this.o[eid].div) {
					this.o[eid].div.remove();
					this.o[eid].css({'visibility':''});
					S.A.E.hl(this.o[eid]);
					delete this.o[eid];
				}
			}
			*/
			var eid='a-edit_n_'+this.index;
			this.o[eid]=$('#'+o);
			this.o[eid].of = this.o[eid].offset();
			/*
			if (this.o[eid].css('text-align')=='center') {
				this.o[eid].of.left=0;
			}
			*/
			/*
			
			if (S.E.cur_y+$(window).scrollTop()>this.o[eid].of.top) {
				this.o[eid].of.left = S.E.cur_x+$(window).scrollLeft()-50;
				this.o[eid].of.top = S.E.cur_y+$(window).scrollTop()-20;
			}
			*/
			this.index += 1;
			if (!value && this.r[o][7]==9) {
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=global&a=col_value'+this.params_url(),{
					table:table,
					id: id,
					column: column
				}, function(data) {
					if (!data) alert('No response');
					else if (data.error) return alert(data.error);
					var value = data.value;
					if (type==2) S.A.E.input(eid,column,table,id,module,type,value,langed);
					else if (type==3 || type==6) S.A.E.textarea(eid,column,table,id,false,module,type,value,langed);
					else if (type==5) S.A.E.editor(eid,column,table,id,true,module,type,value,langed);
					else S.A.E.editor(eid,column,table,id,false,module,type,value,langed);
					S.A.E.o[eid].css({'visibility':'hidden'});
				});
			} else {
				if (type==2) this.input(eid,column,table,id,module,type,value,langed);
				else if (type==3 || type==6) this.textarea(eid,column,table,id,false,module,type,value,langed);
				else if (type==5) this.editor(eid,column,table,id,true,module,type,value,langed);
				else this.editor(eid,column,table,id,false,module,type,value,langed);
				this.o[eid].css({'visibility':'hidden'});
			}
			return false;
		}
		,del_image:function(table,id,filename,o){
			if (confirm('Are you sure to delete "'+filename+'" file?')) {
				url = '?a-edit=quick_save&table='+table+this.params_url();
				S.A.L.json(url, {
					get: 'action',
					a: 'delete',
					id: id
				}, function(data){
					if ($(o).closest('li').length) $(o).closest('li').hide('slide');
					else $(o).closest('div').hide('slide');
				}); 
			}
		}
		,ctrlS:function(oid,e,obj) {
			if (!e||!e.keyCode) e=window.event;
			if ((e.keyCode==83&&(e.ctrlKey||e.altKey)) || (this.vars[oid].type==2&&e.keyCode==13)) {
				S.A.E.save(oid);
				e.preventDefault();
				return false;
			}
			else if (e.keyCode==27) {
				S.A.E.cancel(oid);
			}
		}
		,val:function(html) {
			html=html.replace(/&/g,'&amp;');
			return html;
			/*
			return html.replace(/ href="\/([^"]+)"/gi, function(a){
				return '?'+a.replace(/\//g,'&');
			});
			*/	
		}
		,top:function(oid){
			return this.o[oid].of.top || S.E.cur_y;	
		}
		,left:function(oid){
			return this.o[oid].of.left || S.E.cur_x;	
		}
		,div:function(oid,langed,pos) {
			return '<div'+(pos?' style="position:relative;top:'+pos+'px;"':'')+'>'+(langed?'<label class="a-edit"><img src="'+S.C.FTP_EXT+'tpls/img/flags/16/'+S.A.Conf.lang+'.png" /> <input type="checkbox" id="'+oid+'_all" style="position:relative;top:3px;" title="Save on all languages?" onclick="$(this).prev().attr(\'src\',(this.checked ? \''+S.C.FTP_EXT+'tpls/img/flags/16/un.png\' : \''+S.C.FTP_EXT+'tpls/img/flags/16/'+S.A.Conf.lang+'.png\'));"></label> ':'')+'<button type="button" class="a-edit" onclick="S.A.E.save(\''+oid+'\')"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/document-save.png" /> '+S.A.Lang.save+'</button><a href="javascript:;" class="a-edit" onclick="S.A.E.cancel(\''+oid+'\')"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/application-exit.png" /></a></div>';	
		}
		,textarea:function(oid,column,table,id,big,module,type,value,langed) {
			var width = this.o[oid].width();
			var height = this.o[oid].height();
			if (width<220) width=220;
			else if (width>500) {
				plugin=2
			}
			if (height<60) var height=60;
			else if (height>400) height = 400;
			if (big) {
				width=600;
				height=400;
			}
			if (width>600) width = 600;
			this.vars[oid]={type:type,column:column,table:table,id:id,module:module}
			var fontfamily=this.o[oid].parent().css('font-family');
			var fontsize=this.o[oid].parent().css('font-size');
			this.o[oid].div=$('<div />').html('<textarea id="'+oid+'" style="height:'+height+'px;" onkeydown="S.A.E.ctrlS(\''+oid+'\',event,this)">'+S.A.P.js2(this.val(value?value:this.o[oid].html()))+'</textarea>'+this.div(oid,langed,-10)).css({
				position:'absolute',
				top: this.top(oid),
				left: this.left(oid),			
				width: width+50,
				height: height,
				zIndex: this.index
			}).addClass('a-edit a-textarea').prependTo('body').mousedown(function(){
				$(this).css({
					zIndex: S.A.E.index++
				});
			}).focus(function(){
				$(this).css({
					zIndex: S.A.E.index++
				});
			});
			var text=$('#'+oid);
			text.resizable({
				handles: 'se',
				minWidth: width,
				minHeight: height
			});
			setTimeout(function(){text.focus().val(text.val()).focus()},200);
		}
		,input:function(oid,column,table,id,module,type,value,langed) {
			var width=parseInt(this.o[oid].width()) + 50;
			if (width<250) width = 250;
			else if (width>500) width = 500;
			else width+=50;
			this.vars[oid]={type:type,column:column,table:table,id:id,module:module}
			this.o[oid].div=$('<div />').html('<input id="'+oid+'" class="a-input" value="'+S.A.P.js2(this.val(value?value:this.o[oid].text()))+'" onkeydown="S.A.E.ctrlS(\''+oid+'\',event,this)" />'+this.div(oid,langed,0)).css({
				position:'absolute',
				top: this.top(oid)-2,
				left: this.left(oid),
				width: width+50,
				zIndex: this.index++
			}).addClass('a-edit a-input').prependTo('body').mousedown(function(){
				$(this).css('z-index',S.A.E.index++);
			});
			var text=$('#'+oid);
			setTimeout(function(){text.focus().val(text.val()).focus()},200);
		}
		,editor:function(oid,column,table,id,big,module,type,value) {
			this.kill();
			var width = this.o[oid].width();
			var height = this.o[oid].height();			
			if (height<10&&this.o[oid].parent().height()>100) height = this.o[oid].parent().height();
			var plugin = 1;
			if (width<500) width=500;
			else if (width>600) {
				plugin=2;
				if (width>900) width = 900;
			}
			if (big) {
				plugin=2;
				width=600;
				height=400;	
			}
			if (plugin==2) {
				if (height<270) var height=270;
				else if (height>500) height=500;				
			} else {
				if (height<180) var height=180;
				else if (height>400) height=400;
			}
			this.vars[oid]={type:type,column:column,table:table,id:id,module:module}

			switch (S.A.Conf.editor) {
				case 'ckeditor':
					this.o[oid].div=$('<div id="'+oid+'" name="'+oid+'" style="width:'+width+'px;height:'+height+'px" />').css({
						position:'absolute',
						zIndex: this.index,
						top: this.top(oid),
						left: this.left(oid),
						width: width,
						height: height
					}).appendTo(S.A.prepend);
					CKEDITOR.appendTo(oid,{}, value?value:this.o[oid].html());
				break;
				default:
					this.o[oid].div=$('<div />').html('<textarea id="'+oid+'" name="'+oid+'" style="width:'+width+'px;height:'+height+'px">'+(this.val(value?value:this.o[oid].html()))+'</textarea>').css({
						position:'absolute',
						zIndex: this.index,
						top: this.top(oid),
						left: this.left(oid),
						width: width,
						height: height
					}).prependTo('body');

					var settings = {
						width: width,
						height: height, 
						language: 'en',
						save_onsavecallback: function(){S.A.E.save(oid)},
						save_oncancelcallback: function(){S.A.E.cancel(oid)},
						plugins: S.A.D.get('plugins_'+plugin,1),
						theme_advanced_buttons1: S.A.D.get('buttons_1_'+plugin,1),
						theme_advanced_buttons2: S.A.D.get('buttons_2_'+plugin,1),
						theme_advanced_buttons3: S.A.D.get('buttons_3_'+plugin,1),	
						theme_advanced_toolbar_location: S.A.D.get('buttons_pos_'+plugin,1),
						setup:function(ed){
							ed.onClick.add(function(ed, e) {
								if (parseInt($('#'+oid).parent().css('z-index'))!=S.A.E.index) {					
									$('#'+oid).parent().css({
										zIndex: ++S.A.E.index
									});
								}
							});
	
						}
					}
					settings = $.extend({},S.A.D.get(S.A.Conf.editor),settings);
					$('#'+oid).unbind('tinymce').tinymce(settings);
					setTimeout(function(){
						$('#'+oid+'_toolbar1>tbody>tr>td.mceFirst').after('<td style="position:relative"><label class="mceButton"><img src="'+S.C.FTP_EXT+'tpls/img/flags/16/'+S.A.Conf.lang+'.png" style="position:relative;top:3px;left:2px" id="a-img_'+oid+'" /></label></td><td style="position:relative"><label class="mceButton mceButtonEnabled"><input type="checkbox" id="'+oid+'_all" style="width:18px;margin:4px 0 0 2px" title="Save on all languages?" onclick="$(\'#a-img_'+oid+'\').attr(\'src\',(this.checked ? \''+S.C.FTP_EXT+'tpls/img/flags/16/un.png\' : \''+S.C.FTP_EXT+'tpls/img/flags/16/'+S.A.Conf.lang+'.png\'));" /></label></td>');
					},200);
			}
			$('html, body').animate({
				scrollTop: S.A.E.o[oid].offset().top-160
			});
			return false;
		}
		,save:function(oid){
			this.kill(true);
			var html,text, url;
			if (!this.vars[oid]) return alert('Undefined id: '+oid);
			if (this.vars[oid].type==2) {
				html = $('#'+oid).val();
				text = html;
			}
			else if (this.vars[oid].type==3 || this.vars[oid].type==5 || this.vars[oid].type==6) {
				html = $('#'+oid).val();
				text = $('#'+oid).text();
			} else {
				html = $('#'+oid).html();
				text = $('#'+oid).text();
			}
			var conf = false;
			if (html && (this.vars[oid].type==2 || !conf || confirm('Are you sure to save this:\n'+text.substring(0,250)+(text.length>250?'...':'')+'\n?'))) {
				all = $('#'+oid+'_all').is(':checked') ? 1 : 0;
				this.o[oid].html(html);
				if (this.vars[oid].module=='undefined') this.vars[oid].module = '';
				if (this.vars[oid].type>=6) {
					if (this.o[oid].attr('alt')) {
						this.o[oid].attr('alt',html);
					}
					url = '?a-edit=quick_save&table='+this.vars[oid].table+this.params_url()+(this.vars[oid].module?'&module='+this.vars[oid].module:'');
				} else {
					url = '?'+Conf.URL_KEY_ADMIN+'='+this.vars[oid].table+this.params_url()+'&id='+this.vars[oid].id+(this.vars[oid].module?'&m='+this.vars[oid].module:'');	
				}
				S.A.L.json(url, {
					get: 'action',
					a: 'edit',
					column: this.vars[oid].column,
					value: html,
					all: all,
					id: this.vars[oid].id,
					type: this.vars[oid].type
				}, function(data){
					if (data&&data.text&&data.type=='shield') S.G.msg(data);
					else S.A.E.cancel(oid,true);
				}); 
			}
		}
		,cancel:function(oid,saved){
			if (S.E.ctrlKey) {
				S.E.ctrlKey=false;
				for (eid in this.o){
					if (this.o[eid].div) {
						this.cancel(eid);
						delete this.o[eid];
					}
				}
				return;
			}
			this.kill(true);
			if (this.vars[oid].type==1) {
				/*
				if ($('#mce_fullscreen_container').length) {
					$('#mce_fullscreen_container').remove();
					$('html').css({overflow:'auto'});
				}
				*/
				$('#'+oid).unbind('tinymce');
			}
			this.highlight = true;
			this.o[oid].css({'visibility':''}).show();
			if (saved && 0) {
				this.o[oid].div.fadeOut(function(){
					$(this).hide();/*Wtf happened? cant remove?*/
					S.A.E.highlight = false;
					S.A.E.o[oid].div.remove();
					S.A.E.highlight = false;
				});
			} else {
				this.o[oid].div.remove();
				S.A.E.highlight = false;
			}
			delete S.A.E[oid];
			this.hl(this.o[oid]);
		}
		,over:function(o){
			$(o).css({color:'red',cursor:'hand'});
		}
		,out:function(o){
			$(o).css({color:'inherit'});
		}
	}
	,P:{ // Parser
		js: function(s) {
			if (!s||!isNaN(s)) return s;
			return s.replace(/\n/g,'\\n').replace(/\r/g,'\\r').replace(/"/g,'&quot;').replace(/\\/g,'\\\\').replace(/\'/g,'\\\'');
		}
		,js2: function(s) {
			if (!s||!isNaN(s)) return s;
			return s.replace(/\n/g,'\n').replace(/\r/g,'\r').replace(/"/g,'&quot;');
		}
		,in_array:function(key, arr) {
			for (i in arr) {
				if (arr[i]==key) return true;	
			}
			return false;
		}
		,data:function(o) {
			var post = {_t:S.C._T};
			var arr = o.serializeArray();
			for (i in arr) {
				if (typeof(arr[i].value)=='object') {
					post[arr[i].name]={};
					for (j in arr[i].value) {
						post[arr[i].name][arr[i].value[j].name] = arr[i].value[j].value;
					}
				} else {
					post[arr[i].name]=arr[i].value;
				}
			}
			return post;
		}
		,html:function(s){
			if (!s||!isNaN(s)) return s;
			return s.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\t/g,'&nbsp;&nbsp;&nbsp;&nbsp;').replace(/\n/g,'<br />');
		}
		,secondsToTime: function(secs, toObj) {
			var h = Math.floor(secs / (60 * 60));
			var dm = secs % (60 * 60);
			var m = Math.floor(dm / 60);
			var ds = dm%60;
			var s = Math.ceil(ds);
			if (m<10) m = '0'+m;
			if (s<10) s = '0'+s;
			var obj = {
				h: h,
				m: m,
				s: s
			};
			if (!toObj) return (h>0?h+':':'')+m+':'+s;
			return obj;
		}
	}
	,D:{ // data
		get:function(key, quick) {
			var e = '';
			if (quick) {
				e = 'save,cancel,|,';
			}
			switch (key) {
				case 'tinymce4':
					// doesnt work, and it's crap, waiting for newer versions
					return {
						script_url: S.C.FTP_EXT+'tpls/js/tinymce4/tinymce.min.js',
						content_css: S.C.FTP_EXT+'tpls/'+S.C.TPL+'/css/styles.css',
						language: 'en',
						plugin_filebrowser_width: 750,
						plugin_filebrowser_height: 420,
						plugin_filebrowser_src: S.C.FTP_EXT+'tpls/js/tinymce/plugins/filebrowser/',
						file_browser_callback: 'start_file_browser'
					};
				break;
				case 'tinymce':
					return {
						script_url: S.C.FTP_EXT+'tpls/js/tinymce/tiny_mce.js',
						content_css: S.C.FTP_EXT+'tpls/css/editor.css?n='+this.version+','+S.C.FTP_EXT+'tpls/'+S.C.TPL+'/css/styles.css?n='+this.version,
						theme: 'advanced',
						width: '100%',
						skin:'o2k7',
						language: 'en',
						menubar: false,
						statusbar: false,
						accessibility_warnings: false,
						theme_advanced_toolbar_align: 'left',
						theme_advanced_resizing: false,
						theme_advanced_path: false,
						theme_advanced_resize_horizontal: 0,
						theme_advanced_statusbar_location : 'none',
						convert_urls: false,
						convert_fonts_to_spans: false,
						font_size_style_values: false,
						fix_list_elements: false,
						verify_html: false,
						
						
						valid_elements : '*[*]',
						
						valid_children : "+body[style|div],h1[div|p|strong|b],h2[div|p|strong|b],h3[div|p|strong|b]",
						paste_auto_cleanup_on_paste : false,
						
						force_br_newlines: true,
						force_p_newlines: false,
						forced_root_block: false,
						//remove_linebreaks: false,
						forced_root_block : '',
	
						fix_table_elements: true,
						fix_nesting: true,
						remove_script_host: true,
						
						entity_encoding: 'raw', // raw, named, numeric
						cleanup_on_startup: false,
						cleanup: false,
						body_class: 'editor',
						
						theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px,28px",
					    font_size_style_values: "10px,12px,13px,14px,16px,18px,20px,28px",
						
						plugin_filebrowser_width: 750,
						plugin_filebrowser_height: 420,
						plugin_filebrowser_src: S.C.FTP_EXT+'tpls/js/tinymce/plugins/filebrowser/',
						file_browser_callback: 'start_file_browser',
						/*spellchecker_languages: '+English=en,German=de,Italian=it,Spanish=es,Russian=ru,Estonian=et',*/
						extended_valid_elements: S.A.D.get('elements'),
						setup: function (ed) {
							/*
							ed.onInit.add(function (ed) {
								$('body', ed.getDoc()).blur(function (ev) {
									// blur event
								});
							});
							*/
						},

						remove_instance_callback: function(ed){
							for (win_id in S.A.W.editors) {
								for (j in S.A.W.editors[win_id]) {
									delete S.A.W.editors[win_id][j];
								}
							}
							// fucking disables input and texareas in IE
						}
					}
				break;
				case 'elements':
					return 'iframe[width|height|frameborder|scrolling|marginheight|marginwidth|src]';	
				break;
				case 'plugins_1':
					return 'filebrowser,pagebreak,style,layer,table,save,advimage,advlink,inlinepopups,media,advimage,contextmenu,paste,fullscreen,nonbreaking,xhtmlxtras,template'; /*spellchecker*/
				break;
				case 'plugins_2':
					switch (S.A.Conf.editor) {
						case 'tinymce4':
							return "filebrowser advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste moxiemanager";
						break;
						default:
							return 'filebrowser,pagebreak,style,layer,table,save,advhr,advimage,advlink,inlinepopups,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template'; /*,spellchecker*/
					}
					
				break;
				case 'buttons_1_1':
					return e+',bold,italic,underline,fontsizeselect,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,forecolor,backcolor,image,|,cleanup,template,code';
				break;
				case 'buttons_2_1':
				case 'buttons_3_1':
					return '';
				break;
				case 'buttons_1_2':/*spellchecker,|,*/
					return e+'replace,|,pastetext,pasteword,|,undo,redo,|,table,separator,image,media,|,link,unlink,anchor,|,advhr,pagebreak,nonbreaking,|,insertlayer,|,cleanup,removeformat,|,charmap,attribs,|,code,template,preview,fullscreen';
				break;
				case 'buttons_2_2': // styleselect, fontselect
					return 'styleselect,formatselect,fontsizeselect,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,outdent,indent,|,styleprops,attribs,|,forecolor,backcolor';
				break;
				case 'buttons_3_2':
					return '';
				break;
				case 'buttons_1_3':/*spellchecker,|,*/
					return e+'bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,forecolor,backcolor,|,table,separator,image,media,|,link,unlink,anchor,|,template,code,preview,fullscreen';
				break;
				case 'buttons_pos_1':
					return 'bottom';
				break;
				case 'buttons_pos_2':
					return 'top';
				break;
				case 'templates':
					return S.A.E.templates;
				break;
				case 'images':
					return ['jpeg','jpg','gif','png'];
				break;
				case 'videos':
					return ['asf','avi','divx','dv','mov','mpg','mpeg','mp4','mpv','ogm','qt','rm','vob','wmv', 'm4v','swf','flv','aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma'];
				break;
			}
		}
	}
	
	,I:{ // image
		css:0,process:false,div:0,of:0,current:'',text:false
		,load:function(re){
			
			$('a.a-thumb').each(function(index, obj) {
				try{
					if ($(obj).attr('href').match(/\.(?:jpe?g|gif|png|bmp)/i)) {
						var el = $(obj).children(0);
						if (el.is('img')) {
							$(obj).colorbox({
								title: el.attr('alt'),
								maxWidth: '95%',
								maxHeight: '95%',
								slideshowAuto: false,
								slideshow: true,
								preloading: false,
								overlayClose: true,
								opacity: 0.85,
								transition: 'elastic',
								speed: 320,
								slideshowSpeed: 4500,
								close: '<img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/dialog-close.png">',
								next: '<img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/arrow-right.png">',
								previous: '<img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/arrow-left.png">',
								slideshowStart: 'start slideshow',
								slideshowStop: 'stop slideshow',
								current: '{current} of {total} images'
							});
						}
					}
				}catch(e){}
			});
			if (!re) {
				$(document).ajaxStop(function(){
					S.A.I.load(true);
				});
			}
			/*
			$('a.a-thumb').live('click',function(){
				S.A.I.show($(this));
				return false;
			});
			*/
		}
		,hide:function(obj,_w,_h){
			if (S.A.I.process) return false;
			S.A.I.process = true;
			$(obj).parent().animate({
				top:S.A.I.of.top-$(window).scrollTop(),left:parseInt(S.A.I.of.left),width:_w,height:_h,opacity: 0.8
			}, 400, function(){
				S.A.I.process = false;
				$(this).remove();
				delete S.A.I.div;
				S.A.I.div = false;
			});
			return false;	
		}
		,show:function(o){
			if(S.A.I.process) return false;
			S.A.I.process = true;
			if (S.A.I.css&&S.A.I.div) S.A.I.div.remove();
			var bg = '#555';
			var c=o.children(0).attr('class');
			eval('var data='+c.substring(c.indexOf('{'),c.indexOf('}')+1));
			S.A.I.of=o.parent().offset();
			var _h = o.parent().height(), _w = o.parent().width();
			S.A.I.css={
				backgroundPosition: 'top',
				padding:'10px',opacity:0.8,
				overflow:'hidden',position:'fixed',zIndex: 10000,
				top:parseInt(S.A.I.of.top-$(window).scrollTop()),left:parseInt(S.A.I.of.left),
				width:parseInt(_w),height:parseInt(_h)
			}
			var t=$(window).height()/2-data.h/2;
			var l=$(window).width()/2-data.w/2;
			data.h = parseInt(data.h);
			if (data.h + 20 > $(window).height()) {
				data.h = $(window).height() - 20;
				t = 0;
			}
			var animate={width:data.w,height:data.h,top:t,left:l,opacity:1};
			var src=o.children(0).attr('src');
			if (data.p.substring(0,1)=='[') src = src.replace(/\/th3\//g,'/th1/').replace(/\/th2\//g,'/th1/').replace(/\/th4\//g,'/th1/');
			else if (data.p) src=data.p;
			if (!data.t) data.t = o.children(0).attr('alt');
			S.A.I.current = o.children(0).attr('src');
			S.A.I.div=$('<div>'+(data.t?'<div class="ui-corner-all ui-state-hover ui-widget-header ui-dialog-titlebar" style="position:absolute;left:5px;top:5px;padding:0px 5px;display:inline;font-size:14px;font-weight:bold">'+data.t+'</div>':'')+'<img src="'+S.A.I.current+'" onclick="S.A.I.hide(this,'+_w+','+_h+')" style="width:100%;height:100%;cursor:pointer"><img src="'+src+'" style="display:none;width:100%;height:100%;cursor:pointer" onclick="S.A.I.hide(this,'+_w+','+_h+')" id="a-thumb" onload="S.A.I.loaded(this)" /></div>').appendTo(S.A.prepend).draggable({
				containment: 'document'
			}).resizable()
			.addClass('ui-widget-content ui-corner-all ui-widget-header')
			.css(S.A.I.css)
			.animate(animate,function(){
				S.A.I.process=false;
				setTimeout(function(){
					S.A.I.loaded('#a-thumb',true, 1000);
				})
			});
			return false;
		}
		,loaded:function(obj, doubled) {
			setTimeout(function(){$(obj).show();$(obj).prev().hide();if (!doubled){S.A.I.loaded(obj, true)}},(doubled?500:200));
		}
	}
}