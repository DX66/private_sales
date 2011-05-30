{*
$Id: head.tpl,v 1.4.2.2 2010/09/29 07:24:11 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="line0">

  <div class="logo">
    <a href="{$catalogs.customer}/home.php"><img src="{$ImagesDir}/xlogo.gif" alt="" /></a>
  </div>

  <div class="line1">

  {if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}

    {include file="customer/language_selector.tpl"}

    <div class="auth-row">
    {if $login eq ''}
      {include file="customer/main/login_link.tpl"}
      |
      <a href="register.php">{$lng.lbl_register}</a>
    {else}
      <span>{$fullname|default:$login|escape}</span>
      <a href="{$xcart_web_dir}/login.php?mode=logout">{$lng.lbl_logoff}</a>
      |
      <a href="register.php?mode=update">{$lng.lbl_my_account}</a>
    {/if}
      |
      <a href="help.php" class="last">{$lng.lbl_need_help}</a>
    </div>

  {/if}

  </div>

  <div class="line2">

    {if ($main ne 'cart' or $cart_empty) and $main ne 'checkout'}
      {include file="customer/search.tpl"}
    {/if}
    {include file="customer/phones.tpl"}

  </div>

  <div class="line3">
    {include file="customer/tabs.tpl"}
  </div>

</div>

{include file="customer/noscript.tpl"}
