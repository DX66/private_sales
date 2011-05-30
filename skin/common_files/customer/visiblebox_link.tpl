{*
$Id: visiblebox_link.tpl,v 1.1 2010/05/21 08:32:02 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="expand-section">
  {strip}
    <img src="{$ImagesDir}/spacer.gif" class="plus" id="{$id}_plus" alt="{$lng.lbl_click_to_open|escape}"{if $visible} style="display: none;"{/if} onclick="javascript: switchVisibleBox('{$id}');" />
    <img src="{$ImagesDir}/spacer.gif" class="minus" id="{$id}_minus"{if not $visible} style="display: none;"{/if} alt="{$lng.lbl_click_to_close|escape}" onclick="javascript: switchVisibleBox('{$id}');" />
    <a href="javascript:void(0);" onclick="javascript: switchVisibleBox('{$id}');">{$title}</a>
  {/strip}
</div>
