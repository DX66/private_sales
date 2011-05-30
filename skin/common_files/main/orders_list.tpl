{*
$Id: orders_list.tpl,v 1.8.2.4 2011/02/02 15:56:47 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var="total" value=0.00}
{assign var="total_paid" value=0.00}

<script type="text/javascript">
//<![CDATA[
var txt_delete_selected_orders_warning = "{$lng.txt_delete_selected_orders_warning|wm_remove|escape:javascript|strip_tags}";
//]]>
</script>

{if $orders ne ""}

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="orders.php"}</div>

{include file="main/navigation.tpl"}

{include file="main/check_all_row.tpl" form="processorderform" prefix="orderids"}

<form action="process_order.php" method="post" name="processorderform">
<input type="hidden" name="mode" value="" />

<table cellpadding="2" cellspacing="1" width="100%">

{assign var="colspan" value=6}

<tr class="TableHead">
  <td width="5">&nbsp;</td>
  <td width="5%" nowrap="nowrap">{if $search_prefilled.sort_field eq "orderid"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=orderid{if $search_prefilled.sort_field eq "orderid"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">#</a></td>
  <td nowrap="nowrap">{if $search_prefilled.sort_field eq "status"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=status{if $search_prefilled.sort_field eq "status"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_status}</a></td>
  <td width="30%" nowrap="nowrap">{if $search_prefilled.sort_field eq "customer"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=customer{if $search_prefilled.sort_field eq "customer"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_customer}</a></td>
{if $usertype eq "A" and $single_mode eq ""}
{assign var="colspan" value=7}
  <td width="20%" nowrap="nowrap">{if $search_prefilled.sort_field eq "provider"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=provider{if $search_prefilled.sort_field eq "provider"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_provider}</a></td>
{/if}
  <td width="20%" nowrap="nowrap">{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=date{if $search_prefilled.sort_field eq "date"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_date}</a></td>
  <td width="20%" align="right" nowrap="nowrap">{if $search_prefilled.sort_field eq "total"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=total{if $search_prefilled.sort_field eq "total"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_total}</a></td>
</tr>

{section name=oid loop=$orders}

{inc value=$total inc=$orders[oid].total assign="total"}
{if $orders[oid].status eq "P" or $orders[oid].status eq "C"}
  {inc value=$total_paid inc=$orders[oid].total assign="total_paid"}
{/if}

<tr{cycle values=", class='TableSubHead'"}>
  <td width="5"><input type="checkbox" name="orderids[{$orders[oid].orderid}]" /></td>
  <td><a href="order.php?orderid={$orders[oid].orderid}">#{$orders[oid].orderid}</a></td>
  <td nowrap="nowrap">
{if $is_admin_user}
<input type="hidden" name="order_status_old[{$orders[oid].orderid}]" value="{$orders[oid].status}" />
{if $orders[oid].goid ne ""}
{assign var="is_gcheckout_orders" value="1"}
{include file="main/order_status.tpl" status=$orders[oid].status mode="select" name="order_status[`$orders[oid].orderid`]" extra="disabled='disabled' class='ui-state-disabled'"}
{elseif $orders[oid].status_blocked}
{include file="main/order_status.tpl" status=$orders[oid].status mode="select" name="order_status[`$orders[oid].orderid`]" extra="disabled='disabled' class='ui-state-disabled'"}
{else}
{include file="main/order_status.tpl" status=$orders[oid].status mode="select" name="order_status[`$orders[oid].orderid`]"}
{/if}
{else}
<a href="order.php?orderid={$orders[oid].orderid}"><b>{include file="main/order_status.tpl" status=$orders[oid].status mode="static"}</b></a>
{/if}
{if $active_modules.Stop_List ne '' and $orders[oid].blocked  eq 'Y'}
<img src="{$ImagesDir}/no_ip.gif" style="vertical-align: middle;" alt="{$lng.lbl_blocked}:{$orders[oid].ip}" title="{$lng.lbl_ip_blocked|substitute:"ip":$orders[oid].ip}" />
{/if}
  </td>
  <td>
    {if $is_admin_user and $current_membership_flag ne 'FS' and $orders[oid].existing_userid eq $orders[oid].userid}
      <a href="{$catalogs.admin}/user_modify.php?user={$orders[oid].userid|escape:"url"}&amp;usertype=C" title="{$lng.lbl_modify_profile|escape}" target="_blank">{$orders[oid].firstname|default:$orders[oid].b_firstname} {$orders[oid].lastname|default:$orders[oid].b_lastname}</a>
    {else}
      {$orders[oid].firstname} {$orders[oid].lastname} 
      {if $orders[oid].existing_userid ne $orders[oid].userid and $orders[oid].userid gt 0}
        <font class="Star"> {$lng.lbl_deleted}</font>
      {elseif $orders[oid].userid eq 0}
        <span class="SmallText"> ({$lng.lbl_anonymous})</span>
      {/if}
    {/if}
    {include file="gmap.tpl" address=$orders[oid].gmap.address description=$orders[oid].gmap.description}
  </td>
{if $usertype eq "A" and $single_mode eq ""}
  <td nowrap="nowrap">{if $orders[oid].provider_login ne ''}{$orders[oid].provider_login}{elseif $orders[oid].provider gt 0}<font class="Star"> {$lng.lbl_deleted} ({$lng.lbl_userid}: {$orders[oid].provider})</font>{else}{$lng.txt_not_available}{/if}</td>
{/if}
  <td nowrap="nowrap"><a href="order.php?orderid={$orders[oid].orderid}">{$orders[oid].date|date_format:$config.Appearance.datetime_format}</a></td>
  <td nowrap="nowrap" align="right">
  <a href="order.php?orderid={$orders[oid].orderid}">{currency value=$orders[oid].total}</a>
  </td>
</tr>

{/section}

<tr>
  <td colspan="{$colspan}"><img src="{$ImagesDir}/spacer.gif" width="100%" height="1" alt="" /></td>
</tr>

<tr>
  <td colspan="{$colspan}" align="right">{$lng.lbl_gross_total}: <b>{currency value=$total}</b></td>
</tr>

<tr>
  <td colspan="{$colspan}" align="right">{$lng.lbl_total_paid}: <b>{currency value=$total_paid}</b></td>
</tr>

<tr>
  <td colspan="{$colspan}"><br />

{include file="main/navigation.tpl"}

{if $is_admin_user and $is_gcheckout_orders eq "1"}
{$lng.txt_gcheckout_order_list_status_note}
<br /><br />
{/if}

<div{if $is_admin_user} id="sticky_content"{/if}>

{if $is_admin_user}
<div class="main-button">
  <input type="button" class="big-main-button" value="{$lng.lbl_update_status|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'update');" />
</div>
<br />
{/if}

<input type="button" value="{$lng.lbl_invoices_for_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) {ldelim} document.processorderform.target='invoices'; submitForm(this, 'invoice'); document.processorderform.target=''; {rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
{if $usertype ne "C"}
<input type="button" value="{$lng.lbl_labels_for_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) {ldelim} document.processorderform.target='labels'; submitForm(this, 'label'); document.processorderform.target=''; {rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
{/if}
{if $is_admin_user}
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) if (confirm(txt_delete_selected_orders_warning)) submitForm(this, 'delete');" />
&nbsp;&nbsp;&nbsp;&nbsp;
{/if}

{if $active_modules.Shipping_Label_Generator ne '' and ($usertype eq 'A' or $usertype eq 'P')}
<br />
<br />
<br />
{$lng.txt_shipping_labels_note}
<br />
<br />
<input type="button" value="{$lng.lbl_get_shipping_labels|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) {ldelim} document.processorderform.action='generator.php'; submitForm(this, ''); {rdelim}" />
{/if}

</div>

{if $usertype ne "C"}
<br />
<br />
<br />
{include file="main/subheader.tpl" title=$lng.lbl_export_orders}
{$lng.txt_export_all_found_orders_text}
<br /><br />
{$lng.lbl_export_file_format}:<br />
<select id="export_fmt" name="export_fmt">
  <option value="std">{$lng.lbl_standart}</option>
  <option value="csv_tab">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_tab_delimiter}</option>
  <option value="csv_semi">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_semicolon_delimiter}</option>
  <option value="csv_comma">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_comma_delimiter}</option>
{if $active_modules.QuickBooks eq "Y"}
{include file="modules/QuickBooks/orders.tpl"}
{/if}
</select>
<br />
<br />
<input type="button" value="{$lng.lbl_export_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) submitForm(this, 'export');" />&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_export_all_found|strip_tags:false|escape}" onclick="javascript: self.location='orders.php?mode=search&amp;export=export_found&amp;export_fmt='+document.getElementById('export_fmt').value;" />
{/if}
</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}
{/if}
