<script type="text/javascript">

$().ready(function(){
	S.G.upload({
		name	: '{$upload.name}',
		id		: '{$upload.id}',
		hash	: '{$upload.hash}',
		file	: 'main_photo',
		queueID	: 'uploadQueue',
		multi	: true,
		auto	: true,
		buttonImg: '/{$smarty.const.HTTP_DIR_TPL}images/file_{$lang}.jpg',
		width	: 177,
		height	: 27,
		fileExt	: '{$upload.ext}',
		fileDesc: '{$upload.desc}',
		start	: function(){
			$('#uploadQueue').fadeIn();
		},
		func	: function(response){
			if (response.substring(0,2)!='1/') {
				alert(response);	
			} else {
				if ($('#photo').length) {
					$('#photo').html(response.substring(2)).hide().fadeIn();
				} else {
					$('<li>').prependTo($('#photos')).html(response.substring(2)).hide().fadeIn();
				}
				S.I.init();
			}
		}
	});
});
</script>
{strip}
<h1>{'Your photo'|lang}</h1>
<form class="form ajax_form" method="post" action="?{$URL}" id="login_form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr>
			<th>{'New image'|lang}:</th>
			<td colspan="3" style="padding-left:25px">
				<ul class="photos" id="photos">
					{foreach from=$upload.files item=f}
						<li id="photo">{include file='includes/pic.tpl' f=$f}</li>
					{/foreach}
					<li class="button">
						<div class="up_button corner-tl corner-bl" style="border-left:1px solid #cccccc"><input type="file" id="main_photo" /></div>
					</li>
				</ul>
				<div id="uploadQueue" style="width:400px;clear:both;"></div>
			</td>
		</tr>
	</tbody></table>
</form>
{/strip}