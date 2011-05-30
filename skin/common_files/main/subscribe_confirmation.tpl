{*
$Id: subscribe_confirmation.tpl,v 1.2 2010/07/01 06:54:08 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
{$lng.txt_newsletter_subscription_msg}:<br />
<b>{$smarty.get.email|replace:"\\":""}</b>
<br />
{$lng.txt_unsubscribe_information} <a href="{$http_location}/mail/unsubscribe.php?email={$smarty.get.email|escape|replace:"\\":""}"><font class="FormButton">{$lng.lbl_this_url}</font></a>.
{/capture}
{include file="dialog.tpl" title=$lng.txt_thankyou_for_subscription content=$smarty.capture.dialog extra='width="100%"'}
