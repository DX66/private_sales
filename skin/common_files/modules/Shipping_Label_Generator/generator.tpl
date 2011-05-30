{*
$Id: generator.tpl,v 1.4 2010/06/10 12:06:22 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_shipping_labels}

{$lng.txt_shipping_labels_info}
<br /><br />

<br />

{capture name=dialog}

{$lng.txt_shipping_labels_help}
<br /><br />
<form action="generator.php{if $smarty.get.orderids ne ""}?orderids={$smarty.get.orderids}{/if}" method="post" name="ordersform">
<input type="hidden" name="mode" value="update" />

<script type="text/javascript" language="JavaScript 1.2">
//<![CDATA[
checkboxes_form = 'ordersform';
checkboxes = new Array({foreach from=$orders item=v key=k name=orders}'orderids_update[{$k}]'{if not $smarty.foreach.orders.last},{/if}{/foreach});
 
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

<table cellspacing="1" cellpadding="5" width="100%">
<tr class="TableHead">
  <td width="10"></td>
  <td width="5%">{$lng.lbl_order}</td>
  <td width="25%">{$lng.lbl_customer}</td>
  <td width="5%">{$lng.lbl_date}</td>
  <td width="25%">{$lng.lbl_shipping_method}</td>
  <td width="40%">{$lng.lbl_shipping_label}</td>
</tr>
{foreach from=$orders key=orderid item=v}
<tr{cycle values=", class='TableSubHead'"}>
  <td><input type="checkbox" name="orderids_update[{$orderid}]" value="Y" /><input type="hidden" name="orderids[{$orderid}]" value="{$orderid}" /></td>
  <td align="center"><a href="order.php?orderid={$orderid}">#{$orderid}</a></td>
  <td align="center">{if $v.order.userid gt 0}{$v.userinfo.login|default:"`$lng.lbl_deleted` (`$lng.lbl_id`: `$v.order.userid`)"}{else}{$lng.txt_not_available}{/if}</td>
  <td align="center">{$v.order.date|date_format:$config.Appearance.date_format}</td>
  <td align="center">{$v.order.shipping|trademark|default:$lng.txt_not_available}</td>
  <td align="left">
    <!-- Labels data [[[ -->
    {if $v.labels}
    {foreach from=$v.labels item=label key=labelid}
      {if $label.error ne ""}
        <b>{$lng.lbl_error}:</b> {$label.error}  
      {else}
        <a href="{$current_location}/slabel.php?orderid={$orderid}{if $labelid ne ""}&amp;labelid={$labelid}{/if}">{$label.descr|default:$lng.lbl_download}</a>{if $label.packages_number gt 1 && $label.is_first eq 'Y'} x {$label.packages_number}{/if}<br />
      {/if}
    {/foreach}
    {else}
      {$lng.txt_not_available}
    {/if}
    <!-- Labels data ]]] -->
  </td>
</tr>
{/foreach}

{if $have_ups_orders or $have_img_orders}
<tr>
  <td colspan="6">
    <hr />
  </td>
</tr>
{/if}

{if $have_ups_orders}
<!-- Get all UPS labels link [[[ -->
<tr>
  <td colspan="5">&nbsp;</td>
  <td align="center">
    <a href="{$current_location}/slabel.php?mode=ups_labels">{$lng.lbl_all_ups_labels}</a>
  </td>
</tr>
<!-- Get all UPS labels link ]]] -->
{/if}

{if $have_img_orders}
<!-- Print all image labels link [[[ -->
<tr>
  <td colspan="5">&nbsp;</td>
  <td align="center">
    <a href="{$current_location}/slabel.php?mode=img_labels" target="_blank">{$lng.lbl_slg_print_img_labels}</a>
  </td>
</tr>
<!-- Print all image labels ]]] -->
{/if}

</table>

<br />

{$lng.txt_shipping_labels_note}

<br />
<br />

<input type="button" value="{$lng.lbl_update_shipping_labels}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) {ldelim} submitForm(this, 'update'); {rdelim}"/>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_selected_orders content=$smarty.capture.dialog extra='width="100%"'}
