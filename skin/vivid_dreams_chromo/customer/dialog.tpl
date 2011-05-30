{*
$Id: dialog.tpl,v 1.2 2010/07/13 10:47:11 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="dialog{if $additional_class} {$additional_class}{/if}{if $noborder} noborder{/if}{if $sort and $printable ne 'Y'} list-dialog{/if}">
  {if not $noborder}
    {if $title_page eq 'category'}
      <div class="title_cat">
        <div class="left">
          <div class="right">
    {else}
    <div class="title">
    {/if}
  
    {if $title_page eq 'Y'}
      <h2 class="titles">{$title}</h2>
    {else}
      <h2>{$title}</h2>
    {/if}
    
    {if $sort and $printable ne 'Y'}
      <div class="sort-box">
        {if $selected eq '' and $direction eq ''}
          {include file="customer/search_sort_by.tpl" selected=$search_prefilled.sort_field direction=$search_prefilled.sort_direction url=$products_sort_url}
        {else}
          {include file="customer/search_sort_by.tpl" url=$products_sort_url}
        {/if}
      </div>
    {/if}

    {if $title_page eq 'category'}
        </div>
      </div>
    {/if}
    </div>
  {/if}
  <div class="content">{$content}</div>
</div>
