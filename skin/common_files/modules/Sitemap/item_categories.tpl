{*
$Id: item_categories.tpl,v 1.3 2010/07/23 15:30:48 slam Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
      {foreach from=$items item="item" key="num"}
	    {include file="modules/Sitemap/item_categories_recurs.tpl" item=$item}
      {/foreach}