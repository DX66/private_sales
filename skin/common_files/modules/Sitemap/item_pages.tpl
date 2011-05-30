{*
$Id: item_pages.tpl,v 1.4.2.1 2010/11/15 07:00:43 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
      {foreach from=$items item="item" key="num"}
        <li><a href="{$item.url}" title="{$item.name|escape}">{$item.name|amp}</a></li>
      {/foreach}
