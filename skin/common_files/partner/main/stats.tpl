{*
$Id: stats.tpl,v 1.1.2.1 2010/12/15 09:44:41 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_summary_statistics}
{$lng.txt_summary_stats_note}<br />
<br />

<br />

{capture name=dialog}

  <table cellpadding="1" cellspacing="3">
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_total_sales}</strong></td>
      <td>{$stats_info.total_sales}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_total_unapproved_sales}</strong></td>
      <td>{$stats_info.unapproved_sales}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_total_approved_sales}</strong></td>
      <td>{$stats_info.approved_sales}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_total_sales}</strong> ({$lng.lbl_my_commission_small})</td>
      <td>{$stats_info.my_total_sales}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_total_unapproved_sales}</strong> ({$lng.lbl_my_commission_small})</td>
      <td>{$stats_info.my_unapproved_sales}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_total_approved_sales}</strong> ({$lng.lbl_my_commission_small})</td>
      <td>{$stats_info.my_approved_sales}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_total_paid_sales}</strong> ({$lng.lbl_my_commission_small})</td>
      <td>{$stats_info.my_paid_sales}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_pending_sale_commissions}</strong></td>
      <td>{currency value=$stats_info.pending_commissions}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_approved_sale_commissions}</strong></td>
      <td>{currency value=$stats_info.approved_commissions}</td>
    </tr>
    <tr>
      <td nowrap="nowrap"><strong>{$lng.lbl_paid_sales_commissions}</strong></td>
      <td>{currency value=$stats_info.paid_commissions}</td>
    </tr>
  </table>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_summary_statistics extra='width="100%"'}
