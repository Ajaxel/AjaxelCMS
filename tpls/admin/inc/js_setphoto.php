,setPhoto:function(file) {
	S.A.W.addMainPhoto('<?php echo $this->name_id?>', file, 'main_photo');
	if (file) {
		$('#a-w-main_photo_del_<?php echo $this->name_id?>').show();
	} else {
		$('#a-w-main_photo_del_<?php echo $this->name_id?>').hide();
	}
}