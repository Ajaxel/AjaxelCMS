<?php
$title = lang('$Not allowed');
?><script type="text/javascript">
$().ready(function() {
	<?php $this->inc('js_load')?>
});
</script>
<?php 
$this->title = $title;
//$this->width = 380;
$this->pop = true;

if (isset($_GET['load'])):?>
	<table cellspacing="0" width="100%"><tr><td style="background:url(<?php echo FTP_EXT?>tpls/img/icons/block_48.png) 5px 5px no-repeat;height:58px;padding-left:60px;font-size:13px;font-weight:bold;"><?php echo $allow['text']?></td></tr></table>
<?php else:
$this->inc('window_top');

?>

<table cellspacing="0" width="100%"><tr><td style="background:url(<?php echo FTP_EXT?>tpls/img/icons/block_48.png) 5px 5px no-repeat;height:58px;padding-left:60px;font-size:13px;font-weight:bold;"><?php echo $allow['text']?></td></tr></table>

<?php $this->inc('window_bottom');
endif;
?>
