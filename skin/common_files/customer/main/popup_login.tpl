{*
$Id: popup_login.tpl,v 1.1 2010/05/21 08:32:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$lng.lbl_sign_in}</h1>

<p id="login-error" class="error-message" style="display:none;"></p>

{capture name=dialog}

  {include file="customer/main/login_form.tpl}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_authentication content=$smarty.capture.dialog noborder=true}
