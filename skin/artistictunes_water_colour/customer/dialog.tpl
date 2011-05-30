{*
$Id: dialog.tpl,v 1.2 2010/07/13 10:47:11 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="dialog{if $additional_class} {$additional_class}{/if}{if $noborder} noborder{/if}{if $sort and $printable ne 'Y'} list-dialog{/if}">
  {if not $noborder}
    <div class="title{if $additional_class_title} {$additional_class_title}{/if}">
      {if not $additional_class_title}
        <div class="dialog-pointer-left">&nbsp;</div>
      {/if}
      <h2>{$title}</h2>
      {if $sort and $printable ne 'Y'}
        <div class="sort-box">
          {if $selected eq '' and $direction eq ''}
            {include file="customer/search_sort_by.tpl" selected=$search_prefilled.sort_field direction=$search_prefilled.sort_direction url=$products_sort_url}
          {else}
            {include file="customer/search_sort_by.tpl" url=$products_sort_url}
          {/if}
        </div>
      {/if}
      {if not $additional_class_title}
      <div class="dialog-pointer-right">&nbsp;</div>
      {/if}
    </div>
  {/if}
  <div class="content{if $additional_class_content} {$additional_class_content}{/if}">{$content}</div>
</div>
