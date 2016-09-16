/*<script>*/
,setImages:function(files){
	var h = '<ul id="a-files_<?php echo $this->name_id?>" class="a-images">',a,j=0,text,ti=40;
	for (i=0;i<files.length;i++) {
		ti++;
		a=files[i];
		h += '<li class="a-photo'+(a.m?' a-main_photo':'')+(a.id?' a-sortable':'')+'" id="a-img_'+a.id+'">';
		switch (a.t) {
			case 'image':
				text = '('+a.w+'x'+a.h+') '+a.s+' '+a.f;
				h += '<a href="'+S.A.W.filePath('<?php echo $this->name_id?>',S.A.P.js(a.f),'th1')+'" target="_blank" class="a-thumb" rel="<?php echo $this->name_id?>"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',S.A.P.js(a.f),(a.r?'th3':'th1'))+'?n='+Math.random()+'" data-file="'+a.f+'" style="width:85px" class="a-pic {w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></a>';
			break;
			default:
				text = a.s+' '+a.f;
				h += '<a href="javascript:;" onclick="S.G.download(\''+S.A.W.filePath('<?php echo $this->name_id?>',S.A.P.js(a.f),'th1')+'\', true)"><img src="<?php echo FTP_EXT?>tpls/img/ext/32/'+a.e+'.png" title="'+a.e+'" data-file="'+a.f+'" alt="'+a.e+'" class="{w:'+a.w+',h:'+a.h+',p:\''+S.A.W.filePath('<?php echo $this->name_id?>',S.A.P.js(a.f),'th1')+'\',t:\''+a.media+'\'}" /></a>';
			break;
		}
		h += '<div class="a-photo_info"><input type="text" tabindex="'+ti+'" name="data[files]['+i+'][title]" value="'+a.title+'" /><textarea name="data[files]['+i+'][descr]">'+a.descr+'</textarea><input type="hidden" name="data[files]['+i+'][id]" value="'+a.id+'" /><input type="hidden" name="data[files]['+i+'][file]" value="'+S.A.P.js2(a.f)+'" /></div>';
		h += '</li>';
		j++;
	}
	if (j) S.G.html('a-files_div_<?php echo $this->name_id?>',h);
	S.A.I.load();
	S.A.W.context_images('<?php echo $this->name_id?>','file');
}
