/* 
	Ajaxel poker
	Made by Ajaxel CMS developer
*/

S.P = {
	int: 0, int2:0, speed: 770, speed_single: 2000,
	path:'/tpls/'+S.C.TPL+'/images/poker/',
	buttons: ['fold','check','call','callany','raise'],
	trans: {}, alt: {}, nums: {},
	me: false, total: 0, count:0, seat: 0,  my_seat: 0, action: 'start', my_action: false,
	loading: false, poker_table:false, get_win: 1, got_win: false, pot: 0, timer: 0,
	
	
	init:function(data, path) {
		this.path = path;
		this.trans = data.trans;
		this.nums = data.nums;
		this.seat = data.seat;
		this.game(data, true);
		$('#poker').disableSelection();
		this.go();
	}
	,go:function() {
		clearTimeout(this.int);
		this.int=setTimeout('S.P.loop()',this.me ? this.speed : this.speed_single);	
	}
	,stop:function(){
		clearTimeout(this.int);
		S.G.loader = false;	
		this.loading = true;
	}
	,loop:function() {
		if (this.loading) {
			this.go();
			return false;
		}
		this.stop();
		S.G.json('?poker&loop',{
			do: 'loop',
			get_win: this.get_win
		}, function(data) {
			S.P.game(data);
		});
	}
	,auto:function(act) {
		for (i in this.buttons) {
			if (this.buttons[i]==act) continue;
			$('#poker_'+this.buttons[i]).find('input').removeAttr('checked');	
		}
		if ($('#poker_'+act).find('input').is(':checked')) {
			$('#poker_'+act).find('input').removeAttr('checked');
			this.my_action = 'fold';
		} else {
			$('#poker_'+act).find('input').attr('checked','checked');
			this.my_action = act;
		}
	}
	,act:function(act){
		if (!this.me || this.total<2) return false;
		if (this.seat==this.my_seat) {
			this.stop();
			S.G.json('?poker&loop',{
				do: act,
				bet: $('#poker_bet').val()
			}, function(data) {
				S.P.game(data);
			});
			$('#poker_'+act).find('input').removeAttr('checked');
		} else {
			this.auto(act);
		}
	}
	,sit:function(seat){
		this.my_seat = seat;
		$('#poker_amount').fadeIn('fast').draggable({
			handle: '.poker_title'
		});
	}
	,play:function(seat){
		var cash=$('#poker_amount input').val();
		$('#poker_amount').fadeOut('fast');
		this.stop();
		S.G.json('?poker&loop',{
			do: 'sit',
			seat: this.my_seat,
			cash: cash
		}, function(data) {
			S.P.game(data);
		});
	}
	,img:function(card,deck) {
		if (!deck) deck = '';
		if (card) {
			var n = card.substring(0,1);
			var _n = this.nums[n];
			var is_color = _n>=11 && _n<=13;
			n = this.trans[n];
			var s = this.trans[card.substr(1)];
			var src = this.path+'deck'+deck+'/'+n+'_of_'+s+(is_color?'2':'')+'.png';
		} else {
			var src = this.path+'deck'+deck+'/cardback.jpg';
		}
		return '<img src="'+src+'">';
	}
	,card:function(card, i) {
		$('#poker_card'+i).html(this.img(card));
	}
	
	,game:function(data) {
		
		if (data.error) {
			this.msg(data.error);
			this.go();
			return false;
		} else {
			this.msg(data.table.dealer+' '+data.table.seat+' '+data.table.timer+ ' '+Math.random());
		}
		
		$('#poker').attr('class','poker_table_'+data.table.maxplayers);
		
		this.seat = data.seat;
		this.me = data.me;
		this.pot = data.table.pot;
		this.timer = data.table.timer;
		this.action = data.table.action;
		this.total = data.total;
		this.count = data.count;
		
		if (!this.got_win) {
			var h = '<div class="poker_seat" id="poker_dealer">';
			h += '<div class="poker_deal">Dealer</div>';
			h += '<div class="poker_photo" style="background-image:url(\''+this.path+'nophoto.jpg\')"></div>';
			h += '<div class="poker_cash"><a href="javascript:;" onclick="S.P.tip()">Tip to dealer</a></div>';
			h += '</div>';
			for (i=1;i<=data.table.maxplayers;i++) {
				h += this.tpl(data.players[i],i);
			}
			h += '<ul id="poker_hand">';
			for (i=1;i<=5;i++) {
				h += '<li id="poker_card'+i+'"></li>';
			}
			h += '</ul>';
			h += '<div id="poker_pot"></div>';
			$('#poker_table').html(h);
			
			this.card(data.table.card1,1);
			this.card(data.table.card2,2);
			this.card(data.table.card3,3);
			this.card(data.table.card4,4);
			this.card(data.table.card5,5);
			
			$('#poker_pot').html((this.pot>0 ? '€ '+this.pot : ''));
			
			if (this.action=='final' && !$('#poker_bot').is(':hidden')) {
				$('#poker_bot').hide();
				$('#poker_bot2').fadeIn('fast');
			}
			
			if (this.count>1 && this.me) {
				this.my_seat = data.me.seat;
				
				if (this.action!='final' && $('#poker_bot').is(':hidden')) {
					$('#poker_bot2').hide();
					$('#poker_bot').fadeIn('fast');
				}
				
				if (this.seat==this.my_seat) {
					if (this.my_action) {
						if (this.action!='final') {
							this.act(this.my_action);
						}
						for (i in this.buttons) {
							$('#poker_'+this.buttons[i]).find('input').removeAttr('checked');	
						}
						this.my_action = false;
					} else {
						for (i in this.buttons) {
							$('#poker_'+this.buttons[i]).find('input').css('visibility','hidden');
						}	
					}	
				} else {
					for (i in this.buttons) {
						$('#poker_'+this.buttons[i]).find('input').css('visibility','visible');	
					}	
				}
			}
			else {
				$('#poker_bot2').show();
				$('#poker_bot').hide();
			}
		}
		
		this.loading = false;
		this.got_win = false;
		this.get_win = 1;
		this.go();
		return;
		
		if (data.wins) {
			this.get_win = 0;
			this.got_win = true;
			if (!this.int2) {
				this.loading=true;
				this.int2=true;
				setTimeout(function(){
					S.P.loading=false;
					S.P.int2=false;
					S.P.go();
				},parseInt(data.table.timer)*1000);
			} else {
				this.go();	
			}
			// start coin win animation
			
			
			
		}
		else if (data.table.action=='final') {
			this.loading = false;
			setTimeout(function(){
				S.P.get_win = 0;
				S.P.go();
			},this.speed);
		}
		else {
			this.loading = false;
			this.got_win = false;
			this.get_win = 1;
			this.go();
		}
	}
	
	,seconds:function(s) {
		s = Math.floor(s).toString();
		if (s.length==1) s = '0'+s;
		return s;
	}
	,bet:function(a){
		var r = 0;
		switch (this.action) {
			case 'start':
				r = parseInt(a.bet_blind);
			break;
			case 'flop':
				r = parseInt(a.bet_flop);
			break;
			case 'turn':
				r = parseInt(a.bet_turn);
			break;
			case 'river':
				r = parseInt(a.bet_river);
			break;
			case 'final':
				r = parseInt(a.win);// win
			break;
		}
		return r>0 ? '€ '+r.toMoney(0,'',',') : '';
	}
	,tpl:function(a,seat) {
		if (a) {
			var h = '<div class="poker_seat" id="poker_seat'+seat+'">';
			h += '<div class="poker_deal">'+a.username+'</div>';
			h += '<div class="poker_photo" style="background-image:url(\''+a.photo+'\')">';
			if (this.seat==seat) {
				h += '<div class="poker_timer">'+this.seconds(this.timer)+'</div>';
			}
			h += '</div>';
			h += '<div class="poker_hand"'+(a.card1?' style="display:block;"':'')+'>'+(a.card1?this.img(a.card1,2)+' '+this.img(a.card2,2):'')+'</div>';
			h += '<div class="poker_nohand"'+((!a.card1 && this.total>1 && a.folded!='1')?' style="display:block;"':'')+'><img src="'+this.path+'deck2/cardback.png"><img src="'+this.path+'deck2/cardback.png"></div>';
			h += '<div class="poker_bet">'+this.bet(a)+'</div>';
			h += '<div class="poker_cash">€ '+a.cash+'</div>';
			h += '</div>';
		}
		else if (!this.me) {
			var h = '';
			h += '<div class="poker_seat" id="poker_seat'+seat+'">';
			h += '<button type="button" onclick="S.P.sit('+seat+')">sit here</button>';
			h += '</div>';
		}
		else {
			var h = '';	
		}
		return h;
	}
	,msg:function(m) {
		$('#poker_message').html(m);	
	}
	
	,end:function() {
		var o2=$('#poker_pot').offset();
		$('.poker_bet').each(function(){
			var o=$(this).offset();
			$(this).css({
				position: 'fixed',
				top: o.top,
				left: o.left
			}).animate({
				top: o2.top,
				left: o2.left+400,
				opacity: 0.4
			},'slow','swing');
		});	
	}
	,tip:function() {
		
		alert('Thanx, '+this.me.username+'!');	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}