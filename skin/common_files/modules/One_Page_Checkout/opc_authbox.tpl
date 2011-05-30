{*
$Id: opc_authbox.tpl,v 1.1 2010/05/21 08:32:45 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="opc-authbox" id="opc_authbox">
  {if $login ne ''}

    {$lng.txt_opc_greeting|substitute:"name":$fullname}&nbsp;
    <a href="register.php?mode=update" title="{$lng.lbl_view_profile|escape}">{$lng.lbl_view_profile}</a>&nbsp;
    <a href="login.php?mode=logout" title="{$lng.lbl_sign_out|escape}">{$lng.lbl_sign_out}</a>

  {else}

    {capture name='loginbn'}
      <a title="{$lng.lbl_sign_in|escape}" href="login.php" onclick="javascript: popupOpen('login.php'); return false;">{$lng.lbl_sign_in|lower|escape}</a>
    {/capture}
    {$lng.txt_opc_sign_in|substitute:"sign_in_link":$smarty.capture.loginbn}

  {/if}
</div>
