<?php

/**
* Ajaxel CMS v8.0
* http://ajaxel.com
* =================
* 
* Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* 
* The software, this file and its contents are subject to the Ajaxel CMS
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the source code of Ajaxel CMS.
* 
* @file       tpls/admin/email_mail_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = ($this->folder==Mail::FOLDER_SENT?lang('$Sent mail letter:'):lang('$Received mail letter:')).' ';
$this->title = $title.($this->post('id', false)?date('d M Y H:i',$this->post('added', false)).' '.$this->post('subject', false):' was not found');
if ($this->folder==Mail::FOLDER_SENT) {
	$this->width = 850;
} else {
	$this->width = 850;
}
$tab_height = 460;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.L.C.history();
	}
	,reply:function(id, obj){
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=email&tab=mail', {
			get: 'action',
			a: 'get_msg',
			id: id
		}, function(data){
			$('#a-w-sent_<?php echo $this->name_id?>').html(data.sent);
			$('#a-w-subject_<?php echo $this->name_id?>').html(data.subject);
			$('#a-w-body_<?php echo $this->name_id?>').html(data.body);
			$('#a-w-id_<?php echo $this->name_id?>').html('#'+data.id);
			$('#a-w-from_<?php echo $this->name_id?>').html(data.from);
			$('#a-w-replies_<?php echo $this->name_id?> .ui-state-active').removeClass('ui-state-active');
			$(obj).addClass('ui-state-active');
		})
		/*S.A.W.open('?<?php echo URL_KEY_ADMIN?>=email&<?php echo self::KEY_TAB?>=mail&<?php echo URL_KEY_FOLDER?>=<?php echo $this->folder?>&id='+id);*/
	}
	/*if(this.value=='')){$('#a-div_<?php echo $this->tab?>_reply').show()}else{$('#a-div_<?php echo $this->tab?>_reply').hide()}*/
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table cellspacing="0" class="a-form"><tr valign="top">
		<td width="100%">
			<?php if ($this->post('id', false)):?>
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-m<?php echo $this->ui['a-m']?>" colspan="2">
						<div class="a-l" id="a-w-from_<?php echo $this->name_id?>">
							<?php 
							if ($this->post('folder', false)==Mail::FOLDER_SENT) {
								echo lang('$Message for %1:',$this->post['user']['login'].' &lt;'.$this->post['user']['email'].'&gt;');
							} else {
								echo lang('$Message from %1:',$this->post['user']['login'].' &lt;'.$this->post['user']['email'].'&gt;');
							}
							?>
						</div>
						<div class="a-r" id="a-w-id_<?php echo $this->name_id?>">
							#<?php echo $this->id?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="a-l" width="10%"><?php echo lang('$Sent:')?></td><td class="a-r" width="90%" id="a-w-sent_<?php echo $this->name_id?>">
						<?php echo date('d M Y H:i',$this->post('added', false))?> <a href="javascript:;" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=users&id=<?php echo $this->post('from_id')?>')"><?php echo $this->post['user']['login'].' &lt;'.$this->post['user']['email'].'&gt;';?></a>
					</td>
				</tr>
				<?php if ($this->folder==Mail::FOLDER_SENT):?>
					<tr>
						<td class="a-l"><?php echo lang('$Status:')?></td><td class="a-r">
							<?php if ($this->post('read', false)):?>
								<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-mark-read.png" alt="" /> <?php echo lang('$Message was delivered read on %1',date('d M Y H:i',$this->post('read', false)))?>
							<?php else:?>
								<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-mark-unread.png" alt="" /> <?php echo lang('$Message has not been read')?>
							<?php endif;?>
						</td>
					</tr>
				<?php endif;?>
				<tr>
					<td class="a-l"><?php echo lang('$Subject:')?></td><td class="a-r" id="a-w-subject_<?php echo $this->name_id?>">
						<?php echo $this->post('subject')?>
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Message:')?></td><td class="a-r"><div style="height:<?php echo ($this->folder==Mail::FOLDER_SENT?'380':'220')?>px;overflow:auto" id="a-w-body_<?php echo $this->name_id?>">
						<?php echo ($this->post('body_conv', false)?$this->post('body_conv', false):(strstr($this->post('body', false),'>')?$this->post('body', false):Parser::strPrint($this->post('body', false))))?>
					</div></td>
				</tr>
				<?php if ($this->folder!=Mail::FOLDER_SENT):?>
				<tr>
					<td class="a-m<?php echo $this->ui['a-m']?>" colspan="2">
						<?php echo lang('$Post quick reply to %1:',$this->post['user']['login'])?>
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Template:')?></td><td class="a-r"><select name="data[reply]" class="a-select" onchange="S.A.W.mail_template(this.value,$('#a-w-title_<?php echo $this->name_id?>'), $('#a-w-message_<?php echo $this->name_id?>'))"><?php echo Html::buildOptions($this->post('reply', false), $this->array['replies']['Q'])?></select></td>
				</tr>
				<tr><td class="a-l" width="10%"><?php echo lang('$Subject:')?></td><td class="a-r"><input type="text" style="width:98%" class="a-input" name="data[compose_subject]" id="a-w-title_<?php echo $this->name_id?>" value="RE: <?php echo strform($this->post('compose_subject', false))?>"></td></tr>
				<tr><td class="a-l"><?php echo lang('$Message:')?></td><td class="a-r"><textarea type="text" class="a-textarea" name="data[compose_message]" style="width:99%;height:71px;font:12px monospace" id="a-w-message_<?php echo $this->name_id?>"><?php echo ($this->post('compose_message', false)?strform($this->post('compose_message', false)):strform($this->post('compose_body', false)))?></textarea></td></tr>
				<tr><td class="a-l" width="10%"><?php echo lang('$Save to:')?></td><td class="a-r"><input type="text" style="width:58%" name="data[compose_save]" class="a-input" value="<?php echo strform($this->post('compose_save', false))?>"></td></tr>
				<?php endif;?>
			</table>
		</td>
		<td>
			<div style="width:0px;overflow:hidden;display:none" id="a-w-slide_<?php echo $this->id?>">
					<div style="width:200px">
					<table cellspacing="0" class="a-form">
						<tr>
							<td class="a-m<?php echo $this->ui['a-m']?>" style="cursor:pointer;cursor:hand" onclick="if (S.A.Conf.mail_slided){$('#a-w-slide_<?php echo $this->id?>').animate({width:240});S.A.Conf.mail_slided=false}else{$('#a-w-slide_<?php echo $this->id?>').animate({width:100});S.A.Conf.mail_slided=true}" colspan="2"><?php echo lang('$History:')?></td>
						</tr>
						<tr>
							<td style="height:<?php echo $tab_height?>px;">
								<div style="height:<?php echo $tab_height?>px;overflow:auto;overflow-x:none">
									<table cellspacing="2" class="a-form a-ul" id="a-w-replies_<?php echo $this->name_id?>">
										<?php 
										foreach ($this->post('replies', false) as $i => $rs):
										?>
										<tr><td class="ui-state-default<?php echo ($rs['id']==$this->id?' ui-state-active':'')?>" onclick="window_<?php echo $this->name_id?>.reply(<?php echo $rs['id']?>, this)">
											<?php if ($rs['folder']==Mail::FOLDER_SENT):?>
												<?php if ($rs['read']):?>
													<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-mark-read.png" title="<?php echo lang('$Read message')?>" alt="" />
													<span class="a-date"><?php echo date('H:i d M',$rs['added'])?></span> <?php echo $rs['subject']?>
												<?php else:?>
													<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-mark-unread.png" title="<?php echo lang('$Unread message')?>" alt="" />
													<span class="a-date"><?php echo date('H:i d M',$rs['added'])?></span> <?php echo $rs['subject']?>
												<?php endif;?>
											<?php else:?>
											<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-get.png" title="<?php echo lang('$My reply')?>" alt="" />
											<span class="a-date"><?php echo date('H:i d M',$rs['added'])?></span> <?php echo $rs['subject']?>
											<?php endif;?>
										</td></tr>
										<?php
										endforeach;
										?>
									</table>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</td>
	</tr></table>
	
	<?php else:?>
	<table class="a-form" cellspacing="0">
		<tr><td class="a-r"><div class="a-not_found">Message with this id <?php echo $this->id?> was not found</div></td></tr>
	</table>
	<?php endif;?>
	
	<?php if ($this->folder!=Mail::FOLDER_SENT):?>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<td class="a-td1">
			<button type="button" class="a-button" style="float:left" onclick="S.A.L.C.history(this,$('#a-w-slide_<?php echo $this->id?>'),'<?php echo strjs(lang('$Hide history'))?>')"><?php echo lang('$Show history')?></button>
			<button type="button" class="a-button" style="float:left" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=email&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&compose=true&to=<?php echo urlencode($this->post['user']['login'])?>&subject=<?php echo urlencode('RE: '.$this->post('subject', false))?>')"><?php echo lang('$Post full reply..')?></button>
		</td>
		<td class="a-td3">
			<?php $this->inc('button',array('click'=>'S.A.W.save(\''.$this->url_save.'&id='.$this->id.'&a=send&'.URL_KEY_FOLDER.'='.$this->folder.'&'.self::KEY_TAB.'='.$this->tab.'\', this.form, this)','img'=>'oxygen/16x16/actions/mail-queue.png','text'=>lang('$Send'))); ?>
		</td>
		</tr></table>
	</div>
	<?php endif;?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>