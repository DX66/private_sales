{*
$Id: navigation.tpl,v 1.4 2010/07/28 13:10:55 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul class="simple-list-left width-100">
{assign var="navigation_script" value=$navigation_script|amp}
{if $total_pages gt 2}
<li class="item-left">

  <div class="nav-pages">
    <!-- max_pages: {$navigation_max_pages} -->
    <span class="nav-pages-title">{$lng.lbl_result_pages}:</span>

    {strip}

    {if $navigation_arrow_left}
      <a class="left-arrow right-delimiter" href="{$navigation_script}&amp;page={$navigation_arrow_left}"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_prev_page|escape}" /></a>
    {/if}

    {if $start_page gt 1}
      <a class="nav-page right-delimiter" href="{$navigation_script}&amp;page=1" title="{$lng.lbl_page|escape} #1">1</a>

      {if $start_page gt 2}
        <span class="nav-dots right-delimiter">...</span>
      {/if}

    {/if}

    {section name=page loop=$total_pages start=$start_page}

      {if $smarty.section.page.index eq $navigation_page}
        <span class="current-page{if not $smarty.section.page.last or ($total_pages lte $total_super_pages or $navigation_arrow_right)} right-delimiter{/if}" title="{$lng.lbl_current_page|escape}: #{$smarty.section.page.index}">{$smarty.section.page.index}</span>
      {else}
        <a class="nav-page{if not $smarty.section.page.last or ($total_pages lte $total_super_pages or $navigation_arrow_right)} right-delimiter{/if}" href="{$navigation_script}&amp;page={$smarty.section.page.index}" title="{$lng.lbl_page|escape} #{$smarty.section.page.index}">{$smarty.section.page.index}</a>
      {/if}

    {/section}

    {if $total_pages lte $total_super_pages}

      {if $total_pages lt $total_super_pages}
        <span class="nav-dots right-delimiter">...</span>
      {/if}
      <a class="nav-page{if $navigation_arrow_right} right-delimiter{/if}" href="{$navigation_script}&amp;page={$total_super_pages}" title="{$lng.lbl_page|escape} #{$total_super_pages}">{$total_super_pages}</a>
    {/if}

    {if $navigation_arrow_right}
      <a class="right-arrow" href="{$navigation_script}&amp;page={$navigation_arrow_right}"><img src="{$ImagesDir}/spacer.gif" alt="{$lng.lbl_next_page|escape}" /></a>
    {/if}

    {/strip}

  </div>
</li>
{/if}
<li class="item-right">
{if $per_page eq "Y" and $total_items gte $per_page_values.0}
{include file="customer/main/per_page.tpl"}
{/if}
</li>
</ul>
<div class="clearing"></div>
