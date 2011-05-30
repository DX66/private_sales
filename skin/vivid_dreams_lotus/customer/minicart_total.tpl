{*
$Id: minicart_total.tpl,v 1.1.2.2 2011/03/14 14:03:47 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="minicart">
  {if $minicart_total_items gt 0}

    <div class="valign-middle full">
      {capture name=tt assign=val}
        {currency value=$minicart_total_cost}
      {/capture}
      <span class="minicart-text">
        <strong>{$minicart_total_items}</strong> {$lng.lbl_cart_items}&nbsp;/&nbsp;<strong class="help-link">{include file="main/tooltip_js.tpl" class="help-link" title=$val text=$lng.txt_minicart_total_note}</strong>
      </span>
    </div>

  {else}

    <div class="valign-middle empty">

      <strong>{$lng.lbl_cart_is_empty}</strong>

    </div>

  {/if}

</div>
{if $minicart_total_standalone}
{load_defer_code type="css"}
{load_defer_code type="js"}
{/if}
