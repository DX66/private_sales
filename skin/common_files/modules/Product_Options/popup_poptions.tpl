{*
$Id: popup_poptions.tpl,v 1.4 2010/08/02 10:53:39 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var min_avail = {$min_avail|default:0};
var avail = {inc value=$min_avail};
var product_avail = avail;
var txt_out_of_stock = "{$lng.txt_out_of_stock|wm_remove|escape:javascript|replace:"\n":"<br />"|replace:"\r":" "}";

{literal}
function FormValidationEdit() {

  if(!check_exceptions()) {
    alert(exception_msg);
    return false;
  }
{/literal}
{if $target ne "wishlist"}
{literal}
  else if (min_avail > avail) {
    alert(txt_out_of_stock);
    return false;
  }
{/literal}
{/if}
  {if $product_options_js ne ''}
  {$product_options_js}
  {/if}
{literal}

    return true;
}
{/literal}
//]]>
</script>

<form action="popup_poptions.php" method="post" name="orderform" onsubmit="javascript: return FormValidationEdit();">
  <input type="hidden" name="mode" value="update" />
  <input type="hidden" name="id" value="{$id|escape}" />
  <input type="hidden" name="target" value="{$target|escape}" />
  <input type="hidden" name="eventid" value="{$eventid|escape}" />

  <table cellspacing="0" class="product-properties" summary="{$lng.lbl_options|escape}">

    {include file="modules/Product_Options/customer_options.tpl"}

    <tr>
      <td>&nbsp;</td>
      <td>
        <div class="button-row">
          {include file="customer/buttons/update.tpl" type="input"}
        </div>
      </td>
    </tr>

  </table>

</form>
