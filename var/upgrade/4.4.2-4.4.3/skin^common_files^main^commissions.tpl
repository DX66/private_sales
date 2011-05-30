{*
$Id: commissions.tpl,v 1.4.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_provider_commissions}
{$lng.txt_provider_commissions_note}

<br />

{capture name=dialog}

  <form method="post" action="commissions.php" name="searchform">
    <input type="hidden" name="mode" value="" />

    <table cellpadding="3" cellspacing="1">
      <tr>
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_date_from}:</td>
        <td height="10" width="10">&nbsp;</td>
        <td nowrap="nowrap">
          {include file="main/datepicker.tpl" name="start_date" date=$search.start_date|default:$month_begin}
        </td>
      </tr>
      <tr>
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_date_through}:</td>
        <td height="10" width="10">&nbsp;</td>
        <td nowrap="nowrap">
          {include file="main/datepicker.tpl" name="end_date" date=$search.end_date}
        </td>
      </tr>
      <tr>
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_order_id}:</td>
        <td height="10" width="10">&nbsp;</td>
        <td nowrap="nowrap"><input type="text" size="8" name="search[orderid]" value="{$search.orderid}" /></td>
      </tr>
      <tr>
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_provider}:</td>
        <td height="10" width="10">&nbsp;</td>
        <td nowrap="nowrap">
          <select name="search[provider]">
            <option value="">{$lng.lbl_all}</option>
            {foreach from=$providers item=v}
              <option value="{$v.id}"{if $search.provider eq $v.id} selected="selected"{/if}>{$v.firstname} {$v.lastname} ({$v.login})</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_order_status}</td>
        <td height="10" width="10">&nbsp;</td>
        <td nowrap="nowrap">

          <select name="search[status]">
            <option value=""{if $search.status eq ""} selected="selected"{/if}>{$lng.lbl_all}</option>
            <option value="I"{if $search.status eq "I"} selected="selected"{/if}>{$lng.lbl_not_finished}</option>
            <option value="Q"{if $search.status eq "Q"} selected="selected"{/if}>{$lng.lbl_queued}</option>
            <option value="P"{if $search.status eq "P"} selected="selected"{/if}>{$lng.lbl_processed}</option>
            <option value="B"{if $search.status eq "B"} selected="selected"{/if}>{$lng.lbl_backordered}</option>
            <option value="D"{if $search.status eq "D"} selected="selected"{/if}>{$lng.lbl_declined}</option>
            <option value="F"{if $search.status eq "F"} selected="selected"{/if}>{$lng.lbl_failed}</option>
            <option value="C"{if $search.status eq "C"} selected="selected"{/if}>{$lng.lbl_complete}</option>
          </select>

        </td>
      </tr>
      <tr> 
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_payment_status}</td>
        <td height="10" width="10">&nbsp;</td>
        <td nowrap="nowrap">

          <select name="search[paid]">
            <option value=''{if $search.paid eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
            <option value='N'{if $search.paid eq 'N'} selected="selected"{/if}>{$lng.lbl_pending}</option>
            <option value='A'{if $search.paid eq 'A'} selected="selected"{/if}>{$lng.lbl_approved}</option>
            <option value='Y'{if $search.paid eq 'Y'} selected="selected"{/if}>{$lng.lbl_paid}</option>
          </select>

        </td>
      </tr>

      <tr>
        <td colspan="3" class="main-button SubmitBox">
          <input type="button" value="{$lng.lbl_search|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'go');" />
        </td>
      </tr>

    </table>
  </form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_search extra='width="100%"'}

<br />

{if $mode eq 'search'}

  {if $orders_cnt ne 0}
  
    {$lng.txt_N_results_found|substitute:"items":$orders_cnt}

    {capture name=dialog}

<script type="text/javascript">
//<![CDATA[
var ready = [
{foreach from=$ready item=v name=ready}
{ldelim}
  userid: {$v.userid|default:0},
  min_paid: {$v.min_paid|default:0},
  total: 0
{rdelim}{if not $smarty.foreach.ready.last},{/if}
{/foreach}
];
//]]>
</script>
      <script type="text/javascript" src="{$SkinDir}/js/partner_orders.js"></script>

      <form action="commissions.php" method="post" name="postordersform">
        <input type="hidden" name="mode" value="update" />

      <table cellpadding="3" cellspacing="1" width="100%">

        <tr class="TableHead">
          <td nowrap="nowrap" rowspan="2">{$lng.lbl_partner}</td>
          <td nowrap="nowrap" colspan="2" align="center">{$lng.lbl_order}</td>
          <td nowrap="nowrap" rowspan="2" align="center">{$lng.lbl_total}</td>
          <td nowrap="nowrap" rowspan="2" align="center">{$lng.lbl_commission}</td>
          <td nowrap="nowrap" colspan="2" align="center">{$lng.lbl_status}</td>
          <td nowrap="nowrap" rowspan="2" align="center">{$lng.lbl_note}</td>
        </tr>
        <tr class="TableHead">
          <td nowrap="nowrap" align="center">#</td>
          <td nowrap="nowrap" align="center">{$lng.lbl_date}</td>
          <td nowrap="nowrap" align="center">{$lng.lbl_order}</td>
          <td nowrap="nowrap" align="center">{$lng.lbl_commission}</td>
        </tr>

        {foreach from=$orders item=v}

          <tr{cycle values=', class="TableSubHead"'}>
            <td nowrap="nowrap"><a href="user_modify.php?user={$v.userid}&amp;usertype=P">{$v.firstname} {$v.lastname}</a><br /><span class="SmallText">({$v.login})</td>
            <td><a href="order.php?orderid={$v.orderid}">{$v.orderid}</a></td>
            <td nowrap="nowrap">{$v.date|date_format:$config.Appearance.date_format}</td>
            <td align="right" nowrap="nowrap">{currency value=$v.subtotal}</td>
            <td align="right" nowrap="nowrap">{currency value=$v.commissions}</td>
            <td>{include file="main/order_status.tpl" status=$v.order_status mode="static" name="status"}</td>
            <td class="xaff-comission-cell">
              {if $v.paid eq 'Y'}

                {include file="main/tooltip_js.tpl" title=$lng.lbl_paid text=$lng.txt_provider_payment_paid_note id="note_`$v.orderid`"}

              {elseif $v.paid eq 'A' and $v.ready}
                {include file="main/tooltip_js.tpl" title=$lng.lbl_ready_to_pay text=$lng.txt_provider_payment_ready_note id="note_`$v.orderid`" idfor="paid_`$v.userid`_`$v.orderid`" type="label"}
                <input type="checkbox" id="paid_{$v.userid}_{$v.orderid}" name="paid[{$v.orderid}]" value="{$v.commissions}" onclick="javascript: return markPartnerPayment(this);" />

              {elseif $v.paid eq 'A'}

                {include file="main/tooltip_js.tpl" title=$lng.lbl_approved text=$lng.txt_provider_payment_approved_note id="note_`$v.orderid`"}

              {else}

                {include file="main/tooltip_js.tpl" title=$lng.lbl_pending text=$lng.txt_provider_payment_pending_note id="note_`$v.orderid`"}

              {/if}
            </td>
            <td>
              {if $usertype eq "P"}
                {$v.note}
              {else}
                <textarea name="posted_data[{$v.orderid}][note]" cols="20" rows="2">{$v.note}</textarea>
              {/if}
            </td>
          </tr>

        {/foreach}

      </table>

      <div id="pending_note" class="NeedHelpBox" style="display: none;">{$lng.txt_provider_payment_pending_note}</div>
      <div id="approved_note" class="NeedHelpBox" style="display: none;">{$lng.txt_provider_payment_approved_note}</div>
      <div id="ready_note" class="NeedHelpBox" style="display: none;">{$lng.txt_provider_payment_ready_note}</div>
      <div id="paid_note" class="NeedHelpBox" style="display: none;">{$lng.txt_provider_payment_paid_note}</div>

      {if $ready}

        <br />
        <br />
        <div id="partners" style="display: none;">
          <strong>{$lng.txt_provider_payments_summary}:</strong><br />
          <br />

          <ul class="xaff-paid-partners">
            {foreach from=$ready item=v name=ready}
              <li id="user_{$v.login|escape}" class="zero">
                {$v.firstname} {$v.lastname}:
                <span class="total" id="row_{$v.userid|escape}">{0|formatprice}</span>
                {if $v.min_paid gt 0}
                  <span class="minpaid">({$lng.txt_min_paid_comment}: {$v.min_paid|formatprice})</span>
                {/if}
              </li>
            {/foreach}
          </ul>

          <input type="submit" value="{$lng.lbl_update}" id="update_button" disabled="disabled" />

        </div>

      {/if}

      </form>

    {/capture}
    {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_search_results extra='width="100%"'} 
  {/if}
{/if}
