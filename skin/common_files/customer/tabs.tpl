{*
$Id: tabs.tpl,v 1.1.2.1 2010/09/16 07:04:17 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $speed_bar}
  <div class="tabs{if $all_languages_cnt gt 1} with_languages{/if}">
    <ul>

      {foreach from=$speed_bar item=sb name=tabs}
        <li{interline name=tabs}><a href="{$sb.link|amp}">{$sb.title}</a></li>
      {/foreach}

    </ul>
  </div>
{/if}
