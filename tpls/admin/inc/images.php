<?php if ($this->post('files', false)): ?>
<ul id="a-files_<?php echo $this->name_id?>">
<?php 
$j = 0;
foreach ($this->post('files', false) as $file):
	$j++;
	if (!File::isPicture($file)) continue;
	$size = getimagesize(FTP_DIR_ROOT.'files/'.$this->name.'/'.$this->id.'/th1/'.$file); 
?>
<li class="a-photo<?php if($file==$this->post('main_photo', false)):?> a-main_photo<?php endif?>" id="file_<?php echo $this->name_id?>_<?php echo $j?>">
	<a href="/files/<?php echo $this->name?>/<?php echo $this->id?>/th1/<?php echo $file?>" target="_blank"><img src="/files/<?php echo $this->name?>/<?php echo $this->id?>/th3/<?php echo $file?>" class="show-photo {w:<?php echo $size[0]?>,h:<?php echo $size[1]?>,p:'/files/<?php echo $this->name?>/<?php echo $this->id?>/th1/<?php echo $file?>'}" /></a>
	<div class="a-photo_del">
		<a href="javascript:;" onclick="S.A.W.delPhoto('<?php echo $file?>', false, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" /></a><br />
		<a href="javascript:;" onclick="S.A.W.setMainPhoto('<?php echo $file?>', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/dialog-ok.png" /></a>
	</div>
</li>
<?php endforeach;?>
</ul>
<?php endif; ?>