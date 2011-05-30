{*
$Id: returns.tpl,v 1.2 2010/06/11 08:15:52 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_returns}

<br />

{if $mode eq 'reasons'}
{include file="modules/RMA/reasons.tpl"}

{elseif $mode eq 'actions'}
{include file="modules/RMA/actions.tpl"}

{elseif $mode eq 'modify'}
{include file="modules/RMA/modify_return.tpl"}

{else}

{capture name=dialog}
<form action="returns.php" method="post" name="searchreturnsform">
<input type="hidden" name="mode" value="search" />

<table cellpadding="3" cellspacing="1">
<tr>
  <td>{$lng.lbl_period_from}</td>
  <td>{include file="main/datepicker.tpl" name="start_date" date=$search_prefilled.start_date}</td>
</tr>
<tr>
    <td>{$lng.lbl_period_to}</td>
    <td>{include file="main/datepicker.tpl" name="end_date" date=$search_prefilled.end_date}</td>
</tr>
<tr>
    <td>{$lng.lbl_returnid}</td>
    <td><input type="text" name="search[returnid]" value="{$search_prefilled.returnid}" size="5" /></td>
</tr>
<tr>
  <td>{$lng.lbl_status}</td>
  {assign var=onchange value="onchange=\"javascript: document.getElementById('filter').disabled = (this.options.selectedIndex > 0)\""}
  <td>{include file="modules/RMA/return_status.tpl" status=$search_prefilled.status extra=$onchange mode="select" name="search[status]" extended="Y"}</td>
</tr>
<tr>
  <td>{$lng.lbl_use_filter}</td>
  <td>
    <select name="search[filter]" id="filter"{if $search_prefilled.status ne ""} disabled="disabled"{/if}>
      <option value=""{if $search_prefilled.filter eq ""} selected="selected"{/if}>{$lng.lbl_rma_no_filter}</option>
      <option value="N"{if $search_prefilled.filter eq "N"} selected="selected"{/if}>{$lng.lbl_rma_new_returns}</option>
      <option value="A"{if $search_prefilled.filter eq "A"} selected="selected"{/if}>{$lng.lbl_rma_active_returns}</option>
    </select>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td class="main-button">
    <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
  </td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_search extra='width="100%"'}
<br />
{/if}

{if $mode eq 'search'}
{if $returns_count eq ''}
{assign var="returns_count" value="0"}
{/if}
{$lng.txt_N_results_found|substitute:"items":$returns_count}
<br /><br />
{/if}

{if $returns ne ''}
{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="returns.php"}</div>

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="returnsform" prefix="to_delete"}

<form action="returns.php" method="post" name="returnsform">
<input type="hidden" name="mode" value="" />

{assign var="colspan" value="10"}
<table width="100%" cellpadding="3" cellspacing="1">
<tr class="TableHead">
  <td width="10" rowspan="2">&nbsp;</td>
  <td align="center" rowspan="2">{if $search_prefilled.sort_field eq "returnid"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="returns.php?sort=returnid{if $search_prefilled.sort_field eq "returnid"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_returnid}</a></td>
  <td align="center" rowspan="2">{$lng.lbl_customer}</td>
  <td align="center" rowspan="2">{$lng.lbl_product}</td>
  <td align="center" colspan="2">{$lng.lbl_rma_returned_items}</td>
  <td align="center" rowspan="2">{$lng.lbl_order}</td>
  <td align="center" rowspan="2">{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="returns.php?sort=date{if $search_prefilled.sort_field eq "date"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_date}</a></td>
  <td align="center" rowspan="2">{if $search_prefilled.sort_field eq "status"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="returns.php?sort=status{if $search_prefilled.sort_field eq "status"}&amp;sort_direction={if $search_prefilled.sort_direction eq 1}0{else}1{/if}{/if}">{$lng.lbl_status}</a></td>
  <td align="center" rowspan="2">{$lng.lbl_credit_status}</td>
</tr>
<tr class="TableHead">
  <td align="center">{$lng.lbl_rma_requested}</td>
  <td align="center">{$lng.lbl_rma_confirmed}</td>
</tr>

{foreach from=$returns item=v}
<tr{cycle values=', class="TableSubHead"'}>
  <td align="center"><input type="checkbox" name="to_delete[{$v.returnid}]" value="Y" /></td>
  <td valign="top"><a href="returns.php?mode=modify&amp;returnid={$v.returnid}">RMA#{$v.returnid}</a></td>
  <td valign="top"><a href="user_modify.php?user={$v.userid}&amp;usertype=C">{$v.firstname} {$v.lastname}</a></td>
  <td valign="top">
{if $v.productid gt 0}<a href="product_modify.php?productid={$v.productid}">{/if}{$v.product}{if $v.productid gt 0}</a>{/if}
{if $v.product_options ne "" and $active_modules.Product_Options ne ''}
<div style="padding-left: 20px;">
{include file="modules/Product_Options/display_options.tpl" options_txt=$v.product_options force_product_options_txt=true}
</div>
{/if}
  </td>
  <td align="center">
    <input type="hidden" name="update[{$v.returnid}][amount_orig]" value="{$v.amount}" />
    <select name="update[{$v.returnid}][amount]">
{section name=amnt loop=$v.order_amount+1 start=1}
      <option value="{%amnt.index%}"{if $v.amount eq %amnt.index%} selected="selected"{/if}>{%amnt.index%}</option>
{/section}
    </select>
  </td>
  <td align="center">
    <input type="hidden" name="update[{$v.returnid}][returned_amount_orig]" value="{$v.returned_amount}" />
    <select name="update[{$v.returnid}][returned_amount]"{if $v.status ne "A" and $v.status ne "C"} disabled="disabled"{/if}>
{section name=aamnt loop=$v.order_amount+1 start=0}
      <option value="{%aamnt.index%}"{if $v.returned_amount eq %aamnt.index%} selected="selected"{/if}>{%aamnt.index%}</option>
{/section}
    </select>
  </td>
  <td align="right" valign="top"><a href="order.php?orderid={$v.orderid}">{$v.orderid}</a></td>
  <td valign="top">{$v.date|date_format:$config.Appearance.datetime_format}</td>
  <td valign="top">{include file="modules/RMA/return_status.tpl" status=$v.status name="update[`$v.returnid`][status]" mode="select"}</td>
{if $active_modules.Gift_Certificates ne ''}
  <td valign="top" align="left">
{if $v.credit ne ''}
    <a href="giftcerts.php?mode=modify_gc&amp;gcid={$v.credit}">{$lng.lbl_created}</a>
{elseif $v.status eq 'A' or $v.status eq 'C'}
    {multi assign="gc_amount" x=$v.amount y=$v.price}
    <input type="text" id="gc_amount{$v.returnid}" value="{$gc_amount|formatprice}" size="8" />
    <a href="javascript:self.location='returns.php?mode=credit_create&amp;returnid={$v.returnid}&amp;gc_amount='+document.getElementById('gc_amount{$v.returnid}').value;">{$lng.lbl_create}</a>
{else}
{$lng.lbl_creation_of_credit_forbidden}
{/if}
  </td>
{/if} 
{if $inv_err ne ""}
  <td>{if $inv_err[$v.returnid]}<font class="Star">&lt;&lt;</font>{else}&nbsp;{/if}</td>
{/if}
</tr>
{/foreach}

{if $returns ne ''}
<tr>
  <td colspan="{$colspan}">

<div class="main-button" style="float:left">
  <input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'update');" />
</div>

<div style="float:right">
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('to_delete', 'gi'))) submitForm(this, 'delete');" />
</div>

<br /><br /><br />

{$lng.txt_operation_for_first_selected_only}

<br /><br />

<input type="button" value="{$lng.lbl_modify|strip_tags:false|escape}" onclick="document.returnsform.mode.value='modify'; document.returnsform.submit();" />
&nbsp;&nbsp;&nbsp;&nbsp;
  </td>
</tr>
{/if}
</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_returns extra='width="100%"'}
{/if}
