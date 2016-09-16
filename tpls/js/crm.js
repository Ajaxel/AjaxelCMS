/* -------------------
	CRM scripts
	Ajaxel v5.08
	http://ajaxel.com
   ------------------- */
CRM={
	conf:{
		tabs:{
			0: ['tab_name',true,'Tab Name'],
			1: ['disabled_tab',false,'Disabled tab']
		}
		,status:{}
	}
	,data:{
		top: 151,uploaded:0
	}
	,temp:{}
	,area:'crm'
	,tabs:false,form:false,list:false,fn:{},wins:[],index:2,reset:false,win_id:'',p:[],tp:[],name:'',win_name:'',tim:0,lengths:{},act:'',no_open:false,reload:true,tab:0,height:0,diff_obj:false,blocked:false,no_close:false,win_url:'',win_post:false,bouncing:false,opening:false,s_ul:false,s_div:false,s_down:false
	,config:function(conf,name){
		if (conf) this.conf=conf;
		this.name=name;
	}
	,ready:function(){
		if (this.name) {
			for (i in this.conf.tabs)if(this.conf.tabs[i][0]==this.name)break;
			this.select(i);
		} else {
			var i=0;
			this.select(0);
		}
		$(window).resize(function(){
			CRM.resize('resize');
		});
		this.tabs=$('#c-tabs').tabs({
			activate: function(e, ui){
				CRM.select(ui.newTab.index()-1);
				/*CRM.resize();*/
			}
			,selected: parseInt(i)
			,collapsible: false
			,cache: false
			,load: function(e, ui){
				CRM.select(ui.tab.index()-1);
				CRM.add(true);
				CRM.tab_callback();
				CRM.resize('load');
				CRM.load();
			}
		}).removeClass('ui-corner-all');
		this.tabs.children().removeClass('ui-corner-all');
		for (i in this.conf.tabs){
			if (!this.conf.tabs[i][1]) {
				this.tabs.tabs('disable',parseInt(i));
			}
		}
		this.load();
		this.init();
		this.add(true);
		this.tab_callback();
		
		$('#loading').hide();
		$('#wrapper').fadeIn();
		this.resize('ready');
	}
	,stim:0
	,select:function(index){
		/*if(!index) this.add(true);*/
		if (typeof(index)=='undefined') return alert('Index is undefined ((');
		clearTimeout(this.stim);
		if (this.conf) this.name=this.conf.tabs[index][0];
		this.win_name=this.name;
		this.tab=parseInt(index);
		this.form=$('#c-form_'+this.name);
		this.list=$('#c-list_'+this.name);
		this.stim=setTimeout(function(){
			CRM.focus('c-first_'+CRM.name);
		},300);
		this.lengths={};
	}
	,single:function(){
		if (this.form) $('.c-pager',this.form).remove();
		if (this.list) this.list.remove();
		this.list=false;
	}
	,get_height:function(){
		var h = (this.list?$('#c-content_'+this.name).height():0)+($('#c-pager_'+this.name).is(':visible')?$('#c-pager_'+this.name).height():-2)+$('#c-tabs-ul').height()+25+($('#c-middle_'+this.name).length?25:0);
		return $(window).height()-h;	
	}
	,resize:function(by){
		if (!this.list||!this.list.html()){
			this.single();
		}
		var w = $(window).width();
		if (w<1000) {
			$('#c-search').hide();
		} else {
			$('#c-search').show();
		}
		if (w<800){
			$('#wrapper').css({width:800});
		} else {
			$('#wrapper').css({width:'100%'});
		}

		this.height = CRM.get_height();
		
		$('#c-footer').css({
			position:'absolute',
			bottom:0
		});
		
		if (!this.list) {
			if ($('#c-'+this.name+'_height').length) {
				$('#c-'+this.name+'_height').css({
					height: this.height
				})
			}
			$('#c-content_'+this.name).css({
				/*
				display:'table',
				width:'100%',
				*/
				height: this.height
			});
		} else {
			this.list.css({
				height: this.height,
				'overflow-y': 'scroll'
			});
		}
		this.tab_callback_resize(by);
	}
	,calc:function(all){
		var c=this.checked();
		var o=$('#c-'+this.name+'_a-total');
		var m=c+' item'+(c==1?'':'s')+' selected';
		$('#c-'+this.name+'_a-msg').hide();
		if (all) {
			
		}
		else if (c) {
			if (!o.html()) {
				o.hide().html(m).stop().fadeIn();
			} else {
				o.html(m);
			}
		} else {
			o.fadeOut(function(){
				$(this).html('');	
			});
		}
	}
	,ch:function(obj,date){
		if(obj.checked){
			var s=$(obj).attr('id').substring(4).split('_')[0];
			var i=parseInt(s);
			var w=s.substr(-1);
			$('#c-ch'+i+(w=='Y'?'N':'Y')+'_'+this.win_id).removeAttr('checked');
			if (!$('#c-ch'+i+'d_'+this.win_id).val()) {
				$('#c-ch'+i+'d_'+this.win_id).val(this.data.date);
			}
		}
	}
	,selAll:function(obj,name,full){
		if (!full){
			if (obj.checked) {
				$('input[type=checkbox]',this.list).attr('checked', true).parent().parent().addClass('c-tick');
			} else {
				$('input[type=checkbox]',this.list).removeAttr('checked').parent().parent().removeClass('c-tick');
			}
			$('#c-'+name+'_allfull').removeAttr('checked');
		}
		else {
			$('#c-'+name+'_all').removeAttr('checked');
			$('input[type=checkbox]',this.list).removeAttr('checked').parent().parent().removeClass('c-tick');
		}
		this.calc(full);
	}
	,status:function(obj,id,arr_key,s){
		if (!obj){
			if (!this.s_ul) return;
			this.s_div.parent().removeClass('c-hl');
			this.s_div=false;
			this.s_ul.remove();
			this.s_ul=false;
			return;	
		}
		if (s) {
			s=parseInt($(obj).attr('rel'));			
			CRM.is_down=true;
			var url=S.C.HTTP_EXT+'?json&'+this.area+'&'+this.name+'&action=status',tr;
			CRM.s_down=true;
			$.ajax({
				cache: false,
				dataType: 'json',
				url: url,
				type: 'post',
				data: {id:id,status:s},
				success: function(data){
					if (data.ok){
						CRM.s_div.attr('rel',s).find('span').html(CRM.data[arr_key][s]);
						tr=CRM.s_div.parent();
						for (c in CRM.conf.status_classes) {
							tr.removeClass('c-'+CRM.conf.status_classes[c]);
						}
						tr.addClass('c-'+CRM.conf.status_classes[s]);
					}
					S.G.msg(data);
					CRM.is_down=false;
					CRM.status();
				}
			});
			return;	
		}
		this.s_down=true;
		if (S.G.ajaxel) return false;
		this.status();
		this.s_div=$(obj);
		this.s_div.parent().addClass('c-hl');
		var s=this.s_div.attr('rel'),li;
		this.s_ul=$('<ul>').addClass('c-menu').css({
			position: 'fixed',
			display: 'none',
			zIndex: 1,
			width: this.s_div.width()+20,
			top: this.s_div.offset().top+20,
			left: this.s_div.offset().left+10
		}).prependTo($(document.body));
		$(document.body).mousedown(function(){
			if (!CRM.s_down) {
				CRM.status();
				$(this).unbind('mousedown');
			}
		});

		for (i in this.data[arr_key]){
			if (s==i) continue;
			li=$('<li>').addClass('c-'+this.conf.status_classes[i]).html(this.data[arr_key][i]).attr('rel',i).mousedown(function(){
				CRM.status(this,id,arr_key,true);
			});
			this.s_ul.append(li);	
		}
		this.s_ul.slideDown('fast', function(){
			CRM.s_down=false;	
		});
	}
	,live:function(){
		$('.c-button').button();
		$('input.c-date').unbind('datepicker').datepicker({
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			minDate: null
		});
		$('.c-unsel').disableSelection();
		CRM.countdown(true, function(){
			CRM.unblock();	
		});
	}
	,load:function(){
		this.live();
		this.form.find('.c-sort').click(function(){
			CRM.sort($(this).attr('id').split('-')[2],this);
		}).parent().mousedown(function(){
			$(this).css({
				position:'relative',
				top:1,left:1
			});
		}).mouseup(function(){
			$(this).css({
				position:'static'
			})
		});
		
		this.form.live('submit',function(){
			return CRM.find();
		});
		$('#c-content_'+this.name+' .c-input').live('keyup',function(){
			if (CRM.reset) return false;
			if ($(this).hasClass('c-number')) {
				$(this).val($(this).val().replace(/[^0-9]/g,''));
			}
			n=$(this).attr('name');
			if ((this.value.length&&this.value.length!=CRM.lengths[n])||(!this.value.length&&CRM.lengths[n])) {
				CRM.lengths[n] = this.value.length;
				clearTimeout(CRM.tim);
				CRM.tim=setTimeout(function(){
					CRM.find();
				},300);
			}
		});
		$('#c-content_'+this.name+' .c-select').live('change',function(){
			CRM.find();
		});
		
	}
	,add:function(disable){
		
		if (!this.tabs) return false;
		if (disable){
			this.adding=false;
			this.tabs.tabs('disable',this.conf.tabs.length);
			/*
			$('#c-add').removeClass('ui-state-disabled').mouseover(function(){
				$(this).addClass('ui-state-hover');
			});	
			*/
		} else {
			this.adding=true;
			this.tabs.tabs('enable',this.conf.tabs.length);
			$('#c-add').unbind('click').click(function(){
				CRM.open(0,this,CRM.name);
				return false;
			});
			/*
			$('#c-add').addClass('ui-state-disabled').unbind('mouseover');
			*/
		}
	}
	,init:function(){
		$(window).keydown(function(e){
			if (!S.G.ajaxel&&(e.ctrlKey||e.altKey)&&e.keyCode==192) {
				if (CRM.windows()>1){
					win_id=CRM.win_id;
					for (_win_id in CRM.wins){
						if (_win_id!=CRM.win_id) {
							win_id=_win_id;
						}
					}
					var input=CRM.wins[win_id].win.css({
						zIndex: ++CRM.index
					}).find('.c-input:first').focus();
					input.val(input.val()).focus();
					CRM.win_id=win_id;
				}
				else if (next = CRM.next_tab(e.shiftKey)){
					CRM.tabs.tabs('select',next);
				} else {
					CRM.tabs.tabs('select',0);
				}
			}
			else if (e.ctrlKey&&e.keyCode==37) {/*<-*/
				if (CRM.p[CRM.name]) CRM.page(CRM.p[CRM.name]-1);
			}
			else if (e.ctrlKey&&e.keyCode==39) {/*->*/
				if (CRM.tp[CRM.name]>CRM.p[CRM.name]) CRM.page(CRM.p[CRM.name]+1);
			}
			else if ((e.altKey||e.ctrlKey)&&e.keyCode==38) {/*up*/
				v=$('#c-limit_'+CRM.name+' option:selected').prev().val();
				if (v){
					$('#c-limit_'+CRM.name+'').val(v);
					CRM.find();
				}
			}
			else if ((e.altKey||e.ctrlKey)&&e.keyCode==40) {/*down*/
				v=$('#c-limit_'+CRM.name+' option:selected').next().val();
				if (v){
					$('#c-limit_'+CRM.name+'').val(v);
					CRM.find();
				}
			}
			else if (e.altKey&&(e.keyCode==67||e.keyCode==81)) {/*C,Q*/
				CRM.close(e.shiftKey);
			}
			else if (!S.G.ajaxel&&CRM.adding&&e.altKey&&e.keyCode==69) {/*E*/
				var id=$('.c-active .c-link',this.list).attr('id');
				if (!id) id=$('.c-link',this.list).attr('id');
				if (id) CRM.open(id.substring(CRM.name.length+9),false,CRM.name);
			}
			else if (!S.G.ajaxel&&CRM.form&&e.altKey&&e.keyCode==82) {/*R*/
				CRM.reset=true;
				CRM.form.get(0).reset();
				CRM.find();
			}
			else if (e.altKey&&e.shiftKey&&e.keyCode==70){/*F*/
				CRM.focus('c-global-search');
			}
			else if (CRM.list&&e.altKey&&e.keyCode==70){/*F*/
				CRM.focus('c-first_'+CRM.name);
			}
			else if (!S.G.ajaxel&&CRM.list&&e.altKey&&(e.keyCode>=49&&e.keyCode<=58)){/*1-9*/
				index=e.keyCode-48;
				var id=$('.c-link',CRM.list).eq(index-1).attr('id');
				if (id) {
					id=id.substring(CRM.name.length+9);
					CRM.open(id,false,CRM.name);
					var o=$('#'+CRM.name+'_'+id).find('.c-link').addClass('c-hov');
					setTimeout(function(){
						o.removeClass('c-hov');
						delete o;
					},500);
				}
			}
			else if (!S.G.ajaxel&&CRM.adding&&e.altKey&&(e.keyCode==87||e.keyCode==65)) {/*W,A*/
				CRM.open(0,false,CRM.name);
			}
			else if (!S.G.ajaxel&&CRM.adding&&e.altKey&&e.keyCode==83) {/*S*/
				if (e.shiftKey){
					CRM.saveAll();
				}
				else if (CRM.win_id) {
					$('#c-'+CRM.win_id+'_win').submit();
				} else {
					CRM.find();
				}
			}
			else if (e.altKey&&e.keyCode==72) {/*H*/
				CRM.help();
			}
			else if (e.altKey&&e.keyCode==74) {/*J*/
				window.open('http://ajaxel.com');
			}
		});
	}
	,next_tab:function(shift){
		for (i=this.tab+((shift&&this.tab)?-1:1);i<this.conf.tabs.length;i++){
			if (CRM.conf.tabs[i][1]) return i;
		}
		return false;
	}
	,saveAll:function(){
		var win_id=false;
		for (win_id in this.wins) {}
		if (win_id) {
			CRM.reload=false;
			setTimeout(function(){
				$('#c-'+win_id+'_win').submit();
				CRM.close(win_id);
				CRM.saveAll();
			},500);
		} else {
			CRM.reload=true;	
		}
	}
	,help:function(){
		if (S.G.alerter) {
			S.G.alerter.remove();
			S.G.alerter=false;
			return false;	
		}
		var t = '';
		/*t += 'Ajaxel CRM/CMS hot keys:\n\n';\n[ALT + SHIFT + F] - move cursor to search global*/
		t += '[ALT + A] - open to add new\n[ALT + 1-9] - open beginning entry to edit\n[ALT + E] - open active or first entry again\n';
		t += '[ALT + S, ENTER] - save and close active window\n[ALT + SHIFT + S] - save and close all windows\n';
		t += '[ALT + Q] - close active window without saving\n[ALT + SHIFT + Q] - close all windows\n\n';
		t += '[ALT + F] - move cursor to search for ID field\n[ALT + R] - reset search form\n';
		t += '[CTRL + ~] - next tab\n[CTRL + SHIFT + ~] - previous tab\n[ALT + ~] - switch window or tab\n';
		t += '[CTRL + RIGHT] - next page\n[CTRL + LEFT] - previous page\n[CTRL + UP] - reduce limit\n[CTRL + DOWN] - increase limit\n\n';
		t += '[CTRL + F12] - admin panel\n[ALT + F12] - site public\n[CTRL + ALT + F12] - logout\n';
		t += '[ALT + H] - show / hide help\n[ALT + J] - product website';
		S.G.alert(t.replace(/\n/g,'<br />').replace(/\[/g,'<b>[').replace(/\]/g,']</b>'),'Ajaxel CRM/CMS hot keys:');
	}
	,pager:function(pager,id,name,no){
		if (!no) no={};
		if (!pager) return;
		h = '<table><tr><td class="c-p1"><label><input type="checkbox" id="c-'+name+'_all" onclick="CRM.selAll(this,\''+name+'\')" /></label><!-- <label><input type="checkbox" id="c-'+name+'_allfull" name="data[all]" onclick="CRM.selAll(this,\''+name+'\',true)" /> Select all '+pager.total+' found</label>-->';
		/*if (!no.mail) h += '<button type="button" id="c-'+name+'_a-mail" class="c-button" onclick="CRM.action(\'mail\');">Send a mail</button>';*/
		if (!no.del) h += '<button type="button" id="c-'+name+'_a-delete" class="c-button" onclick="CRM.action(\'delete\');">Delete</button>';
		/*if (!no.print) h += '<button type="button" id="c-'+name+'_a-print" class="c-button" onclick="CRM.action(\'print\');">Print</button>';*/
		if (!no.lock) h += '<button type="button" id="c-'+name+'_a-lock" class="c-button" onclick="CRM.action(\'lock\');">Lock</button>';
		if (!no.lock) h += '<button type="button" id="c-'+name+'_a-unlock" style="display:none" class="c-button ui-state-highlight" onclick="CRM.action(\'unlock\');">Unlock<span></span></button>';
		/*if (!no.groups) h += '<button type="button" id="c-'+name+'_a-groups" class="c-button" onclick="CRM.action(\'groups\');">Groups</button>';*/
		h += '&nbsp;&nbsp;&nbsp;<span id="c-'+name+'_a-msg"></span><span id="c-'+name+'_a-total"></span>';
		h += '</td>';
		/*
		h += '<td class="c-p3">';
		h += '<button type="button" id="c-'+name+'_a-lock" class="c-button" onclick="CRM.action(\'lock\');">Back</button>';
		h += '<button type="button" id="c-'+name+'_a-lock" class="c-button" onclick="CRM.action(\'lock\');">Back</button>';
		h += '</td>';
		*/
		h += '<td class="c-p4 c-pages">';
		for (i in pager.pages) {
			h += '<a href="javascript:;" onmouseover="$(this).addClass(\'ui-state-hover\');" onmouseout="$(this).removeClass(\'ui-state-hover\');" class="ui-state-default ui-corner-all'+(pager.pages[i]?' ui-state-active':'')+'"'+(pager.pages[i]?'':' onclick="CRM.page('+(i-1)+',\''+name+'\')"')+'>'+i+'</a>';	
		}
		h += '</td>';
		if (pager.total>10) {
			h += '<td class="c-p5"><select name="limit" onchange="CRM.find();" id="c-limit_'+name+'" class="ui-state-active">';
			var limits=[10,20,28,40,50,100,200,400,1000];
			for (i in limits) if (pager.limit>=limits[i] || pager.total>=limits[i]) h += '<option value="'+limits[i]+'"'+(pager.limit==limits[i]?' selected="selected"':'')+'>'+limits[i];
			h += '</select></td>';
		}
		h += '</tr></table>';
		$('#c-pager_'+name).html(h).find('.c-button').button();
		this.tp[name]=parseInt(pager.total_pages)-1;
		this.p[name]=parseInt(pager.page);
		$('#c-page_'+name).val(pager.page);
	}
	,tick:function(obj){
		if($(obj).find('input').is(':checked')){
			$(obj).find('input').removeAttr('checked');
			$(obj).parent().removeClass('c-tick');
		}else{
			$(obj).find('input').attr('checked','checked');	
			$(obj).parent().addClass('c-tick');
		}
		this.calc();
	}
	,close:function(win_id){
		if (win_id===true) {
			for (i in this.wins)this.close(i);
			return;	
		}
		if(!win_id)win_id=this.win_id;
		if(this.wins&&this.wins[win_id]){
			this.unblock();
			this.win_id='';
			this.wins[win_id].win.remove();
			delete this.wins[win_id];
			i=0;
			for (i in this.wins){}
			if (i) {
				this.win_id=i;
				CRM.focus('c-name_'+this.win_id);
			} else {
				CRM.focus('c-first_'+this.name);
			}
		}
	}
	,max:function(){
		if (this.wins[this.win_id].max){
			this.wins[this.win_id].win.animate({
				left:this.wins[this.win_id].left,
				width: this.wins[this.win_id].width
			});
			this.wins[this.win_id].max = false;
		} else {
			this.wins[this.win_id].win.animate({
				left: 25,
				width: $(window).width()-50
			});
			this.wins[this.win_id].max = true;
		}
	}
	,min:function(){
		
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
	,focus:function(id){
		if (!$('#'+id).length) return false;
		setTimeout(function(){
			$('#'+id).focus().val($('#'+id).val()).focus();	
		},100);
	}
	,user:function(id){
		return false;
		S.A.W.open('?'+S.C.URL_KEY_ADMIN+'=users&id='+id);
	}
	,context:function(obj){
		
	}
	,open:function(id,obj,name,post,diff_obj,diff_name){
		if (S.G.ajaxel||this.no_open){
			this.no_open=false;
			return false;
		}
		if (!name) name=this.win_name;
		if (!id) id = 0;
		var win_id=name+'_'+id;
		
		if (obj) {
			$('.c-link',this.list).parent().removeClass('c-active');
			$(obj).parent().addClass('c-active');
		}
		if (this.win_id==win_id) {
			this.wins[this.win_id].win.css({
				zIndex:++this.index
			});
			if (!id||this.locked!=id) {
				if (!this.bouncing) {
					CRM.bouncing=true;
					this.wins[this.win_id].win.effect('bounce', {times:2}, 300, function(){
						CRM.focus('c-name_'+win_id);
						CRM.bouncing=false;
					});
				}
			}
			return false;
		}
		else if (win_id in this.wins){
			this.win_id=win_id;
			this.wins[this.win_id].win.css({
				zIndex:++this.index
			});
			CRM.focus('c-name_'+win_id);
			return false;
		}
		if (this.opening) return false;
		this.opening=true;
		url = S.C.HTTP_EXT+'?window&'+this.area+'&'+name+'='+(id?id:0)+'&action=form'+(diff_obj?'&diff_id='+$(diff_obj).attr('id')+'&diff_name='+diff_name:'');
		var total=this.windows();
		$.ajax({
			cache: false,
			dataType: 'html',
			url: url,
			data: post,
			type: 'POST',
			success: function(data){
				var win=$('<div>').addClass('c-win').html(data).prependTo($(document.body));
				var height=win.height();
				var width=win.width();
				var top=$(window).height()/2-height/2+total*15;
				var left=$(window).width()/2-width/2+total*15;
				if(top>50) top-=50;
				win.css({
					visibility:'visible',
					width:width,height:height,
					top:top,left:left,
					zIndex: ++CRM.index
				}).show().draggable({
					handle:'.c-win-top, .c-buttons'
				}).mousedown(function(){
					CRM.win_url=url;
					CRM.win_post=post;
					CRM.win_id=win_id;
					$(this).css({
						zIndex:++CRM.index
					});
				}).find('form').css({
					width: '100%'
				}).resizable({
					
				}).find('.c-win-top').dblclick(function(){
					CRM.max();
				});
				
				CRM.wins[win_id]={win:win,max:false,min:false,width:width,height:height,top:top,left:left};
				CRM.win_id=win_id;
				delete win;
				/*CRM.block();*/
				CRM.callback();
				CRM.live();
				CRM.opening=false;
			}
		});
	}
	,reopen:function(){
		if (!CRM.win_url||S.G.ajaxel) return;
		$.ajax({
			cache: false,
			dataType: 'html',
			url: this.win_url,
			data: this.win_post,
			type: 'POST',
			success: function(data){
				CRM.wins[CRM.win_id].win.html(data);
				CRM.callback();
				CRM.live();
			}
		});
	}
	,windows:function(){
		var total = 0;
		for (id in this.wins)total++;
		return total;
	}
	,callback:function(){}
	,tab_callback:function(){}
	,tab_callback_resize:function(){}
	,diff_callback:function(data){
		$('#'+data.diff_id).children().html(data.diff_html);
	}
	,block:function(){
		if (this.blocked) return;
		if ($.blockUI) {
			$.blockUI({
				overlayCSS: {
					cursor: 'default',
					backgroundColor: '#7B8BB7',
					opacity: .3
				},
				fadeIn: 500,
				fadeOut: 200,
				message: false
			});
			this.blocked = true;
		}
	}
	,unblock:function(){
		if (!this.blocked) return;
		if ($.unblockUI) $.unblockUI();
		this.blocked = false;
	}
	,upload:function(field,folder,fn){
		$('#c-'+field+'_'+this.win_id).unbind('uploadify').uploadify({
			wmode			: 'transparent',
			uploader		: S.C.FTP_EXT+'tpls/js/uploadify.swf',
			script			: S.C.HTTP_EXT+'?upload=crm.'+this.win_name+'.'+field,
			cancelImg		: S.C.FTP_EXT+'tpls/img/cancel.png',
			expressInstall	: S.C.FTP_EXT+'tpls/js/expressInstall.swf',
			folder			: folder,
			queueID			: 'a-s-upload_progress',
			auto			: true,
			multi			: true,
			buttonImg		: S.C.FTP_EXT+'/tpls/img/upload.gif',
			width			: 48,
			height			: 20,
			simUploadLimit	: 1,
			queueSizeLimit	: 10,
			onComplete: function(e, queueID, fileObj, response, data) {
				if (response.substring(0,1)!='/' && response.substring(1,2)!='1') {
					if (response.substring(0,1)!='{') alert(response);
					try{
						eval('var d='+response+';');
						S.A.W.dialog(d);
					}catch(e){
						alert(response);
					}
					CRM.upload_div.hide();
				} else {
					CRM.data.uploaded++;
					if (fn) fn(response.substring(2));
				}
			},
			onError			 : function(e, ID, fileObj, errorObj) {
				CRM.data.errors.push(errorObj);
			},
			onAllComplete	 : function() {
				if(!CRM.data.uploaded) {
					CRM.upload_div.delay(1000).hide();
					alert('Nothing uploaded');
					return false;
				}
				CRM.data.uploaded=0;
				CRM.upload_div.effect('blind', {}, 300, function(){
					
				});
				CRM.upload_div.delay(1000).hide();
			},
			onSelect		: function() {
				CRM.upload_div.show('slow');
			}
		});
		this.upload_init(130);
	}
	,upload_init:function(h){
		if (this.upload_div) {
			this.upload_div.css({height:h});
			return false;
		}
		this.upload_div=$('<div class="s-upload_progress fileQueue" style="display:none" id="a-s-upload_progress" ondblclick="$(this).hide()"></div>').css({
			position: 'fixed',
			top: parseInt($(window).height()/2-h/2),
			left: parseInt($(window).width()/2-350/2),
			width: 350,
			height: h,
			overflow: 'auto',
			display: 'none',
			zIndex: 5000
		}).draggable().prependTo($(document.body));	
	}
	,file:function(file, obj){
		window.open('/files/crm/'+file);
	}
	,tinymce:function(field,height){
		var settings={
			height: height,
			language: 'en',
			script_url: '/'+S.C.FTP_EXT+'tpls/js/tiny_mce/tiny_mce.js',
			content_css: '/'+S.C.FTP_EXT+'tpls/css/editor.css',
			theme: 'advanced',
			width: '100%',
			skin:'default',
			language: 'en',
			theme_advanced_toolbar_align: 'left',
			theme_advanced_resizing: false,
			theme_advanced_path: false,
			theme_advanced_resize_horizontal: 0,
			accessibility_warnings: false,
			convert_urls: false,
			remove_script_host: true,
			entity_encoding: 'raw',
			cleanup_on_startup: true,
			cleanup: true,
			paste_use_dialog: true,
			plugin_filebrowser_width: 750,
			plugin_filebrowser_height: 420,
			plugin_filebrowser_src: '/'+S.C.FTP_EXT+'tpls/js/tiny_mce/plugins/filebrowser/',
			file_browser_callback: 'start_file_browser',
			spellchecker_languages: '+English=en,German=de,Russian=ru,Estonian=et',
			extended_valid_elements: '',
			setup: function(ed) {
				ed.addShortcut('ctrl+alt+f', 'Fullscreen', 'mceFullScreen');
			},
			plugins: 'filebrowser,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,spellchecker',
			theme_advanced_buttons1: 'spellchecker,|,replace,|,pastetext,pasteword,|,undo,redo,|,table,separator,image,media,|,link,unlink,anchor,|,advhr,pagebreak,nonbreaking,|,insertlayer,|,cleanup,removeformat,|,charmap,attribs,|,code,template,preview,fullscreen',
			theme_advanced_buttons2: 'fontselect,formatselect,fontsizeselect,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,outdent,indent,|,styleprops,attribs,|,forecolor,backcolor',
			theme_advanced_buttons3: '',
			theme_advanced_toolbar_location: 'top'
		}
	
		$('#c-'+field+'_'+this.win_id).tinymce(settings);	
	}
	,find:function(fn2){
		if (!this.list||S.G.ajaxel) return false;
		if(!this.act) this.act='find';
		this.list.animate({
			opacity: 0.1
		});
		var url=S.C.HTTP_EXT+'?json&'+this.area+'&'+this.name+'&action=find';
		S.G.loader=false;
		this.form.find('.c-sort').removeClass('c-up').removeClass('c-down');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: url,
			type: 'post',
			data: (!this.reset?this.form.serializeArray():''),
			success: function(data){
				CRM.list.stop().css({
					opacity: 1
				});
				CRM.reset=false;
				CRM.put(data,fn2);
				this.act='';
			}
		});
		return false;
	}
	,put:function(data, fn2){
		eval('var fn=CRM.fn.html_'+CRM.name+';');
		fn(data,0,CRM.name);
		if (fn2) fn2(data);
		S.G.loader=true;
		var obj=$('#c-order_'+this.name+'-'+$('#c-order_'+this.name).val());
		var by=$('#c-by_'+this.name).val();
		if (by=='DESC'){
			obj.removeClass('c-down').addClass('c-up');
		} else {
			obj.removeClass('c-up').addClass('c-down');
		}
	}
	,checked:function(){
		return $('input[type=checkbox]:checked',this.list).length;
	}
	,msg:function(msg){
		$('#c-'+this.name+'_a-total').hide().html('');
		$('#c-'+CRM.name+'_a-msg').hide().stop().fadeIn().html(msg).delay(2400).fadeOut();
	}
	,mailtpl:function(v) {
		S.G.json('?crm&mail&action=mailtpl',{name:v}, function(data){
			if (!data||!data.body) return false;
			$('#c-subject_'+CRM.win_id).val(data.subject);
			$('#c-body_'+CRM.win_id).html(data.body);
		});
	}
	,action:function(action){
		if (S.G.ajaxel) return;
		this.act='action';
		var total=this.checked();
		switch (action){
			case 'delete':
			case 'print':
			case 'mail':
			case 'lock':
				if (!total) {
					this.msg('No entries selected');
					return false;
				}
			break;
		}
		if (action=='mail'){
			this.open('',false,'mail',this.form.serializeArray());
			return;	
		}
		if (action=='lock') {
			if (total!=1) {
				this.msg('No multiple locking possibility');
				return false;
			}
		}
		var url=S.C.HTTP_EXT+'?json&'+this.area+'&'+CRM.name+'&action='+action;
		S.G.loader=false;
		$.ajax({
			cache: false,
			dataType: 'json',
			url: url,
			type: 'post',
			data: (!this.reset?this.form.serializeArray():''),
			success: function(data){
				if (data.list) CRM.put(data);
				S.G.msg(data);
			}
		});
		return false;
	}

	,counts:[],counts_o:[],tim:0
	,countdown:function(start, fn){
		if (start) {
			this.counts=[];
			this.counts_o=[];
			$('.c-countdown').each(function(){
				var ex=$(this).html().split(':');
				id=$(this).attr('id');
				if (!CRM.counts_o[id]) CRM.counts_o[id]=[];
				CRM.counts_o[id].push($(this));
				if (!CRM.counts[id]) {
					CRM.counts[id]={
						h: 	parseInt(ex[0]),
						m: 	parseInt(ex[1]),
						s: 	parseInt(ex[2]),
						fn: fn
					};
				}
			});
			clearTimeout(this.ctim);
			this.countdown();
		} else {
			for (id in this.counts) {
				var set=this.counts[id];
				set.s-=1;
				if (set.s<0){set.s=59;set.m-=1}
				if (set.m<0){set.m=59;set.h-=1}
				if (set.h<0){
					set.h=0;set.m=0;set.s=0;
					delete this.counts[id];
					if (set.fn) {
						var fn=set.fn;
						fn(set.o);
					}
				}
				for (i=0;i<this.counts_o[id].length;i++) {
					var o=this.counts_o[id][i];
					o.html(this.double(set.h)+':'+this.double(set.m)+':'+this.double(set.s));
				}
			}
			this.ctim=setTimeout(function(){
				CRM.countdown()
			},1000);
		}
	}
	,countdown_stop:function(){
		$('.c-countdown').remove();
		this.counts=[];
		this.counts_o=[];
		clearTimeout(this.ctim);
	}
	,double:function(n){
		if (n.toString().length==1) return '0'+n.toString();
		return n;
	}
	,locked:0
	,lock:function(id){
		this.locked=id;
		$('.c-row',this.form).attr('disabled','disabled').removeClass('c-tick').removeClass('c-hov');
		$('#'+this.name+'_'+id).removeAttr('disabled').addClass('c-hov').find('input[type=checkbox]').removeAttr('checked');
		$('#c-'+this.name+'_a-lock').hide();
		var s=$('#c-'+this.name+'_a-unlock').fadeIn().find('span');
		s.html(s.html()+'<span class="c-x"> ID:'+id+'</span>');
		if (!this.act||this.act=='action') this.open(id);
		this.msg('');
		this.countdown(true,function(c){
			CRM.unlock();
		});
	}
	,unlock:function(){
		$('.c-row',this.form).removeAttr('disabled').removeClass('c-active').addClass('c-hov');
		$('#c-'+this.name+'_a-unlock').hide().find('span.c-x').remove();
		$('#c-'+this.name+'_a-lock').fadeIn();
		$('#'+this.name+'_'+this.locked).addClass('c-active');
		this.countdown_stop();
		this.act='';
		this.locked=0;
	}
	// open:function(id,obj,name,post,diff_obj,diff_name){
	,mail:function(email,name,id){
		this.open(id,false,'mail',{email:email,table:name,id:id});
		/*
		location.href='mailto:'+email+'?subject=RE:%20';
		*/
		this.no_open=true;	
		setTimeout(function(){
			CRM.no_open=false;
		},500);
	}
	
	,sort:function(order,obj){
		this.act='sort';
		var obj=$(obj), by;
		$('#c-order_'+this.name).val(order);
		if (obj.hasClass('c-down')) by='DESC'; else by='ASC';
		$('#c-by_'+this.name).val(by);
		this.find();
	}
	,page:function(page){
		this.act='page';
		CRM.p[CRM.name]=page;
		$('#c-page_'+CRM.name).val(page);
		this.find();
	}
	,save:function(form,id,name){
		if (S.G.ajaxel) return;
		this.act='save';
		if (!name) name = this.win_name;
		var url=S.C.HTTP_EXT+'?json&'+this.area+'&'+name+'&action=save';
		$.ajax({
			cache: false,
			dataType: 'json',
			url: url,
			type: 'POST',
			data: $(form).serialize()+'&'+this.form.serialize(),
			success: function(data){
				if (!CRM.reload) {
					return false;
				}
				else if (data.diff_id) {
					if (!CRM.no_close) CRM.close();
					else CRM.reopen();
					CRM.diff_callback(data);
				}
				else if (data.ok) {
					if (!CRM.no_close) CRM.close();
					else CRM.reopen();
					eval('var fn=CRM.fn.html_'+name+';');
					if (typeof(fn)=='function') fn(data,id,name);
				} else {
					if (data.focus) data.focus='c-'+data.focus+'_'+name+'_'+(id?id:0);
					S.G.msg(data);	
				}
				CRM.no_close = false;
				CRM.act='';
			}
		});
		return false;
	}
}
$(document).ready(function(){
	CRM.ready();
});
