{*
$Id: affiliate_search_manufacturer.tpl,v 1.1 2010/05/21 08:32:16 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $manufacturers}

  {capture name=dialog}
  <ul class="xaff-categories">
    {foreach from=$manufacturers item=m}
      <li><a href="partner_banners.php?bannerid={$banner.bannerid}&amp;get=1&amp;manufacturerid={$m.manufacturerid}">{$m.manufacturer}</a></li>
    {/foreach}
  </ul>
  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_manufacturers extra='width="100%"'}

{/if}
