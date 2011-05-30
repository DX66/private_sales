{*
$Id: minicart_total.tpl,v 1.1.2.1 2010/12/15 09:44:38 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<span class="minicart">
  {if $minicart_total_items gt 0}
    {capture name=tt assign=val}
      {currency value=$minicart_total_cost}
    {/capture}
    <strong>{$minicart_total_items}</strong> {$lng.lbl_cart_items|lower} / <strong class="help-link">{include file="main/tooltip_js.tpl" class="help-link" title=$val text=$lng.txt_minicart_total_note}</strong>
  {else}
    <strong>{$lng.lbl_cart_is_empty}</strong>
  {/if}
</span>
