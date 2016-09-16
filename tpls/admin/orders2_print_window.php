<style>
@media all {
	.page-break {
		margin:30px 0 60px 0;
		border-top:1px solid #999;
	}
}
@media print {
	.page-break {
		display:block;
		page-break-before:always;
		border-top:none;
		margin:0;
	}
}
</style>
<?php
	foreach ($this->data as $v) {
		echo '<h3>'.Email::parseVariables($_SESSION['mail_tpl']['subject'], $v).'</h3>';
		echo Email::parseVariables($_SESSION['mail_tpl']['message'], $v);
		echo '<div class="page-break"></div>';
			
	}
?>