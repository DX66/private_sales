{*
$Id: shipping.tpl,v 1.4.2.2 2011/04/08 09:59:18 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_shipping_methods}

{if $config.Shipping.enable_shipping ne "Y"}

{$lng.txt_shipping_methods_top_text}

<br /><br />

{capture name=dialog}

<br />

{$lng.txt_shipping_disabled}

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_shipping_methods content=$smarty.capture.dialog extra='width="100%"'}

{else}

<br />

{$lng.txt_shipping_methods_top_text}

<br /><br />

<script type="text/javascript" src="{$SkinDir}/js/change_all_checkboxes.js"></script>
<script type="text/javascript" language="JavaScript 1.2">//<![CDATA[
checkboxes_form = 'shippingmethodsform';
//]]></script>

{include file="permanent_warning.tpl"}

{capture name=dialog}

<br />

<form action="shipping.php" method="post" name="shippingmethodsform">
<input type="hidden" name="carrier" value="{$carrier|escape}" />

<script type="text/javascript">//<![CDATA[
var expands = new Array({foreach from=$carriers item=car}'{$car.code}',{/foreach}'');
{literal}
function expand_all(flag) {
  var x;
  for (x = 0; x < expands.length; x++) {
    if (expands[x].length == 0)
      continue;
      var elm1 = document.getElementById("open"+expands[x]);
    var elm2 = document.getElementById("close"+expands[x]);
    var elm3 = document.getElementById("box"+expands[x]);

    if(!elm3 || !elm1 || !elm2)
      continue;

    if (!flag) {
      elm1.style.display = "none";
      elm2.style.display = "";
      elm3.style.display = "none";
    } else {
      elm1.style.display = "";
      elm2.style.display = "none";
      elm3.style.display = "";
    }
  }
}
{/literal}
//]]></script>

<table cellpadding="2" cellspacing="1" width="100%">

{if $carriers ne ''}
<tr>
  <td colspan="2"><a name="rt"></a>{include file="main/subheader.tpl" title=$lng.lbl_realtime_shipping_methods}</td>
</tr>

<tr>
  <td colspan="2">
  <div align="right" style="line-height:170%"><a href="javascript:expand_all(true);">{$lng.lbl_expand_all}</a> / <a href="javascript:expand_all(false);">{$lng.lbl_collapse_all}</a></div>
  </td>
</tr>

{if $new_shipping eq 'Y'}
<tr class="TableSubHead">
  <td colspan="2">
<table cellpadding="1" cellspacing="0" width="100%">
<tr>
  <td width="25%"><b>{$lng.lbl_new_shipping_methods}</b></td>
  <td width="40%"></td>
  <td>&nbsp;</td>
</tr>
</table>
  </td>
</tr>

<tr>
  <td colspan="2">
<table cellpadding="2" cellspacing="1" width="100%">
<tr class="TableHead">
  <td>{$lng.lbl_shipping_method}</td>
  <td>{$lng.lbl_delivery_time}</td>
  <td>{$lng.lbl_destination}</td>
  <td>{$lng.lbl_service_code}</td>
  <td nowrap="nowrap">{$lng.lbl_weight_limit} ({$config.General.weight_symbol})</td>
  <td>{$lng.lbl_pos}</td>
  <td>{$lng.lbl_active}</td>
  <td>{$lng.lbl_cod}</td>
  {if $active_modules.Amazon_Checkout}
  <td>{$lng.lbl_amazon_service}</td>
  {/if}
</tr>

{foreach from=$shipping item=s}
{if $s.is_new eq 'Y'}

<tr{cycle values=", class='TableSubHead'"}>
  <td>
  <input type="hidden" name="data[{$s.shippingid}][is_new]" value="" />
  <input type="text" name="data[{$s.shippingid}][shipping]" value="{$s.shipping|escape}" size="17" />
  </td>
  <td align="center"><input type="text" name="data[{$s.shippingid}][shipping_time]" size="8" value="{$s.shipping_time}" /></td>
  <td align="center"><select name="data[{$s.shippingid}][destination]">
  <option value="L"{if $s.destination eq "L"} selected="selected"{/if}>{$lng.lbl_national}</option>
  <option value="I"{if $s.destination eq "I"} selected="selected"{/if}>{$lng.lbl_international}</option>
  </select></td>
  <td align="center"><input type="text" size="8" name="data[{$s.shippingid}][service_code]" value="{$s.service_code|escape}" /></td>
  <td align="center"><input type="text" size="8" name="data[{$s.shippingid}][weight_min]" value="{$s.weight_min|formatprice}" /> - <input type="text" size="8" name="data[{$s.shippingid}][weight_limit]" value="{$s.weight_limit|formatprice}" /></td>
  <td align="center"><input type="text" name="data[{$s.shippingid}][orderby]" size="4" value="{$s.orderby}" /></td>
  <td align="center"><input type="checkbox" name="data[{$s.shippingid}][active]" value="Y"{if $s.active eq "Y"} checked="checked"{/if} /></td>
  <td align="center"><input type="checkbox" name="data[{$s.shippingid}][is_cod]" value="Y"{if $s.is_cod eq "Y"} checked="checked"{/if} /></td>
  {if $active_modules.Amazon_Checkout}
  <td align="center">
  <select name="data[{$s.shippingid}][amazon_service]">
    <option value="Standard"{if $s.amazon_service eq "Standard"} selected="selected"{/if}>{$lng.lbl_amazon_standard}</option>
    <option value="Expedited"{if $s.amazon_service eq "Expedited"} selected="selected"{/if}>{$lng.lbl_amazon_expedited}</option>
    <option value="OneDay"{if $s.amazon_service eq "OneDay"} selected="selected"{/if}>{$lng.lbl_amazon_oneday}</option>
    <option value="TwoDay"{if $s.amazon_service eq "TwoDay"} selected="selected"{/if}>{$lng.lbl_amazon_twoday}</option>
  </select>
  </td>
  {/if}
  <td><input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="self.location='shipping.php?mode=delete&amp;shippingid={$s.shippingid}'" /></td>
</tr>
{/if}
{/foreach}

</table>
<br />
  </td>
</tr>
{/if}

{foreach from=$carriers item=car}

<tr class="TableSubHead">
  <td colspan="2">
<table cellpadding="1" cellspacing="0" width="100%">
<tr>
  <td width="25%">{include file="main/visiblebox_link.tpl" mark=$car.code title=$car.shipping}</td>
  <td width="40%">{$lng.lbl_X_from_Y_shipping_methods_enabled|substitute:"enabled":$car.total_enabled:"methods":$car.total_methods}</td>
  <td>{if $config.Shipping.realtime_shipping eq "Y" and $config.Shipping.use_intershipper ne "Y" and $active_modules.UPS_OnLineTools eq "" and ($car.code eq "CPC" or $car.code eq "FDX" or $car.code eq "USPS" or $car.code eq "ARB" or $car.code eq "APOST")}<a href="shipping_options.php?carrier={$car.code}">{$lng.lbl_options} &gt;&gt;</a>{elseif $config.Shipping.realtime_shipping eq "Y" and $active_modules.UPS_OnLine_Tools and $config.Shipping.use_intershipper ne "Y" and $car.code eq "UPS"}<a href="ups.php">{$lng.lbl_ups_online_tools_configure} &gt;&gt;</a>{/if}</td>
</tr>
</table>
  </td>
</tr>
<tr id="box{$car.code}" style="display: none;">
  <td colspan="2">

<div align="right" style="line-height:170%"><a href="javascript:void(0);" onclick="change_all(true,false,new Array({foreach from=$shipping item=v key=k}{if $v.code eq $car.code}'data[{$v.shippingid}][active]',{/if}{/foreach}''));">{$lng.lbl_check_all}</a> / <a href="javascript:void(0);" onclick="change_all(false,false,new Array({foreach from=$shipping item=v key=k}{if $v.code eq $car.code}'data[{$v.shippingid}][active]',{/if}{/foreach}''));">{$lng.lbl_uncheck_all}</a></div>

<table cellpadding="2" cellspacing="1" width="100%">
<tr class="TableHead">
  <td>{$lng.lbl_shipping_method}</td>
  <td>{$lng.lbl_delivery_time}</td>
  <td>{$lng.lbl_destination}</td>
{if $active_modules.Google_Checkout and (($car.code eq "FDX") or ($car.code eq "UPS") or ($car.code eq "USPS")) and $config.Google_Checkout.gcheckout_use_gc_shipping eq "Y"}
  <td>{$lng.lbl_gcheckout_shipping}</td>
{/if}
  <td nowrap="nowrap">{$lng.lbl_weight_limit} ({$config.General.weight_symbol})</td>
  <td>{$lng.lbl_pos}</td>
  <td>{$lng.lbl_active}</td>
  <td>{$lng.lbl_cod}</td>
  {if $active_modules.Amazon_Checkout}
  <td>{$lng.lbl_amazon_service}</td>
  {/if}
</tr>

{foreach from=$shipping item=s}
{if $s.code eq $car.code and $s.is_new ne 'Y'}

<tr{cycle values=", class='TableSubHead'"}>
  <td>{$s.shipping|trademark}</td>
  <td align="center"><input type="text" name="data[{$s.shippingid}][shipping_time]" size="8" value="{$s.shipping_time}" /></td>
  <td align="center">{if $s.destination eq "L"}{$lng.lbl_national}{else}{$lng.lbl_international}{/if}</td>
{if $active_modules.Google_Checkout and (($car.code eq "FDX") or ($car.code eq "UPS") or ($car.code eq "USPS")) and $config.Google_Checkout.gcheckout_use_gc_shipping eq "Y"}
  <td align="center">{$s.gc_shipping|escape}</td>
{/if}
  <td align="center" nowrap="nowrap"><input type="text" size="8" name="data[{$s.shippingid}][weight_min]" value="{$s.weight_min|default:0|formatprice}" /> - <input type="text" size="8" name="data[{$s.shippingid}][weight_limit]" value="{$s.weight_limit|formatprice}" /></td>
  <td align="center"><input type="text" name="data[{$s.shippingid}][orderby]" size="4" value="{$s.orderby}" /></td>
  <td align="center"><input type="checkbox" name="data[{$s.shippingid}][active]" value="Y"{if $s.active eq "Y"} checked="checked"{/if} /></td>
  <td align="center"><input type="checkbox" name="data[{$s.shippingid}][is_cod]" value="Y"{if $s.is_cod eq "Y"} checked="checked"{/if} /></td>
  {if $active_modules.Amazon_Checkout}
  <td align="center">
  <select name="data[{$s.shippingid}][amazon_service]">
    <option value="Standard"{if $s.amazon_service eq "Standard"} selected="selected"{/if}>{$lng.lbl_amazon_standard}</option>
    <option value="Expedited"{if $s.amazon_service eq "Expedited"} selected="selected"{/if}>{$lng.lbl_amazon_expedited}</option>
    <option value="OneDay"{if $s.amazon_service eq "OneDay"} selected="selected"{/if}>{$lng.lbl_amazon_oneday}</option>
    <option value="TwoDay"{if $s.amazon_service eq "TwoDay"} selected="selected"{/if}>{$lng.lbl_amazon_twoday}</option>
  </select>
  </td>
  {/if}
</tr>

{/if}
{/foreach}

</table>
<br />
  </td>
</tr>

{/foreach}

<tr>
  <td colspan="2">
{if $carrier ne ''}
<script type="text/javascript">//<![CDATA[
visibleBox('{$carrier}');
//]]></script>
{/if}
  </td>
</tr>
{/if}
<tr>
  <td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_defined_shipping_methods}</td>
</tr>

<tr>
  <td colspan="2">
<table cellpadding="2" cellspacing="1" width="100%">

<tr class="TableHead">
  <td>{$lng.lbl_shipping_method}</td>
  <td>{$lng.lbl_delivery_time}</td>
  <td>{$lng.lbl_destination}</td>
  <td>{$lng.lbl_weight_limit} ({$config.General.weight_symbol})</td>
  <td>{$lng.lbl_pos}</td>
  <td>{$lng.lbl_active}</td>
  <td>{$lng.lbl_cod}</td>
  {if $active_modules.Amazon_Checkout}
  <td>{$lng.lbl_amazon_service}</td>
  {/if}
</tr>

{foreach from=$shipping item=s}
{if $s.code eq ""}
<tr>
  <td><input type="text" name="data[{$s.shippingid}][shipping]" size="17" value="{$s.shipping|escape}" /></td>
  <td align="center"><input type="text" name="data[{$s.shippingid}][shipping_time]" size="8" value="{$s.shipping_time}" /></td>
  <td align="center"><select name="data[{$s.shippingid}][destination]">
    <option value="I"{if $s.destination eq "I"} selected="selected"{/if}>{$lng.lbl_international}</option>
    <option value="L"{if $s.destination eq "L"} selected="selected"{/if}>{$lng.lbl_national}</option>
  </select></td>
  <td align="center"><input type="text" size="8" name="data[{$s.shippingid}][weight_min]" value="{$s.weight_min|default:0|formatprice}" /> - <input type="text" size="8" name="data[{$s.shippingid}][weight_limit]" value="{$s.weight_limit|formatprice}" /></td>
  <td align="center"><input type="text" name="data[{$s.shippingid}][orderby]" size="4" value="{$s.orderby}" /></td>
  <td nowrap="nowrap" align="center"><input type="checkbox" name="data[{$s.shippingid}][active]" value="Y"{if $s.active eq "Y"} checked="checked"{/if} /></td>
  <td nowrap="nowrap" align="center"><input type="checkbox" name="data[{$s.shippingid}][is_cod]" value="Y"{if $s.is_cod eq "Y"} checked="checked"{/if} /></td>
  {if $active_modules.Amazon_Checkout}
  <td align="center">
  <select name="data[{$s.shippingid}][amazon_service]">
    <option value="Standard"{if $s.amazon_service eq "Standard"} selected="selected"{/if}>{$lng.lbl_amazon_standard}</option>
    <option value="Expedited"{if $s.amazon_service eq "Expedited"} selected="selected"{/if}>{$lng.lbl_amazon_expedited}</option>
    <option value="OneDay"{if $s.amazon_service eq "OneDay"} selected="selected"{/if}>{$lng.lbl_amazon_oneday}</option>
    <option value="TwoDay"{if $s.amazon_service eq "TwoDay"} selected="selected"{/if}>{$lng.lbl_amazon_twoday}</option>
  </select>
  </td>
  {/if}
  <td><input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="self.location='shipping.php?mode=delete&amp;shippingid={$s.shippingid}'" /></td>
</tr>
{/if}
{/foreach}

<tr>
  <td colspan="6"><br />{include file="main/subheader.tpl" title=$lng.lbl_add_shipping_method}</td>
</tr>

<tr>
  <td><input type="text" name="add[shipping]" size="17" /></td>
  <td align="center"><input type="text" name="add[shipping_time]" size="10" /></td>
  <td align="center"><select name="add[destination]">
    <option value="I">{$lng.lbl_international}</option>
    <option value="L">{$lng.lbl_national}</option>
  </select></td>
  <td align="center" nowrap="nowrap"><input type="text" name="add[weight_min]" size="8" value="{0|formatprice}" /> - <input type="text" name="add[weight_limit]" size="8" value="{0|formatprice}" /></td>
  <td align="center"><input type="text" name="add[orderby]" size="4" /></td>
  <td align="center"><input type="checkbox" name="add[active]" value="Y" checked="checked" /></td>
  <td align="center"><input type="checkbox" name="add[is_cod]" value="Y" /></td>
</tr>

<tr>
  <td>&nbsp;</td>
</tr>

<tr>
  <td colspan="3" class="main-button">
    <input type="submit" value="{$lng.lbl_apply_changes|strip_tags:false|escape}" class="big-main-button" />
  </td>

{if $shipping}
  <td colspan="5" align="right">
    <input type="button" onclick="javascript: self.location='shipping.php?mode=enable_all&amp;carrier={$carrier}'" value="{$lng.lbl_enable_all|strip_tags:false|escape}" />
  &nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" onclick="javascript: self.location='shipping.php?mode=disable_all&amp;carrier={$carrier}'" value="{$lng.lbl_disable_all|strip_tags:false|escape}" />
  </td>
{/if}
</tr>



</table>
  </td>
</tr>

</table>
</form>

<br /><br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_shipping_methods content=$smarty.capture.dialog extra='width="100%"'}

{/if}
