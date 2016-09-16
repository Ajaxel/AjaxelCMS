<?php
/**
* Admin Templates
* Ajaxel CMS v5.0
* Author: Alexander Shatalov <admin@ajaxel.com>
* http://ajaxel.com
*/
$this->name = 'settings_eval_tab';
?><script type="text/javascript">
S.A.L.setEvals={};
S.A.L.setEvalBody=function(id) {
	$('html,body').animate({
		scrollTop: 0
	},'fast', function(){
		$('#a_set_<?php echo $this->tab?>_body').val(S.A.L.setEvals[id]).focus();	
	});
}
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	var html = '<table class="a-list a-list-one" cellspacing="0">';	
	for(i=0;i<data.length;i++) {
		a = data[i];
		S.A.L.setEvals[a.id]=a.php;
		html += '<tr>';
		html += '<td class="a-l a-nb" style="padding-top:10px"><span class="a-date">'+a.added+'</span>'+(a.userid>0?' <a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.userid+'\')" title="'+a.user+'">'+a.user+'</a>':'&nbsp;')+'<div class="a-action_buttons" style="float:right" ><a href="javascript:;" onclick="S.A.L.setEvalBody('+a.id+');"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-paste.png" title="<?php echo lang('$Paste this PHP code to eval into textarea')?>" /></a></div><div style="clear:both;margin:5px 2px;max-height:120px;overflow:auto;border:1px solid #bbb;padding:2px 5px;background:#f1f1f1;font-family:\'Lucida Console\', \'Courier New\';font-size:12px">'+a.title+'</div></td>';
		html += '</tr>';
	}
	html += '</table>';
	if (!a) html = ' ';
	S.A.L.ready(html);
});
</script>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<?php 
if ($this->output):?>
<div class="a-search" style="height:auto">
	<?php echo lang('$Output:')?>
	<div style="margin:5px;padding:5px 10px;background:#fff;border:1px solid #ccc">
	<?php echo $this->output?>
	</div>
</div>
<?php else:?>
<div class="a-search">
	<div class="a-l">
	<?php echo lang('$PHP script code evaluation - use it at your own risk')?>!
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<?php endif;?>
<textarea class="a-textarea a-textarea_code" spellcheck="false" autocorrect="off" autocapitalize="off" name="data[eval]" id="a_set_<?php echo $this->tab?>_body" style="width:99%;height:300px;"><?php echo strform(isset($this->data['eval']) ? $this->data['eval'] : '')?></textarea>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'if(confirm(\''.lang('$Are you sure to evaluate this php script?').'\\n\\n\'+$(\'#a_set_eval_body\').text().substr(0,300))){$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()}','class'=>'a-button disable_button','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Execute'))); ?>
</div>
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<input type="hidden" name="<?php echo self::KEY_ACTION?>" id="a_set_<?php echo $this->tab?>_a" value="eval">
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>