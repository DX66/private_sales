{*
$Id: minicart_total.tpl,v 1.1.2.1 2010/12/15 09:44:38 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="minicart">
  {if $minicart_total_items gt 0}

    <div class="valign-middle full">

      <table cellspacing="0" summary="{$lng.lbl_your_cart|escape}">
        <tr>
          <td><strong>{$lng.lbl_cart_items}: </strong></td>
          <td>{$minicart_total_items}</td>
        </tr>
        <tr>
          <td><strong>{$lng.lbl_total}: </strong></td>
          <td>
            {capture name=tt assign=val}
              {currency value=$minicart_total_cost}
            {/capture}
            {include file="main/tooltip_js.tpl" class="help-link" title=$val text=$lng.txt_minicart_total_note}
          </td>
        </tr>
      </table>

    </div>

  {else}

    <div class="valign-middle empty">

      <strong>{$lng.lbl_cart_is_empty}</strong>

    </div>

  {/if}

</div>
