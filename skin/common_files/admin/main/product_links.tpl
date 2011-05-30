{*
$Id: product_links.tpl,v 1.3.2.3 2010/12/15 11:57:05 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $product}
  {assign var="product_title" value=$product.product|truncate:30:"...":false}
  {assign var="page_title" value="`$lng.lbl_product_links`<br /><span class='ProductTitle'>`$product_title`</span>"}
{/if}

{include file="page_title.tpl" title=$page_title}

<p>{$lng.txt_product_links_top_text}</p>

<br />

<script src="{$SkinDir}/js/product_links.js" type="text/javascript"></script>
<iframe src="{$catalogs.customer}/product.php?productid={$product.productid}&amp;is_admin_preview=Y" style="border: 1px solid black; height: 400px; overflow: auto; width:100%;" id="product-frame"></iframe>

<br />
<br />

{capture name="product_thumbnail"}
{include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type full_url="Y"}
{/capture}

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td valign="top" width="20%">
{include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type full_url="Y"}
  </td>
  <td>&nbsp;</td>
  <td>
<b>{$lng.lbl_html_code}:</b><br />
<textarea cols="65" rows="5">{$smarty.capture.product_thumbnail|escape}</textarea>
  </td>
</tr>

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_product_link_thumbnail content=$smarty.capture.dialog extra='width="100%"'} 

{*** Simple HTML link to add 1 product to cart ***}

<br /><br />

{capture name="add_to_cart"}
{include file="buttons/add_to_cart.tpl" href="`$http_customer_location`/cart.php?mode=add&amp;productid=`$product.productid`&amp;amount=1"}
{/capture}

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
  <td width="20%">
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount)}
<script type="text/javascript">
//<![CDATA[
var txt_this_link_is_for_demo_purposes = '{$lng.txt_this_link_is_for_demo_purposes|wm_remove|escape:javascript}';
//]]>
</script>
<br />{include file="buttons/add_to_cart.tpl" href="javascript: alert(txt_this_link_is_for_demo_purposes); return false;"}
{/if}
  </td>
  <td>&nbsp;</td>
  <td>
<b>{$lng.lbl_html_code}:</b><br />
<textarea cols="65" rows="5">
{$smarty.capture.add_to_cart|escape}
</textarea>
  </td>
</tr>

</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_add_1_product_link content=$smarty.capture.dialog extra='width="100%"'} 

{*** Full functionallity 'Add to cart' button ***}

<br /><br />

{capture name=dialog}

<table cellpadding="3" cellspacing="1" width="100%">
<tr>
  <td width="100%">

{*** Start preview version ***}
<table width="100%" cellpadding="0" cellspacing="0">
{if $active_modules.Subscriptions ne "" and $subscription}
{include file="modules/Subscriptions/subscription_info.tpl"}
{else}
<tr>
    <td class="ProductPriceConverting" valign="top">{$lng.lbl_price}:</td>
    <td valign="top">
{if $product.taxed_price ne 0 or $variants ne ''}
<font class="ProductDetailsTitle">{currency value=$product.taxed_price}</font><font class="MarketPrice"> {alter_currency value=$product.taxed_price}</font>
{if $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes}{/if}
{else}
<input type="text" size="7" name="price" />
{/if}
    </td>
</tr>
{/if}

{if $active_modules.Product_Options ne "" and $product_options ne ''}
{foreach from=$product_options item=v}
{if $v.options ne '' or $v.is_modifier eq 'T'}
<tr>
    <td valign="middle" height="25">{$v.classtext|default:$v.class|escape}</td>
    <td valign="middle">
{if $v.is_modifier eq 'T'}
<input type="text" value="{$v.default|escape}" />
{else}
<select>
{foreach from=$v.options item=o}
    <option value="{$o.optionid}"{if $o.selected eq 'Y'} selected="selected"{/if}>{$o.option_name|escape}{if $v.is_modifier eq 'Y' and $o.price_modifier ne 0} ({if $o.modifier_type ne '%'}{currency value=$o.price_modifier display_sign=1 plain_text_message=1}{else}{$o.price_modifier}%{/if}){/if}</option>
{/foreach}
</select>
{/if}
    </td>
</tr>
{/if}
{/foreach}
{/if}
<tr>
    <td height="25" width="30%">
    {$lng.lbl_quantity}{if $product.min_amount gt 1}<br /><font class="ProductDetailsTitle">{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}</font>{/if}
    </td>
    <td>

{if $product.appearance.empty_stock and $variants eq '' or ($variants ne '' and $product.avail le 0)}

<script type="text/javascript">
//<![CDATA[
var min_avail = 1;
var avail = 0;
var product_avail = 0;
//]]>
</script>

                  <strong>{$lng.txt_out_of_stock}</strong>

                {elseif not $product.appearance.force_1_amount and $product.forsale ne "B"}

<script type="text/javascript">
//<![CDATA[
var min_avail = {$product.appearance.min_quantity|default:1};
var avail = {$product.appearance.max_quantity|default:1};
var product_avail = {$product.avail|default:"0"};
//]]>
</script>

                  <select id="product_avail" name="amount"{if $active_modules.Product_Options ne '' and ($product_options ne '' or $product_wholesale ne '')} onchange="javascript: check_wholesale(this.value);"{/if}>
                    {section name=quantity loop=$product.appearance.loop_quantity start=$product.appearance.min_quantity}
                      <option value="{%quantity.index%}"{if $smarty.get.quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
                    {/section}
                  </select>

                {else}

<script type="text/javascript">
//<![CDATA[
var min_avail = 1;
var avail = 1;
var product_avail = 1;
//]]>
</script>

                  <span class="product-one-quantity">1</span>
                  <input type="hidden" name="amount" value="1" />

                  {if $product.distribution ne ""}
                    {$lng.txt_product_downloadable}
                  {/if}

                {/if}

    </td>
</tr>
<tr>
    <td colspan="2">
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount)}
<script type="text/javascript">
//<![CDATA[
var txt_this_form_is_for_demo_purposes = '{$lng.txt_this_form_is_for_demo_purposes|wm_remove|escape:javascript}';
//]]>
</script>
<br />{include file="buttons/add_to_cart.tpl" href="javascript: alert(txt_this_form_is_for_demo_purposes); return false;"}
{/if}
    </td>
</tr>
</table>
{*** End preview version ***}

  </td>
</tr>
<tr>
    <td>

{*** Start HTML version ***}
{capture name=add2cart}

<form name="orderform_{$product.productid}" method="post" action="{$http_customer_location}/cart.php">
<input type="hidden" name="mode" value="add" />
<input type="hidden" name="productid" value="{$product.productid}" />

<table width="100%">
<tr>
    <td valign="top">
<br />
<table width="100%" cellpadding="0" cellspacing="0">
{if $active_modules.Subscriptions ne "" and $subscription}
{include file="modules/Subscriptions/subscription_info.tpl"}
{else}
<tr>
    <td class="ProductPriceConverting" valign="top">{$lng.lbl_price}:</td>
    <td valign="top">
{if $product.taxed_price ne 0 or $variants ne ''}
<font class="ProductDetailsTitle">{currency value=$product.taxed_price}</font><font class="MarketPrice"> {alter_currency value=$product.taxed_price}</font>
{if $product.taxes}<br />{include file="customer/main/taxed_price.tpl" taxes=$product.taxes is_subtax=true}{/if}
{else}
<input type="text" size="7" name="price" />
{/if}
    </td>
</tr>
{/if}

{if $active_modules.Product_Options ne ""}
{include file="modules/Product_Options/customer_options.tpl" nojs="Y"}
{/if}
<tr>
    <td height="25" width="30%">
    {$lng.lbl_quantity}{if $product.min_amount gt 1}<br /><font class="ProductDetailsTitle">{$lng.txt_need_min_amount|substitute:"items":$product.min_amount}</font>{/if}
    </td>
    <td>

{if $product.appearance.empty_stock and $variants eq '' or ($variants ne '' and $product.avail le 0)}

<script type="text/javascript">
//<![CDATA[
var min_avail = 1;
var avail = 0;
var product_avail = 0;
//]]>
</script>

                  <strong>{$lng.txt_out_of_stock}</strong>

                {elseif not $product.appearance.force_1_amount and $product.forsale ne "B"}

<script type="text/javascript">
//<![CDATA[
var min_avail = {$product.appearance.min_quantity|default:1};
var avail = {$product.appearance.max_quantity|default:1};
var product_avail = {$product.avail|default:"0"};
//]]>
</script>

                  <select id="product_avail" name="amount"{if $active_modules.Product_Options ne '' and ($product_options ne '' or $product_wholesale ne '')} onchange="javascript: check_wholesale(this.value);"{/if}>
                    {section name=quantity loop=$product.appearance.loop_quantity start=$product.appearance.min_quantity}
                      <option value="{%quantity.index%}"{if $smarty.get.quantity eq %quantity.index%} selected="selected"{/if}>{%quantity.index%}</option>
                    {/section}
                  </select>

                {else}

<script type="text/javascript">
//<![CDATA[
var min_avail = 1;
var avail = 1;
var product_avail = 1;
//]]>
</script>

                  <span class="product-one-quantity">1</span>
                  <input type="hidden" name="amount" value="1" />

                  {if $product.distribution ne ""}
                    {$lng.txt_product_downloadable}
                  {/if}

                {/if}

    </td>
</tr>
<tr>
    <td colspan="2">
{if $config.General.unlimited_products eq "Y" or ($product.avail gt 0 and $product.avail ge $product.min_amount)}
<br />{include file="buttons/add_to_cart.tpl" href="javascript: document.orderform_`$product.productid`.submit();"}
{/if}
    </td>
</tr>
</table>
    </td>
</tr>
</table>
</form>
{/capture}
{*** End HTML version ***}

  </td>
</tr>
<tr>
  <td>
  <b>{$lng.lbl_html_code}:</b><br />
  <textarea cols="75" rows="10">{$smarty.capture.add2cart|escape}</textarea>
  </td>
</tr>
</table>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_advanced_product_link content=$smarty.capture.dialog extra='width="100%"'} 

