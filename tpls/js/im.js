S.IM={
	inited:false,b:false,a:false,int:0,wins:[],z:505,lto:0,o:false,mh:false,bh:false,ex:false,menu:false,folder:1,unloaded:false,win:false,subwinning:false
	,delay: 2600 /* make it 9999999 if you dont want to loop, and IM data will be loaded on page refresh */
	,b_opacity: 0.4
	,width: 360
	,height: 525
	,sizes:[8,15,20,25,50,75]
	,init:function(re){
		if (typeof(Conf)=='undefined') return false;
		if (this.inited) return false;
		this.inited=true;
		S.IM.bh = re;
		this.loading(true);
		S.G.json('?im',{action:'init'}, function(data){		
			if (!data||!data.opts) {
				S.G.alert(data);
				S.IM.loading(false);
				return alert('Cannot initialize instant messenger');	
			}
			S.IM.o=data.opts;
			
			S.IM.b=$('<div>').attr('id','im_m_board').addClass('im_m_board').html(S.IM.board(data)).appendTo($(document.body)).css({
				opacity: ((data.opts.settings.expanded||!S.IM.b_opacity)?1:S.IM.b_opacity)
			}).mouseover(function(){
				if (!S.IM.ex&&S.IM.b_opacity) {
					$(this).stop().animate({
						opacity: 1
					});
				}
			}).mouseout(function(){
				if (!S.IM.ex&&!data.opts.settings.expanded&&S.IM.b_opacity) {
					$(this).stop().animate({
						opacity: S.IM.b_opacity
					});
				}
			});
			S.IM.put(data, true);
			S.IM.int=setInterval("S.IM.loop()", S.IM.delay);
			$('#im_m_wrapper').hover(function(){
				S.IM.a = true;
			}, function(){
				S.IM.a = false;
			}).disableSelection().click(function(){
				if (!S.IM.mh) S.IM.closemenu();
			}).hover(function(){
				S.IM.bh=true;
			}, function(){
				S.IM.bh=false;
			});
			S.IM.loading(false);

			if (!re) {
				$(document.body).bind('click', function(){
					if (!S.IM.bh&&!S.IM.total()&&!S.IM.o.settings.expanded) {
						S.IM.collapse();
						S.IM.b.stop().animate({
							opacity: S.IM.b_opacity
						});
					}
					if (!S.IM.subwinning) {
						$('.im_subwin').remove();
					}
					S.IM.subwinning=false;
				});
				$(window).bind('beforeunload', function(){
					if (!S.IM.unloaded) {
						S.IM.unload();
						S.IM.unloaded=true;
					}
				});
			}
		});
	}
	,put:function(data, onInit){
		S.G.msg(data);
		if (onInit) {
			this.o=data.opts;
		}
		if (data.folders) this.o.folders=data.folders;
		if (data.board) {
			this.top(data.board);
			this.contacts(data.board);
		}
		this.context();
	}
	,invite:function(id){
		this.open(id);
	}
	,unload:function() {
		if (this.total()) {
			S.G.json('?im',{action:'unload'});
		}
	}
	,loading:function(set, showloader){
		if (set) {
			if (!showloader) S.G.loader = false;
			this.a = true;
		} else {
			if (!showloader) S.G.loader = true;
			S.G.loading = false;
			this.a = false;
		}
	}
	,is_loading:function(){
		return this.a || S.G.loading;	
	}
	,reinit:function(){
		this.inited=false;
		S.IM.b.remove();
		this.init(true);
	}
	,total:function(){
		return $('.im_win').length;
	}
	,setTotal:function(to, total) {
		if (this.wins[to]) this.wins[to].total = total;	
	}
	,i:0
	,loop:function(){
		if (S.G.ajaxel) return false;
		if (S.G.halted) {
			S.IM.loading(false);
			clearInterval(this.int);
			return false;	
		}
		if (this.is_loading()) {
			return false;
		}
		S.G.no_error=true;
		S.IM.loading(true);
		S.G.json('?im',{action:'loop', type: this.type(), folder: this.folder}, function(data) {
			S.IM.i++;
			S.IM.put(data);
			if ($('#im_alert').length&&!S.IM.total()&&S.IM.folder!=3) {
				/*
				$('#im_alert').append($('<EMBED>').attr('width','1').attr('height','1').attr('src',S.C.FTP_EXT+'tpls/js/im/alert.wav'));
				*/
				$('#im_alert').html(flash_play(S.C.FTP_EXT+'tpls/js/im/alert.swf?r='+Math.round(Math.random()*10000),'',1,1,1));
				if (S.IM.b_opacity) {
					S.IM.b.animate({
						opacity: 1
					});
				}
			}
			S.IM.loading(false);
		},false,true);
	}
	,reloop:function(){
		clearInterval(this.int);
		this.a=false;
		this.loop();
		S.IM.int=setInterval("S.IM.loop()", S.IM.delay);	
	}
	,context:function(name, to, data){
		if (!S.IM.o.settings.userid) return false;
		if (name) {
			switch (name) {
				case 'folder':
					S.IM.loading(true);
					S.G.json('?im='+to,{action:'folder', move: data, type: S.IM.type(), folder: this.folder}, function(data){
						S.IM.context();
						S.G.msg(data);
						S.IM.loading(false);
					});
				break;	
			}
			return;	
		}
		$('#im_m_contacts .im_username').bind('contextmenu', function(e){
			var o=$(this);
			S.IM.closemenu(function(){
				var to = o.attr('id').substring(9);
				var c=[{
					optgroup: true,
					label: S.IM.o.lang.move_to
				}];
				for (i in S.IM.o.folders) {
					if (i=='4' || i==S.IM.folder) continue;
					c[c.length] = {
						label: S.IM.o.folders[i],
						icon: S.IM.folderImage(i),
						func: 'S.IM.context(\'folder\', \''+to+'\', \''+i+'\');'			
					}
				}
				S.X.context(e, c, o, '#im_m_board');
			});
			return false;
		});		
	}
	,send:function(to, obj){
		this.delSm(to);
		if (!this.o.settings.on) {
			$('#im_text_'+to).show('highlight');
			return false;	
		}
		var t=$('#im_text_'+to);
		var msg=t.val();
		if (msg.length<2) {
			t.val(S.IM.o.lang.please_type).show('highlight', function(){
				$(this).val('').focus();
			});
			return;
		}
		t.val(this.o.lang.sending).attr('disabled','true');
		this.loading(true);
		$(obj).attr('disabled','true');
		S.G.json('?im='+to,{action:'send', msg: msg, type: this.type(), folder: this.folder}, function(data) {
			$('#im_list_'+to).animate({
				scrollTop: 0
			}, function(){
				S.G.msg(data);
				S.IM.loading(false);
				t.val('');
				$(obj).removeAttr('disabled');t.removeAttr('disabled');t.focus();
			});
		});
	}
	,folderImage:function(folder) {
		var img=S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/note.png';
		switch (folder) {
			case '1':img=S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/mail.png';break;// common
			case '2':img=S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/love.png';break;// favorites
			case '3':img=S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/process-stop.png';break;// ignored
			case '4':img=S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/edit-select-all.png';break;// ignored
		}
		return img;	
	}
	,setFolder:function(folder, name){
		var img=this.folderImage(folder);
		$('#im_m_folder').css({'background-image':'url(\''+img+'\')'}).html(name.replace(/\(([0-9]+)\)/g,'').substring(0,9));
		S.IM.folder=folder;
		this.reloop();
	}
	,type:function(){
		var ret='';
		if (!this.o.settings.on) return ret;
		for(to in this.wins) {
			if ($('#im_text_'+to).length) ret += to+':'+$('#im_text_'+to).val().length+'|';
		}
		return ret;
	}
	,wrapText:function(o, ot, ct) {
		var s = o[0].selectionStart; 
		var e = o[0].selectionEnd;
		o.val(o.val().substring(0, s)+ot+o.val().substring(s,e)+ct+o.val().substring(e, o.val().length)); 
		if (o[0].createTextRange){
			var sr = o[0].createTextRange(); 
			sr.collapse(true); 
			sr.moveStart('character', s); 
			sr.moveEnd('character',e-s+ot.length+ct.length); 
			sr.select();
		}
		else if(o[0].setSelectionRange){ 
			o[0].setSelectionRange(s,e+ot.length+ct.length); 
		}
		else if(o[0].selectionStart){ 
			o[0].selectionStart=s; 
			o[0].selectionEnd=e+ot.length+ct.length; 
		}
	}
	,bb:function(to, type, data, arg) {
		if (!this.o.settings.on) return false;
		var o = $('#im_text_'+to);
		if (!o.length) return false;
		switch (type) {
			case 'col_over':
				$('#im_color_top_'+to).show().css({
					background: this.color(o,data)
				});
			break;
			case 'col':
				this.wrapText(o,'['+arg+'='+this.color(o,data)+']','[/'+arg+']');
				this.delSm(to);
			break;
			case 'col_set':
				this.wrapText(o,'['+arg+'='+data+']','[/'+arg+']');
				this.delSm(to);
			break;
			case 'image':
				o.insertAtCaret('[img=http://]');
			break;
			case 'youtube':
				o.insertAtCaret('[youtube=http://]');
			break;
			case 'url':
				this.wrapText(o,'[url=http://]','[/url]');
			break;
			case 'list':
				o.insertAtCaret('[list=1]\n* item 1\n* item 2\n[/list]');
			break;
			case 'table':
				o.insertAtCaret('[table]\n- header 1 | header 2\n* cell 1 | cell 2\n* cell 1 | cell 2\n[/table]');
			break;
			case 'color_line':
			case 'color_fill':
				this.delSm(to);
				var h = '<div class="im_top" id="im_color_top_'+to+'">'+this.o.lang.color+'</div>';
				h += '<div class="im_sw_contents"><img src="'+S.C.FTP_EXT+'tpls/img/colorpicker.jpg" class="im_color" onmousemove="S.IM.bb(\''+to+'\', \'col_over\', event)" onclick="S.IM.bb(\''+to+'\', \'col\', event, \''+(type=='color_line'?'color':'highlight')+'\')" />';
				var arr = this.colors();
				h += '<select onchange="S.IM.bb(\''+to+'\', \'col_set\', this.value, \''+(type=='color_line'?'color':'highlight')+'\');this.value=\'\'"><option>';
				for (i in arr) {
					h += '<option value="#'+i+'" style="'+(type=='color_fill'?'background-':'')+'color:#'+i+'">'+arr[i];
				}
				h += '<select>';
				h += '</div>';
				this.showSm(o,to,h);
			break;
			case 'size':
				this.wrapText(o,'[size='+data+']','[/size]');
			break;
			case 'sm':
				this.wrapText(o,'',':'+data+':');
				this.delSm(to);
			break;
			case 'smile':
				this.delSm(to);
				var h = '<div class="im_top">'+this.o.lang.smilies+'</div><div class="im_sw_contents">';
				for (i in this.o.sm){
					h += '<img style="cursor:pointer;cursor:hand;margin:3px;" onclick="S.IM.bb(\''+to+'\', \'sm\', \''+this.o.sm[i]+'\');" src="'+S.C.FTP_EXT+'tpls/img/sm/'+this.o.sm[i]+'.gif" alt="'+this.o.sm[i]+'" title="'+this.o.sm[i]+'" />';
				}
				h += '</div>';
				this.showSm(o,to,h);
			break;
			default:
				this.wrapText(o,'['+type+']','[/'+type+']');
			break;
		}
	}
	,colors:function(){
		return {'F0F8FF':'AliceBlue','FAEBD7':'AntiqueWhite','00FFFF':'Aqua','7FFFD4':'Aquamarine','F0FFFF':'Azure','F5F5DC':'Beige','FFE4C4':'Bisque','000000':'Black','FFEBCD':'BlanchedAlmond','0000FF':'Blue','8A2BE2':'BlueViolet','A52A2A':'Brown','DEB887':'BurlyWood','5F9EA0':'CadetBlue','7FFF00':'Chartreuse','D2691E':'Chocolate','FF7F50':'Coral','6495ED':'CornflowerBlue','FFF8DC':'Cornsilk','DC143C':'Crimson','00FFFF':'Cyan','00008B':'DarkBlue','008B8B':'DarkCyan','B8860B':'DarkGoldenRod','A9A9A9':'DarkGray','A9A9A9':'DarkGrey','006400':'DarkGreen','BDB76B':'DarkKhaki','8B008B':'DarkMagenta','556B2F':'DarkOliveGreen','FF8C00':'Darkorange','9932CC':'DarkOrchid','8B0000':'DarkRed','E9967A':'DarkSalmon','8FBC8F':'DarkSeaGreen','483D8B':'DarkSlateBlue','2F4F4F':'DarkSlateGray','2F4F4F':'DarkSlateGrey','00CED1':'DarkTurquoise','9400D3':'DarkViolet','FF1493':'DeepPink','00BFFF':'DeepSkyBlue','696969':'DimGray','696969':'DimGrey','1E90FF':'DodgerBlue','B22222':'FireBrick','FFFAF0':'FloralWhite','228B22':'ForestGreen','FF00FF':'Fuchsia','DCDCDC':'Gainsboro','F8F8FF':'GhostWhite','FFD700':'Gold','DAA520':'GoldenRod','808080':'Gray','808080':'Grey','008000':'Green','ADFF2F':'GreenYellow','F0FFF0':'HoneyDew','FF69B4':'HotPink','CD5C5C':'IndianRed ','4B0082':'Indigo  ','FFFFF0':'Ivory','F0E68C':'Khaki','E6E6FA':'Lavender','FFF0F5':'LavenderBlush','7CFC00':'LawnGreen','FFFACD':'LemonChiffon','ADD8E6':'LightBlue','F08080':'LightCoral','E0FFFF':'LightCyan','FAFAD2':'LightGoldenRodYellow','D3D3D3':'LightGray','D3D3D3':'LightGrey','90EE90':'LightGreen','FFB6C1':'LightPink','FFA07A':'LightSalmon','20B2AA':'LightSeaGreen','87CEFA':'LightSkyBlue','778899':'LightSlateGray','778899':'LightSlateGrey','B0C4DE':'LightSteelBlue','FFFFE0':'LightYellow','00FF00':'Lime','32CD32':'LimeGreen','FAF0E6':'Linen','FF00FF':'Magenta','800000':'Maroon','66CDAA':'MediumAquaMarine','0000CD':'MediumBlue','BA55D3':'MediumOrchid','9370D8':'MediumPurple','3CB371':'MediumSeaGreen','7B68EE':'MediumSlateBlue','00FA9A':'MediumSpringGreen','48D1CC':'MediumTurquoise','C71585':'MediumVioletRed','191970':'MidnightBlue','F5FFFA':'MintCream','FFE4E1':'MistyRose','FFE4B5':'Moccasin','FFDEAD':'NavajoWhite','000080':'Navy','FDF5E6':'OldLace','808000':'Olive','6B8E23':'OliveDrab','FFA500':'Orange','FF4500':'OrangeRed','DA70D6':'Orchid','EEE8AA':'PaleGoldenRod','98FB98':'PaleGreen','AFEEEE':'PaleTurquoise','D87093':'PaleVioletRed','FFEFD5':'PapayaWhip','FFDAB9':'PeachPuff','CD853F':'Peru','FFC0CB':'Pink','DDA0DD':'Plum','B0E0E6':'PowderBlue','800080':'Purple','FF0000':'Red','BC8F8F':'RosyBrown','4169E1':'RoyalBlue','8B4513':'SaddleBrown','FA8072':'Salmon','F4A460':'SandyBrown','2E8B57':'SeaGreen','FFF5EE':'SeaShell','A0522D':'Sienna','C0C0C0':'Silver','87CEEB':'SkyBlue','6A5ACD':'SlateBlue','708090':'SlateGray','708090':'SlateGrey','FFFAFA':'Snow','00FF7F':'SpringGreen','4682B4':'SteelBlue','D2B48C':'Tan','008080':'Teal','D8BFD8':'Thistle','FF6347':'Tomato','40E0D0':'Turquoise','EE82EE':'Violet','F5DEB3':'Wheat','FFFFFF':'White','F5F5F5':'WhiteSmoke','FFFF00':'Yellow','9ACD32':'YellowGreen'};
	}
	,colors_length:function(){
		var i = 0;
		for (c in this.colors()){i++}
		return i;	
	}
	,detail:50,strhex:'0123456789abcdef'
	,color:function(o,e) {
		var x,y,pw,pd,ih,r,g,b,coef,i;
		x=e.offsetX?e.offsetX:(e.target?e.clientX-e.target.x:0);
		y=e.offsetY?e.offsetY:(e.target?e.clientY-e.target.y:0);
		pw=300/6;
		pd=this.detail/2;
		ih=150;
		r=(x>=0)*(x<pw)*255+(x>=pw)*(x<2*pw)*(2*255-x*255/pw)+(x>=4*pw)*(x<5*pw)*(-4*255+x*255/pw)+(x>=5*pw)*(x<6*pw)*255;
		g=(x>=0)*(x<pw)*(x*255/pw)+(x>=pw)*(x<3*pw)*255	+(x>=3*pw)*(x<4*pw)*(4*255-x*255/pw);
		b=(x>=2*pw)*(x<3*pw)*(-2*255+x*255/pw)+(x>=3*pw)*(x<5*pw)*255+(x>=5*pw)*(x<6*pw)*(6*255-x*255/pw);
		coef=(ih-y)/ih;
		r=128+(r-128)*coef;g=128+(g-128)*coef;b=128+(b-128)*coef;
		return '#'+this.dechex(r)+this.dechex(g)+this.dechex(b);
	},
	dechex:function(n) {
		return this.strhex.charAt(Math.floor(n/16))+this.strhex.charAt(n%16);
	}
	,showSm:function(o,to,h){
		this.subwinning=true;
		this.wins[to].sm = $('<div>').addClass('im_subwin').css({
			left: this.wins[to].s.w2 + this.width-194,
			top: this.wins[to].s.h2-270
		}).html(h).prependTo(o.parent()).show().draggable({
			handle: '.im_top',
			containment: '#im_list_'+to
		}).disableSelection().click(function(){
			S.IM.delSm(to);
		});
	}
	,delSm:function(to){
		if (this.wins[to].sm) {
			this.wins[to].sm.remove();
			this.wins[to].sm=false;
		}
	}
	,click:function(to){
		if(to==this.lto) return false;
		this.lto=to;
		var c=S.IM.wins[to];
		c.div.css({
			zIndex: ++this.z
		});
		$('#im_text_'+to).focus();
	}
	,open:function(to, obj){
		if (S.G.ajaxel) return false;
		if(this.wins[to]){
			return this.click(to);
		}
		var html=this.opts(false,'loading');this.loading(true);
		this.wins[to]={i:0};
		if (obj) {
			var o=$(obj);
		} else {
			var o=$(document.body);
		}
		var doc=$(window),of=o.offset();
		var w1=parseInt(o.width()),h1=parseInt(o.height()),w2=this.width,h2=this.height;
		var t1=parseInt(of.top),l1=parseInt(of.left);
		var t2=parseInt(doc.height())/2-h2/2-40,l2=parseInt(doc.width())/2-w2/2;
		this.z++;
		var total = this.total();
		t2+=total*15-50;l2+=total*15-50;
		if (t2<0) t2=0;if(l2<0) l2=0;
		this.wins[to].div = $('<div>').addClass('im_win').attr('id','im_win_'+to).css({
			position: 'fixed',
			zIndex: this.z
			/*
			width: 100,
			height: 50,
			top:t1,
			left:l1
			*/
		}).prependTo($(document.body)).mousedown(function(){
			S.IM.click(to);
		}).css({
			opacity: 0.4,
			width: w2,
			height: h2,
			top: t2,
			left: l2
		},'slow').html(html).keydown(function(e){
			S.IM.keydown(to, e);
		}).hover(function(){
			S.IM.bh=true;
		}, function(){
			S.IM.bh=false;
		});/*.css({
			opacity: 0.2
		}).hover(function(){
			$(this).animate({
				opacity:1	
			}, function(){
				$(this).css({opacity:''});	
			})
		}, function(){
			$(this).animate({
				opacity: 0.2
			})
		});*/
		
		this.wins[to].s={h1:h1,w1:w1,t1:t1,l1:l1,h2:h2,w2:w2,t2:t2,l2:l2}
		this.wins[to].sm=false;
		this.load(to);
	}
	,load:function(to, re) {
		S.G.json('?im='+to,{action:'open',mini:this.mini,folder:this.folder}, function(data) {
			if (!data || data.error) {
				S.G.msg({text:(data?data.error:'Undefined error'), type: 'stop', delay: (data?data.delay:2000)});
				S.IM.wins[to].div.remove();
				delete S.IM.wins[to];
				return;	
			}
			if (re) {
				$('#im_list_'+to).children().remove().end();
				S.G.msg(data);
				S.IM.loading(false);
				S.IM.add(to, data.data, true);
			} else {
				S.IM.wins[to].div.css({
					opacity: 1
				});
				S.G.msg(data);
				S.IM.loading(false);
				S.IM.add(to, data.data, true);
				S.IM.wins[to].div.draggable({
					handle: '#im_top_'+to,
					start: function(e, ui) {
						S.IM.wins[to].div.css({opacity:0.4});
						$('#im_list_'+to).css({visibility:'hidden'});
					},
					stop: function(e,ui){
						var c=S.IM.wins[to];
						c.s.t2=ui.position.top;
						c.s.l2=ui.position.left;
						/*S.IM.wins[to].div.animate({opacity:1}, function(){*/
							S.IM.wins[to].div.css({opacity:''});
							$('#im_list_'+to).css({visibility:''});
						/*});*/
					},
					containment: 'document'
				}).resizable(S.IM.opts(to, 'resizable')).find('.im_bbcode').html(S.IM.opts(to,'bb_code'));
				$('#im_top_'+to).dblclick(function(){
					S.IM.maximize(to);
				});
			}
			if (S.IM.o.settings.on) {
				$('#im_text_'+to).focus();
			} else {
				$('#im_text_'+to).val(S.IM.o.lang.switched_off).attr('disabled','true');	
			}
		});
	}
	,keydown:function(to, e){
		if (e.keyCode==13&&e.ctrlKey) this.send(to);
		else if (e.keyCode==27) this.close(to);	
	}
	,board:function(data){
		var h = '<div id="im_m_wrapper"><div id="im_m_hide"'+(!this.o.settings.expanded?' style="display:none"':'')+'>';
		h += '<table class="im_top"><tr class="im_top im_title" onclick="S.IM.toggle()"><td colspan="3"><img id="im_m_status_icon" src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/status/user-'+(!this.o.settings.on?'offline':'online')+'.png" /> IM: ('+this.o.settings.user_name+')</td></tr>';
		h += '<tr class="im_buttons">';
		if (this.o.settings.userid) {
			h += '<td onclick="S.IM.folders(this)" id="im_m_folder" class="im_folders">'+this.o.lang.common+'</td>';
			/*h += '<td onclick="S.IM.userlist(this)" class="im_userlist">'+this.o.lang.userlist+'</td>';*/
		}else{
			h += '<td colspan="2" style="width:66%" onclick="alert(\''+this.o.lang.login_notify+'\')"> </td>';
		}
		h += '<td onclick="S.IM.settings(this)" class="im_settings">'+this.o.lang.settings+'</td>';
		h += '</tr></table><div id="im_m_contacts"></div></div><div id="im_m_top" onclick="S.IM.toggle()"></div></div>';
		return h;
	}
	,contacts:function(data){
		var h = '', m;
		if (data.total) {
			h += '<table class="im_list">';
			for (i in data.list){
				m=data.list[i];
				h += '<tr class="im_row '+(m.online?'im_online':'im_offline')+'"><td class="im_pic"><div>';
				if (m.user.pic) {
					h += '<img src="'+S.C.FTP_EXT+''+m.user.pic.th3+'" alt="'+m.user.login+(m.user.age?' ('+m.user.age+')':'')+'" />';
				} else {
					h += '<img src="'+S.C.FTP_EXT+'tpls/img/no-photo.png" alt="'+this.o.lang.no_photo+'" />';
				}
				h += '</div></td><td class="im_username" id="username_'+m.to+'" onclick="S.IM.open(\''+m.to+'\', this)">';
				h += (m.user.genre?'<img src="'+S.C.FTP_EXT+'tpls/img/'+(m.user.genre=='F'?this.o.lang.female:this.o.lang.male)+'.png" alt="'+(m.user.genre=='F'?'Female':'Male')+'" title="'+(m.user.genre=='F'?this.o.lang.female:this.o.lang.male)+'" width="16" height="16" /> ':'')+m.user.login+(m.user.age?' <span class="im_age">('+m.user.age+')</span>':'');
				h += '</td><td class="im_totals">'+(m.total_new>0?'<b>'+m.total_new+'</b><br />':'')+m.total+'</td></tr>';
			}
			h += '</table>';
		}else{
			if (this.o.settings.online){
				h += '<div class="im_no_contacts">'+this.o.lang.no_online+'</div>';
			}else{
				h += '<div class="im_no_contacts">'+this.o.lang.no_contacts+'</div>';
			}
		}
		$('#im_m_contacts').html(h);
	}
	,top:function(data){
		var h = '<table><tr><td width="1%"><a class="im_init"></a></td><td>'+data.online_text+'. ';
		if (!this.o.settings.on) {
			h += '<span class="im_off">'+this.o.lang.off_mode+'</span>';
		}else{
			if (data.new_msg) {
				h +='<span class="im_new">'+data.new_msg_text+'</span>';
				if (data.new_prev<data.new_msg && this.o.settings.sound){
					h += '<span id="im_alert"></span>';
				}
			}else{
				h += this.o.lang.no_new_msg;
			}
		}
		h += '</td></tr></table>';
		$('#im_m_top').html(h);
	}
	,status:function(data){
		
	}
	,first:function(to, data){
		$('<li class="im_me im_unread" id="im_'+data.id+'">').html(this.opts(to, 'im_row', data, false)).prependTo($('#im_list_'+to+'')).hide().slideDown(); 	
	}
	,add:function(to, data, start){
		if (start) {
			var html = '';
			for (i in data){
				html += S.IM.opts(to, 'im_row', data[i]);
				this.wins[to].i++;
			}
			if (this.wins[to].i<this.wins[to].total) {
				html += S.IM.opts(to, 'im_more');
			}
			$('#im_list_'+to).html(html).show();
		} else {
			for (i in data){
				data[i].cur=true;
				$('<li id="im_'+data[i].id+'" class="'+(data[i].read?'im_read':'im_unread')+(data[i].my?' im_me':' im_him')+'">').html(S.IM.opts(to, 'im_row', data[i])).appendTo($('#im_list_'+to));
				this.wins[to].i++;
			}
			if (this.wins[to].i<this.wins[to].total) {
				$('<li class="im_more">').html(S.IM.opts(to, 'im_more', true)).appendTo($('#im_list_'+to));
			}
		}
	}
	,fill:function(to, data) {
		var a;
		for (i in data.data){
			a=data.data[i];
			a.cur=true;
			$('<li id="im_'+a.id+'" class="'+(a.read?'im_read':'im_unread')+(a.my?' im_me':' im_him')+'">').html(S.IM.opts(to, 'im_row', a)).prependTo($('#im_list_'+to)).hide().slideDown();
			this.wins[to].i++;
			this.wins[to].total++;
		}
		$('#im_more_'+to).html(this.opts(to, 'im_more', true));
		for (i in data.read) {
			$('#im_'+data.read[i]).removeClass('im_unread').addClass('im_read');
		}
		$('#im_typing_'+to).html((data.typing>0 && !data.data.length>0?this.o.lang.typing.replace('%1',data.login)+' ('+data.typing+')':''));
		$('#im_status_'+to).html(data.status);
	}
	,opts: function(to, s, data){
		switch(s){
			case 'resizable':
				return {
					minWidth: 300,
					minHeight: 350,
					stop: function(e, ui) {
						var c=S.IM.wins[to];
						c.s.t2=ui.position.top;
						c.s.l2=ui.position.left;
						c.s.w2=ui.size.width;
						c.s.h2=ui.size.height;
					}
				}
			break;
			case 'im_row':
				var h = '';
				if (!data.cur) h += '<li id="im_'+data.id+'" class="'+(data.read?'im_read':'im_unread')+(data.my?' im_me':' im_him')+'">';
				h += '<div class="im_time">'+data.time+'</div><div class="im_msg">'+data.msg+'</div>';
				if (!data.cur) h += '</li>';
				return h;
			break;
			case 'im_more':
				if (!this.wins[to]) return '';
				return (!data?'<li class="im_more" id="im_more_'+to+'">':'')+'<button type="button" onclick="S.IM.more(\''+to+'\', this);">'+this.o.lang.more+' '+this.wins[to].i+'/'+this.wins[to].total+'</button>'+(!data?'</li>':'');
			break;
			case 'folders':
				var h = '';
				for (i in S.IM.o.folders) {
					h += '<li onclick="S.IM.setFolder(\''+i+'\',\''+S.IM.o.folders[i]+'\')"><img src="'+S.IM.folderImage(i)+'" /> '+S.IM.o.folders[i]+'</li>';
				}
				return h;
			break;
			case 'bb_code':
				var h = '';
				var p = S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/';
				h += '<img src="'+p+'format-text-bold.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'b\')" />';
				h += '<img src="'+p+'format-text-italic.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'i\')" />';
				h += '<img src="'+p+'format-text-underline.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'u\')" />';
				h += '<img src="'+p+'format-text-strikethrough.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'s\')" />';
				h += '<img src="'+p+'format-text-subscript.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'sub\')" />';
				h += '<img src="'+p+'format-text-superscript.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'sup\')" />';
				h += '<img src="'+p+'format-list-ordered.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'list\')" />';
				h += '<img src="'+p+'table.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'table\')" />';
				h += '<select onchange="S.IM.bb(\''+to+'\',\'size\', this.value);this.value=\'\'"><option value="">size</option>';
				for (i in this.sizes) {
					h += '<option value='+this.sizes[i]+'>'+this.sizes[i]+'px</option>';
				}
				h += '</select>';
				h += '<select onchange="S.IM.bb(\''+to+'\', \'h\'+this.value, event);this.value=\'\'"><option value="">H</option>';
				for (i=1;i<=6;i++) {
					h += '<option value="'+i+'">'+i+'</option>';
				}
				h += '<select>';
				h += '<img src="'+p+'color-line.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'color_line\')" />';
				h += '<img src="'+p+'color-fill.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'color_fill\')" />';
				h += '<img src="'+p+'fileview-preview.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'image\')" />';
				h += '<img src="'+p+'go-home.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'url\')" />';
				h += '<img src="'+p+'smiley.png" unselectable=on onclick="S.IM.bb(\''+to+'\',\'smile\')" />';
				return h;
			break;
			case 'loading':
				return '<table width="100%"><tr><td style="text-align:center"><img src="'+S.C.FTP_EXT+'tpls/img/loading/loading51.gif" alt="Loading.." /></td></tr></table>'
			break;
		}
	}
	,maximize:function(to){
		var c=S.IM.wins[to];
		if(c.maximized) {
			$('#im_m_hide').show('blind', {direction:'top'}, 200);
			c.div.resizable(S.IM.opts(to, 'resizable'));
			c.div.css({width: c.s.w2,left: c.s.l2,top: c.s.t2,height: c.s.h2});
			c.maximized = false;
			$('html, body').css({
				overflow: ''
			});
			$(window).unbind('resize');
		} else {
			$('html, body').css({
				overflow: 'hidden'
			});
			$('#im_m_hide').hide('blind', {direction:'top'}, 200);
			c.div.resizable('destroy');
			var win=$(window);
			c.div.css({width: win.width()-20,left: 10,top: 10,height: win.height()-40});
			c.maximized = true;
			$(window).resize(function(){
				if(c.maximized) {
					var win=$(window);
					c.div.css({width: win.width()-20,left: 10,top: 10,height: win.height()-40});
				}
			});
		}
	}
	,dock:function(to){
		
	}
	,close:function(to){
		S.IM.bh = false;
		if (to) {
			S.IM.loading(true);
			S.G.json('?im='+to,{action:'close', type: this.type(), folder: this.folder}, function(data) {
				S.G.msg(data);
				S.IM.loading(false);
			});
			if (S.IM.wins[to]) {
				S.IM.wins[to].div.remove();
				delete S.IM.wins[to];
				/*
				S.IM.wins[to].div.fadeOut(function(){
					$(this).remove();
					delete S.IM.wins[to];
				});
				*/
			}
		} else {
			$('.im_win').remove();
		}
	}
	,toggle:function(){
		this.ex=!this.ex;
		if (!this.ex) this.closemenu(false,true);
		$('#im_m_hide').toggle('blind');	
	}
	,expand:function(){
		if (this.ex) return false;
		$('#im_m_hide').show('blind', {direction:'top'}, 200);
		this.ex=true;
	}
	,collapse:function(){
		if (!this.ex) return false;
		$('#im_m_hide').hide('blind', {}, 200);
		this.ex=false;
		this.closemenu(false, true);
	}
	,more:function(to, obj){
		$(obj).attr('disabled','true');
		S.G.json('?im='+to,{action:'more'}, function(data) {
			$(obj).parent().slideUp(function(){$(this).remove()});
			if (data.data.length) {
				S.IM.add(to, data.data, false);
				/*
				$('#im_list_'+to).animate({
					scrollTop: $('#im_list_'+to).scrollTop()+200
				});
				*/
			}
		});
	}
	,abuse:function(to, obj){
		S.IM.popup('abuse', obj, to);
	}
	,sendAbuse:function(form){
		var data=$(form).serialize();
		S.IM.closepopup();
		S.G.json('?im='+to,data, function(data){
			S.G.msg(data);
		});
	}
	,block:function(to, obj){
		S.G.confirm(this.o.lang.block_text,this.o.lang.block_title, function(){
			S.IM.loading(true);
			S.G.json('?im='+to,{action:'block',type: S.IM.type(), folder: this.folder}, function(data){
				S.G.msg(data);
				S.IM.loading(false);
			});
		});
	}
	,unblock:function(to, obj){
		S.G.confirm(this.o.lang.unblock_text,this.o.lang.unblock_title, function(){
			S.IM.loading(true);
			S.G.json('?im='+to,{action:'unblock',type: S.IM.type(), folder: this.folder}, function(data){
				S.G.msg(data);
				S.IM.loading(false);
			});
		});
	}
	,set:function(type, obj){
		var name,val;
		switch (type) {
			case 'off':
				S.IM.o.settings.on=false;
				name='on';val='N';
				$('.im_text').val(S.IM.o.lang.switched_off).attr('disabled','true');
				$('#im_m_status_icon').attr('src', $('#im_m_status_icon').attr('src').replace('online','offline'));
			break;
			case 'on':
				S.IM.o.settings.on=true;
				name='on';val='Y';
				$('.im_text').val('').removeAttr('disabled');
				$('#im_m_status_icon').attr('src', $('#im_m_status_icon').attr('src').replace('offline','online'));
			break;
			case 'sound':
				this.o.settings.sound=obj.checked;
				name='sound';val=(obj.checked?'Y':'N');
			break;
			case 'online':
				this.o.settings.online=obj.checked;
				name='online';val=(obj.checked?'Y':'N');
			break;
			case 'expanded':
				this.o.settings.expanded=obj.checked;
				name='expanded';val=(obj.checked?'Y':'N');
				if (val=='N') {
					$('#im_m_hide').toggle('blind');
					this.ex=false;
				}
			break;
			case 'help':
				this.popup('help', obj);
			break;
			case 'about':
				this.popup('about', obj);
			break;
		}
		S.IM.loading(true,true);
		S.G.json('?im',{action:'set',name:name,val:val,folder: this.folder}, function(data){
			S.IM.put(data);
			S.IM.loading(false,true);
		});
	}
	,settings:function(obj){
		this.closemenu(function(){
			var html = '';
			if (S.IM.o.settings.userid) {
				html += (S.IM.o.settings.on?'<li onclick="S.IM.set(\'off\', false)"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/status/user-offline.png" alt="" /> '+S.IM.o.lang.off+'</li>':'<li onclick="S.IM.set(\'on\', false)"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/status/user-online.png" alt="" /> '+S.IM.o.lang.on+'</li>')+'<li><input type="checkbox" id="im_chk_sound" onclick="S.IM.set(\'sound\', this)"'+(S.IM.o.settings.sound?' checked="checked"':'')+'><label for="im_chk_sound"> '+S.IM.o.lang.sound+'</label></li><li><input type="checkbox" id="im_chk_online" onclick="S.IM.set(\'online\', this)"'+(S.IM.o.settings.online?' checked="checked"':'')+'><label for="im_chk_online"> '+S.IM.o.lang.online+'</label></li><li><input type="checkbox" id="im_chk_expanded" onclick="S.IM.set(\'expanded\', this)"'+(S.IM.o.settings.expanded?' checked="checked"':'')+'><label for="im_chk_expanded"> '+S.IM.o.lang.expanded+'</label></li>';
			}
			html += '<li onclick="S.IM.set(\'help\')"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/apps/khelpcenter.png" alt="" /> '+S.IM.o.lang.help+'</li><li onclick="S.IM.set(\'about\')"><img src="'+S.C.FTP_EXT+'tpls/img/oxygen/16x16/actions/help-about.png" alt="" /> '+S.IM.o.lang.about+'</li>';
			S.IM.openmenu('settings', obj, html, -51);
		});
	}
	,folders:function(obj){
		this.closemenu(function(){
			var html = S.IM.opts(false, 'folders');
			S.IM.openmenu('folders', obj, html, -1);
		});
	}
	
	,openmenu:function(name, obj, html, left) {
		var o=$(obj);
		S.IM.bh=true;
		this.mh=true;
		var of=o.offset();var t=of.top-$(window).scrollTop();var l=of.left;
		this.menu=$('<ul>').addClass('im_menu').html(html).css({
			position: 'fixed',
			top: t+18,
			display: 'none',
			left: l+left
		}).appendTo($('#im_m_wrapper')).slideDown('fast').mouseout(function(){
			S.IM.mh=false;
		}).click(function(){
			S.IM.closemenu();
		});
		$(obj).addClass('im_hl');
	}

	,closemenu:function(fn,quick){
		$('#im_m_folder').parent().children().removeClass('im_hl');
		if (!this.menu) return fn?fn():false;
		if (quick) {
			this.menu.remove();
			S.IM.menu=false;
			if (fn) fn();
		} else {
			this.menu.slideUp('fast',function(){
				$(this).remove();
				S.IM.menu=false;
				if(fn) fn();
			});
		}
	}
	
	,userlist:function(obj){
		this.closemenu(function(){
			S.IM.popup('userlist', obj);
		});
	}
	,popup:function(name, obj, to) {
		$(obj).attr('disabled','true');
		this.closepopup(function(){
			S.IM.loading(true);
			var w1=70,h1=70;
			var t1=$(window).height() / 2 - h1 / 2;
			var l1=$(window).width() / 2 - w1 / 2;
			S.IM.win = $('<div>').addClass('im_popup').css({
				width: w1,
				height: h1,
				top: t1,
				left: l1,
				position: 'fixed'
			}).html('<div class="im_top">Loading..</div>').prependTo($(document.body)).hover(function(){
				S.IM.bh=true;
			}, function(){
				S.IM.bh=false;
			});
			S.G.json('?im'+(to?'='+to:''), {action: 'popup', win: name, folder: S.IM.folder}, function(data) {
				S.IM.loading(false);
				var t2=$(window).height() / 2 - data.height / 2 - 40;
				var l2=$(window).width() / 2 - data.width / 2;
				S.IM.win.animate({
					width: data.width,
					height: data.height,
					top: t2,
					left: l2
				}, function(){
					$(this).html(data.html).draggable({
						handle: '.im_top'
					}).find('.im_top .im_close').click(function(){
						S.IM.closepopup();
					});
					$(obj).removeAttr('disabled');
				});
			});
		});
	}
	,closepopup:function(fn){
		if (this.win) {
			this.win.fadeOut(function(){
				$(this).remove();
				$('.im_popup').remove();
				S.IM.win = false;
				S.IM.bh=false;
				if (fn) fn();
			});
		} else if (fn) fn();
	}
}