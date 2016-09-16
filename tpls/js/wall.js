/* ----------------------------------------------- 
	Wall javascript library.
	Ajaxel CMS 6.00
	http://ajaxel.com
   ----------------------------------------------- */
S.W={
	vars:{},setid:0,object:'',item_class:'wall_action',offset:0
	,t_css_a:{ height:40,color:'#2F2F2F',lineHeight:'15px' }
	,t_css:{ height:20,color:'#888',lineHeight:'20px' }
	
	,init:function(vars){
		this.vars=vars;
		this.offset = 0;
		this.setid=$('#wall_setid').val();
		this.object=$('#wall_object').val();
		S.E.keyCode=0;
		S.E.ctrlKey=false;

		S.G.json('?wall',{ setid:this.setid,object:this.object }, function(data) {
			var h = '';
			for (var i in data.list) {
				h += S.W.tpl(data.list[i]);
			}
			S.G.html('wall_comments',h);
			S.I.init($('#wall_comments'));
			S.W.actions();
			if (i>18) $('#wall_list .wall_show_more').show();
		});
	}
	,actions:function(){
		$('#wall_comment').focus(function(){
			if ($(this).val()==S.W.vars.write_a_comment) {
				$(this).val('').css(S.W.t_css_a).autosize();
			}
		}).blur(function(){
			if ($(this).val()=='') {
				$(this).val(S.W.vars.write_a_comment);
				$(this).css(S.W.t_css).unbind('autosize');
			}
		});
		
		var list=$('#wall_list');
		$('.remove_action',list).die().live('click',function(){
			$(this).closest('.'+S.W.item_class).hide().before($('#remove_action_tpl').html());
		});
		$('.hide_action',list).die().live('click',function(){
			$(this).closest('.'+S.W.item_class).hide().before($('#hide_action_tpl').html());
		});
		$('.cancel_btn',list).die().live('click',function(){
			$(this).closest('.'+S.W.item_class).next().show().prev().remove().next().remove();
		});
		$('.hide_btn',list).die().live('click',function(){
			var o=$(this),id=o.closest('.'+S.W.item_class).next().attr('id').replace(S.W.item_class+'_','');
			S.G.json('?wall&action=hide',{ id:id }, function(data) { 
				o.closest('.'+S.W.item_class).next().hide().prev().remove().next().remove();
			});
		});
		$('.remove_btn',list).die().live('click',function(){
			var o=$(this),id=o.closest('.'+S.W.item_class).next().attr('id').replace(S.W.item_class+'_','');
			S.G.json('?wall&action=delete',{ id:id }, function(data) { 
				o.closest('.'+S.W.item_class).next().hide().prev().remove().next().remove();
			});
		});
		
		$('.like_btn',list).die().live('click',function(){
			var o=$(this),id=o.closest('.'+S.W.item_class).attr('id').replace(S.W.item_class+'_','');
			S.G.json('?wall&action=like',{ id:id }, function(data){
				if (data.ok) {
					if (data.liked) {
						o.closest('.'+S.W.item_class).find('.like_box').removeClass('display_none').addClass('active');
						var l=o.closest('.'+S.W.item_class).find('.like_num');
						var n=parseInt(l.html());
						l.html(n+1);
						o.html(S.W.vars.unlike);
					} else {
						o.closest('.'+S.W.item_class).find('.like_box').removeClass('active').addClass('display_none');
						var l=o.closest('.'+S.W.item_class).find('.like_num');
						var n=parseInt(l.html());
						l.html(n-1);
						o.html(S.W.vars.like);
					}
				}
			});
		});
		
		$('.comment_btn',list).die().live('click',function(){
			$(this).closest('.'+S.W.item_class).find('.comment_add').removeClass('display_none').addClass('active').find('textarea').css(S.W.t_css_a).val('').focus(function(){
				if (this.value==S.W.vars.write_a_comment) $(this).css(S.W.t_css_a).val('');
				$(this).parent().next().show();
			}).blur(function(){
				if (!this.value) {
					$(this).css(S.W.t_css).val(S.W.vars.write_a_comment);
					$(this).parent().next().hide()
				}
			}).focus().keyup(function(){
				if (S.E.keyCode==13&&S.E.ctrlKey) {
					S.E.keyCode=0;
					S.E.ctrlKey=false;
					var o=$(this).closest('.'+S.W.item_class),id=o.attr('id').replace(S.W.item_class+'_','');
					S.W.reply(o,id);
					return false;
				}
			}).parent().next().show();
		});
		$('.delete_comment',list).die().live('click',function(){
			if (confirm('Are you sure you want to delete this comment?')) {
				var id=$(this).closest('.comment').attr('id').replace('comment_','');
				var parentid=$(this).closest('.'+S.W.item_class).attr('id').replace(S.W.item_class+'_','');
	
				S.G.json('?wall&action=delete',{ id:id,parentid:parentid, setid:S.W.setid,object:S.W.object },function(data){
					if (data.ok) {
						$('#comment_'+id+'').fadeOut();
					}
				});
			}
		});
		$('.add_comment_btn',list).die().live('click',function(){
			var o=$(this).closest('.'+S.W.item_class),id=o.attr('id').replace(S.W.item_class+'_','');
			S.W.reply(o,id);
		});
		$('#show_more_btn').die().live('click', function(){
			S.W.offset += 20;
			S.G.json('?wall',{ setid:S.W.setid,object:S.W.object,offset:S.W.offset }, function(data) {
				if (data.total>0) {
					var h = '';
					for (i in data.list) {
						h += S.W.tpl(data.list[i]);
					}
					if (i>18) $('#wall_list .wall_show_more').show();
					else  $('#wall_list .wall_show_more').hide();
					$('#wall_comments').find('.wall_action:last').after($('<div>').html(h));
					S.I.init($('#wall_comments'));
				} else {
					$('#show_more_btn').hide().next().removeClass('display_none');
				}
			});
		});
		$('.all_comments',list).die().live('click',function(){
			var o=$(this).closest('.'+S.W.item_class),id=o.attr('id').replace(S.W.item_class+'_','');
			var a=$(this);
			S.G.json('?wall&action=replies',{id:id}, function(data) {
				a.parent().parent().remove();
				var h = '';
				for (i in data.list) {
					h += S.W._tpl(data.list[i]);
				}
				o.find('.comment:last').after($('<div>').html(h));
			});
		});
	}
	,reply:function(o,id){
		var v=o.find('.comment_add').find('textarea').val();
		o.find('.comment_add').find('textarea').attr('disabled','disabled').addClass('loading');
		S.G.json('?wall&action=reply',{id:id,comment:v}, function(data){
			o.find('.comment_add').find('textarea').val(S.W.vars.write_a_comment).css(S.W.t_css).removeAttr('disabled').removeClass('loading').focus();
			if (o.find('.comment:last').length) {
				o.find('.comment:last').after($('<div>').html(S.W._tpl(data)));
			} else {
				o.find('.feed_comments').append($('<div>').html(S.W._tpl(data)));	
			}
		});	
	}
	,click:function(o){
		if ($(o).hasClass('i_photo')) {
			$('#wall_buttons').hide();
			$('#wall_attach_photo').show();
		}
		else if ($(o).hasClass('i_link')) {
			$('#wall_buttons').hide();
			$('#wall_attach_link').show().find('input').focus();
		}
		else if ($(o).hasClass('i_close')) {
			$('#wall_buttons').show();$('#wall_attach_photo').hide();$('#wall_attach_link').hide();
		}
	}
	,submit:function(){
		if ($('#wall_comment').val()==this.vars.write_a_comment) {
			$('#wall_comment').val('');
		}
		if (!$('#wall_comment').val() && !$('#wall_link').val() && !$('#wall_file').val()) {
			$('#wall_comment').css(this.t_css_a).autosize().focus();
			return false;
		}
		$('#wall_form').submit();
		$('#wall_comment').css(this.t_css).html(this.vars.write_a_comment);
		$('#wall_file').parent().html('<input type="file" name="wall_file" id="wall_file" />');
		$('#wall_link').val('');
		$('#wall_attach_photo, #wall_attach_link').hide();$('#wall_buttons').show();
	}
	,insert:function(a){
		if ($('#wall_comments .wall_action').length) {
			$('#wall_comments .wall_action:first').before($('<div>').html(this.tpl(a)));
		} else {
			$('#wall_comments').html(this.tpl(a));
		}
		S.I.init($('#wall_comments'));
	}
	,_tpl:function(_a) {
		var h = '';
		h += '<div class="comment" id="comment_'+_a.id+'"><a class="comment_photo_block" href="/profile-'+(_a.subid>0?_a.subid:_a.login)+'"><img src="'+_a.userpic+'" class="comment_photo" alt=""></a>';
		h += '<div class="comment_body"><div class="comment_text"><a class="comment_author" href="/profile-'+(_a.subid>0?_a.subid:_a.login)+'">'+_a.user+'</a> ';
		h += '<div class="comment_actual_text">'+_a.comment+'</div>';
		h += '<div class="comment_actions"><div class="wall_delete_comment"><span class="date_time">'+_a.added+'</span> '+(_a.del==1?' <a href="javascript://" class="delete_comment">'+S.W.vars.del+'</a>':'')+'</div></div></div></div><div class="c"></div></div>';
		return h;
	}
	,tpl:function(a) {
			
		var h = '<div class="wall_action" id="wall_action_'+a.id+'">';
		h += '<a class="owner_photo" href="/profile-'+(a.subid>0?a.subid:a.login)+'"><img src="'+a.userpic+'" class="photo" width="35.5" alt=""></a>';
		if (S.C.USER_ID){
			h += '<div class="hide_action_block">';
			if (a.del==1) {
				h += '<a href="javascript:;" class="remove_action">'+S.W.vars.remove+'</a>';
			} else {
				h += '<a href="javascript:;" class="hide_action">'+S.W.vars.hide+'</a>';
			}
			h += '</div>';
		}
		h += '<div class="media_container">';
		h += '<a href="/profile-'+(a.subid>0?a.subid:a.login)+'" class="owner_name">'+a.user+'</a> '+a.comment+(a.link?' <a rel="nofollow" target="_blank" href="http://'+a.link.replace('http://','')+'">'+a.link.replace('http://','')+'</a>':'');
		if (a.img) {
			h += '<div class="recentaction_div_media">';
			h += '<div class="photo_cont"><a href="'+a.img+'" class="colorbox" rel="wall"><img src="'+a.th_img+'" border="0" width="150" class="recentaction_media"></a></div>';
			h += '</div>';
		}
		h += '</div><div class="c"></div>';
		var i='he_wall_post_icon.png';
		if (a.link) i='he_wall_link_icon.png'; 
		else if (a.img) i='he_wall_photo_icon.png';
		h += '<div class="action_options"><div class="wall_action_options"><img src="'+S.C.FTP_EXT+'tpls/img/icons_wall/'+i+'" border="0" class="icon"><span class="date_time"> '+a.added+' </span>'+(S.C.USER_ID?' <a href="javascript://" class="comment_btn">'+S.W.vars.comment+'</a> <a href="javascript:;" class="like_btn">'+(a.liked?S.W.vars.unlike:S.W.vars.like)+'</a>':'')+' <span class="like_num">'+a.likes+'</span></div></div>';
		
		h += '<div class="comment_box -display_none">';
		h += '<div class="like_box'+(!a.liked?' display_none':'')+'"><div class="like_content">'+S.W.vars.liked_this+'</div></div>';
		h += '<div class="feed_comments">';

		if (a.sub) {
			for (i in a.sub) {
				h += S.W._tpl(a.sub[i]);
			}
		}
		if (a.sub_total>4) {
			h += '<div class="comment"><div class="comment_count"><a href="javascript:;" class="all_comments">'+S.W.vars.view_all_comments.replace('[num]',a.sub_total)+'</a></div></div>';
		}
		
		h += '</div>';
		
		h += '<div class="comment_add display_none"><a class="comment_photo_block" href="/profile-'+(a.subid>0?a.subid:a.login)+'"><img src="'+a.userpic+'" class="comment_photo" alt=""></a>';
		h += '<div class="comment_body"><div class="comment_text">';
		h += '<div class="comment_actual_text"><textarea>'+S.W.vars.write_a_comment+'</textarea></div>';
		h += '<div class="comment_actions" style="text-align:right;"><input type="button" value="'+S.W.vars.comment+'" class="button add_comment_btn"/></div>';
		h += '</div></div><div class="c"></div>';
		h += '</div></div></div>';
		return h;
	}
};