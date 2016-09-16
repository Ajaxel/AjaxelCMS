/* ----------------------------------------------- 
	Global javascript library.
	Ajaxel CMS 7.2
	http://ajaxel.com
   ----------------------------------------------- */
var S={
	L: {}
	,C: {
		HTTP_EXT:'', FTP_EXT:'', TPL:'', USER_ID:0, DEVICE:'pc', SESSION_LIFETIME:3600, REFERER: '', temp:{}
	}
	,M: {
		vars:{},marker:false,markers:[],map:false,point:false,lat:0,lng:0,zoom:0,place:false,loc:{},mc:false,post:{}
		,minX:0,maxX:0,minY:0,maxY:0,start:false
		,set:function(vars){
			this.vars=vars;
				
		}
		,init:function(){
			$.getScript(S.C.FTP_EXT+'tpls/js/markerclusterer.min',function(d,s) {
				S.M.map = new GMap2(document.getElementById(S.M.vars.id));
				S.M.map.setUI(S.M.map.getDefaultUI());
				S.M.map.addMapType(G_PHYSICAL_MAP);
				
				S.M.map.addControl(new GScaleControl());
				S.M.map.enableScrollWheelZoom();

				/*S.M.map.disableDoubleClickZoom();*/
				S.M.map.setUIToDefault();
				S.M.start=true;
				GEvent.addListener(S.M.map,'moveend',function(){S.M.moveEnd()});
				GEvent.addListener(S.M.map,'zoomend',function(){S.M.moveEnd()});
				$('#'+S.M.vars.id).bind('mousewheel', function(event, delta) {
					return false;
				});
				S.M.start=false;
				if (S.M.vars.draggable) {
					GEvent.addListener(S.M.map,'click', function(e, point){
						S.M.moveMarker(point.y,point.x);
					});
				}
				S.M.run();
				$('#'+S.M.vars.id).mouseout(function(){
					S.M.markerOut()
				});
			});
		}
		,moveMarker:function(lat,lng) {
			this.remMarker();
			this.lat=lat;this.lng=lng;
			this.point = new GLatLng(lat,lng);
			this.addMarker();
			this.valCoords();
		}
		,moveEnd:function(id){
			this.zoom=this.map.getZoom();
			this.valCoords();
			if (!this.start) this.chargeMarqueur();
			S.M.drag();
		}
		,drag:function(){
			// additional func	
		}
		,form:function(form){
			this.post=$(form).serializeArray();
			this.chargeMarqueur();
		}
		,chargeMarqueur:function(){
			if (!this.vars.markers) return false;
			this.minX=this.map.getBounds().getSouthWest().lng();
			this.maxX=this.map.getBounds().getNorthEast().lng();
			this.minY=this.map.getBounds().getSouthWest().lat();
			this.maxY=this.map.getBounds().getNorthEast().lat();
			var url = this.vars.markers+'&zoom='+this.zoom+'&minX='+this.minX+'&maxX='+this.maxX+'&minY='+this.minY+'&maxY='+this.maxY+'&n='+Math.random();
			S.G.loader=false;
			
			S.G.json(url,this.post,function(data){
				S.M.remMarkers();
				S.M.markers=[];
				
				if (data.data) {
					var a;
					var total = 0;
					for (i in data.data){
						a = data.data[i];
						total++;
						var point=new GLatLng(parseFloat(a.lat),parseFloat(a.lng));
						S.M.markers.push(S.M.createMarker(point,a));
					}
					if ($('#find_total').length) {
						$('#find_total').html(total);
					}
					if (S.M.mc) {
						S.M.mc.clearMarkers();
					}
					S.M.mc = new MarkerClusterer(S.M.map, S.M.markers, {maxZoom: 14, gridSize:50});
				} else {
					if (S.M.mc) {
						S.M.mc.clearMarkers();
					}
					if ($('#find_total').length) {
						$('#find_total').html('0');
					}
				}
			});
			S.G.loader=true;
		}
		,createMarker:function(point,a){			
			var marker=new GMarker(point,{title: a.title,icon: this.icon(a.icon,a)});
			marker.id=a.id;
			GEvent.addListener(marker,'mouseover',function(e){S.M.markerHover(e,marker,a)});
			GEvent.addListener(marker,'mouseout',function(e){S.M.markerOut(e)});
			GEvent.addListener(marker,'click',function(){S.M.markerClick(marker,a)});
			if(this.userid==a.userid){GEvent.addListener(marker,'contextmenu',function(){S.E.context=true;S.M.markerContext(a)})}
			return marker;
		}
		,hover:false
		,markerHover:function(e,marker,a) {
			this.markerOut();
		}
		,markerOut:function(e) {
			$('#bubble').remove();
		}
		,markerClick:function(marker,a){
			alert(a.id);
		}
		,markerContext:function(m){
			
		}
		,search:function(a){
			if(!a.country) return false;
			S.M.loc=a;
			var s = a.country+' '+a.state+' '+a.city+' '+a.street;
			(new GClientGeocoder()).getLocations(s, S.M.addAddressToMap);
		}
		,find:function(){
			
		}
		,addAddressToMap:function(r){
			if(!r||r.Status.code!=200) return false
			else{
				S.M.place=r.Placemark[0];
				S.M.lat=S.M.place.Point.coordinates[1];
				S.M.lng=S.M.place.Point.coordinates[0];
				S.M.setMarker();
				S.M.moveEnd();
				S.M.found=true;
			}
		}
		,getZoom:function(){
			if (!this.zoom) {
				var z=10;
				if (this.loc.country)z=6;
				if (this.loc.state)z=9;
				if (this.loc.city)z=11;
				if (this.loc.street)z=14;
				this.zoom=z;
			}
			return z;
		}
		,run:function(){
			alert('Please add S.M.run=function(){...} into document');
		}
		,setMarker:function(){
			this.getZoom();this.setCenter();if (this.vars.auto){this.remMarker();this.addMarker()}this.valCoords();	
		}
		,setCenter:function(lat,lng,zoom){
			if (!lat&&!this.lat) return false;
			if (!lat){lat=this.lat;lng=this.lng;}
			else{this.lat=lat;this.lng=lng;}
			if (!zoom) zoom=this.zoom;
			this.point = new GLatLng(parseFloat(lat),parseFloat(lng));
			this.map.setCenter(this.point, parseInt(zoom));
		}
		,addMarker:function(){
			if (!this.point) return false;
			this.marker=new GMarker(this.point,this.draggable());
			if (this.vars.draggable) {
				GEvent.addListener(this.marker,'drag',function(){
					  S.M.lat=S.M.marker.getPoint().lat();
					  S.M.lng=S.M.marker.getPoint().lng();
					  S.M.find();
					  S.M.valCoords();
				});
				GEvent.addListener(this.marker, 'click', function() {
					
				});
			}
			this.map.addOverlay(this.marker);	
		}
		,draggable:function(){
			return {icon:this.icon(),draggable:this.vars.draggable}
		}
		,icon:function(t,m){
			if (t) {
				var i=this.vars.icons[t];
				var is=this.vars.icons[t+'_shadow'];
			} else {
				var i=this.vars.icon;
				var is=this.vars.icon_shadow
			}
			if(!i) return null;
			var ic=new GIcon();
			ic.image=i.src;
			ic.iconSize=new GSize(i.w,i.h);
			if (is){
				ic.shadow=is.src;
				ic.shadowSize=new GSize(is.w,is.h);
			} else ic.shadow=false;
			ic.iconAnchor=new GPoint(6,22);
			ic.infoWindowAnchor=new GPoint(5,1);
			return ic;
		}
		,remMarker:function(){
			if(this.marker){
				this.map.removeOverlay(this.marker);
				this.marker=false;
			}	
		}
		,remMarkers:function(){
			if(this.markers){
				for (i in this.markers) {
					this.map.removeOverlay(this.markers[i]);
				}
				this.markers=[];
			}
		}
		,valCoords:function(){
			$('#map_lat').val(this.lat);
			$('#map_lng').val(this.lng);
			$('#map_zoom').val(this.zoom);
		}
		,isPointInPoly:function(poly, pt){
			for(var c = false, i = -1, l = poly.length, j = l - 1; ++i < l; j = i)
				((poly[i][1] <= pt[1] && pt[1] < poly[j][1]) || (poly[j][1] <= pt[1] && pt[1] < poly[i][1]))
				&& (pt[0] < (poly[j][0] - poly[i][0]) * (pt[1] - poly[i][1]) / (poly[j][1] - poly[i][1]) + poly[i][0])
				&& (c = !c);
			return c;
		}
	}
	,G:{
		conf:function(conf){
			S.C = conf;
		}
		,addJS:function(path,callback){
			var done = false;
			
			function handleLoad() {
				if (!done) {
					done = true;
					callback(path,'ok');
				}
			}
			function handleReadyStateChange() {
				var state;
				if (!done) {
					state = scr.readyState;
					if (state==='complete') {
						handleLoad();
					}
				}
			}
			function handleError() {
				if (!done) {
					done = true;
					callback(path,'error');
				}
			}
			
			if (typeof(path)=='object') {
				for (i=0;i<path.length;i++) {
					var scr = document.createElement('script');
					if (callback && i==path.length-1) {
						scr.onload = handleLoad;
						scr.onreadystatechange = handleReadyStateChange;
						scr.onerror = handleError;
					}
					scr.src = path[i];
					document.getElementsByTagName('head')[0].appendChild(scr);
				}
			} else {
				var scr = document.createElement('script');
				if (callback) {
					scr.onload = handleLoad;
					scr.onreadystatechange = handleReadyStateChange;
					scr.onerror = handleError;
				}
				scr.src = path;
				document.getElementsByTagName('head')[0].appendChild(scr);
			}
		}
		,css_unload:[],_css_unload:[]
		,addCSS:function(path,id,unload){
			if(typeof(path)=='object'){
				for (i=0;i<path.length;i++)	this.addCSS(path[i]);
			} else {
				var l=$('<link>').attr('type','text/css').attr('rel','stylesheet').attr('href',path).appendTo('head');
				if (id) l.attr('id',id);
				if (unload) this.css_unload.push(id);
			}
		},
		html:function(div,html,outer){
			if (typeof(div)=='object') var o=$(div).get(0);
			else var o=document.getElementById(div);
			if (!o) return false;
			if (typeof(html)=='undefined') return o.innerHTML;
			if (0&&outer) o.outerHTML=html;
			else o.innerHTML=html;
			var a=o.getElementsByTagName('script'),s,i;
			for (i in a) {
				if (!a[i] || !a[i].innerHTML) continue;
				$('<span>').html('<scr'+'ipt type="text/javascript">'+a[i].innerHTML+'</scr'+'ipt>').appendTo(document.body);
			}
		},
		toggleCookie:function(n,f){
			if($.cookie('show_'+n)=='Y') {
				$.cookie('show_'+n,null);
				$('#'+n).hide(f?f:'fade');
			} else {
				$.cookie('show_'+n,'Y');
				$('#'+n).show(f?f:'fade');
			}
		},
		loader: true,
		loadHtml:function(div, url, fn) {
			$.ajax({
				cache: false,
				type: 'GET',
				dataType: 'html',
				url: '/?ajax'+url.replace(/\?/,'&'),
				success: function(data) {
					/*$('#'+div).html(data);*/
					S.G.html(div,data);
					if (fn) fn();
				}
			});
		}
		,scrollCalled:false
		,scrollTo:function(obj,add,fn,duration) {
			$('html,body').stop().animate({
				scrollTop: parseInt($(obj).offset().top)+(add ? parseInt(add) : 0)
			}, {
				duration: (duration ? duration : 'fast'),
				_easing:	'easeOutBounce',
				easing: 'linear',
				complete: function(){
					if (!S.G.scrollCalled&&fn){
						fn();
						S.G.scrollCalled=true;
					}
				}
			});
			S.G.scrollCalled=false;
		}
		,edit_obj:false, edit_obj_html:''
		,edit:function(name, col, obj, div, value, chk) {			
			if (value) {
				S.G.json('?edit',{name:name,col:col,div:div,value:value}, function(data){
					if (data&&data.html) S.G.edit_obj.html(data.html);
					S.G.edit_obj.prev().html(S.C.temp.edit_text);
					S.G.edit_obj=false;
				});
			} else {
				if (S.G.edit_obj) {
					S.G.edit_obj.html(S.G.edit_obj_html);
					if (obj.prev().html()==S.C.temp.cancel_text) {
						S.G.edit_obj.prev().html(S.C.temp.edit_text);
						return false;
					}
					S.G.edit_obj.prev().html(S.C.temp.edit_text);
				}
				obj.prev().html(S.C.temp.cancel_text);
				this.edit_obj=obj;
				this.edit_obj_html=obj.html();
				if (div) {
					var html = obj.next().html();
				} else {
					var val = obj.html();
					if (val.substring(0,5).toLowerCase()=='<span') {
						val = '';
					}
					var html = '<input type="text" id="__QuickEdit" value="'+val+'" />';
				}
				obj.html('<div class="edit">'+html+' <button type="button" onclick="S.G.edit(\''+name+'\', \''+col+'\', false,  \''+div+'\', '+(chk?'$(this).parent().find(\'.radio:checked\').val()':'$(this).prev().val()')+')">'+S.C.temp.save_text+'</button></div>');
				
			}
		}
		,checkUsername:function(input, obj, text_error, text_ok) {
			S.G.json('?check_username',{username:input.val()}, function(data){
				if (data.is=='1') {
					$(obj).html(text_error).attr('class','error');
				} else {
					$(obj).html(text_ok).attr('class','success');
				}
			});				
		}
		,geo_label:[]
		,populateGeo:function(obj,area,suffix) {
			if (!suffix) suffix = '';
			if (!this.geo_label[suffix]) {
				this.geo_label[suffix]={
					state:$('#find_states'+suffix+' option:first').text(),
					city:$('#find_cities'+suffix+' option:first').text(),
					district:$('#find_districts'+suffix+' option:first').text()
				}
			}
			var o=$(obj);
			var country = $('#find_countries'+suffix).val();
			var state = $('#find_states'+suffix).val();
			var city = $('#find_cities'+suffix).val();
			if (suffix) var id=o.attr('id').replace(suffix,'');
			else id=o.attr('id');
			var from = id.substr(5);
			var s = {}, first = '',i=0;
			
			var has_selectBox = o.next().hasClass('selectBox-dropdown') || o.next().hasClass('selectbox') || o.hasClass('styled');
			var has_cusel = /*o.parent().hasClass('cuselScrollArrows')*/false;
			var ids = [], nexts=[], to = '';
			
			
			switch (id) {
				case 'find_countries':	
					to='states';				
					nexts.push('districts');nexts.push('cities');nexts.push('states');
					for (k in nexts) {
						s=$('#find_'+nexts[k]+suffix);
						s.find('option').remove().end();
						$('#'+nexts[k]+'_td'+suffix).hide();
					}			
					first = this.geo_label[suffix].state;
					if (has_cusel) {
						ids.push('#find_cities'+suffix);
						ids.push('#find_districts'+suffix);
					}
					else if (has_selectBox && $.selectBox) {
						$('#find_cities'+suffix).selectBox('options',{0:this.geo_label[suffix].city});
						$('#find_districts'+suffix).selectBox('options',{0:this.geo_label[suffix].district});
					} else {
						$('#find_cities'+suffix).find('option').remove().end();
						this.option($('#find_cities'+suffix),'0',this.geo_label[suffix].city);
						$('#find_districts'+suffix).find('option').remove().end();
						this.option($('#find_districts'+suffix),'0',this.geo_label[suffix].district);
					}
				break;
				case 'find_states':
					to='cities';
					nexts.push('districts');nexts.push('cities');
					for (k in nexts) {
						s=$('#find_'+nexts[k]+suffix);
						s.find('option').remove().end();
						$('#'+nexts[k]+'_td'+suffix).hide();
					}					
					first = this.geo_label[suffix].city;
					if (has_cusel) {
						ids.push('#find_cities'+suffix);
						ids.push('#find_districts'+suffix);
					}
					else if (has_selectBox && $.selectBox) {
						$('#find_districts'+suffix).selectBox('options',{0:this.geo_label[suffix].district});
					} else {
						$('#find_districts'+suffix).find('option').remove().end();
						this.option($('#find_districts'+suffix),'0',this.geo_label[suffix].district);
					}
				break;
				case 'find_cities':
					to='districts';
					s=$('#find_districts'+suffix);
					s.find('option').remove().end();
					$('#districts_td'+suffix).hide();
					nexts.push('districts');
					first = this.geo_label[suffix].district;
					ids.push('#find_districts'+suffix);
				break;
				default:
					has_selectBox=false;
					s=false;
			}
			if (s) {
				if (has_selectBox) {
					s.show();
				}
				else if (has_cusel) {
					
				}
				else if (has_selectBox) {
					s.selectBox('disable');
					s.next().slideUp();
				} else {
					s.animate({opacity:0.01});
				}
			}
			var params = {
				country:country,state:state,city:city,district:$('#find_districts'+suffix).val(),street:$('#find_street'+suffix).val(),house:$('#find_house'+suffix).val()
			};
			this.json('?action=geo&name='+from+(area?'&area='+area:''),params, function(data) {
				if (s&&data.list) {
					for (x in data.list) i++;
					if (i>0) {
						$('#'+to+'_td'+suffix).show();	
					}
					if (has_selectBox && typeof(selectbox_init)=='function') {
						if (!data||!i) {
							// nothing
						} else {
							s.find('option').remove().end();
							S.G.option(s,'',(first?first+' ('+i+')':''));
							for (id in data.list){
								S.G.option(s,id,data.list[id]);
							}
							var h = '<li rel="">'+(first?first+' ('+i+')':'')+'</li>';
							for (id in data.list){
								h += '<li rel="'+id+'">'+data.list[id]+'</li>';
							}
							s.prev().find('ul.dropdown').html(h);
						}
						if (i) {
							s.removeAttr('disabled').animate({opacity:1});
						} else {
							s.attr('disabled','disabled');
						}
						
						var option = s.find('option');
						selectbox_init(s,option);
					}
					else if (has_cusel && typeof(cuSelRefresh)=='function') {
						var h = '';
						for (id in data.list){
							h += '<span val="'+id+'">'+data.list[id]+'</span>';	
						}
						$('#cusel-scroll-'+s.attr('id')).html(h);
						var params = {
							refreshEl: '#'+s.attr('id')
						}
						cuSelRefresh(params);
					}
					/*
					else if (has_selectBox) {
						s.selectBox('enable');
						if (!data||!i) {
							s.selectBox('options', {'':first});
						} else {
							s.selectBox('options', $.extend({'0':(first?first+' ('+i+')':'')},data.list));
							s.selectBox('value', '0').next().slideDown('fast');
						}
					}
					*/ else {
						s.find('option').remove().end();
						if (!data||!i) {
							// nothing
						} else {
							first = '';
							S.G.option(s,'0',(first?first+' ('+i+')':''));
							for (id in data.list){
								S.G.option(s,id,data.list[id]);
							}
						}
						if (i) {
							s.removeAttr('disabled').animate({opacity:1});
						} else {
							s.attr('disabled','disabled');
						}
					}
				}
				if (data.js){
					eval(data.js);
				}
			});
		}
		,category_label:[]
		,populateCat:function(obj){
			var id=$(obj).attr('id');
			var catref=obj.value;
			if (!id) return alert('No ID of category dropdown defined!');
			var name=id.substring(0,id.length-1);
			var level=id.substring(id.length-1);
			var next_id = name+(parseInt(level)+1);
			var s=$('#'+next_id);
			has_selectBox = s.next().hasClass('selectBox-dropdown');
			if (!this.category_label[level]) {
				this.category_label[level]=$('option:first',s).text()
			}
			S.G.json('?action=categories',{table:name, level:level, after:catref}, function(data){
				s.find('option').remove().end();
				S.G.option(s,'',S.G.category_label[level]);
				if (data.list) {
					if (!has_selectBox) {
						for (i in data.list) {
							S.G.option(s,data.list[i].value,data.list[i].text);
						}
					}else {
						var arr=[];
						for (i in data.list) {
							arr[data.list[i].value] = data.list[i].text;
						}
						s.selectBox('options', arr);
						s.selectBox('value', '0').next().slideDown('fast');
					}
				}
			});
		}
		,option:function(s,id,val) {
			s.append($('<option>').attr('value',id).html(val));
		}
		,options:function(arr,id,keyAsVal) {
			var k,v,val,o,s;
			if (typeof(id)=='object') s=id;
			else s=$('#'+id);
			var sel=s.data('selected');
			s.children().remove().end();
			if (!arr.length) return false;
			var is_vk=typeof(arr[0])=='object';
			for (i in arr) {
				if (is_vk) {
					k=(keyAsVal ? arr[i].v : arr[i].k);
					v=arr[i].v;
				} else {
					k=(keyAsVal ? arr[i] : i);
					v=arr[i];
				}
				o=$('<option>').attr('value',k);
				o.html(v);
				s.append(o);
				if(k==sel) o.attr('selected','selected');
			}			
		}
		,foundScript:''
		,findScript:function(data) {
			var pos=data.indexOf('<script');
			if (pos!==-1) {
				var pos2=data.indexOf('</script>')+9;
				var next=data.substr(pos2);
				data=data.substring(0,pos2);
				this.foundScript+=data.substr(pos);
				return this.findScript(next);
			}
			var ret=this.foundScript;
			this.foundScript = '';
			return ret;
		}

		,handleFileSelect:function(evt) {
			var files = evt.target.files;
			for (var i = 0, f; f = files[i]; i++) {
				if (!f.type.match('image.*')) {
					continue;
				}
				var reader = new FileReader();
				reader.onload = (function(theFile) {
					return function(e) {
						var span = document.createElement('span');
						span.innerHTML = '<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>';
						document.getElementById('list').insertBefore(span, null);
					};
				})(f);
				reader.readAsDataURL(f);
			}
		}

		,fileImage:function(o,fn){
			if (window.FileReader) {
				var fr = new FileReader();
				fr.onload = function() {
					fn(fr.result);
				};
				fr.readAsDataURL(o.files[0]);
			} else {
				fn($(o).val().replace(/\\/g,'/'));
			}
		}
		
		,uploadfile_fail: false
		,uploadfile:function(set){
			$('#'+set.field).die().fileupload({
				url: S.C.HTTP_EXT+'?upload=public.'+set.name+'.'+set.id,
				maxChunkSize: 1024*2000,
				formData: {
					_t: Conf._T,
					folder:set.upload
				},
				add: function (e, data) {
					if (set.regex && !set.regex.test(data.files[0].name)) {
						S.G.msg({type:'warning',text:set.error_msg?set.error_msg:'Illegal file extension. Select correct file.',delay:3000});
					} else {
						data.submit();
					}
				},
				start:function(e,data){
					S.G.uploadfile_fail=false;
					$('#progress').show();
					$('#'+set.field).hide();
				},
				complete:function(e,data){
					
				},
				done: function (e, data) {
					if (data.result.substr(0,2)!='1/') {
						S.G.uploadfile_fail = true;
						S.G.msg({
							title: 'Upload error',
							text: data.result,
							type: 'stop'
						});
					}
					if (set.limit>1 || S.G.uploadfile_fail) {
						$('#'+set.field).show();
						$('#progress').fadeOut(function(){
							if (S.G.uploadfile_fail) return;
							$('#progress .progress-title').html('Uploaded!');
							if (set.success) (set.success)(data);
							else {
								S.G.get(S.C.REFERER);
							}
						});
					} else {
						$('#progress .progress-title').html('Uploaded!');
						if (set.success) (set.success)(data);
						else {
							S.G.get(S.C.REFERER);
						}	
					}
					setTimeout(function(){
						$('#progress .progress-bar').css('width','1%');
						$('#progress').fadeOut();
					},500);
				},
				progress: function(e,data){
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('#progress .progress-bar').css('width',progress + '%').html(progress+'%');
					$('#progress .progress-title').html('Uploading '+(data.files&&data.files[0].name?data.files[0].name+' ':''));
				},
				progressall: function (e, data) {
					
				},
				maxRetries: 100,
				retryTimeout: 500,
				fail: function (e, data) {
					S.A.L.uploadfile_fail=true;
					if (!$(this).data) return;
					var fu = $(this).data('blueimp-fileupload') || $(this).data('fileupload'),
					retries = data.context.data('retries') || 0,
					retry = function () {
						$.getJSON(S.C.HTTP_EXT+'?upload=public.'+set.name+'.'+set.id, {file: data.files[0].name,folder:set.upload}).done(function (result) {
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
						data.context.data('retries', retries);
						window.setTimeout(retry, retries * fu.options.retryTimeout);
						return;
					}
					data.context.removeData('retries');
					$.blueimp.fileupload.prototype.options.fail.call(this, e, data);
				}
			});
		}
		
		,upload2:function(set) {
			if ($('#'+set.field).fileupload) {
				this.uploadfile(set);
			} else {
				this.addJS(S.C.FTP_EXT+'tpls/js/fileupload/jquery.iframe-transport.js');
				this.addJS(S.C.FTP_EXT+'tpls/js/fileupload/jquery.fileupload.js', function(){
					S.G.uploadfile(set);
				});
			}	
		}
		,upload:function(vars) {
			var uploaded=0;
			if ($('#'+vars.file).next().find('param').length>0) return false;
			$('#'+vars.file).uploadify({
				wmode      		: 'transparent',
				uploader		: S.C.FTP_EXT+'tpls/js/uploadify/uploadify.swf',
				script			: S.C.HTTP_EXT+'?upload=public.'+vars.name+'.'+vars.id,
				cancelImg		: S.C.FTP_EXT+'tpls/img/cancel.png',
				expressInstall	: S.C.FTP_EXT+'tpls/js/uploadify/expressInstall.swf',
				folder			: vars.hash,
				queueID			: vars.queueID,
				auto			: vars.auto,
				multi			: vars.multi,
				fileExt			: vars.fileExt,
				fileDesc		: vars.fileDesc,
				buttonImg		: vars.buttonImg,
				width			: vars.width,
				height			: vars.height,
				scriptData		: vars.data,
				queueSizeLimit	: vars.limit,
				onComplete: function(e, queueID, fileObj, response, data) {
					if (vars.func) vars.func(response);
				},
				onError			: function(e, ID, fileObj, errorObj) {
					S.G.alert(errorObj);
					$('#'+vars.queueID).fadeOut();
				},
				onAllComplete	: function() {
					$('#'+vars.queueID).fadeOut();
					if (vars.complete) vars.complete();
				},
				onSelect		: function() {
					if (vars.start) vars.start();
					$('#'+vars.queueID).fadeIn('fast');
				},
				onProgress		:function(e, ID, fileObj){
					/*
					$('#test').html(e.loaded);
					if (e.lengthComputable) {
						var percent = Math.round((e.loaded / e.total) * 100);
						$('#test').html(percent);
					}
					*/
				}
			});
		}
		,delFile:function(id,file,name,obj,fn){
			S.G.json('?action=uploadify&delete',{
				id:id,
				file:file,
				name:name
			},function(data){
				if (data.ok) {
					$(obj).parent().fadeOut(function(){
						$(this).remove();
						if (fn) fn(id,file,name,obj,data);
						S.I.init($(obj).parent().parent());
					});
				} else {
					alert(data.error);
					$(obj).parent().fadeOut(function(){
						$(this).remove();
						if (fn) fn(id,file,name,obj,data);
						S.I.init($(obj).parent().parent());	
					});
				}
			});
		}
		,mainFile:function(id,file,name,obj,fn){
			$(obj).parent().parent().parent().find('li>div,tr').removeClass('main');
			S.G.json('?action=uploadify&main',{
				id:id,
				file:file,
				name:name
			},function(data){
				if (data.ok) {
					$(obj).parent().addClass('main');
				} else {
					alert(data.error);	
				}
				if (fn) fn(data);
			});
		}
		,tinymce:function(el, params){
			var settings = {
				width: '100%',
				height: 270,
				language: 'en',
				script_url: S.C.FTP_EXT+'tpls/js/tinymce/tiny_mce.js',
				content_css: S.C.FTP_EXT+'tpls/css/editor.css',
				theme: 'advanced',
				skin:'default',
				language: 'en',
				theme_advanced_toolbar_align: 'left',
				theme_advanced_resizing: false,
				theme_advanced_path: false,
				theme_advanced_resize_horizontal: 0,
				accessibility_warnings: false,
				convert_urls: false,
				convert_fonts_to_spans: false,
				font_size_style_values: false,
				fix_list_elements: false,
				force_br_newlines: true,
				fix_table_elements: false,
				fix_nesting: true,
				remove_script_host: true,
				entity_encoding: 'raw',
				cleanup_on_startup: true,
				cleanup: true,
				paste_use_dialog: true,
				
				/*spellchecker_languages: '+English=en,German=de,Russian=ru,Estonian=et',*/
				extended_valid_elements: '',
				setup: function(ed) {
					ed.addShortcut('ctrl+alt+f', 'Fullscreen', 'mceFullScreen');
				},
				plugins: 'filebrowser,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
				theme_advanced_buttons1: 'replace,|,pastetext,pasteword,|,undo,redo,|,table,separator,image,media,|,link,unlink,anchor,|,advhr,pagebreak,nonbreaking,|,insertlayer,|,cleanup,removeformat,|,charmap,attribs,|,preview,fullscreen,code',
				theme_advanced_buttons2: 'fontselect,formatselect,fontsizeselect,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,outdent,indent,|,forecolor,backcolor',
				theme_advanced_buttons3: '',
				theme_advanced_toolbar_location: 'top'
			}
			var filebrowser={};
			if (S.C.USER_ID) {
				filebrowser={
					plugin_filebrowser_width: 750,
					plugin_filebrowser_height: 420,
					plugin_filebrowser_src: S.C.FTP_EXT+'tpls/js/tinymce/plugins/filebrowser/',
					file_browser_callback: 'start_file_browser'	
				}
			}
			$(el).tinymce($.extend(settings,params,filebrowser));
		}
		,basket:function(name,id,qty,options,fn) {
			S.G.json('?basket&table='+name+'&id='+id+'&quantity='+qty,options,false,fn);
		}
		,download:function(path, admin){
			$('<iframe>').attr('src',S.C.HTTP_EXT+'?download='+path+(admin?'&'+(S.C.URL_KEY_ADMIN?S.C.URL_KEY_ADMIN:'cms'):'')).hide().appendTo($(document.body));
		}
		,hash:function(url,div,html,pageTitle){
			url=url.replace(/(&|\/)reset/,'').replace(/\?index/,'?');
			if (window.history.pushState) {
				if (url.substr(0,1)=='?') url='/'+url;
				if (div) {
					window.history.pushState({html:html,pageTitle:pageTitle,div:div,url:url},pageTitle,url);
				} else {
					window.history.pushState(null,null,url);
				}
			} else {
				window.location.hash=url;
			}
			S.G.U.hash=window.location.hash;
		}
		,xhr:false
		,abort:function(){
			/*return false;*/
			if (this.xhr&&S.G.loader) {
				this.xhr.abort();
				this.xhr = false;	
			}
		}
		,url:function(url) {
			if (url.substr(0,2)=='/?') url=url.substring(1);
			if (url.substr(0,7)=='http://') {
				var s=url.split('/');
				delete s[0];delete s[1];delete s[2];
				if (S.C.HTTP_EXT) {
					var c=S.C.HTTP_EXT.split('/').length;
					for (i=0;i<c;i++)delete s[3+i];
				}
				url=s.join('/').replace(/\/\//g,'/');
				if (url.substr(0,2)=='//') url=url.substring(1);
			}
			if (url.substr(0,1)=='/') url=url.substring(1);
			return S.C.HTTP_EXT+url;
		}
		,put:function(data,div,fn,url,nohash) {
			if (data.substr(0,1)==='{') eval('var data='+data);	
			if (typeof(data)=='object' || typeof(data)=='array') {
				S.G.msg(data, function(){
					S.G.ready(true);
				});
				return;
			}
			try{
				if (typeof(div)=='object') div.html(data);
				else if (typeof(data)=='object') return S.G.msg(data);
				else if (data.indexOf(' id="'+div+'"')!==-1) {
					S.G.html(div,data,true);/*$('#'+div).replaceWith($(data));*/
					if (!nohash) S.G.hash(url,div,data,window.document.title);
				}
				else {
					if (div=='body') div = document.body;
					S.G.html(div,data);
					if (!nohash) S.G.hash(url,div,data,window.document.title);
				}
			}catch(e){}
			try {
				if (fn) fn();
			}catch(e){}
			S.G.ready(true);	
		}
		,get:function(url,div,nohash,fn,no_scroll) {
			if (!url/*||S.G.ajaxel*/) return false;
			if(!div){div='center-area'}
			
			url=this.url(url);
			if (typeof(no_scroll)!='undefined') this.no_scroll=no_scroll;
			this.abort();
			this.xhr=$.ajax({
				type: 'get',
				url: url,
				success: function(data) {
					S.G.put(data,div,fn,url,nohash);
				}
			});	
		}
		,get_all:function(url, postfix) {
			if (!url) url = '?';
			location.href=url+postfix;
		}
		,post:function(url,post,div,nohash,fn,no_scroll) {
			if (!url) return false;
			if(!div){div='center-area'}
			var url=this.url(url);
			if (typeof(no_scroll)!='undefined') this.no_scroll=no_scroll;
			this.abort();
			post._t=Conf._T;
			this.xhr=$.ajax({
				cache: false,
				type: 'post',
				data: post,
				url: url,
				success: function(data) {
					S.G.put(data,div,fn,url,nohash);
				}
			});	
		}
		,ajax:function(url, post, div, fn) {
			if (!url) return false;
			if(!div){div='center-area'}
			var url=this.url(url);
			this.abort();
			this.xhr=$.ajax({
				cache: false,
				type: 'post',
				url: S.C.HTTP_EXT+'?ajax'+url.replace(/\?/,'&'),
				data: S.G.data(post),
				success: function(data) {
					if (typeof(data)=='object') return S.G.msg(data);
					if (fn) fn(data);
					else S.G.html(div,data);
				}
			});
		}
		,json:function(url, data, func, func2, no_err) {
			if (typeof(no_err)!=='undefined') this.no_err=no_err;
			if (url.substr(0,2)=='/?') url=url.substring(1);
			this.abort();
			
			this.xhr=$.ajax({
				_cache: false,
				dataType: 'json',
				url: (url.indexOf('://')===-1?S.C.HTTP_EXT+'?json'+url.replace(/\?/,'&').replace(/&&/,'&'):url),
				type: (data ? 'POST' : 'GET'),
				_async: false,
				data: S.G.data(data),
				success: function(data) {
					if (func) {
						func(data);	
					} else {
						S.G.msg(data, func2);
					}
				}
			});	
		}
		,data:function(data) {
			if (!data) return null;
			if (data.length) var ret = $(data).serialize();
			if (!ret) ret = data;
			ret._t=Conf._T;
			return ret;
		}
		,msg:function(data, fn) {
			if (!data) return null;
			if (typeof(data)=='String' && data.text.substring(0,1)=='{') eval('data='+data.text);
			S.G.message(data.text,data.type,false,data.focus,0,(data.delay?data.delay:1500),function() {
				if (data.redirect) {
					location.href=data.redirect;
				} 
				else if (data.get) {
					S.G.get(data.get,false,true);
				}
				else if (data.json) {
					S.G.json(data.json);
				} else {
					if (data.js) {
						eval(data.js);
					}
					if (data.reload) {
						if (location.hash) {
							S.G.get(location.hash.substring(1));
						} else {
							location.href=location.href;
						}
					}
					else {
						if (data.append) {
							for (div in data.append) {
								for (i in data.append[div]) {
									$('#'+div).append($(data.append[div][i]));
								}
							}
						}
						if (data.html) {
							for (div in data.html) {
								S.G.html(div,data.html[div]);
							}
						}
						if (data.js2) {
							eval(data.js2);	
						}
					}
				}
				if (fn) fn(data);
			});
		},
		error:function(x, t, e){
			if (S.G.no_err) return;
			if (x.readyState==4) {
				var a={
					responseText:x.responseText,
					status:x.status,
					statusText:x.statusText
				}
				S.G.alert(a,t+': '+e);
			}
			if ($.unblockUI) $.unblockUI();
		}
		,U: {
			hash:''
			,getHash:function(){
				
				/*
				if (S.G.ajaxel) {
					if (window.location.hash) this.hash=window.location.hash;
					return false;
				}
				*/
				if(window.location.hash&&window.location.hash!=this.hash&&(window.location.hash.substr(1,1)=='?'||window.location.hash.substr(1,1)=='/')){
					var l=window.location.href.replace(window.location.hash,'');
					var h=window.location.hash.replace('#','');
					if (l.indexOf(h)!==-1&&l.indexOf(h)==l.length-h.length) this.hash = window.location.hash;
					else {
						S.G.get(window.location.hash.substring(1));
					}
				}
				else if (!window.location.hash&&this.hash) {
					// this.hash='';
					window.location.href=window.location.href;
				}
			}
			/* not used
			,getUrlParams:function(url) { 
				var re = /(?:\?|&(?:amp;)?)([^=&#]+)(?:=?([^&#]*))/g, match, params = {}, 
				decode = function (s) {return decodeURIComponent(s.replace(/\+/g, " "));}; 
				if (typeof url == "undefined") url = document.location.href;
				while (match = re.exec(url)) { 
					params[decode(match[1])] = decode(match[2]); 
				}
				return params; 
			}
			*/
		}
		,highlight:function(){
			var hash=window.location.hash;
			if (hash.substring(1).indexOf('#')>1) {
				var s=hash.split('#');
				hash='#'+s[2];
			}
			if (hash.length==1||hash.substr(1,1)=='?'||hash.substr(1,1)=='/') return false;
			if (isNaN(hash.substring(1)) && isNaN(hash.substring(2))) return false;
			if (!$(hash).length || S.G.no_scroll) return false;
			
			$('html').stop().animate({
				scrollTop: $(hash).offset().top-60
			}, 'fast', function(){
				$(hash).show('highlight', {}, 3000);	
			});
			return true;
		}
		,loop:0, ajaxel:false, no_scroll:false
		,attach:function(e,f){
			if (window.attachEvent) {
				window.attachEvent(e,f); 
			} else if (window.addEventListener) { 
				window.addEventListener(e,f,false); 
			} else if (document.addEventListener) {  
				document.addEventListener(e,f);
			} else {
				return false;
			}
			return true;
		}
		,no_err:false
		,noerror:function(v){
			if (!v) {S.G.loader=false;S.G.no_err=true;return}
			var ne=S.G.no_err;
			S.G.no_err=false;
			if (ne||(S.IM&&S.IM.a)) return true;
			return false;
		}
		,sess_int:0,ajax_e:{}
		,ready:function(ajax){
			if (typeof(Conf)!='object') Conf = S.C;
			S.I.init();
			S.M.drag();
			if (typeof(videojs)!='undefined') videojs.options.flash.swf = S.C.FTP_EXT+'tpls/js/video-js/video-js.swf';
			
			if (Conf.SESSION_LIFETIME>1200) {
				clearInterval(this.sess_int);
				this.sess_int=setInterval(function(){
					S.G.loader=false;
					S.G.json('?session_lifetime');
				}, (Conf.SESSION_LIFETIME-60) * 1000);
			}
			
			if (!ajax) {
				if (typeof(Conf)=='object') S.C = Conf;
				S.F.init();S.E.init();S.X.init();
				if (typeof(S.IM)!='undefined') S.IM.init();

				if ($.loader) $.loader();
				S.G.U.getHash();
				
				/*
				// problem!!! WHY?? already initialized
				if (1||!this.attach('hashchange', S.G.U.getHash)) {
					this.loop = setInterval("S.G.U.getHash()",400);	
				}
				*/

				if (window.history.pushState && document.getElementById('center-area')) {
					var url=window.location.href;
					var ex=url.split('/');
					ex.shift();ex.shift();ex.shift();
					url='/'+ex.join('/');
					//S.G.hash(url,'center-area',document.getElementById('center-area').innerHTML,window.document.title);
					try{
						window.history.pushState({
							html:document.getElementById('center-area').innerHTML,
							pageTitle:window.document.title,
							div:'center-area',
							url:url
						},window.document.title,url);
					}catch(e){}
				}
				
				window.onpopstate = function(e){
					if(e&&e.state){
						S.G.html(e.state.div, e.state.html);
						window.document.title=e.state.pageTitle;
						S.G.no_scroll=true;
						S.G.ready(true);
					}
				}

				/*
				window.onunload=function(e){
					if (window.history.replaceState) {
						window.history.replaceState({div:'center-area',html:''},window.document.title,window.location.href);
					}
				}
				*/
				
				$(window.document).ajaxSend(function(e, r, s){
					/*
					if (S.G.ajaxel) {
						e.abort();
					}
					*/
				}).ajaxStart(function() {
					S.G.ajaxStart();
				}).ajaxStop(function(e) {
					S.G.ajaxStop(e);
				}).ajaxComplete(function(e, request, settings){
					S.G.ajaxStop(e, request, settings);
				}).ajaxError(function(e, r, s, ex){
					/*if (!S.G.ajaxel) return;*/ /*ajaxError() runs twice on https, using setTimeout to prevent double call*/
					S.G.ajax_e.e=e;
					S.G.ajax_e.r=r;
					S.G.ajax_e.s=s;
					S.G.ajax_e.ex=ex;
					S.G.no_scroll=false;
					S.G.hideLoader();
					if (r.readyState==4&&s.success) {
						
						S.G.ajaxel=false;
						clearTimeout(S.G.ajax_e.t);
						S.G.ajax_e.t=setTimeout(function(){
							var e=S.G.ajax_e.e,r=S.G.ajax_e.r,s=S.G.ajax_e.s,ex=S.G.ajax_e.ex;
							if (s.dataType=='json'&&typeof(r.responseText)!='object') {
								if (r.responseText.indexOf('{"halt":{"')!==-1) {
									if (!S.G.no_err) {
										r.responseText=r.responseText.substring(r.responseText.indexOf('{"halt":{"'));
										eval('var data='+r.responseText);
										S.G.halt(data);
									}
								}
								else if (r.responseText.indexOf('<!--error-->')!==-1) {
									if (!S.G.no_err) {
										var s=r.responseText;
										s=s.substring(s.indexOf('<!--error-->')+12);
										if (s.substring(0,6)=='<br />') s=s.substring(6);
										return S.G.halt({
											halt:{
												descr: s,
												icon: 'stop',
												title: 'PHP script error'
											}
										});
									}
								}
								var f=r.responseText.substring(0,1);
								if (f=='{'||f=='['){
									try{
										eval('ret='+r.responseText+';');
										r.responseText = ret;
									}catch(e){
										r=false;
										if (!S.G.no_err) {
											S.G.alert(r.responseText);
										}
									}
								} else {
									if (!S.G.no_err) {
										S.G.halt({
											halt:{
												descr: r.responseText,
												icon: 'stop',
												title: ex
											}
										});
									}
									return;
								}
							}
							
							if (r.responseText) {
								try{
									s.success(r.responseText);	
								}catch(e){}
							}
						},1);
					}
					S.G.no_err = false;
					
					return false;
				});
				this.no_scroll=true;
			} else {
				if (this._css_unload.length) for (i in this._css_unload) $('#'+this._css_unload[i]).remove();
				this._css_unload=this.css_unload;
				this.css_unload=[];
			}
			
			if ($.fn.mousewheel) {
				$('html').mousewheel(function(event,delta,deltaX,deltaY){
					$('html').stop(true);
					$('body').stop(true);
				});
			}
			
			if ($('#form_errors').length) {
				$('html,body').stop().animate({
					scrollTop: $('#form_errors').offset().top-60
				}, 'fast');
			}
			else if (!this.no_scroll&&!this.highlight()) {
				if ($('#top').length) var top = $('#top').offset().top;
				else var top = 0;
				$('html,body').stop().animate({
					scrollTop: top
				}, 'fast');
			}
			if (ajax) {
				if (window.history.pushState && window.location.hash && window.location.hash.substr(0,2)!='#?' && $(window.location.hash).length) {
					var top = $(window.location.hash).offset().top-60;
					$('html,body').stop().animate({
						scrollTop: top
					}, 'fast');
				}
			}
			try{
				if (typeof(ajax_ready_function)=='function') ajax_ready_function(ajax);
				if (typeof(ready)=='function') ready(ajax);
			}catch(e){}
			S.G.submitted=false;
		}
		,ajaxStart:function(){
			this.ajaxel=true;
			if (S.G.loader&&$.loader) {
				$.loader.show();
			}
		}
		,hideLoader:function(){
			S.G.loader=true;
			if ($.loader) $.loader.hide()
		}
		,ajaxStop:function(e, r, settings) {
			//if (!this.ajaxel) return;
			this.ajaxel=false;
			/*this.no_scroll=false;*/
			this.hideLoader();
			if (!r || !r.responseText || S.G.no_err) return false;
			try{
				if (r.responseText.substring(0,10)=='{"halt":{"') {
					eval('var data='+r.responseText);
					return S.G.halt(data);
				}
				if (r.responseText.indexOf('<!--error-->')!==-1) {
					var s=r.responseText;
					s=s.substring(s.indexOf('<!--error-->')+12);
					if (s.substring(0,6)=='<br />') s=s.substring(6);
					return S.G.halt({
						halt:{
							descr: s,
							icon: 'warning',
							title: 'Parse error'
						}
					});
				}
			}catch(e){}
			S.G.no_err=false;
		}
		,win:function(url, d){
			var div = $('<div />').load(url,{},function (responseText, textStatus, XMLHttpRequest) {
				d.autoOpen = false;
				d.dialogClass = 'a-fixed';
				var dialog = div.dialog(d);
				$('.a-fixed.ui-dialog').css({position: 'fixed'});
				dialog.dialog('open');
			});
		}
		,confirm:function(m,t,fok, fcancel){
			$('<div>').html(m.replace(/\n/,'<br />')).addClass('g-alert').prependTo(document.body).dialog({
				title: (t?t:'Please confirm'),
				draggable: true,
				resizable: false,
				width: 410,
				zIndex: 4000,
				modal: true,
				height: 'auto',
				close: function(ev, ui) {
					if (fcancel) fcancel();
					$(this).remove()
				},
				buttons: {
					'Cancel': function() {
						$(this).dialog('close');
					},
					'OK': function() {
						fok();
						$(this).remove();
					}
				}
			});	
		}
		,alerter:false
		,alert:function(m,t,f){
			if (this.alerter) return false;
			if (!m) return false;

			var obj=false;
			z = '';
			if (typeof(m)=='object' || typeof(m)=='array') {
				/*if (m.halt || (m.responseText && m.responseText.indexOf('{"halt":{"')>0)) return false;*/
				var h='<table style="font-size:12px;color:inherit">';
				for(i in m){
					h+='<tr><td class="a-l" style="color:inherit"><b>'+i+'</b></td><td class="a-r" colspan="2" style="color:inherit">'+m[i]+'</td></tr>';
					if (m[i]) z+=i+': '+m[i]+'\n';
					if (typeof(m[i])=='object' || typeof(m[i])=='array') {
						continue;
						for(j in m[i]){
							if (typeof(m[i][j])=='undefined') continue;
							if (typeof(m[i][j])==='function') m[i][j] = '<i>function</i>';
							else if (typeof(m[i][j])==='object') m[i][j] = '<i>object</i>';
							if (m[i][j]) z+=j+': '+m[i][j]+'\n';
							h+='<tr><td>&nbsp;</td><td>'+j+'</td><td>'+m[i][j]+'</td></tr>';
						}
					}
				}
				m=h+'</table>';
				obj=true;
			}
			else if (typeof(m)=='function') {
				obj=true;
				m=m.toString();
			}
			var w=420;
			if (obj) {
				w=700;
			}
			else {
				try{
				var ex=m.replace(/<br>/g,'<br />').split("<br />"), l=0;
				for (i in ex) if(ex[i].length>l) l = ex[i].length;
				w=l*4;
				if (w>$(window).width()-100)w=$(window).width()-100; 
				else if (w<420) w=420;
				}catch(e){}
			}
			this.alerter=$('<div>').html(m).addClass('g-alert').attr('id','g-alert').appendTo(document.body);
			try{
				this.alerter.dialog({
					title: (t?t:'Message from window'),
					draggable: true,
					resizeable: true,
					zIndex:99999,
					width: w,
					modal: false,
					height: (obj?650:'auto'),
					close: function(ev, ui) {S.G.alerter.remove();S.G.alerter=false},
					buttons: {
						'OK': function() {
							if(f) f();
							$(this).dialog('close');
						}
					}
				});
			}catch(e){
				z += '\ndialog error: '+e.message;
				alert(z);	
			}
			
			return false;
		}
		,halted:false
		,halt:function(data) {
			if(!data||!data.halt||S.G.no_err) return false;
			this.halted = true;
			window.onscroll=function() { return null }
			var top=$(window).scrollTop()+100;
			$.blockUI({ 
				css: {
					border: 'none', 
					padding: '10px',
					backgroundColor: 'maroon',
					padding: '5px',
					width: '600px',
					height: 'auto',
					cursor: 'default',
					top:  '60px', 
					left: ($(window).width() - 600) /2 + 'px'
				},
				overlayCSS: {
					cursor: 'default',
					backgroundColor: 'red',
					opacity: .3
				},
				fadeIn: 200,
				fadeOut: 100,
				title: data.halt.title,
				message: '<div class="a-halt a-'+data.halt.type+'" style="background-image:url(\''+S.C.FTP_EXT+'tpls/img/icons/'+data.halt.icon+'_48.png\')"><table><tr><td>'+data.halt.descr+''+(data.halt.db?data.halt.db:'')+'<td></tr></table></div>'
			});
			$('.blockOverlay').click($.unblockUI);
			var h=$('.blockMsg').height();var w=$('.blockMsg').width();
			$('.blockMsg').css({
				display: 'block',
				top: '-'+(h+60),
				opacity: 0.4,
				left: $(document).width()/2-400,
				position: 'absolute'
			});
			$('.blockMsg').css({
				width:700
			}).animate({
				top: top,
				opacity: 1
			},500).css({'z-index':5000}).draggable().resizable();
			return true;
		}
		,isErrorType:function(type) {
			return (type=='key' || type=='error' || type=='warning' || type=='block');
		}
		,mt:0,
		message:function(msg,type,stay,toFocus,noFade,delay,fn) {
			if (S.G.no_err) {
				if (fn) fn();
				return false;
			}
			if (!msg) {
				if (fn) fn();
				return false;
			}
			
			var t=false;
			if (typeof(msg)=='object') {
				t=msg[0];
				msg=msg[1];
			}
			clearTimeout(this.mt);
			if (type=='error') type = 'block';
			var m='<table class="a-message a-'+type+'" style="background-image:url(\''+S.C.FTP_EXT+'tpls/img/icons/'+type+'_48.png\')" cellspacing="0" cellpadding="0"><tr><td class="a-td">'+msg+'</td></tr></table>';
			try{
				$.blockUI({
					css: {
						border: 'none', 
						padding: '5px 10px',
						'border-radius': '5px',
						'-webkit-border-radius': '5px', 
						'-moz-border-radius': '5px',
						opacity: 1,
						color: 'inherit',
						height: 'auto',
						width: 450,
						top: ($(window).height() - 450) /2 + 'px', 
						left: ($(window).width() - 450) /2 + 'px',
						cursor: 'default'
					},
					overlayCSS: {
						cursor: 'default',
						backgroundColor: '#000',
						opacity: .6
					},
					title: t,
					message: m,
					fadeIn: noFade?null:300,
					fadeOut: noFade?null:100,
					bindEvents: false
				});
				$('.blockOverlay').click(function(){clearTimeout(this.mt);$.unblockUI();if(fn){fn()}});
			}catch(e){
				this.isErrorType(type) ? alert(msg) : false;
			}
			if (!stay) {
				this.mt=setTimeout(function(){
					S.G.halted = false;
					$.unblockUI();
					if(fn){fn()}
				}, (delay?delay:1300));
			}
			if (toFocus){$('#'+toFocus).focus()}
		}
	}
	,E:{
		keyCode:0,ctrlKey:false,altKey:false,shiftKey:false,cur_x:0,cur_y:0,context:false
		,init:function(e){
			if (e) {
				S.E.keyCode=e.keyCode;
				S.E.ctrlKey=e.ctrlKey;
				S.E.altKey=e.altKey;
				S.E.shiftKey=e.shiftKey;
			}
			if ($.browser.msie) {
				$(document).keydown(function(e, ui){			
					S.E.down(e);
				}).keyup(function(e, ui){
					S.E.up(e);
				});
			} else {
				$(window).keydown(function(e, ui){			
					S.E.down(e);
				}).keyup(function(e, ui){
					S.E.up(e);
				});
			}
			$(window).mousemove(function(e){
				S.E.move(e);
			});
			$(window).bind('mousedown', function(e){
				S.E.move(e);
				S.E.context=false;
			});
		}
		,move:function(e){
			if (!S.E.context) {
				e = e ? e : window.event;
				S.E.cur_x = (e&&e.clientX) ? e.clientX : S.E.cur_x;
				S.E.cur_y = (e&&e.clientY) ? e.clientY : S.E.cur_y;
			}
		}
		,up:function(e){
			this.ctrlKey=false;
			this.altKey=false;	
			this.shiftKey=false;
			if (e.keyCode==13) e.keyCode=0;
		}
		,down:function(e) {
			this.keyCode=e.keyCode;
			this.ctrlKey=e.ctrlKey;
			this.altKey=e.altKey;
			this.shiftKey=e.shiftKey;
			if (e.keyCode==123 && !e.ctrlKey && e.altKey) {
				location.href=S.C.HTTP_EXT+'';
				e.preventDefault();
			}
			else if (e.keyCode==123 && e.ctrlKey && !e.altKey) {
				location.href=S.C.HTTP_EXT+'?'+(S.C.URL_KEY_ADMIN?S.C.URL_KEY_ADMIN:'cms');
				e.preventDefault();
			}
			else if (e.keyCode==123 && e.ctrlKey && e.altKey) {
				location.href=S.C.HTTP_EXT+'?logout';
				e.preventDefault();
			}
		}
		,reset:function(){
			this.keyCode=0;this.ctrlKey=0;this.altKey=0;this.shiftKey=0;
		}
	}
	,I:{ // image
		init:function(o){
			if (!$.colorbox) return false;
			$('a.colorbox',o ? o : $('#center-area')).each(function(index, obj) {
				if ($(obj).attr('href').match(/\.(?:jpe?g|gif|png|bmp)/i)) {
					var el = $(obj).children(0);
					if (!el.length) el=$(obj);
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
				} else {
						
				}
			});
		}
	}
	,F: {
		init: function() {
			$('form.ajax_form, form.ajax').live('submit',function(e){
				e.preventDefault();
				return S.F.submit(this);
			});
			
			$('a.ajax_link, a.ajax, #center-area:not(.admin-area) a:not(.no_ajax)').die().live('click',function(e){
				var href=$(this).attr('href');
				if (!href || href=='#') return false;
				if (isNaN(href)) {
					var ex=href.split('/');
				} else {
					var ex=[href,'']	
				}
				var file=ex[ex.length-1];
				var ex=file.split('.');
				var ext=ex[ex.length-1];
				var p=ext.indexOf('?');
				if (p!==-1) ext=ext.substr(0,p);
				var p=ext.indexOf('#');
				if (p===0) {
					S.G.scrollTo($(ext),0,false,'fast');
					return false;	
				}
				else if (p!==-1) ext=ext.substr(0,p);
				if (!$(this).attr('target') && href.indexOf(':')===-1 && (ex.length==1 || (!isNaN(ext) || ext.length<3 || ext.length>4))) {
					if ($(this).hasClass('no_loader')) S.G.loader=false;
					S.G.get(href,false,$(this).hasClass('no_hash'),false,$(this).hasClass('no_scroll'));
					e.preventDefault();
					return false;
				}
			});
			/*
			$('form').keydown(function(e){
				if (!$(this).hasClass('no-enter')) {
					if(e.keyCode==13 && e.target.nodeName.toUpperCase()!='TEXTAREA') {
						$(this).submit();	
					}
				}
			});
			$('form:first').find('.focus').focus();
			*/
			
		}
		,field:false,maxlength:0
		,left:function(o,ku){
			
			if (!this.field) {
				this.field=$(o).blur(function(){
					if (S.F.field) {
						S.F.field.parent().find('.left').fadeOut();
						S.F.field=false;
					}
				});
				this.maxlength=this.field.attr('maxlength');
				if (!this.maxlength) this.maxlength = 100;
			}
			if (!ku) {
				this.field.unbind('keyup').bind('keyup',function(){
					S.F.left(this, true)
				});
			}
			var left;
			if (this.field.data('left')) left=$('#'+this.field.data('left'));
			else left=this.field.parent().find('.left');
			if (left.fadeIn('fast').find('.max').length) {
				left.fadeIn('fast').find('.max').html(this.maxlength-this.field.val().length);
			} else {
				left.fadeIn('fast').html(this.maxlength-this.field.val().length);	
			}
			
		}
		,chars:function(o) {
			if (!this.field){
				this.field=$(o).blur(function(){
					if (S.F.field) {
						S.F.field.parent().find('.chars').fadeOut();
						S.F.field=false;
					}
				});
			}
			this.field.parent().find('.chars').fadeIn('fast').find('.max').html(this.field.val().length);
		}
		,checkboxes:function(id,max){
			S.F._checkboxes(id,max);
			$('#'+id+' input:checkbox').click(function() {
				S.F._checkboxes(id,max);
			});
		}
		,_checkboxes:function(id,max) {
			if($('#'+id+' input:checkbox:checked').length >= max) {
				$('#'+id+' input:checkbox:not(:checked)').attr('disabled', 'disabled');
			} else {
				$('#'+id+' input:checkbox').removeAttr('disabled');
			}	
		}
		,datepicker:function(obj){
			$('input.datepicker',obj).unbind('datepicker').datepicker({
				numberOfMonths: 3,
				dateFormat: 'dd/mm/yy',
				onSelect: function(){
					if ($(this).hasClass('datepicker_from')) {
						if (!$(this).val()) $(this).next().val('');
						var date = $(this).datepicker("getDate");
						$(this).next().datepicker('option', 'minDate', date).trigger('focus');
					}
					else if ($(this).hasClass('datepicker_to')) {

					}
				}
			}).css({cursor:'pointer'});	
		}
		,upload:function(n,a) {
			S.G.upload({
				name	: a.name,
				id		: a.id,
				hash	: a.hash,
				file	: n+'_photo',
				queueID	: 'uploadQueue_'+n,
				multi	: false,
				auto	: true,
				buttonImg: a.img.src,
				width	: a.img.width,
				height	: a.img.height,
				fileExt	: a.ext,
				fileDesc: a.desc,
				start	: function(){
					$('#uploadQueue_'+n).fadeIn();
				},
				func	: function(r){
					if (r.substring(0,2)!='1/') {
						alert(r);	
					} else {
						var h = r.substring(2);
						$('#'+n+'_photos').find('li:not(.upload)').remove();
						$('<li>').html(h).prependTo($('#'+n+'_photos')).hide().fadeIn();
						S.I.init();
					}
				}
			});
		}
		
		,focusNext:function(obj,length,div){
			if (S.E.keyCode>=49) {
				if ($(obj).val().length>=length) {
					$('#'+div).focus().select();
				}
			}
		}
		,submitted:false
		,submit:function(form) {
			/*if (S.G.submitted) return false;*/
			S.G.submitted=true;
			var form = $(form);
			var top=form.position().top;
			if (!form.length) {
				alert('Cannot find form');
				return false;
			}
			if (!form.parent().hasClass('form_wrap')) form.wrap('<div class="form_wrap">');
			var no_scroll = false;
			if (form.hasClass('no_scroll')) {
				S.G.no_scroll=true;
				no_scroll = true;
			}
			var id = form.attr('id');
			var action = form.attr('action');
			if (!action) action = window.location.href;
			if (!isNaN(action)&&action.indexOf('#')!==-1) action=action.substring(0,action.indexOf('#'));
			
			if(!form.hasClass('no_fade')) {
				form.attr('disabled','true').stop().animate({opacity:0.2}, 'fast', function(){
					$(this).css({
						opacity: '',
						visibility: 'hidden'
					})
				});	
			}

			var type=form.attr('method') ? form.attr('method').toUpperCase() : 'GET';
			var data=form.serialize();
			if (type=='POST') {
				S.G.hash(action);
				form.find('button, input[type="button"]').prop('disabled','disabled');
			} else {
				var url=S.G.url('?'+data);
				S.G.hash(url);
			}
			
			if (S.C.HTTP_EXT && S.C.HTTP_EXT!='/') action=S.C.HTTP_EXT+action;


			$.ajax({
				type: type,
				url: action,
				data: data,
				cache: false,
				dataType: 'html',
				success: function(data) {
					if (data.substr(0,1)=='{') eval('var data='+data);
					if (typeof(data)=='object' || typeof(data)=='array') {
						S.G.msg(data, function(){
							form.show().removeAttr('disabled').stop().css({visibility:'',opacity:''});
						});
						return;
					}
					var form_id='center-area';
					if (form.hasClass('center-area')) {
						form.removeAttr('disabled').show().stop().css({visibility:'',opacity:''});
						S.G.html('center-area',data);
						form_id='center-area';
					} else {
						if (!id) var re=new RegExp('(?:<form([^>]+)class="(.*)ajax_form(.*)")');
						else var re=new RegExp('(?:<form([^>]+)id="'+id+'")');
						var m=data.match(re);
						if (m) {
							if (id) form_id=id;
							var p1=data.indexOf(m[0]);
							var h=data.substr(p1);
							var p2=h.indexOf('</form>')+7;
							if (p2==6) {
								S.G.html('center-area',data);
							} else {
								data=S.G.findScript(data.substring(0,p1))+h.substring(0,p2)+S.G.findScript(data.substr(p2+p1));
								/*form.parent().html(data);*/
								/*form.parent().get(0).innerHTML=data;*/
								S.G.html(form.parent().get(0),data);
							}
							delete h;delete js,delete m;
						} else {
							S.G.html('center-area',data);
						}
					}

					if (type=='POST') {
						S.G.submitted=true;
					//	S.G.no_scroll=true;
					}
					S.G.ready(true);
					if (no_scroll && $('.report').length) {
						S.G.scrollTo($('.report'),-150);
						S.G.no_scroll=false;
					}
					
					if (type=='POST' && !form.hasClass('no_focus')) {
						if (!no_scroll) $('html').stop().animate({scrollTop: top}, {easing: 'swing', duration: 'fast', complete: function() {
							if (form_id) {
								$('#'+form_id+' input[value=""]:not(:checkbox,:button):visible:first').focus();
							}
						}});
						else if (form_id) {
							$('#'+form_id+' input[value=""]:not(:checkbox,:button):visible:first').focus();
						}
					}
				}
			});
			return false;
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
				S.X.close();
			});
		}
		,context:function(e, x, obj, to) {
			this.close();
			var of=$(obj).offset();
			var c=$('<div>').addClass('context_shadow').css({
				position: 'fixed',
				zIndex: 1000,
				top: of.top+25-parseInt($(window).scrollTop()),
				left: of.left+5
			}).prependTo($(to?to:document.body));
			var sep=0;
			var h='<div class="context">';
			for (i=0;i<x.length;i++) {
				if(!x[i]) continue;
				if (!x[i].label) continue;
				if(x[i].sep) {
					h+='<div class="separator"></div>';
					sep++;
				}
				if (x[i].optgroup) {
					h+='<div class="optgroup" style="'+(x[i].icon?'background-image:url(\''+x[i].icon+'\')':'')+'" onclick="S.X.close();'+x[i].func+'">'+x[i].label+'</div>';
				} else {
					h+='<div class="item" onmousedown="this.className=\'item-down\'" onmouseout="this.className=\'item\'">';
					h+='<div class="inner" style="'+(x[i].icon?'background-image:url(\''+x[i].icon+'\')':'')+'" onclick="S.X.close();'+x[i].func+'">'+x[i].label+'</div></div>';
				}
			}
			h+='</div>';
			c.html(h).css({height: x.length * 20 + (sep*5) + 7}).slideDown(100, function(){
				this.opened=true;	
			});
			return false;
		}
		,close:function(){
			if (!this.opened) $('.context').remove();
			setTimeout(function(){
				S.X.opened=false;
			},100);
		}
	}
};

(function($){
	$.fn.getStyleObject = function(){
		var dom = this.get(0);
		var style;
		var returns = {};
		if(window.getComputedStyle){
			var camelize = function(a,b){
				return b.toUpperCase();
			};
			style = window.getComputedStyle(dom, null);
			for(var i = 0, l = style.length; i < l; i++){
				var prop = style[i];
				var camel = prop.replace(/\-([a-z])/g, camelize);
				var val = style.getPropertyValue(prop);
				returns[camel] = val;
			};
			return returns;
		};
		if(style = dom.currentStyle){
			for(var prop in style){
				returns[prop] = style[prop];
			};
			return returns;
		};
		return this.css();
	};
	$.fn.copyCSS = function(source){
		this.css($(source).getStyleObject());
	}
})(jQuery);

$.fn.extend({
	insertAtCaret: function(v){
		var o;
		if (typeof this[0].name !='undefined') o = this[0];
		else o = this;
		if ($.browser.msie) {
			o.focus();
			sel = document.selection.createRange();
			sel.text = v;
			o.focus();
		}
		else if ($.browser.mozilla || $.browser.webkit) {
			var s = o.selectionStart;
			var e = o.selectionEnd;
			var t = o.scrollTop;
			o.value = o.value.substring(0, s)+v+o.value.substring(e,o.value.length);
			o.focus();
			o.selectionStart = s + v.length;
			o.selectionEnd = s + v.length;
			o.scrollTop = t;
		} else {
			o.value += v;
			o.focus();
		}
	}
	,prettyCheckboxes: function(s) {
		s = jQuery.extend({
			checkboxWidth: 17,
			checkboxHeight: 17,
			className : 'prettyCheckbox',
			display: 'inline-block'
		}, s);

		$(this).each(function(){
			$label = $('label[for="'+$(this).attr('id')+'"]');
			$label.prepend("<span class='holderWrap'><span class='holder'></span></span>");
			if($(this).is(':checked')) $label.addClass('checked');
			$label.addClass(s.className).addClass($(this).attr('type')).addClass(s.display);
			$label.find('span.holderWrap').width(s.checkboxWidth).height(s.checkboxHeight);
			$label.find('span.holder').width(s.checkboxWidth);
			$(this).addClass('hiddenCheckbox');
			$label.bind('click',function(){
				$('input#' + $(this).attr('for')).triggerHandler('click');				
				if($('input#' + $(this).attr('for')).is(':checkbox')){
					$(this).toggleClass('checked');
					$('input#' + $(this).attr('for')).checked = true;
					$(this).find('span.holder').css('top',0);
				}else{
					$toCheck = $('input#' + $(this).attr('for'));
					$('input[name="'+$toCheck.attr('name')+'"]').each(function(){
						$('label[for="' + $(this).attr('id')+'"]').removeClass('checked');	
					});
					$(this).addClass('checked');
					$toCheck.checked = true;
				};
			});			
			$('input#' + $label.attr('for')).bind('keypress',function(e){
				if(e.keyCode == 32){
					if($.browser.msie){
						$('label[for="'+$(this).attr('id')+'"]').toggleClass("checked");
					}else{
						$(this).trigger('click');
					}
					return false;
				};
			});
		});
	}
	,checkAllPrettyCheckboxes: function(caller, container){
		if($(caller).is(':checked')){
			$(container).find('input[type=checkbox]:not(:checked)').each(function(){
				$('label[for="'+$(this).attr('id')+'"]').trigger('click');
				if($.browser.msie){
					$(this).attr('checked','checked');
				}else{
					$(this).trigger('click');
				};
			});
		}else{
			$(container).find('input[type=checkbox]:checked').each(function(){
				$('label[for="'+$(this).attr('id')+'"]').trigger('click');
				if($.browser.msie){
					$(this).attr('checked','');
				}else{
					$(this).trigger('click');
				};
			});
		};
	}
	,prettyComments: function(s) {
		s = jQuery.extend({
			animate: false,
			animationSpeed: 'fast',
			maxHeight : 400,
			alreadyAnimated: false,
			init: true
		}, s);
		$('body').append('<div id="comment_hidden"></div>');
		var setCSS = function(which){
			$("#comment_hidden").css({
				'position':'absolute',
				'top': -10000,
				'left': -10000,
				'width': $(which).width(),
				'min-height': $(which).height(),
				'font-family': $(which).css('font-family'),
				'font-size': $(which).css('font-size'),
				'line-height': $(which).css('line-height')
			});
			if($.browser.msie && parseFloat($.browser.version) < 7){
				$("#comment_hidden").css('height',$(which).height());
			}
		}
		var copyContent = function(which){
			// Convert the line feeds into BRs
			theValue = $(which).attr('value') || "";
			theValue = theValue.replace(/\n/g,'<br />');			
			$("#comment_hidden").html(theValue + '<br />');			
			if(!s.init){
				if($("#comment_hidden").height() > $(which).height()){
					if($('#comment_hidden').height() > s.maxHeight){
						$(which).css('overflow-y','scroll');
					}else{
						$(which).css('overflow-y','hidden');
						expand(which);
					};
				}else if($("#comment_hidden").height() < $(which).height()){
					if($('#comment_hidden').height() > s.maxHeight){
						$(which).css('overflow-y','scroll');
					}else{
						$(which).css('overflow-y','hidden');
						shrink(which);
					}
				}
			}
		}
		var expand = function(which){
			if(s.animate && !s.alreadyAnimated){
				s.alreadyAnimated = true;
				$(which).animate({'height':$("#comment_hidden").height()},s.animationSpeed,function(){
					s.alreadyAnimated = false;
				});
			}else if(!s.animate && !s.alreadyAnimated){
				$(which).height($("#comment_hidden").height());
			}
		}
		
		var shrink = function(which){
			if(s.animate && !s.alreadyAnimated){
				s.alreadyAnimated = true;
				$(which).animate({'height':$("#comment_hidden").height()},s.animationSpeed,function(){
					s.alreadyAnimated = false;
				})
			}else{
				$(which).height($("#comment_hidden").height());
			}
		}
		
		$(this).each(function(){
			$(this).css({
				'overflow':'hidden'
			})
			.bind('keyup',function(){
				copyContent($(this));
			});			
			// Make sure all the content in the textarea is visible
			setCSS(this);
			copyContent($(this));
			if($("#comment_hidden").height() > s.maxHeight){
				$(this).css({
					'overflow-y':'scroll',
					'height':s.maxHeight
				});
			}else{
				$(this).height($("#comment_hidden").height());
			};
			
			s.init = false;
		});
	}
});

(function (factory) {
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else {
		factory(window.jQuery || window.$);
	}
} (function ($) {
	var
	defaults = {
		className: 'autosizejs',
		append: '',
		callback: false,
		resizeDelay: 10
	},
	copy = '<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',
	typographyStyles = ['fontFamily', 'fontSize', 'fontWeight', 'fontStyle', 'letterSpacing', 'textTransform', 'wordSpacing', 'textIndent'],
	mirrored,
	mirror = $(copy).data('autosize', true)[0];
	mirror.style.lineHeight = '99px';
	if ($(mirror).css('lineHeight') === '99px') {
		typographyStyles.push('lineHeight');
	}
	mirror.style.lineHeight = '';
	$.fn.autosize = function (options) {
		options = $.extend({},
		defaults, options || {});
		if (mirror.parentNode !== document.body) {
			$(document.body).append(mirror);
		}
		return this.each(function () {
			var
			ta = this,
			$ta = $(ta),
			maxHeight,
			minHeight,
			boxOffset = 0,
			callback = $.isFunction(options.callback),
			originalStyles = {
				height: ta.style.height,
				overflow: ta.style.overflow,
				overflowY: ta.style.overflowY,
				wordWrap: ta.style.wordWrap,
				resize: ta.style.resize
			},
			timeout,
			width = $ta.width();
			if ($ta.data('autosize')) {
				return;
			}
			$ta.data('autosize', true);
			if ($ta.css('box-sizing') === 'border-box' || $ta.css('-moz-box-sizing') === 'border-box' || $ta.css('-webkit-box-sizing') === 'border-box') {
				boxOffset = $ta.outerHeight() - $ta.height();
			}
			minHeight = Math.max(parseInt($ta.css('minHeight'), 10) - boxOffset || 0, $ta.height());
			$ta.css({
				overflow: 'hidden',
				overflowY: 'hidden',
				wordWrap: 'break-word',
				resize: ($ta.css('resize') === 'none' || $ta.css('resize') === 'vertical') ? 'none': 'horizontal'
			});
			function initMirror() {
				var styles = {},
				ignore;
				mirrored = ta;
				mirror.className = options.className;
				maxHeight = parseInt($ta.css('maxHeight'), 10);
				$.each(typographyStyles, function (i, val) {
					styles[val] = $ta.css(val);
				});
				$(mirror).css(styles);
				if ('oninput' in ta) {
					var width = ta.style.width;
					ta.style.width = '0px';
					ignore = ta.offsetWidth;
					ta.style.width = width;
				}
			}
			function adjust() {
				var height, original, width, style;
				if (mirrored !== ta) {
					initMirror();
				}
				mirror.value = ta.value + options.append;
				mirror.style.overflowY = ta.style.overflowY;
				original = parseInt(ta.style.height, 10);
				if ('getComputedStyle' in window) {
					style = window.getComputedStyle(ta);
					width = ta.getBoundingClientRect().width;
					$.each(['paddingLeft', 'paddingRight', 'borderLeftWidth', 'borderRightWidth'], function (i, val) {
						width -= parseInt(style[val], 10);
					});
					mirror.style.width = width + 'px';
				}
				else {
					mirror.style.width = Math.max($ta.width(), 0) + 'px';
				}
				mirror.scrollTop = 0;
				mirror.scrollTop = 9e4;
				height = mirror.scrollTop;
				if (maxHeight && height > maxHeight) {
					ta.style.overflowY = 'scroll';
					height = maxHeight;
				} else {
					ta.style.overflowY = 'hidden';
					if (height < minHeight) {
						height = minHeight;
					}
				}
				height += boxOffset;
				if (original !== height) {
					ta.style.height = height + 'px';
					if (callback) {
						options.callback.call(ta, ta);
					}
				}
			}
			function resize() {
				clearTimeout(timeout);
				timeout = setTimeout(function () {
					if ($ta.width() !== width) {
						adjust();
					}
				},
				parseInt(options.resizeDelay, 10));
			}
			if ('onpropertychange' in ta) {
				if ('oninput' in ta) {
					$ta.on('input.autosize keyup.autosize', adjust);
				} else {
					$ta.on('propertychange.autosize', function () {
						if (event.propertyName === 'value') {
							adjust();
						}
					});
				}
			} else {
				$ta.on('input.autosize', adjust);
			}
			if (options.resizeDelay !== false) {
				$(window).on('resize.autosize', resize);
			}
			$ta.on('autosize.resize', adjust);
			$ta.on('autosize.resizeIncludeStyle', function () {
				mirrored = null;
				adjust();
			});
			$ta.on('autosize.destroy', function () {
				mirrored = null;
				clearTimeout(timeout);
				$(window).off('resize', resize);
				$ta.off('autosize').off('.autosize').css(originalStyles).removeData('autosize');
			});
			adjust();
		});
	};
}));


(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        module.exports = factory;
    } else {
        factory(jQuery);
    }
}(function ($) {
    var toFix = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'];
    var toBind = 'onwheel' in document || document.documentMode >= 9 ? ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'];
    var lowestDelta, lowestDeltaXY;
    if ($.event.fixHooks) {
        for (var i = toFix.length; i;) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }
    $.event.special.mousewheel = {
        setup: function() {
            if (this.addEventListener) {
                for (var i = toBind.length; i;) {
                    this.addEventListener(toBind[--i], handler, false);
                }
            } else {
                this.onmousewheel = handler;
            }
        },
        teardown: function() {
            if (this.removeEventListener) {
                for (var i = toBind.length; i;) {
                    this.removeEventListener(toBind[--i], handler, false);
                }
            } else {
                this.onmousewheel = null;
            }
        }
    };
    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
        },

        unmousewheel: function(fn) {
            return this.unbind('mousewheel', fn);
        }
    });
    function handler(event) {
        var orgEvent   = event || window.event,
            args       = [].slice.call(arguments, 1),
            delta      = 0,
            deltaX     = 0,
            deltaY     = 0,
            absDelta   = 0,
            absDeltaXY = 0,
            fn;
        event = $.event.fix(orgEvent);
        event.type = 'mousewheel';
        if (orgEvent.wheelDelta) { delta = orgEvent.wheelDelta; }
        if (orgEvent.detail)     { delta = orgEvent.detail * -1; }
        deltaY = delta;
        if (orgEvent.axis !== undefined && orgEvent.axis === orgEvent.HORIZONTAL_AXIS) {
            deltaY = 0;
            deltaX = delta * -1;
        }
        if (orgEvent.deltaY) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if (orgEvent.deltaX) {
            deltaX = orgEvent.deltaX;
            delta  = deltaX * -1;
        }
        if (orgEvent.wheelDeltaY !== undefined) { deltaY = orgEvent.wheelDeltaY; }
        if (orgEvent.wheelDeltaX !== undefined) { deltaX = orgEvent.wheelDeltaX * -1; }
        absDelta = Math.abs(delta);
        if (!lowestDelta || absDelta < lowestDelta) { lowestDelta = absDelta; }
        absDeltaXY = Math.max(Math.abs(deltaY), Math.abs(deltaX));
        if (!lowestDeltaXY || absDeltaXY < lowestDeltaXY) { lowestDeltaXY = absDeltaXY; }
        fn     = delta > 0 ? 'floor' : 'ceil';
        delta  = Math[fn](delta  / lowestDelta);
        deltaX = Math[fn](deltaX / lowestDeltaXY);
        deltaY = Math[fn](deltaY / lowestDeltaXY);
        args.unshift(event, delta, deltaX, deltaY);
        return ($.event.dispatch || $.event.handle).apply(this, args);
    }
}));


Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep) {  
   var n = this, 
   c = isNaN(decimals) ? 2 : Math.abs(decimals),
   d = decimal_sep || '.',
   t = (typeof thousands_sep === 'undefined') ? ' ' : thousands_sep,
   sign = (n < 0) ? '-' : '', 
   i = parseInt(n = Math.abs(n).toFixed(c)) + '',
   j = ((j = i.length) > 3) ? j % 3 : 0;  
   return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
} 

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined'){
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else {
        var v = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    v = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return v;
    }
}
$.loader = function(s) {
	if (typeof(Conf)=='undefined') {
		Conf = {};
	}
	s = $.extend({
		delay: 0,
		loader: S.C.FTP_EXT+'tpls/img/loading/ajax-loader.gif',
		offset_top: 13,
		offset_left: 10
	}, s);
	$.loader.pos = function(e){
		e = e ? e : window.event;
		S.E.cur_x = (e&&e.clientX) ? e.clientX : S.E.cur_x;
		S.E.cur_y = (e&&e.clientY) ? e.clientY : S.E.cur_y;
		$('#loader').css({zIndex:9000,position:'fixed','top':S.E.cur_y + s.offset_top,'left':S.E.cur_x + s.offset_left});
	}	
	$.loader.show = function(delay){
		if ($('#loader').length) return;
		$('<div></div>').attr('id','loader').addClass('loader').addClass('loader_'+ s.theme).prependTo('body').hide();
		if ($.browser.msie && $.browser.version==6) $('#loader').addClass('pl_ie6');
		$('<img />').attr('src',s.loader).prependTo('#loader');
		$.loader.pos();
		$('#loader').show();
		delay = (delay) ? delay : s.delay;			
		if(delay){setTimeout(function(){jQuery.loader.hide()}, delay);}
		$.loader.int=setInterval(function(){jQuery.loader.move()},1);
	}
	$.loader.move = function(){
		$('#loader').css({top:S.E.cur_y + s.offset_top,left:S.E.cur_x + s.offset_left});
	}
	$.loader.hide = function(){
		$('#loader').fadeOut('fast',function(){
			clearInterval($.loader.int);
			$(this).remove();	
		});
	}
	return this;
};
$.fn.center = function () {
    this.css("position","fixed");
    this.css("top", ($(window).height() / 2) - (this.outerHeight() / 2));
    this.css("left", ($(window).width() / 2) - (this.outerWidth() / 2));
    return this;
};

$.sound = {
	tracks: {},
	enabled: true,
	template: function(src, vol, loop) {
		return '<embed style="height:0" loop="false" src="'+src+'" volume="'+vol+'" autostart="true" hidden="true"/>';
	},
	play: function(url, options){
		if (!this.enabled) return;
		
		var settings = $.extend({
			url: url,
			timeout: 3000,
			loop_timeout: 5000,
			volume: -1000,
			loop: false,
			append : document.body
		}, options);	
		if (S.C.DEVICE=='mobile' || S.C.DEVICE=='tablet') {
			var h = '<audio preload autoplay><source src="'+(settings.url)+'" type="audio/ogg" /><source src="'+((settings.url).replace('.ogg','.mp3'))+'" type="audio/mpeg" /></audio>';
			var div=$('<div>').html(h);
			div.appendTo(settings.append);
			setTimeout(function() {
				div.remove();
			},5000);
			return div;
		}
		if (window.Audio) {
			var audio = new Audio();
			audio.src = url;
			audio.load();
			audio.play();
			return audio;
		}
		if (settings.track) {
			if (this.tracks[settings.track]) {
				var current = this.tracks[settings.track];
				current.Stop && current.Stop();
				current.remove();  
			}
		}
		var element = $.browser.msie ? $('<bgsound/>').attr({src: settings.url,loop: 1,autostart: true,volume: settings.volume}) : $(this.template(settings.url, settings.volume, settings.loop));
		element.appendTo(settings.append);
		if (settings.track) this.tracks[settings.track] = element;
		setTimeout(function() {element.remove()}, settings.loop ? settings.loop_timeout: settings.timeout);
		return element;
	}
};


function popup(url,w,h,s){if(s){s='yes'}else{s='no'}if(!w){w=800}if(!h){h=600}var c=[$(window).width(), $(window).height()];var top=(c[1]-h)/2;var left=(c[0]-w)/2;var win=window.open(url,url.replace(/[^A-Za-z0-9]/gi,''),'directories=no,statusbar=no,location=no,scrollbars='+s+',resizable=yes,padding=0,margin=0,width='+w+',height='+h+',top='+top+',left='+left);win.focus();return win}
function popup2(url){popup(url,1000,$(window).height()-110,1)}
function modal(url,w,h) {if(window.showModalDialog){var win=window.showModalDialog(url,url.replace(/[^A-Za-z0-9]/gi,''),'dialogWidth:'+w+'px;dialogHeight:'+h+'px')}else{if(!w){w=800}if(!h){h=600}var c=[$(window).width(), $(window).height()];var top=(c[1]-h)/2;var left=(c[0]-w)/2;var win=window.open(url,'name','width='+w+',height='+h+',toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,top='+top+',left='+left)}}
function printwin(url){var w=1000,h=700;var c=[$(window).width(), $(window).height()];var top=(c[1]-h)/2;var left=(c[0]-w)/2;var win=window.open('/'+url,url.replace(/[^A-Za-z0-9]/gi,''),'directories=0,toolbar=1,menubar=1,statusbar=0,location=0,scrollbars=1,resizable=1,width='+w+',height='+h+',top='+top+',left='+left);win.focus();win.print()}
function flash_play(ad,ct,w,h,r){var url=ad+(ct?'?clickTAG='+ct:'');var t='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="'+w+'" height="'+h+'" align="middle" style="height:'+h+'px!important"><param name="movie" value="'+url+'"/><param name="menu" value="false"><param name="allowScriptAccess" value="sameDomain" /><param name="quality" value="high"><param name="wmode" value="transparent">'+(!$.browser.msie?'<object type="application/x-shockwave-flash" style="height:'+h+'px!important" data="'+url+'" width="'+w+'" height="'+h+'"><param name="movie" value="'+url+'"/>':'')+'<a href="http://www.adobe.com/go/getflash" target="_blank"><img src="//www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/></a>'+(!$.browser.msie?'</object>':'')+'</object>';if(r){return t}else{document.write(t)}}
function addbookmark(obj,title,url){var title = title ? title : document.title,url = url ? url : window.location.href;if (window.sidebar){window.sidebar.addPanel (title, url, "")}else if(window.opera && window.print){obj.setAttribute('rel','sidebar');obj.setAttribute('href',url);obj.setAttribute('title',title)}else if(document.all){window.external.AddFavorite(url, title)}return true}
function copy(t){var d=document.createElement('TEXTAREA');d.value=t.replace(/    /g,"\t");var c=d.createTextRange();c.execCommand('Copy')}
function p(r) {S.G.alert(r,'dump')}
if (typeof(Conf)=='object') S.C=Conf;
$(function() {S.G.ready(false)});