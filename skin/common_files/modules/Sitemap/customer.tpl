{*
$Id: customer.tpl,v 1.4 2010/07/23 15:30:48 slam Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$lng.sitemap_location}</h1>

{capture name=dialog}
  <div id="Sitemap">
    {if $config.Sitemap.use_cache eq "Y"}
      {$sitemap_items}
    {else}
      {foreach from=$config.Sitemap.items item="item"}
        {if $sitemap_items.$item ne false}
          {include file="modules/Sitemap/item_`$item`_header.tpl"}
          {include file="modules/Sitemap/item_`$item`.tpl" items=$sitemap_items.$item}
          {include file="modules/Sitemap/item_`$item`_footer.tpl"}
        {/if}
      {foreachelse}
        {$lng.sitemap_noitems}
      {/foreach}
    {/if}
  </div>
{/capture}
{include file="customer/dialog.tpl" title=$lng.sitemap_location content=$smarty.capture.dialog noborder=true}
