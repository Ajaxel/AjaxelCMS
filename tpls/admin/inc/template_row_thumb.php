/*<script>*/
var icon_folder = 130;
if (a.d) {
	folder_url = '?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&file_folder=<?php echo $this->file_folder?>&folder='+a.p;
	if (a.f!='..') {
		h +=  '<div class="a-pic"><a href="javascript:;" onclick="S.A.L.get(\''+folder_url+'\')"><img src="<?php echo FTP_EXT?>tpls/img/ext/'+icon_folder+'/folder.png" alt="" /></a></div>';
		h +=  '<div class="a-title"><a href="javascript:;" id="a-file_'+a.x+'" onclick="S.A.L.get(\''+folder_url+'\')">'+a.f+'</a></div><div class="a-size">'+a.t+' '+a.m+'</div>';
	} else {
		h +=  '<div class="a-pic"><a href="javascript:;" onclick="S.A.L.get(\''+folder_url+'\')"><img src="<?php echo FTP_EXT?>tpls/img/ext/'+icon_folder+'/folder-parent.png" alt="" /></a></div>';
		h +=  '<div class="a-title"><a href="javascript:;" onclick="S.A.L.get(\''+folder_url+'\')">.. <?php echo lang('$parent')?></a></div>';
	}
	h +=  '<div class="a-actions">';
	if (a.f!='..' && a.a) {
		h +=  '<a href="javascript:;" title="<?php echo lang('$Rename?')?>" onclick="S.A.L.rename_file(\''+a.f+'\', \''+a.p+'\', \'&file_folder=<?php echo $this->file_folder?>\', $(\'#a-file_'+a.x+'\'), this, true, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/draw-text.png" alt="" /></a>';
		h +=  '<a href="javascript:;" onclick="S.A.L.del_file(\''+a.f+'\', \''+a.p+'\', \'&file_folder=<?php echo $this->file_folder?>\',this, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" /></a>';
	}
}
else if (a.e) {
	var edit_link='/?window&<?php echo URL_KEY_ADMIN?>=templates&popup=1&file_folder=<?php echo $this->file_folder?>&folder=<?php echo $this->folder?>&file='+a.f;
	h +=  '<div class="a-pic"><a href="javascript:;" onclick="S.A.W.popup(\''+edit_link+'\',$(window).width()-$(window).width()/3,$(window).height()-$(window).height()/3);">'+(a.i?'<img src="<?php echo FTP_EXT?>tpls/img/ext/'+icon_folder+'/'+a.o+'.png" alt="" />':'<img src="<?php echo FTP_EXT?>tpls/img/ext/'+icon_folder+'/txt.png" alt="" />')+'</a></div>';
	h +=  '<div class="a-title"><a href="javascript:;" id="a-file_'+a.x+'" onclick="S.A.W.popup(\''+edit_link+'\',$(window).width()-$(window).width()/3,$(window).height()-$(window).height()/3);">'+a.f.replace('.'+a.o+'','')+'</a></div><div class="a-size">'+a.s+' '+a.m+' <b>'+a.o+'</div>';
	h +=  '<div class="a-actions">';
	if (a.a) {
		h +=  '<a href="javascript:;" title="<?php echo lang('$Rename?')?>" onclick="S.A.L.rename_file(\''+a.f+'\', \'<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', $(\'#a-file_'+a.x+'\'), this, false, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/draw-text.png" /></a>';
	}
	h +=  '<a href="javascript:;" title="<?php echo lang('$Edit?')?>" onclick="S.A.W.popup(\''+edit_link+'\',$(window).width(),$(window).height());"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
	h +=  '<a href="javascript:;" title="<?php echo lang('$Download?')?>" onclick="S.G.download(\'<?php echo FTP_DIR_TPLS.$this->file_folder?>/<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'\', true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/apps/ktorrent.png" /></a>';
	if (a.a) {
		h +=  '<a href="javascript:;" onclick="S.A.L.del_file(\''+a.f+'\', \'<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-trash.png" /></a>';
	}
}
else {
	if (a.u) {
		h +=  '<div class="a-pic"><a href="<?php if ($this->file_folder!=AdminTemplates::ROOT_FOLDER):?>/<?php echo HTTP_DIR_TPLS.$this->file_folder?><?php endif;?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'" class="a-thumb" rel="templates" style="background:url(\'<?php if ($this->file_folder!=AdminTemplates::ROOT_FOLDER):?>/<?php echo HTTP_DIR_TPLS.$this->file_folder?><?php endif;?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\') center no-repeat;background-size:fit;width:130px;height:130px"><img src="<?php if ($this->file_folder!=AdminTemplates::ROOT_FOLDER):?>/<?php echo HTTP_DIR_TPLS.$this->file_folder?><?php endif;?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'" width="130" alt="<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'" title="<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'" style="visibility:hidden" alt="<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'" title="<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'" /></a></div>';
	} else {
		h +=  '<div class="a-pic"><a href="/<?php echo HTTP_DIR_TPLS.$this->file_folder?>/<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'" target="_blank">'+(a.i?'<img src="<?php echo FTP_EXT?>tpls/img/ext/'+icon_folder+'/'+a.o+'.png" alt="" />':'<img src="<?php echo FTP_EXT?>tpls/img/ext/'+icon_folder+'/txt.png" alt="" />')+'</a></div>';
	}
	h +=  '<div class="a-title"><a href="/<?php echo HTTP_DIR_TPLS.$this->file_folder?>/<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'" id="a-file_'+a.x+'" target="_blank">'+a.f.replace('.'+a.o+'','')+'</a></div><div class="a-size">'+a.s+' '+a.m+' <b>'+a.o+'</b></div>';
	h +=  '<div class="a-actions">';
	if (a.a) {
		h +=  '<a href="javascript:;" title="<?php echo lang('$Rename?')?>" onclick="S.A.L.rename_file(\''+a.f+'\', \'<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', $(\'#a-file_'+a.x+'\'), this, false, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/draw-text.png" alt="" /></a>';
	}
	h +=  '<a href="javascript:;" title="<?php echo lang('$Download?')?>" onclick="S.G.download(\'<?php echo FTP_DIR_TPLS.$this->file_folder?>/<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'\', true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/apps/ktorrent.png" /></a>';
	if (a.a) {
		h +=  '<a href="javascript:;" onclick="S.A.L.del_file(\''+a.f+'\', \'<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-trash.png" /></a>';
	}
}
if (!a.d) {
	h +=  '<a href="javascript:;" title="<?php echo lang('$Info')?>" onclick="S.G.alert(\'<div style=&quot;font-size:12px&quot;><?php echo HTTP_BASE?>tpls/<?php echo $this->file_folder?>/<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'<hr /><a target=&quot;_blank&quot; href=&quot;<?php echo HTTP_BASE?>tpls/<?php echo $this->file_folder?>/<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'&quot;>tpls/<?php echo $this->file_folder?>/<?php echo ($this->folder?$this->folder.'/':'')?>'+a.f+'</div>\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/documentinfo-koffice.png" /></a>';
}
h +=  '</div>';