/* 
	Ajaxel slots
	Made by Ajaxel CMS developer
*/

S.L={
	path:'/tpls/'+S.C.TPL+'/images/slots/', what: 'COIN', what2: 'AJAXEL POINT', what3: 'POINT', whats: 'S',
	line_num:9, clicked:false, spinning:false, was:[], sounds:[], play:{}, now:false, int:0, first:18, second:5, height:93,
	blinked:false, called:false, muted:false, sound_ext:'mp3',
	coins_range:[1,2,5,10/*,20,50,100,200,500,1000,5000,10000*/],coin_num:0,coins:1,credits:0,by_nuggets:false,mobile:false,
	
	init:function(fake_data, path, what, what2, what3, whats){
		if (typeof(path)!='undefined') this.path = path;
		if (typeof(what)!='undefined') this.what = what;
		if (typeof(what2)!='undefined') this.what2 = what2;
		if (typeof(what3)!='undefined') this.what3=what3;
		if (typeof(whats)!='undefined') this.whats=whats;
		var h = '', h1 = '', h2 = '', h3 = '';
		for (i=0;i<5;i++) h += '<li class="slots_barrel_'+i+'"></li>';
		for (i=1;i<=9;i++) {
			h1 += '<p class="slots_cir c'+i+'"></p>';
			h2 += '<img src="'+this.path+'line'+i+'.png">';
		//	h3 += '<td><button type="button" class="btn'+i+'" id="slots_line_'+i+'" onclick="S.L.lines('+i+')">'+i+' line'+(i!=1?'s':'')+'</button></td>';
		}
		
		h3 += '<td>';
		h3 += '<button type="button" id="slots_lines_rem" onclick="S.L.lines_ch(-1)">&lt;</button>';
		h3 += '<input type="text" value="9" id="slots_lines" readonly>';
		h3 += '<button type="button" id="slots_lines_add" onclick="S.L.lines_ch(+1)" class="slots_disabled">&gt;</button>';
		h3 += '</td>';
		
		h3 += '<td style="text-align:right">';
		h3 += '<button type="button" id="slots_coins_rem" onclick="S.L.coins_ch(-1)" class="slots_disabled">--</button>';
		h3 += '<input type="text" value="1 coin" id="slots_coins" readonly>';
		h3 += '<button type="button" id="slots_coins_add" onclick="S.L.coins_ch(+1)">++</button>';
		h3 += '</td>';
		
		$('#slots_barrels ol').html(h);
		$('#slots_barrels .slots_left').html(h1);
		$('#slots_barrels .slots_lines').html(h2);
		$('#slots_buttons').html('<table cellspacing="0"><tr>'+h3+'</tr></table>');
		S.L.msg('SPIN TO WIN '+this.what2+'S!');
		this.game(fake_data, true);
		this.set(fake_data);
		var sounds=['bonus','coinwin','loose','spin','win','start','stop'];
		this.mobile=S.C.DEVICE=='mobile' || S.C.DEVICE=='tablet';
		if (this.mobile) {
			this.sound_ext = 'ogg';
		} else {
			if ($.browser.safari || ($.browser.mozilla && parseInt($.browser.version)<10)) this.sound_ext = 'wav';
		}
		for (i=0;i<sounds.length;i++){
			$('<img>').attr('src',this.path+sounds[i]+'.'+this.sound_ext).css({width: 1,height: 1}).appendTo($('#slots'));
			if (this.mobile) $('<img>').attr('src',this.path+sounds[i]+'.mp3').css({width: 1,height: 1}).appendTo($('#slots'));
		}
		$('#slots').disableSelection();
	}
	,mute:function(o){
		if (this.muted) {
			this.muted = false;
			$(o).removeClass('slots_muted');
		}
		else {
			this.muted = true;
			$(o).addClass('slots_muted');
		}
	}
	,sound:function(s,vol,loop){
		if (this.muted) return false;
		var a = {append: '#slots_sounds'};
		if (vol) a.volume = vol;
		if (loop) a.loop = true;
		$.sound.play(this.path+s+'.'+this.sound_ext, a);
	}
	,msg:function(msg) {
		$('#slots_win').fadeOut('fast',function(){
			$(this).html(msg).fadeIn('fast');
		});
		return $('#slots_win');
	}
	,spin:function(free) {
		if (this.clicked) return false;
		$('#slots_barrels button').removeClass('active').addClass('clicked');
		this.clicked=true;
		$('#slots_hl').stop();
		$('#slots_sounds').html('');
		if (!free) {
			S.L.msg('GOOD LUCK!');
			if (this.by_nuggets && parseInt($('#slots_nuggets').html().replace(/,/g,''))>=this.line_num * this.coins) {
				$('#slots_nuggets').html((parseInt($('#slots_nuggets').html().replace(/,/g,''))-this.line_num * this.coins).toMoney(0,'',','));
			}
			else if (!this.by_nuggets && parseInt($('#slots_credits').html().replace(/,/g,''))>=this.line_num * this.coins) {
				$('#slots_credits').html((parseInt($('#slots_credits').html().replace(/,/g,''))-this.line_num * this.coins).toMoney(0,'',','));
			} else {
				S.L.msg('NO MORE '+this.what+this.whats).fadeOut(function(){
					$(this).fadeIn(function(){
						$(this).fadeOut(function() {
							$(this).html('COME BACK TOMORROW!').fadeOut(function(){
								$(this).fadeIn();
							});
						});
					});
				});
			}
			
		} else {
			S.L.msg('FREE SPIN GAME, LEFT: '+(free-1)+'');
			$('#slots_free').html(parseInt($('#slots_free').html())-1);
		}
		$('#slots_barrels ul li').each(function(){
			$(this).stop().animate({
				backgroundColor: '#fff'
			}, 'fast' , 'swing');
		});

		$('#slots_buttons button').removeClass('active').addClass('clicked');
		$('#slots_double').hide();
		$('#slots_barrels .slots_lines img').stop().fadeOut('fast');
		$('#slots_barrels .slots_left .slots_cir').stop().fadeIn('fast');
		
		this.spinning=true;
		var j = z = y = 0;
		this.now = false;
		var div = [];
		var fin = [];
		this.blinked=[];
		
		var easie = ['Expo','Cubic','Circ','Quart','Quint']
		
		if (!this.mobile) S.L.sound('spin',-500,100);
		$('#slots_barrels ul li').each(function(){
			var o=this;
			setTimeout(function() {
				div[y]=$('<div>').html($(o).html());
				var d = div[y];
				$(o).html('');
				S.L.first=S.L.rand(8,15);
				for (i=1;i<=S.L.first;i++) {
					$('<p class="slot">').html('<img src="'+S.L.path+S.L.rand(1,10)+'.png">').prependTo(d);
				}
				d.prependTo(o);
				var r=Math.floor(Math.random()*10)+1;
				d.css({
					position:'relative',
					top: (-S.L.first*92)+'px'
				})
				var index=y;
				if (y || !S.L.mobile) S.L.sound('start',-500);
				d.animate({
					top: '-='+(r+4)+'px'
				},7*r,'easieEaseInExpo', function(){
					easie=S.L.shuffle(easie);
					d.animate({
						top: '+='+(S.L.first*S.L.height-8)+'px'
					}, S.L.first*r*8+(free?S.L.rand(S.L.first*6,S.L.first*10):S.L.rand(S.L.first*22,S.L.first*50)),'easieEaseIn'+easie[0], function(){
						S.L.now=true;
						fin[index]=true;
					});
				});
				y++;
			},j*(free?30:100));
			j++;
		});
		
		S.G.loader = false;
		S.G.json('?slots&loop&mini',{
			do: 'spin',
			lines: S.L.line_num,
			coins: S.L.coins
		}, function(data) {
			if (!data) return alert('Connection failure.');
			z = 0;
			S.L.by_nuggets = data.by_nuggets;
			if (data.by_nuggets) S.L.credits=data.nuggets;
			else S.L.credits=data.credits;
			if (!data.spin) {
				if (data.error) S.L.msg(data.error);
				else S.L.msg('SYSTEM ERROR');
				S.L.int=setInterval(function() {
					if (S.L.now) {
						for (j=0;j<5;j++) {
							if (fin[j]) {
								fin[j] = false;
								$('#slots_barrels ul li').eq(j).animate({
									backgroundColor: '#8F6025',
								}).find('img').animate({
									opacity: 0.4
								});;
								z++;
								if (z==5) {
									S.L.sound('loose',-500);
									clearInterval(S.L.int);
									S.L.set(data);
									S.L.clear();
									S.L.spinning=false;
									if ((!data.by_nuggets&&!data.credits)||(data.by_nuggets&&!data.nuggets)) {
										S.L.clicked=true;
										S.L.spinning=true;
									}
								}
							}
						}
					}
				},40);
				return;
			}

			S.L.int=setInterval(function() {
				if (S.L.now) {
					for (j in data.spin) {
						if (fin[j]) {
							fin[j] = false;
							var h = '';
							var r=1;
							for (i=1;i<=S.L.rand(4,8);i++) {
								r=Math.ceil(Math.random()*10);
								h += '<p class="slot"><img src="'+S.L.path+r+'.png"></p>';
							}
							var d = $('<div>').html(S.L._html(j+1,data.spin[j])+h);
							var r=Math.ceil(Math.random()*12);
							d.css({
								position: 'relative',
								top: (-S.L.height*(3+S.L.second))+'px',
								marginBottom: (-S.L.height*(3+S.L.second))+'px'
							}).prependTo(div[j]);
							
							setTimeout(function(){
								S.L.sound('stop');	
							}, 20*r);
							easie=S.L.shuffle(easie);
							div[j].animate({
								top: ''+(S.L.height*8)+'px'
							},(free?S.L.rand(20,40):S.L.rand(70,140))*r,'easieEaseOut'+easie[0],function(){
								z++;
								if (z==5) {
									clearInterval(S.L.int);
									S.L.set(data);
									S.L.clear();
									S.L.game(data);
								}
							});
						}
					}
				}
			},40);
		});
	}
	,set:function(data) {
		this.coins = parseInt(data.coins);
		for (i in this.coins_range) {
			if (this.coins==this.coins_range[i]) {
				this.coin_num = parseInt(i);
				break;
			}
		}
		this.line_num = parseInt(data.lines);
		this.lines(this.line_num, true);
		$('#slots_coins').val(this.coins/*+' '+this.what.toLowerCase()+(this.coins!=1?'s':'')*/);
		if (this.coins>1) $('#slots_coins_rem').removeClass('slots_disabled');
		if (data.by_nuggets) $('#slots_nuggets').html(parseInt(data.nuggets).toMoney(0,'',','));
		$('#slots_credits').html(parseInt(data.credits).toMoney(0,'',','));
		if ('free' in data) $('#slots_free').html(data.free);	
	}
	,clear:function(){
		this.clicked = false;
		$('#slots_double').hide();
		$('#slots_barrels ul p').stop().show();
		$('#slots_buttons button').removeClass('active').removeClass('clicked');
		$('#slots_barrels .slots_lines').hide();
		$('#slots_barrels .slots_left .slots_cir').stop();
	}
	,double:function() {
		S.L.msg('DOUBLE UP GAME, GOOD LUCK!');
		$('#slots_spin').hide();
		$('#slots_double').hide();
		$('#slots_buttons').css('visibility','hidden');
		this.clear();
		this.spinning=true;
		S.G.loader = false;
		S.G.json('?slots&loop&mini',{
			do: 'double'
		}, function(data) {
			if (!data.spin) return alert('No connection');
			S.L.html(data.spin);
		});
	}
	,doubleup:function(answer) {
		S.G.loader = false;
		S.G.json('?slots&loop&mini',{
			do: 'doubleup',
			answer: answer
		}, function(data) {
			S.L.spinning=false;
			if (!data.spin) return;
			S.L.extra(data.html);
			S.L.html(data.spin);
			if (data.js) eval(data.js);
			if (data.ok) {
				S.L.msg('YOU WON '+(data.win).toMoney(0,'',',')+' '+S.L.one(S.L.what3)+S.L.whats);
				$('#slots_double').fadeIn();
				S.L.sound('bonus',500);
			}
			else {
				S.L.msg('GAME LOST');
				$('#slots_double').hide();
				S.L.sound('loose',-500);
			}
			S.L.set(data);
			$('#slots_buttons').css('visibility','visible');
			$('#slots_spin').fadeIn();
			$('#slots_barrels button').addClass('active').show().removeClass('clicked');
		});
	}
	,_html:function(i,a) {
		var h = '';
		for (j in a) {
			if (a[j]==0) {
				if (j==0 || j==2) h += '<p></p>';
				else h += '<p id="b'+i+''+j+'" style="z-index:200" class="slots_hov"><a href="javascript:;" onclick="S.L.doubleup(\''+i+'.'+j+'\')"><img src="'+this.path+'0.png" /></a></p>';
			} else {
				h += '<p id="b'+i+''+j+'"'+(a[j]<=10?' class="slot"':'')+'><img src="'+this.path+a[j]+'.png" /></p>';
			}
		}
		return h;
	}
	,extra:function(arr) {
		if (!arr) return;
		for (i in arr) $('#'+i).html(arr[i]);
	}
	,html:function(spin) {
		var h = '';
		for (i in spin) {
			h += '<li class="slots_barrel_'+i+'">'+this._html(i,spin[i])+'</li>';
		}
		$('#slots_barrels ul').html(h);
	}
	,color:function(){
		var c = ['F0F8FF','FAEBD7','00FFFF','7FFFD4','F0FFFF','F5F5DC','FFE4C4','000000','FFEBCD','0000FF','8A2BE2','A52A2A','DEB887','5F9EA0','7FFF00','D2691E','FF7F50','6495ED','FFF8DC','DC143C','00FFFF','00008B','008B8B','B8860B','A9A9A9','A9A9A9','006400','BDB76B','8B008B','556B2F','FF8C00','9932CC','8B0000','E9967A','8FBC8F','483D8B','2F4F4F','2F4F4F','00CED1','9400D3','FF1493','00BFFF','696969','696969','1E90FF','B22222','FFFAF0','228B22','FF00FF','DCDCDC','F8F8FF','FFD700','DAA520','808080','808080','008000','ADFF2F','F0FFF0','FF69B4','CD5C5C','4B0082','FFFFF0','F0E68C','E6E6FA','FFF0F5','7CFC00','FFFACD','ADD8E6','F08080','E0FFFF','FAFAD2','D3D3D3','D3D3D3','90EE90','FFB6C1','FFA07A','20B2AA','87CEFA','778899','778899','B0C4DE','FFFFE0','00FF00','32CD32','FAF0E6','FF00FF','800000','66CDAA','0000CD','BA55D3','9370D8','3CB371','7B68EE','00FA9A','48D1CC','C71585','191970','F5FFFA','FFE4E1','FFE4B5','FFDEAD','000080','FDF5E6','808000','6B8E23','FFA500','FF4500','DA70D6','EEE8AA','98FB98','AFEEEE','D87093','FFEFD5','FFDAB9','CD853F','FFC0CB','DDA0DD','B0E0E6','800080','FF0000','BC8F8F','4169E1','8B4513','FA8072','F4A460','2E8B57','FFF5EE','A0522D','C0C0C0','87CEEB','6A5ACD','708090','708090','FFFAFA','00FF7F','4682B4','D2B48C','008080','D8BFD8','FF6347','40E0D0','EE82EE','F5DEB3','FFFFFF','F5F5F5','FFFF00','9ACD32'];
		c=this.shuffle(c);
		return '#'+c[0];
	}
	,shuffle:function(o) {
		for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
		return o;
	}
	,bigwin:function(o,i){
		if (this.clicked) return;
		var t;
		if (!i) var c = '#FFFFFF';
		else var c = S.L.color();
		if (i%2==0) t='+='+(i?'4':'2')+'px'; else t = '-=4px';
		o.find('img').removeClass('slots-deg-o').addClass('slots-deg');
		$(o).css({
			position: 'relative',
			outline: '4px solid '+c
		}).stop().animate({
			top: t,
			backgroundColor: c
		}, 200, 'swing', function(){
			o.find('img').removeClass('slots-deg').addClass('slots-deg-o');
			S.L.bigwin(o,i+1);
		});
		$('#slots_barrels ul li').each(function(){
			$(this).animate({
				backgroundColor: (!i?'#FFFFFF':S.L.color())
			}, 300, 'linear');
		});
		
	}
	,rand:function(min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}
	,win_hl:function(bw){
		if (bw) var i1=50,i2=70;
		else var i1=S.L.rand(50,100),i2=S.L.rand(50,400);
		$('#slots_hl').stop().fadeIn(i1,function(){
			$(this).fadeOut(i2,function(){
				S.L.win_hled++;
				if (!bw&&S.L.win_hled>4) return;
				S.L.win_hl(bw);
			});
		});
	}
	,one:function(v) {
		if (typeof(v)=='object') {
			v=this.shuffle(v);
			return v[0];	
		}
		return v;
	}
	,game:function(data, fake) {
		this.extra(data.html);
		this.html(data.spin);
		if (data.js) eval(data.js);
		this.spinning=false;
		this.by_nuggets = data.by_nuggets;
		if (data.by_nuggets) S.L.credits=data.nuggets;
		else S.L.credits=data.credits;
		
		$('#slots_barrels button').addClass('active').removeClass('clicked').show().focus();
		if (!fake) {
			if (data.win>0) {
				$('#slots_double').fadeIn();
				S.L.msg('YOU HAVE WON '+(data.win).toMoney(0,'',',')+' '+this.one(this.what3)+(data.win>1?this.whats:''));
				
				this.called = false;
				if (data.bigwin) {
					setTimeout(function(){
						S.L.sound('bonus',-500);
					},400);
					var j=[];
					for (i in data.bigwin[3]) {
						j.push('#b'+data.bigwin[3][i][0]+data.bigwin[3][i][1]);
					}
					this.bigwin($(j.join(', ')),0);
					S.L.msg('BIG WIN!!! '+(data.bigwin[2]).toMoney(0,'',',')+' + '+(data.win-data.bigwin[2]).toMoney(0,'',',')+' '+this.one(this.what3)+this.whats);
					S.L.free(data, true);
				} else {
					this.sound('coinwin',-1500);
					S.L.blink(data.blink,data.blink_lines,0,function(){
						S.L.free(data);
						S.L.blinked=false;
					},data.free);
				}
				if ($('#slots_hl').length) {				
					S.L.win_hled=0;
					S.L.win_hl(data.bigwin);
				}
			} else {
				S.L.msg('GAME OVER, LET\'S SPIN AGAIN');
				this.free(data);
			}
		} else {
			this.free(data);
		}
	}
	,free:function(data, no_auto) {
		if (data.free>0) {
			$('#slots_free').html(data.free);
			this.lines(data.lines, true);
			if (!no_auto) this.spin(data.free);
		}
		$('#slots_barrels .slots_lines img').hide();
	}
	,_blink:function(o,i) {
		if (this.clicked) return;
		var c = S.L.color();
		if (i==10) {
			return;	
		}
		o.find('img').removeClass('slots-deg-o').addClass('slots-deg');
		o.animate({
			'background-color': c
		}, 200, 'swing', function(){
			S.L._blink(o,i+1);
			o.find('img').removeClass('slots-deg').addClass('slots-deg-o');
		});
	}
	,blink:function(b,bl,i,fn,free) {
		if (!b[i]) {
			if (fn && !this.called) {
				this.called = true;
				fn();
			}
			return;
		}
		
		/*
		$('#slots_barrels .slots_barrels ol li').css({
			opacity: 0.2	
		});
		
		*/
		
		if (!free) {
			$('#slots_barrels>ul>li>p').removeClass('slots_win').stop().css({
				'background-color':'transparent'
			});
		}
		var e = 'easieEaseInOutCubic';
		var e2 = 'easieEaseInCubic';
		for (j in b[i]) {
			
			if (!free) this._blink($('#b'+b[i][j][0]+b[i][j][1]).addClass('slots_win'),0);
			
			$('#b'+b[i][j][0]+b[i][j][1]+'>img').stop().animate({opacity:0},200,e2,function(){
				if (S.L.spinning) return;
				$(this).animate({opacity:1},300,e,function(){
					if (free) {
						$('#slots_barrels .slots_lines img').eq(bl[i]-1).fadeOut();
						setTimeout(function(){
							if (S.L.spinning) return;
							S.L.blinked=true;
							S.L.blink(b,bl,i+1,fn,free);
						},100);
					} else {
						if (S.L.spinning) return;
						$(this).animate({opacity:0.1},200,e2,function(){
							if (S.L.spinning) return;
							
							$(this).animate({opacity:1},300,e,function(){
								if (S.L.spinning) return;
								$(this).parent().stop().css({
									'background-color':'transparent'
								});
								$('#slots_barrels .slots_lines img').eq(bl[i]-1).fadeOut();
								setTimeout(function(){
									if (S.L.spinning) return;
									S.L.blinked=true;
									S.L.blink(b,bl,i+1,fn,free);
								},200);
							});
						});	
					}
				});
			});
		}
		if (bl[i]) {
			$('#slots_barrels .slots_lines').show();
			$('#slots_barrels .slots_left').children().eq(bl[i]-1).fadeOut(400,e2,function(){
				$(this).fadeIn(300,e,function(){
					if (S.L.spinning) return;
					$(this).fadeOut(300,e2,function(){
						if (S.L.spinning) return;
						$(this).fadeIn(400,e);
					});
				});
			});
			$('#slots_barrels .slots_lines img').eq(bl[i]-1).fadeIn(400,e2,function(){
				if (S.L.spinning) return;
				setTimeout(function(){
					if (S.L.spinning) return;
					$('#slots_barrels .slots_lines img').eq(bl[i]-1).fadeOut(400,e);
				},900);
			});
		}
	}
	,lines:function(lines, no_hl) {
		if (S.L.spinning) return;
		this.line_num = lines;
		/*$('#slots_buttons').attr('class','slots_lines'+lines);*/
		$('#slots_barrels .slots_left').attr('class','slots_left slots_lines'+lines);
		if (!no_hl) {
			$('#slots_barrels .slots_lines').show();
			for (i=0;i<lines;i++) {
				$('#slots_barrels .slots_lines img').eq(i).show();
			}
			if (lines<9) {
				for (i=lines;i<9;i++) {
					$('#slots_barrels .slots_lines img').eq(i).hide();
				}
			}
		}
		$('#slots_lines').val(this.line_num);
		$('#slots_lines_rem, #slots_lines_add').removeClass('slots_disabled');
		if (this.line_num==9) $('#slots_lines_add').addClass('slots_disabled');
		else if (this.line_num==1) $('#slots_lines_rem').addClass('slots_disabled');
	}
	,lines_ch:function(to) {
		if (S.L.spinning) return;
		this.line_num += to;
		if (this.line_num>9) {
			this.line_num = 9;
		}
		else if (this.line_num<1) this.line_num = 1;
		this.lines(this.line_num);
	}
	,coins_ch:function(to) {
		if (S.L.spinning) return;
		if (this.credits < 1) return;
		$('#slots_coins_rem, #slots_coins_add').removeClass('slots_disabled');
		this.coin_num += to;
		if (this.coin_num < 0) this.coin_num = 0;
		if (this.coin_num >= this.coins_range.length) this.coin_num = this.coins_range.length-1;
		this.coins=this.coins_range[this.coin_num];
		
		if (this.coin_num==this.coins_range.length-1) $('#slots_coins_add').addClass('slots_disabled');
		else if (this.coin_num==0) $('#slots_coins_rem').addClass('slots_disabled');
		if (this.coin_num < this.coins_range.length) var next_coins = this.coins_range[this.coin_num+1];
		else var next_coins = this.coins;
		if (next_coins * this.line_num > this.credits) {
			for (i=0;i<this.coins_range.length;i++) if (this.credits < this.coins_range[i] * this.line_num) break;
			this.coin_num = i-1;
			this.coins=this.coins_range[this.coin_num];
			$('#slots_coins_add').addClass('slots_disabled');
		}
		$('#slots_coins').val(this.coins/*+' '+this.what.toLowerCase()+(this.coins!=1?'s':'')*/);
	}
	,open:function(w, no_hide) {
		$('#slots_'+w).css('height',800).show('fold').click(function(){
			if (!no_hide) $(this).hide('fold');
		});
	}
}