{*
$Id: subscribe_bad_email.tpl,v 1.1 2010/05/21 08:32:45 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.txt_unsubscribe_email}</h1>

{capture name=dialog}

  <p class="text-block">
    {$lng.txt_unsubscribe_bad_email}
  </p>

{/capture}
{include file="customer/dialog.tpl" title=$lng.txt_unsubscribe_email content=$smarty.capture.dialog noborder=true}
