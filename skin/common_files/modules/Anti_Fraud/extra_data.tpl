{*
$Id: extra_data.tpl,v 1.1 2010/05/21 08:32:19 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="main/subheader.tpl" title=$lng.lbl_anti_fraud_module_data}

{if $data ne ''}
{if $data.result eq 'sk_invalid'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.msg_adm_err_anti_fraud_sk_invalid}<br />
{$lng.txt_anti_fraud_purchase_sk}<br />
{elseif $data.result eq 'sk_expire'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.msg_adm_err_anti_fraud_sk_expire}<br />
{$lng.txt_anti_fraud_purchase_sk}<br />
{elseif $data.result eq 'bad_request'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.msg_adm_err_anti_fraud_bad_request}<br />
{elseif $data.result eq 'not_avail'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.msg_adm_err_anti_fraud_not_avail}<br />
{elseif $data.result eq 'no_user_ip'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.msg_adm_err_anti_fraud_no_user_ip}<br />
{elseif $data.result eq 'no_https'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$lng.msg_adm_err_anti_fraud_no_https}<br />
{elseif $data.result eq 'msg'}
{$data.result_msg_label}<br />
{elseif $data.result eq 'warning'}
<font class="ErrorMessage">{$lng.lbl_warning}!</font> {$data.result_msg_label}<br />
{else}
<table>
<tr>
  <td>{$lng.lbl_anti_fraud_coef}:</td> 
  <td>{if $data eq ''}{$lng.txt_not_available}{else}{if $data.total_trust_score gt $config.Anti_Fraud.anti_fraud_limit}<font color="#dd0000">{/if}{$data.total_trust_score}{if $data.total_trust_score gt $config.Anti_Fraud.anti_fraud_limit}</font>{/if}{/if}</td>
</tr>
<tr>
    <td>{$lng.lbl_anti_fraud_request_total}:</td>
    <td>{$data.available_request|default:$lng.txt_not_available}</td>
</tr> 
<tr>
    <td>{$lng.lbl_anti_fraud_request_used}:</td>
    <td>{$data.used_request|default:$lng.txt_not_available}</td>
</tr>
</table>
{if $data.data ne ''}
{$lng.lbl_anti_fraud_additional_fields}:<br />
<table>
{foreach from=$data.data item=v key=k}
<tr>
    <td>&nbsp;</td>
    <td>{$k}:</td>
    <td>{$v}</td>
</tr>
{/foreach}
    </td>
</tr>
</table>
{/if}

{/if}

{if $config.Anti_Fraud.anti_fraud_license ne ""}
<br />
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_check_order_in_anti_fraud href="order.php?orderid=`$order.orderid`&mode=anti_fraud"}</div>
<br />
{/if}
{if $config.Anti_Fraud.anti_fraud_license ne ''}
<hr class="Line" size="1" />
<br />
{$lng.lbl_send_customer_ip_address}
<br /><br />
<textarea id="af_reason" cols="60" rows="5"></textarea>
<br /><br />
<input type="button" value=" {$lng.lbl_send|strip_tags:false|escape} " onclick="javascript: self.location='order.php?orderid={$orderid}&amp;mode=send_ip&amp;reason='+document.getElementById('af_reason').value;" />
{/if}
{else}
{$lng.txt_warning_antifraud_order_checking}<br />
{if $config.Anti_Fraud.anti_fraud_license ne ""}
<br />
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_check_order_in_anti_fraud href="order.php?orderid=`$order.orderid`&mode=anti_fraud"}</div>
{/if}
{/if}
