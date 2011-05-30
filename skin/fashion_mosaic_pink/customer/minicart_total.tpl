{*
$Id: minicart_total.tpl,v 1.1.2.2 2011/03/14 14:03:47 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<span class="minicart">
{strip}
  {if $minicart_total_items gt 0}
    <span class="full">
      {currency value=$minicart_total_cost assign=total}
      <span class="minicart-items-value">{$minicart_total_items}</span>&nbsp;
      <span class="minicart-items-label">{$lng.lbl_sp_items}</span>&nbsp;
      <span class="minicart-items-delim">/</span>&nbsp;
      {include file="main/tooltip_js.tpl" class="minicart-items-total help-link" title=$total text=$lng.txt_minicart_total_note}
    </span>
  {else}
    <span class="empty">
      <strong>{$lng.lbl_cart_is_empty}</strong>
    </span>
  {/if}
{/strip}
</span>
{if $minicart_total_standalone}
{load_defer_code type="css"}
{load_defer_code type="js"}
{/if}
