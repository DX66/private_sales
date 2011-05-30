{*
$Id: payment_history.tpl,v 1.1.2.1 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_payment_history}
{$lng.txt_payment_history_note}<br />
<br />
 

 
<br />
 
{include file="main/navigation.tpl"}

{capture name=dialog}

  <form method="get" action="payment_history.php" name="searchform">
    <input type="hidden" name="mode" value="go" />

    <table>

      <tr>
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_date_from}:</td>
        <td height="10" width="10">&nbsp;</td>
        <td>{include file="main/datepicker.tpl" name="start_date" date=$start_date|default:$month_begin}</td>
      </tr>

      <tr>
        <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_date_through}:</td>
        <td height="10" width="10">&nbsp;</td>
        <td>{include file="main/datepicker.tpl" name="end_date" date=$end_date}</td>
      </tr>

      <tr>
        <td colspan="2">&nbsp;</td>
        <td class="SubmitBox">
          <input type="submit" value="{$lng.lbl_search}" />
          <input type="button" value="{$lng.lbl_list_all}" onclick="javascript: self.location='payment_history.php?mode=go';" />
        </td>
      </tr>

    </table>

  </form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_search extra='width="100%"'}

{if $smarty.get.mode ne ""}

  <br />

  {capture name=dialog}

    {if $payments eq ""}

      {$lng.lbl_no_records_found}<br />

    {else}

      <table cellpadding="3" cellspacing="1">

        <tr class="TableHead">
          <td>{$lng.lbl_order_id}</td>
          <td>{$lng.lbl_date}</td>
          <td>{$lng.lbl_amount}</td>
        </tr>

        {foreach from=$payments item=p}
          <tr>
            <td>{$p.orderid}</td>
            <td>{$p.add_date|date_format:$config.Appearance.datetime_format}</td>
            <td>{currency value=$p.commissions}</td>
          </tr>
        {/foreach}

        <tr>
          <td colspan="3"><hr style="margin: 0px;" size="1" /></td>
        </tr>

        <tr>
          <td colspan="2"><strong>{$lng.lbl_paid_total}</strong></td>
          <td><strong>{currency value=$paid_total}</strong></td>
        </tr>

      </table>
    {/if}

  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_payment_history extra='width="100%"'}

{/if}
