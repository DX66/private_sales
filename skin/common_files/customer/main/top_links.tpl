{*
$Id: top_links.tpl,v 1.2.2.1 2011/03/09 10:47:40 aim Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<div id="top-links" class="ui-tabs ui-widget ui-corner-all">
  <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-corner-all">
  {foreach from=$tabs item=tab key=ind}
    {inc value=$ind assign="ti"}
    <li class="ui-corner-top ui-state-default{if $tab.selected} ui-tabs-selected ui-state-active{/if}">
      <a href="{if $tab.url}{$tab.url|amp}{else}#{$prefix}{$ti}{/if}">{$tab.title|wm_remove|escape}</a>
    </li>
  {/foreach}
  </ul>
  <div class="ui-tabs-panel ui-widget-content"></div>
</div>
