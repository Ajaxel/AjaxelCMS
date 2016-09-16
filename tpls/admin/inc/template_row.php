/*<script>*/
h += '<td class="a-l" width="1%"><input type="checkbox" class="a_chk_<?php echo $this->name?>" value="'+a.f+'"></td>';
if (a.d) {
	folder_url = '?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&file_folder=<?php echo $this->file_folder?>&folder='+a.p;
	if (a.f!='..') {
		h +=  '<td class="a-l a-action_buttons"><a href="javascript:;"'+(a.a?' title="<?php echo lang('$Rename?')?>" onclick="S.A.L.rename_file(\''+a.f+'\', \''+a.p+'\', \'&file_folder=<?php echo $this->file_folder?>\', $(\'#a-file_'+a.x+'\'), this, true)"':'')+'><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/places/folder-yellow.png" alt="" /></a></td>';
		h +=  '<td class="a-l a-links_block" nowrap><a href="javascript:;" id="a-file_'+a.x+'" onclick="S.A.L.get(\''+folder_url+'\')">'+a.f+'</a></td><td class="a-l" nowrap>'+a.t+'</td>';
	} else {
		h +=  '<td class="a-l a-links_block"><a href="javascript:;" onclick="S.A.L.get(\''+folder_url+'\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/places/folder-blue.png" alt="" /></a></td>';
		h +=  '<td class="a-l a-links_block"><a href="javascript:;" onclick="S.A.L.get(\''+folder_url+'\')">.. <?php echo lang('$parent')?></a></td><td class="a-l">&nbsp;</td>';
	}
	h +=  '<td class="a-l">'+a.m+'</td>';
	h +=  '<td class="a-r a-action_buttons">';
	if (a.f!='..') {
		h +=  '<a href="javascript:;" title="<?php echo lang('$Download?')?>" onclick="S.G.download(\'<?php echo FTP_DIR_TPLS.$this->file_folder?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\', true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/apps/ktorrent.png" /></a>';
	}
	if (a.f!='..' && a.a) {
		//h +=  '<a href="javascript:;" title="<?php echo lang('$Delete folder?')?>" onclick="S.A.L.del_file(\''+a.f+'\', \''+a.p+'\', \'&file_folder=<?php echo $this->file_folder?>\',this, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" /></a>';
	}
}
else if (a.e) {
	edit_link='/?window&<?php echo URL_KEY_ADMIN?>=templates&popup=1&file_folder=<?php echo $this->file_folder?>&folder=<?php echo $this->folder?>&file='+a.f;
	h +=  '<td class="a-l a-action_buttons"><a href="javascript:;"'+(a.a?' title="<?php echo lang('$Rename?')?>" onclick="S.A.L.rename_file(\''+a.f+'\', \'<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', $(\'#a-file_'+a.x+'\'), this)"':'')+'>'+(a.i?'<img src="<?php echo FTP_EXT?>tpls/img/ext/16/'+a.o+'.png" alt="" />':'<img src="<?php echo FTP_EXT?>tpls/img/ext/16/txt.png" alt="" />')+'</a></td>';
	h +=  '<td class="a-l a-links_block"><a href="javascript:;" id="a-file_'+a.x+'" onclick="S.A.W.popup(\''+edit_link+'\',$(window).width()-$(window).width()/4.5,$(window).height()-$(window).height()/4.5);">'+a.f+'</a></td>';
	h +=  '<td class="a-l">'+a.s+'</td>';
	h +=  '<td class="a-l">'+a.m+'</td>';
	h +=  '<td class="a-r a-action_buttons">';
	h +=  '<a href="javascript:;" title="<?php echo lang('$Edit?')?>" onclick="S.A.W.popup(\''+edit_link+'\',$(window).width(),$(window).height());"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
	h +=  '<a href="javascript:;" title="<?php echo lang('$Download?')?>" onclick="S.G.download(\'<?php echo FTP_DIR_TPLS.$this->file_folder?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\', true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/apps/ktorrent.png" /></a>';
	if (a.a) {
		//h +=  '<a href="javascript:;" title="<?php echo lang('$Delete file?')?>" onclick="S.A.L.del_file(\''+a.f+'\', \'<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" /></a>';
	}
}
else {
	h +=  '<td class="a-l a-action_buttons"><a href="javascript:;"'+(a.a?' title="<?php echo lang('$Rename?')?>" onclick="S.A.L.rename_file(\''+a.f+'\', \'<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', $(\'#a-file_'+a.x+'\'), this)"':'')+'>'+(a.i?'<img src="<?php echo FTP_EXT?>tpls/img/ext/16/'+a.o+'.png" alt="" />':'<img src="<?php echo FTP_EXT?>tpls/img/ext/16/txt.png" alt="" />')+'</a></td>';
	h +=  '<td class="a-l a-links_block"><a href="<?php if ($this->file_folder!=AdminTemplates::ROOT_FOLDER):?>/<?php echo HTTP_DIR_TPLS.$this->file_folder?><?php endif;?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'" id="a-file_'+a.x+'" target="_blank">'+a.f+'</a></td>';
	h +=  '<td class="a-l">'+a.s+'</td>';
	h +=  '<td class="a-l">'+a.m+'</td>';
	h +=  '<td class="a-r a-action_buttons">';
	h +=  '<a href="javascript:;" title="<?php echo lang('$Download?')?>" onclick="S.G.download(\'<?php echo FTP_DIR_TPLS.$this->file_folder?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\', true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/apps/ktorrent.png" /></a>';
	if (a.a) {
		//h +=  '<a href="javascript:;" title="<?php echo lang('$Delete file?')?>" onclick="S.A.L.del_file(\''+a.f+'\', \'<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'\', \'&file_folder=<?php echo $this->file_folder?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" /></a>';
	}
};
if (a.f!='..') {
	if (!a.d) {
		if (a.b) {
			h +=  '<a href="javascript:;" onclick="S.G.confirm((\'<?php echo lang('_$Are you sure to restore this backup file: %1?')?>\\n<?php echo lang('_$Warning! The original file: %2 will be removed')?>\').replace(/%1/g,\'<b>'+a.f+'</b>\').replace(/%2/g,\'<b>'+a.b+'</b>\'), \'<?php echo lang('_$.bak file restore')?>\', function(){S.A.L.no_hash=1;S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&restore_backup='+a.f+'\')})" title="<?php echo lang('$Restore backup?')?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-undo.png" /></a>';
		} else {
			h +=  '<a href="javascript:;" onclick="S.A.L.no_scroll=true;S.A.L.no_hash=1;S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&create_backup='+a.f+'\')" title="<?php echo lang('$Create backup?')?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/cdcopy.png" /></a>';
		}
	}
	h +=  '<a href="javascript:;" title="<?php echo lang('$Info')?>" onclick="S.G.alert(\'<div style=&quot;font-size:12px&quot;><?php echo FTP_EXT?>tpls/<?php echo $this->file_folder?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'<hr /><a target=&quot;_blank&quot; href=&quot;<?php echo FTP_EXT?>tpls/<?php echo $this->file_folder?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'&quot;><?php echo FTP_EXT?>tpls/<?php echo $this->file_folder?>/<?php echo $this->folder?><?php if ($this->folder):?>/<?php endif;?>'+a.f+'</div>\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/documentinfo-koffice.png" /></a>';
}
h +=  '</td>';