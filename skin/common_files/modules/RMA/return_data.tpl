{*
$Id: return_data.tpl,v 1.1 2010/05/21 08:32:47 joy Exp $ 
vim: set ts=2 sw=2 sts=2 et:
*}
<strong>{$lng.eml_return_request}</strong>:
<br />
<table border="0" cellpadding="3" cellspacing="1">
<tr>
  <td>{$lng.lbl_returnid}</td>
  <td>{$return.returnid}</td>
</tr>
<tr>
  <td>{$lng.lbl_product}</td>
  <td>{$return.product.product}</td>
</tr>
{if $return.product.product_options}
<tr>
  <td>{$lng.lbl_product_options}</td>
  <td>{include file="modules/Product_Options/display_options.tpl" options=$return.product.product_options}</td>
</tr>
{/if}
<tr>
  <td>{$lng.lbl_quantity}</td>
  <td>{$return.amount}</td>
</tr>
{if $return.reason ne ""}
<tr>
  <td>{$lng.lbl_reason_for_returning}</td>
  <td>{$reasons[$return.reason]}</td>
</tr>
{/if}
{if $return.action ne ""}
<tr>
  <td>{$lng.lbl_what_you_would_like_us_to_do}</td>
  <td>{$actions[$return.action]}</td>
</tr>
{/if}
{if $return.comment ne ""}
<tr>
  <td>{$lng.lbl_comment}</td>
  <td>{$return.comment}</td>
</tr>
{/if}
</table>
<br />
{$lng.eml_click_to_view_return}:<br />
<a href="{$catalogs.customer}/returns.php?mode=modify&amp;returnid={$return.returnid}">{$catalogs.customer}/returns.php?mode=modify&returnid={$return.returnid}</a>
<br />
