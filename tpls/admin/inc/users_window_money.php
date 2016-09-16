<?php

$r = (!@$return ? '<tr class="a-hov">' : '').'<td class="a-r" width="15%" nowrap>'.date('H:i d.m.Y',$rs['added']).'</td><td class="a-r" width="80%"><input type="text" name="transfer['.$rs['id'].']" style="width:100%;border:none;background:transparent" class="-a-input" value="'.$rs['title'].'" /></td><td class="a-l" style="color:'.($rs['price']>0?'green':'red').'" nowrap>'.number_format($rs['price'],2,',',' ').' '.$rs['currency'].'</td>'.($this->has_balance?'<td class="a-r" style="text-align:right;color:#777" nowrap>'.number_format($rs['balance'],2,'.','').' '.$this->post['profile']['currency'].'</td>':'').(!@$return ? '</tr>' : '');

if (@$return) return $r;
else echo $r;
