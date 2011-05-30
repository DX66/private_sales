{*
$Id: error_disabled_cookies.tpl,v 1.1 2010/05/21 08:32:04 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_warning}</h1>

<div class="inline-message">
  <img src="{$ImagesDir}/spacer.gif" class="icon-w" alt="" />
  <strong>{$lng.txt_browser_doesnt_accept_cookies}</strong>
</div>

{if $save_data ne ""}

  <div class="text-block">{$lng.txt_enable_cookies_to_continue}</div>
  {include file="customer/buttons/continue.tpl" href="`$save_data.PHP_SELF`?NO_COOKIE_WARNING=1&amp;ti=`$ti`" style="link"}

{/if}
