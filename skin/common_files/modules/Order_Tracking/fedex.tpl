{*
$Id: fedex.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<form name="tracking" action="http://www.fedex.com/Tracking" target="_blank">
<input type="hidden" name="ascend_header" value="1" />
<input type="hidden" name="clienttype" value="dotcom" />
<input type="hidden" name="cntry_code" value="us" />
<input type="hidden" name="language" value="english" />
<input type="hidden" name="tracknumbers" value="{$order.tracking|escape}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_fedex_redirection}
</form>
