<?php
$db = Conf()->g('DB');
if (count($db)>1):
	ksort($db);
	echo lang('$DB:')?> <select id="a-db_change" onchange="location.replace('/?<?php echo URL::rq(URL_KEY_DB, URL::get())?>&<?php echo URL_KEY_DB?>='+$(this).val());">
	<?php echo Html::buildOptions($_SESSION['DB'],array_label($db,'[[:KEY:]]'));?></select>
<?php endif;?>