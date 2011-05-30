{*
$Id: minicart_total.tpl,v 1.1.2.1 2010/12/15 09:44:43 aim Exp $
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
