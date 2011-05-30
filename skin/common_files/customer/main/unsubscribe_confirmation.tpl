{*
$Id: unsubscribe_confirmation.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.txt_thankyou_for_unsubscription}</h1>

{capture name=dialog}

  {$lng.txt_unsubscribed_msg}<br />
  {$lng.lbl_email}: <strong>{$smarty.get.email|replace:"\\":""}</strong>

{/capture}
{include file="customer/dialog.tpl" title=$lng.txt_thankyou_for_unsubscription content=$smarty.capture.dialog noborder=true}
