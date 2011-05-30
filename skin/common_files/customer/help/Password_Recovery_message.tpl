{*
$Id: Password_Recovery_message.tpl,v 1.1 2010/05/21 08:32:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_confirmation}</h1>

{capture name=dialog}
  {$lng.txt_password_recover_message1} {$smarty.get.email|escape:"html"}.
  {$lng.txt_password_recover_message2}
{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog noborder=true}
