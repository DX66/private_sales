{*
$Id: bread_crumbs.tpl,v 1.4.2.2 2010/10/27 13:58:17 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $location}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top" align="left">
  <div id="location">
      {foreach from=$location item=l name=location}
        {if $l.1 and not $smarty.foreach.location.last}
          <a href="{$l.1|amp}" class="bread-crumb{if $smarty.foreach.location.last} last-bread-crumb{/if}">{if $webmaster_mode eq "editor"}{$l.0}{else}{$l.0|amp}{/if}</a>
        {else}
          <font class="bread-crumb{if $smarty.foreach.location.last} last-bread-crumb{/if}">{if $webmaster_mode eq "editor"}{$l.0}{else}{$l.0|amp}{/if}</font>
        {/if}
        {if not $smarty.foreach.location.last && $config.Appearance.breadcrumbs_separator ne ''}
          <span>{$config.Appearance.breadcrumbs_separator|amp}</span>
        {/if}
      {/foreach}
  </div>
  </td>
  <td width="130" valign="top" align="right">
  {include file="customer/printable_link.tpl"}
  </td>
</tr>
</table>
{/if}
