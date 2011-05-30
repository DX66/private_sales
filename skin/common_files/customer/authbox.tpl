{*
$Id: authbox.tpl,v 1.2.2.1 2010/08/06 07:56:24 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=menu}

<div class="login-text item">

{if $login ne ''}

  <strong>{$fullname|default:$login|escape}</strong>
  <hr class="minicart" />
  <ul>
    <li class="modify-profile-link"><a href="register.php?mode=update">{$lng.lbl_my_account}</a></li>
    <li class="logout-link">
      <form action="login.php?mode=logout" method="post" name="loginform">
        <input type="hidden" name="mode" value="logout" />
        <a href="javascript:void(0);" onclick="javascript: setTimeout(function() {ldelim}document.loginform.submit();{rdelim}, 100);">{$lng.lbl_logoff}</a>
      </form>
    </li>
  </ul>

{else}
  
  <ul>
    <li>{include file="customer/main/login_link.tpl"}</li>
    <li><a href="register.php" title="{$lng.lbl_register|escape}">{$lng.lbl_register}</a></li>
    <li><a href="help.php?section=Password_Recovery" title="{$lng.lbl_forgot_password|escape}">{$lng.lbl_forgot_password}</a></li>
  </ul>

{/if}

</div>

{/capture}
{include file="customer/menu_dialog.tpl" title=$lng.lbl_authentication content=$smarty.capture.menu additional_class="menu-auth"}
