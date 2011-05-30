{*
$Id: paypal_trans.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $data and $data.main}
<br />
<a name="paypal"></a>
{capture name=dialog}

{if $data.method eq 'US'}
{$lng.txt_paypal_section_note_us}
{else}
{$lng.txt_paypal_section_note_uk}
{/if}
<br />

<br />
{include file="main/subheader.tpl" title=$lng.lbl_paypal_orig_transaction class="grey"}

{if not $data.main.update_date and $data.method eq 'US'}
<b>{$lng.lbl_note}:</b> {$lng.txt_paypal_main_trans_no_updated}<br />
<br />
{/if}

<table cellspacing="1" cellpadding="2">
<tr>
  <td>{$lng.lbl_paypal_pmethod}:</td>
  <td>{if $data.method eq 'US'}PayPal Website Payments Pro{else}PayPal Website Payments Pro PayFlow Edition{/if}</td>
</tr>
<tr>
  <td>{$lng.lbl_paypal_used_api}:</td>
  <td>{if $data.api eq 'EC'}Express Checkout{elseif $data.api eq 'STD'}Standard{else}Direct Payment{/if}</td>
</tr>
<tr>
  <td>{$lng.lbl_paypal_txnid}:</td>
  <td>{$data.main.txnid}</td>
</tr>
{if $data.method eq 'US'}
<tr>
  <td>{$lng.lbl_paypal_txn_type}:</td>
  <td>{$data.main.txn_type|default:$lng.txt_not_available}</td>
</tr>
<tr>
    <td>{$lng.lbl_paypal_payer_id}:</td>
    <td>{$data.main.payer_id|default:$lng.txt_not_available}</td>
</tr>
<tr>
    <td>{$lng.lbl_paypal_payer_status}:</td>
    <td>{$data.main.payer_status|default:$lng.txt_not_available}</td>
</tr>
<tr>
    <td>{$lng.lbl_paypal_payer_email}:</td>
    <td>{$data.main.payer_email|default:$lng.txt_not_available}</td>
</tr>
<tr>
    <td>{$lng.lbl_paypal_receiver_id}:</td>
    <td>{$data.main.receiver_id|default:$lng.txt_not_available}</td>
</tr>

<tr>
    <td>{$lng.txt_paypal_payment_status}:</td>
    <td>{$data.main.payment_status|default:$lng.txt_not_available}</td>
</tr>

{if $data.main.payment_status eq 'Pending' and $data.main.pendingreason}

  <tr>
      <td>{$lng.txt_paypal_pending_reason}:</td>
      <td>{$data.main.pendingreason_text|default:$data.main.pendingreason}</td>
  </tr>

{elseif $data.main.payment_status eq 'Reversed' and $data.main.reasoncode}

  <tr>
      <td>{$lng.txt_paypal_reversal_reason}:</td>
      <td>{$data.main.reasoncode_text|default:$data.main.reasoncode}</td>
  </tr>

{/if}

<tr>
    <td>{$lng.lbl_amount}:</td>
    <td>{if $data.main.amount}{$data.main.amount|formatprice} {$data.main.currencycode|default:"USD"}{else}{$lng.txt_not_available}{/if}</td>
</tr>
<tr>
  <td>{$lng.lbl_paypal_update_date}:</td>
  <td>{if $data.main.update_date}{$data.main.update_date|date_format:$config.Appearance.datetime_format}{else}{$lng.txt_not_available}{/if}</td>
</tr>

{* TODO (wait bugfix in PayPal - Aug 2009)

<tr>
  <td>{$lng.txt_paypal_fmf_list}:</td>
  <td>
    {if $data.filters}
      <ul>
        {foreach from=$data.filters item=f}
          <li></li>
        {/foreach}
      </ul>
    {else}
      {$lng.lbl_paypal_fmf_list_is_empty}
    {/if}
  </td>
</tr>
*}

<tr>
  <td>&nbsp;</td>
  <td><input type="button" value="{$lng.lbl_update|escape}" onclick="javascript: self.location = 'order.php?orderid={$orderid}&amp;mode=update_paypal';" /></td>
</tr>
{/if}
</table>

{if $data.fmf}
<br />
{$lng.txt_paypal_fmf_note}<br/>
<input type="button" value="{$lng.lbl_accept|escape}" onclick="javascript: self.location = 'order.php?orderid={$orderid}&amp;mode=paypal_accept_pending';" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_decline|escape}" onclick="javascript: self.location = 'order.php?orderid={$orderid}&amp;mode=paypal_decline_pending';" />
<br />
{/if}

<br />
{include file="main/subheader.tpl" title=$lng.lbl_paypal_refund_transactions class="grey"}
<table cellspacing="1" cellpadding="2" width="100%">
<tr class="TableHead" style="white-space: nowrap;">
  <th>{$lng.lbl_paypal_txnid}</th>
  <th>{$lng.lbl_amount}</th>
  <th>{$lng.lbl_date}</th>
  <th width="100%">{$lng.lbl_note}</th>
</tr>
{foreach from=$data.refunds item=r key=rid}
<tr{cycle values=', class="TableSubHead"' name="paypal_refunds"}>
  <td>{$rid}</td>
  <td align="right" nowrap="nowrap">{$r.amount|formatprice} {$r.currencycode}</td>
  <td align="center" nowrap="nowrap">{$r.date|date_format:$config.Appearance.datetime_format}</td>
  <td>{$r.note}</td>
</tr>
{foreachelse}
<tr>
  <td colspan="4" align="center">{$lng.lbl_paypal_refunds_list_is_empty}</td>
</tr>
{/foreach}
</table>

{if $data.no_refund_total gt 0}

<br />
{include file="main/subheader.tpl" title=$lng.lbl_paypal_create_refund class="grey"}
{$lng.txt_paypal_section_note_refund}<br />
<form action="order.php" method="post" name="paypalrefundform">
<input type="hidden" name="orderid" value="{$orderid}" />
<input type="hidden" name="mode" value="create_refund" />

<table cellspacing="1" cellpadding="2">

{if $data.method eq 'US'}
<tr>
  <td>{$lng.lbl_amount}:</td>
  <td><input type="text" name="amount" value="{$data.no_refund_total}" /></td>
</tr>
{/if}

<tr>
  <td>{$lng.lbl_note}:</td>
  <td><textarea name="note" cols="30" rows="4"></textarea></td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" value="{$lng.lbl_create|escape}" /></td>
</tr>

</table>

</form>
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_paypal_transactions content=$smarty.capture.dialog extra='width="100%"'}

{/if}
