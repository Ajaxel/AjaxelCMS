</td></tr></table>
<table cellspacing="4" class="ui-widget-content ui-widget-header ui-corner-all" style="font-size:8px;margin:5px auto"><tr><td style="font:11px 'Trebuchet MS';"><a href="http://ajaxel.com" target="_blank">Ajaxel CMS</a> v<?php echo Site::VERSION?>. <?php echo lang('Hosted on %1','<a href="'.HTTP_BASE.'">'.str_replace('http://','',trim(HTTP_BASE,'/')).'</a> v'.$this->Index->My->version)?></td></tr></table>
</body>
<?php echo $this->Index->getVar('conf').$this->Index->getVar('js')?>
</html>