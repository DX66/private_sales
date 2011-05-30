{*
$Id: authbox.tpl,v 1.1.2.1 2010/08/06 07:56:24 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="login-text item right-box">
  <div class="register-button vertical-align">

{if $login ne ''}

  <strong>{$fullname|default:$login|escape}</strong>

  <a href="register.php?mode=update">{$lng.lbl_my_account}</a>&nbsp;&nbsp;
  <form action="login.php?mode=logout" method="post" name="loginform">
    <input type="hidden" name="mode" value="logout" />
    <a href="javascript:void(0);" onclick="javascript: setTimeout(function() {ldelim}document.loginform.submit();{rdelim}, 100);">{$lng.lbl_logoff}</a>
  </form>

{else}
  
    {include file="customer/main/login_link.tpl"}&nbsp;&nbsp;
    <a href="register.php" title="{$lng.lbl_register|escape}">{$lng.lbl_register}</a>

{/if}

  </div>
</div>
