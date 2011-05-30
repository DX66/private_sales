{*
$Id: profile_menu.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $main eq 'user_profile' or ($main eq 'register' and $login ne '')}

{if $main eq 'user_profile'}
{assign var="query_str" value="?user=`$smarty.get.user`&amp;usertype=P"}
{else}
{assign var="query_str" value="?mode=update"}
{/if}

{if not $single_mode}
<table cellpadding="5" cellspacing="0" width="100%">
<tr>
  <td width="100%">&nbsp;</td>
  <td nowrap="nowrap">{if $smarty.get.submode ne 'seller_address'}<span class="simple-button">{$lng.lbl_profile_details}</span>{else}<a href="{$register_script_name}{$query_str}" class="simple-button">{$lng.lbl_profile_details}</a>{/if}</td>
  <td nowrap="nowrap">{if $smarty.get.submode ne 'seller_address'}<a href="{$register_script_name}{$query_str}&amp;submode=seller_address" class="simple-button">{$lng.lbl_seller_address}</a>{else}<span class="simple-button">{$lng.lbl_seller_address}</span>{/if}</td>
</tr>
</table>
{/if}
{/if}
