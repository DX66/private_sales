{*
$Id: history_order.tpl,v 1.8.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_order_details_label}

{$lng.txt_order_details_top_text}

<br /><br />

{if $usertype eq 'A' and $is_merchant_password ne 'Y' and $config.Security.blowfish_enabled eq 'Y'}
{capture name=dialog}
<form action="{$catalogs.admin}/merchant_password.php" method="post" name="mpasswordform">
<input type="hidden" name="orderid" value="{$orderid}" />
{$lng.txt_enter_merchant_password_note}
<br /><br />
<table>
<tr>
  <td><font class="VertMenuItems">{$lng.lbl_merchant_password}</font></td>
  <td><input type="password" name="mpassword" size="16" /></td>
  <td><input type="submit" value="{$lng.lbl_enter_mpassword|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_enter_merchant_password content=$smarty.capture.dialog extra='width="100%"'}
<br />
{/if}

<table width="100%">
<tr> 
  <td valign="top">

  <div style="float: left"><strong>{$lng.lbl_order} #{$order.orderid}</strong><br />{$lng.lbl_date}: {$order.date|date_format:$config.Appearance.datetime_format}</div>

{if $search_data_orders}
  <div style="float: right">
    <form action="orders.php" method="post" name="ordersearch">
      {foreach from=$search_data_orders key=key item=item}
        {if $key eq "features"}
          {foreach from=$item key=fkey item=fitem}
            <input type="hidden" name="posted_data[{$key}][]" value="{$fkey}" />
          {/foreach}
        {else}
          <input type="hidden" name="posted_data[{$key}]" value="{$item}" />
        {/if}
      {/foreach}
      {include file="buttons/button.tpl" button_title=$lng.lbl_go_to_orders_list href="javascript:document.ordersearch.submit()" js_to_href="Y"}
    </form>
  </div>
{/if}

<div style="clear: both"></div>

<br />
{if $orderid_prev ne ""}<a href="order.php?orderid={$orderid_prev}">&lt;&lt;&nbsp;{$lng.lbl_order} #{$orderid_prev}</a>{/if}
{if $orderid_next ne ""}{if $orderid_prev ne ""} | {/if}<a href="order.php?orderid={$orderid_next}">{$lng.lbl_order} #{$orderid_next}&nbsp;&gt;&gt;</a>{/if}
<br /><br />

<table width="100%" cellspacing="0" cellpadding="0">
<tr>
  <td align="left">
<table cellspacing="0" cellpadding="2" class="ButtonsRow">
<tr>
{if ($usertype eq 'A' or ($usertype eq 'P' and $active_modules.Simple_Mode))}
<td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_delete href="javascript: if(confirm(txt_delete_order_warning)) self.location = 'process_order.php?mode=delete&amp;orderid=`$order.orderid`';" substyle="delete"}</td>
{/if}
{if ($usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode)) and $active_modules.Advanced_Order_Management}
<td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_modify href="order.php?orderid=`$order.orderid`&mode=edit" substyle="edit"}</td>
{/if}
{if $active_modules.RMA ne '' and $current_membership_flag ne 'FS'}
{if ($usertype eq 'A' or ($usertype eq 'P' and $active_modules.Simple_Mode)) and $return_products ne ''}
<td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_create_return href="#returns" substyle="return"}</td>
{/if}
{if ($usertype eq 'A' or ($usertype eq 'P' and $active_modules.Simple_Mode)) and $order.is_returns}
<td class="ButtonsRow">{include file="buttons/button.tpl" button_title=$lng.lbl_order_returns href="returns.php?mode=search&search[orderid]=`$order.orderid`" substyle="link"}</td>
{/if}
{/if}
</tr>
</table>
  </td>
  <td align="right">
<table cellspacing="0" cellpadding="2" class="ButtonsRow">
<tr>
{if $active_modules.Shipping_Label_Generator ne '' and ($usertype eq 'A' or $usertype eq 'P')} 
<td class="ButtonsRowRight">{include file="buttons/button.tpl" button_title=$lng.lbl_shipping_label href="generator.php?orderids=`$order.orderid`" substyle="link"}</td>
{/if}
<td class="ButtonsRowRight">
  {if $order.status eq 'A' or $order.status eq 'P' or $order.status eq 'C'}
    {assign var=bn_title value=$lng.lbl_print_receipt}
  {else}
    {assign var=bn_title value=$lng.lbl_print_invoice}
  {/if}
  {include file="buttons/button.tpl" button_title=$bn_title target="_blank" href="order.php?orderid=`$order.orderid`&mode=invoice" substyle="link"}
</td>
{if $active_modules.Advanced_Order_Management and $order.history ne ""}
<td class="ButtonsRowRight">{include file="buttons/button.tpl" button_title=$lng.lbl_aom_show_history href="javascript: window.open('order.php?orderid=`$order.orderid`&amp;mode=history', 'HISTORY', 'width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');" substyle="link"}</td>
{/if}
</tr>
</table>
  </td>
</tr>
</table>

<br />
{include file="main/order_info.tpl"}
  </td>
</tr>
<tr>
  <td height="1" valign="top">
<script type="text/javascript">
//<![CDATA[
var txt_delete_order_warning = "{$lng.txt_delete_order_warning|wm_remove|escape:javascript|strip_tags}";
var details_mode = false;
var details_fields_labels = new Object();
{foreach from=$order_details_fields_labels key=dfield item=dlabel}
details_fields_labels["{$dfield|wm_remove|escape:javascript}"] = "{$dlabel|wm_remove|escape:javascript}";
{/foreach}
//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/history_order.js"></script>
<form action="order.php" method="post">

<br />
{$lng.lbl_customer_notes}:<br />
<iframe src="order.php?mode=view_cnote&amp;orderid={$order.orderid}" width="100%" height="170" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" title="{$lng.lbl_customer_notes|escape}" style="overflow: visible; border: 0px none;" name="cnotesframe"></iframe>
{*
<textarea name="customer_notes" cols="70" rows="8" style="width: 520px;">{$order.customer_notes|escape}</textarea>
*}
<br />
{if $usertype eq "A"}

{if $split_checkout_data ne ""}
{* //TODO *}
<br />

<ul>
{foreach from=$split_checkout_data.data item=data}
<li>
{$data.payment} ({currency value=$data.paid_amount})<br />
<a href="#">{$lng.lbl_void}</a>&nbsp;<a href="#">{$lng.lbl_capture}</a>
</li>
{/foreach}
</ul>
{* //TODO *}
{/if}

{if $active_modules.Google_Checkout ne '' and $order.extra.goid ne ''}

  {$lng.lbl_status}:<br />
  {include file="main/order_status.tpl" status=$order.status mode="select" name="status" extra="disabled='disabled'"}<br />
  {$lng.txt_gcheckout_order_status_note}

{elseif $order.fmf and $order.fmf.blocked}

  {if $order.capture_enable and ($order.fmf.can_accept or $order.fmf.can_decline)}
    {$lng.lbl_status}:&nbsp;&nbsp;<strong>{$lng.lbl_pre_authorized}</strong><br />

  {else}
    {$lng.lbl_status}:<br />
    {include file="main/order_status.tpl" status=$order.status mode="select" name="status"}<br />
  {/if}
  <br />

  <strong>{$lng.lbl_warning}:</strong> {$lng.txt_paypal_fmf_note}<br />
  <br />

  {if $order.fmf.can_accept}
    <input type="button" value="{$lng.lbl_accept|escape}" onclick="javascript: self.location = 'process_order.php?orderid={$order.orderid}&amp;mode=accept';" />
  &nbsp;&nbsp;
  {/if}
  {if $order.fmf.can_decline}
    <input type="button" value="{$lng.lbl_decline|escape}" onclick="javascript: self.location = 'process_order.php?orderid={$order.orderid}&amp;mode=decline';" />
  {/if}
  {if not $order.fmf.can_accept and not $order.fmf.can_decline}
    {$lng.txt_fmf_blocked_order}
  {/if}
  <br />

{elseif $order.capture_enable}

  {if $order.paypal.fmf}
    <strong>{$lng.lbl_warning}:</strong> {$lng.txt_paypal_fmf_note}<br />
  {/if}
  {$lng.lbl_status}:&nbsp;&nbsp;<strong>{$lng.lbl_pre_authorized}</strong>

  {currency value=$order.init_total assign=init_total plain_text_message=Y}

  {if not $order.capture_noninit or $order.capture_limit}
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="{$lng.lbl_capture_n|substitute:cost:$init_total}"{if $order.paypal.fmf} disabled="disabled"{/if}onclick="javascript: self.location = 'process_order.php?orderid={$order.orderid}&amp;mode=capture';" />
    {if $order.void_enable}
      &nbsp;&nbsp;&nbsp;&nbsp;
      <input type="button" value="{$lng.lbl_decline}" onclick="javascript: self.location = 'process_order.php?orderid={$order.orderid}&amp;mode=void';"/>
    {/if}
  {/if}
 
  {if $order.capture_noninit}

    <br />
    <br />
    {$lng.txt_order_total_was_changed_manually}<br />
    <table cellspacing="0" cellpadding="2">
      <tr>
        <td>{$lng.lbl_initial_total}:</td>
        <td>{currency value=$order.init_total}</td>
      </tr>
      <tr>
        <td>{$lng.lbl_current_total}:</td>
        <td>{currency value=$order.capture_full_total}</td>
      </tr>
    </table>

    {if not $order.capture_limit}
      <br />
      <table cellspacing="1" cellpadding="3">
        <tr>
          <td><img src="{$ImagesDir}/icon_warning_small.gif" alt="{$lng.lbl_warning|escape}" /></td>
          <td>{$lng.txt_current_amount_cant_be_captured}</td>
        </tr>
      </table>
      <br />

      <input type="button" value="{$lng.lbl_capture_n|substitute:cost:$init_total}"{if $order.paypal.fmf} disabled="disabled"{/if} onclick="javascript: self.location = 'process_order.php?orderid={$order.orderid}&amp;mode=capture';" />
      {if $order.void_enable}
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input type="button" value="{$lng.lbl_decline}" onclick="javascript: self.location = 'process_order.php?orderid={$order.orderid}&amp;mode=void';"/>
      {/if}
    {/if}

  {/if}

{elseif $order.non_capture_message}

  {$lng.lbl_status}:&nbsp;&nbsp;<strong>{$lng.lbl_pre_authorized}</strong>&nbsp;&nbsp;&nbsp;&nbsp;{$order.non_capture_message}

{else}

  {$lng.lbl_status}:<br />
  {include file="main/order_status.tpl" status=$order.status mode="select" name="status"}<br />
  {if $order.status eq "A"}
    {$lng.txt_preauthorized_order}
  {/if}

  {if $order.extra.preauth_expired eq 'Y'}
    <br />
    <strong>{$lng.lbl_note}:</strong> {$lng.txt_order_declined_by_preauth_ttl}<br />
    <br />
  {/if}

{/if}

{else}

{$lng.lbl_status}:<br />
<strong>{include file="main/order_status.tpl" status=$order.status mode="static"}</strong>

{/if}

{if $order.can_get_info}
  <br />
  <br />
  <input type="button" value="{$lng.lbl_update_payment_info|escape}" onclick="javascript: self.location = 'process_order.php?orderid={$order.orderid}&amp;mode=get_info';" /><br />
{/if}

{if $active_modules.XPayments_Connector and $order.extra.xpc_txnid and $config.XPayments_Connector.xpc_xpayments_url}
  <br />
  {strip}
<table cellpadding="2" cellspacing="2">
  <tr>
  <td>
    <a href="process_order.php?orderid={$order.orderid}&amp;mode=xpayments&amp;submode=get_info" target="_blank" onclick="javascript: window.open(this.href, 'paymentInfo', 'width=750, height=400, resizable=yes, toolbar=no, status=no, menubar=no, location=no'); return false;">{$lng.txt_xpc_view_payment_info}</a>
  </td>
  <td>
    <a class="external-link" href="{$config.XPayments_Connector.xpc_xpayments_url}/admin.php?target=payment&amp;txnid={$order.extra.xpc_txnid|escape}">{$lng.lbl_xpc_go_to_payment_page}</a>
  </td>
  </tr>
</table>
  {/strip}
  <br />
{/if}

{if $advinfo ne ""}
<div align="left">{include file="main/visiblebox_link.tpl" mark="advinfo" title=$lng.lbl_payment_gateway_log visible=false}</div>
<div id="boxadvinfo">
<hr />
{$advinfo|escape:"html"|nl2br}
<hr />
</div>
<script type="text/javascript">
//<![CDATA[
$("#boxadvinfo").hide();
//]]>
</script>
{/if}

<br />

{$lng.lbl_tracking_number}:<br />
<input type="text" name="tracking" value="{$order.tracking|escape}" />

{if $usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode)}

{if $order.extra.ip ne ''}
<br />
{$lng.lbl_ip_address}: {$order.extra.ip}{if $order.extra.proxy_ip ne ''} ({$order.extra.proxy_ip}){/if}<br />
{if $active_modules.Stop_List ne ''}
{if $order.blocked eq 'Y'}
<font class="Star">{$lng.lbl_ip_address_blocked}</font><br />
{else}
<input type="button" value="{$lng.lbl_block_ip_address|strip_tags:false|escape}" onclick="javascript: self.location='order.php?mode=block_ip&amp;orderid={$orderid}'" />
{/if}
{/if}
{* $active_modules.Stop_List ne '' *}

{/if}

{if $active_modules.Anti_Fraud ne ''}
<input type="button" value="{$lng.lbl_af_lookup_address|strip_tags:false|escape}" onclick="javascript: window.open('{$catalogs.admin}/anti_fraud.php?mode=popup&amp;ip={$order.extra.ip}&amp;proxy_ip={$order.extra.proxy_ip}?&amp;city={$order.b_city|escape:'url'}&amp;state={$order.b_state|escape:'url'}&amp;country={$order.b_country|escape:'url'}&amp;zipcode={$order.b_zipcode|escape:'url'}','AFLOOKUP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');" />
{/if}{* $active_modules.Anti_Fraud ne '' *}

<br />
{$lng.lbl_order_details}:

<br />

{if not $order.details_encrypted}
<div style="text-align: right; width: 520px; padding-bottom: 3px;">
<a id="view_mode" href="javascript:void(0);" onclick="javascript: switch_details_mode(false, this, document.getElementById('edit_mode'));" style="font-weight: bold;">{$lng.lbl_view_mode}</a>
&nbsp;&nbsp;&nbsp;
<a id="edit_mode" href="javascript:void(0);" onclick="javascript: switch_details_mode(true, this, document.getElementById('view_mode'));">{$lng.lbl_edit_mode}</a>
</div>
{else}
<br />
{/if}

<textarea id="details_view" cols="70" style="color: #666666; background-color:#EEEEEE; width: 520px;" readonly="readonly" rows="12"{if $order.details_encrypted} disabled="disabled"{/if}>{$order.details|order_details_translate|escape}</textarea>

{if $order.details_encrypted eq ''}
<textarea id="details_edit" style="display: none; width: 520px;" name="details" cols="70" rows="12">{$order.details|escape}</textarea>
{/if}
{/if}

<br />
<br />

{$lng.lbl_order_notes}:<br /><br />
<textarea name="notes" cols="70" style="width: 520px;" rows="8">{$order.notes|escape}</textarea>

{if $usertype eq "A" or $usertype eq "P"}
<br />
<script type="text/javascript">
//<![CDATA[
{literal}
function submitOrder(obj) {
  if (window.cnotesframe && cnotesframe.document && cnotesframe.document.cnotesform) {
    try {
      cnotesframe.document.cnotesform.submit();
    } catch(e) {
      return true;
    }

    setTimeout(
      function() {
        obj.form.submit();
      },
      500
    );
    return false;
  }

  return true;
}
{/literal} 
//]]>
</script>
{if $active_modules.Advanced_Order_Management}
<br />
{include file="modules/Advanced_Order_Management/comment_form.tpl"}
<br />
{/if}
<table cellpadding="3" cellspacing="1">
<tr>
  <td class="main-button">
    <input class="big-main-button" type="submit" value="{$lng.lbl_apply_changes|strip_tags:false|escape}" onclick="javascript: return submitOrder(this);" />
  </td>
  <td>
{if $usertype eq "A"}
{$lng.txt_change_order_status}
{else}
{$lng.txt_apply_changes}  
{/if}
  </td>
</tr>
</table>
{/if}

{if $active_modules.Special_Offers ne "" and ($usertype eq "A" or $usertype eq "P")}
<br /><br /><br />
{include file="modules/Special_Offers/order_extra_data.tpl" data=$order.extra}
{/if}

{if ($usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode)) and $active_modules.Anti_Fraud}
<br /><br /><br />
{include file="modules/Anti_Fraud/extra_data.tpl" data=$order.extra.Anti_Fraud}
{/if}

{if ($usertype eq 'A' or ($usertype eq 'P' and $active_modules.Simple_Mode)) and $order.is_egood ne '' and $active_modules.Egoods}
<br />
<input type="button" value="{if $order.is_egood eq 'Y'}{$lng.lbl_prolong_ttl|strip_tags:false|escape}{else}{$lng.lbl_regenerate_ttl|strip_tags:false|escape}{/if}" onclick="javascript: self.location='order.php?mode=prolong_ttl&amp;orderid={$orderid}'" /><br />
{$lng.txt_prolong_ttl}
{/if}

<input type="hidden" name="mode" value="status_change" />
<input type="hidden" name="orderid" value="{$order.orderid}" />
</form>

{if $usertype eq "A" and (not $active_modules.Google_Checkout or not $order.extra.goid) and $order.charge_info and ($order.charge_info.refunded_total gt 0 or $order.charge_info.refund_avail gt 0)}

  <br />
  {include file="main/subheader.tpl" title=$lng.lbl_refund}

  <form action="process_order.php" method="post" name="refundform">
    <input type="hidden" name="mode" value="refund" />
    <input type="hidden" name="orderid" value="{$order.orderid}" />

    <table cellspacing="1" cellpadding="3">
      {if $order.charge_info.refunded_total gt 0}
        <tr>
          <td>{$lng.lbl_refunded_amount}:</td>
          <td>{currency value=$order.charge_info.refunded_total}</td>
        </tr>
      {/if}
      {if $order.charge_info.refund_avail gt 0}
        {if $order.charge_info.refund_mode eq 'P'}
          <tr>
            <td>{$lng.lbl_refund_amount}:</td>
            <td><input type="text" name="refund_amount" value="{$order.charge_info.refund_avail}" /></td>
          </tr>
        {/if}
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" value="{$lng.lbl_refund|escape}" /></td>
        </tr>
      {/if}
    </table>

  </form>

{/if}

{if $usertype eq "P" and $order.status ne "C"}
<br />
<form action="order.php" method="post">
<input type="hidden" name="mode" value="complete_order" />
<input type="submit" value="{$lng.lbl_complete_order|strip_tags:false|escape}" /><br />
<br />
{$lng.txt_complete_order}
<input type="hidden" name="orderid" value="{$order.orderid}" />
</form>
{/if}

{if $active_modules.Order_Tracking ne "" and $order.tracking ne ""}

<br /><br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_tracking_order}

{assign var="postal_service" value=$order.shipping|truncate:3:"":true}
{$lng.lbl_tracking_number}: {$order.tracking}
<br /><br />

{if $postal_service eq "UPS"}
{include file="modules/Order_Tracking/ups.tpl"}
{elseif $postal_service eq "USP"}
{include file="modules/Order_Tracking/usps.tpl"}
{elseif $postal_service eq "Fed"}
{include file="modules/Order_Tracking/fedex.tpl"}
{elseif $postal_service eq "Aus"}
{include file="modules/Order_Tracking/australia_post.tpl"}
{elseif $postal_service eq "DHL"}
{include file="modules/Order_Tracking/dhl.tpl"}
{/if}

{/if}

  </td>
</tr>
</table>

{if $active_modules.RMA ne '' and (($usertype eq 'A' and $current_membership_flag ne 'FS') or ($usertype eq 'P' and $active_modules.Simple_Mode))}

<br />
<a name="returns"></a>
{include file="modules/RMA/add_returns.tpl"}
{/if}

{if ($usertype eq 'A' or ($usertype eq 'P' and $active_modules.Simple_Mode)) and $active_modules.Google_Checkout ne '' and $order.extra.goid ne ''}

<br />
<a name="gcheckout"></a>
{include file="modules/Google_Checkout/gcheckout_order.tpl"}
{/if}

{if $order.paypal and ($usertype eq 'A' or ($usertype eq 'P' and $active_modules.Simple_Mode))}
{include file="main/paypal_trans.tpl" data=$order.paypal}
{/if}

