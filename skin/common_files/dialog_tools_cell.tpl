{*
$Id: dialog_tools_cell.tpl,v 1.1 2010/05/21 08:31:57 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $cell.separator}
<li class="dialog-cell-separator"><img src="{$ImagesDir}/spacer.gif" alt="" /></li>
{else}
<li>
  <a class="dialog-cell{if $cell.style eq "hl"}-hl{/if}" href="{$cell.link|amp}" title="{$cell.title|escape}"{if $cell.target ne ""} target="{$cell.target}"{/if}>
    {$cell.title}
  </a>
</li>
{/if}
