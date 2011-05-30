{*
$Id: usps.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<form action="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do" method="post" name="getTrackNum" id="getTrackNum" target="_blank">
<input type="hidden" id="strOrigTrackNum" name="strOrigTrackNum" value="{$order.tracking|escape}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_usps_redirection}
</form>
