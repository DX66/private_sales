{*
$Id: compare_checkbox.tpl,v 1.1 2010/05/21 08:32:21 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product.fclassid gt 0}

<div class="fcomp-checkbox-box">

  <label for="fe_pid_{$product.productid}">
    <input type="checkbox" id="fe_pid_{$product.productid}" value="Y" />
    {$lng.lbl_check_to_compare}
  </label>

</div>

{/if}
