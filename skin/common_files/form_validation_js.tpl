{*
$Id: form_validation_js.tpl,v 1.3 2010/06/08 06:17:38 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var txt_out_of_stock = "{$lng.txt_out_of_stock|wm_remove|escape:javascript|replace:"\n":"<br />"|replace:"\r":" "}";

{literal}
function FormValidation(form) {

  if (typeof(window.check_exceptions) != 'undefined' && !check_exceptions()) {
    alert(exception_msg);
    return false;
  }

{/literal}
  {if $product_options_js ne ''}
  {$product_options_js}
  {/if}

{literal}
  var selavailObj = document.getElementById('product_avail');
  var inpavailObj = document.getElementById('product_avail_input');

  if ((!selavailObj || selavailObj.disabled == true) && inpavailObj && inpavailObj.disabled == false) {
      if (!check_quantity_input_box(inpavailObj))
        return false;

  } else if ((!inpavailObj || inpavailObj.disabled == true) && selavailObj && selavailObj.disabled == false && selavailObj.value == 0) {
      alert(txt_out_of_stock);
      return false;
  }

  return !ajax.widgets.add2cart || !ajax.widgets.add2cart(form);
}

// Check quantity input box
function check_quantity_input_box(inp) {
  if (isNaN(inp.minQuantity))
    inp.minQuantity = min_avail;

  if (isNaN(inp.maxQuantity))
    inp.maxQuantity = product_avail;

  if (!isNaN(inp.minQuantity) && !isNaN(inp.maxQuantity)) {
    var q = parseInt(inp.value);
    if (isNaN(q)) {
      alert(substitute(lbl_product_quantity_type_error, "min", inp.minQuantity, "max", inp.maxQuantity));
      return false;
    }

    if (q < inp.minQuantity) {
      alert(substitute(lbl_product_minquantity_error, "min", inp.minQuantity));
      return false;
    }

    if (q > inp.maxQuantity && is_limit) {
      alert(substitute(lbl_product_maxquantity_error, "max", inp.maxQuantity));
      return false;
    }

    if (typeof(window.check_wholesale) != 'undefined')
      check_wholesale(inp.value);

  }
  return true;
}
{/literal}
//]]>
</script>
