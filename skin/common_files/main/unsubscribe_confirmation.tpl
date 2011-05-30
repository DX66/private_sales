{*
$Id: unsubscribe_confirmation.tpl,v 1.1 2010/05/21 08:32:19 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
{$lng.txt_unsubscribed_msg}<br />
{$lng.lbl_email}: <b>{$smarty.get.email|replace:"\\":""}</b>
{/capture}
{include file="dialog.tpl" title=$lng.txt_thankyou_for_unsubscription content=$smarty.capture.dialog extra='width="100%"'}
