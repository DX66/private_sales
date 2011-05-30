{*
$Id: error_disabled_cookies.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table>
<tr>
  <td rowspan="2" width="20"><img src="{$ImagesDir}/icon_warning_small.gif" alt="" /></td>
  <td><font class="ErrorMessage">{$lng.txt_browser_doesnt_accept_cookies}</font></td>
</tr>
{if $save_data ne ""}
<tr>
  <td>{$lng.txt_enable_cookies_to_continue}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>{include file="buttons/continue.tpl" href="`$save_data.PHP_SELF`?NO_COOKIE_WARNING=1&amp;ti=`$ti`"}</td>
</tr>
{/if}
</table>
