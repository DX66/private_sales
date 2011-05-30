{*
$Id: table_head_cell.tpl,v 1.1 2010/05/21 08:32:02 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{strip}
{if not $sort_field}
{assign var="sort_field" value=$search_prefilled.sort_field}
{/if}

{if not $sort_direction}
{assign var="sort_direction" value=$search_prefilled.sort_direction}
{/if}

{if $sort_field eq $column}

<img class="{if $sort_direction eq 1}img-down-direction{else}img-up-direction{/if}" src="{$ImagesDir}/spacer.gif" alt="" />
<a href="{$url}&amp;sort={$column}&amp;sort_direction={if $sort_direction eq 1}0{else}1{/if}">{$title}</a>

{else}

<a href="{$url}&amp;sort={$column}">{$title}</a>

{/if}
{/strip}
