<script type="text/javascript">
$().ready(function(){
	S.A.L.user($('#<?php echo $this->name?>-user'), '<?php echo $table?>');
});
</script>
<?php echo lang('$User:')?> <input type="text" id="<?php echo $this->name?>-user" name="user" style="width:150px" value="<?php echo strform(post('user'))?>"><input type="hidden" id="<?php echo $this->name?>-user_search"><input type="hidden" id="<?php echo $this->name?>-user_search_id" name="userid" value="<?php echo strform(post('userid'))?>">