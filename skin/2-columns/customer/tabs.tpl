{*
$Id: tabs.tpl,v 1.1 2010/05/21 08:31:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $speed_bar}
  <div class="tabs">
    <ul>

      {assign var=speed_bar value=$speed_bar|@array_reverse}
      {foreach from=$speed_bar item=sb name=tabs}
        <li{interline name=tabs}><a href="{$sb.link|amp}">{$sb.title}</a></li>
      {/foreach}

    </ul>
  </div>
{/if}
