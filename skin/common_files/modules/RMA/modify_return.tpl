{*
$Id: modify_return.tpl,v 1.1.2.1 2011/01/20 08:03:46 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{include file="page_title.tpl" title=$lng.lbl_return_details}
<br />

{capture name=dialog}
<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_returns_list href="returns.php"}</div>
<form action="returns.php" method="post">
<input type="hidden" name="returnid" value="{$returnid}" />
<input type="hidden" name="mode" value="modify" />
<table cellpadding="3" cellspacing="1">
{if $usertype ne 'C'}
<tr>
  <td>{$lng.lbl_customer}</td>
  <td><a href="{$catalogs.admin}/user_modify.php?user={$return.order.userid}&amp;usertype=C">{$return.order.firstname} {$return.order.lastname}</a></td>
</tr>
{/if}
<tr>
  <td valign="top">{$lng.lbl_product}</td>
  <td>
{if $return.product.productid gt 0}<a href="product{if $usertype ne 'C'}_modify{/if}.php?productid={$return.product.productid}">{/if}
{$return.product.product} ({$return.amount} {$lng.lbl_items})
{if $return.product.productid gt 0}</a>{/if}
{if $return.product.product_options ne "" and $active_modules.Product_Options ne ''}
<div style="padding-left: 20px;">
{include file="modules/Product_Options/display_options.tpl" options=$return.product.product_options}
</div>
{/if}
  </td>
</tr>
{if $usertype ne 'C'}
<tr>
  <td>{$lng.lbl_order}</td>
  <td><a href="order.php?orderid={$return.order.orderid}">{$return.order.orderid}</a></td>
</tr>
<tr>
  <td>{$lng.lbl_date}</td>
  <td>{$return.date|date_format:$config.Appearance.datetime_format}</td>
</tr>
{/if}
{if $reasons ne ''}
<tr>
  <td>{$lng.lbl_reason_for_returning}</td>
  <td><select name="posted_data[reason]"{if $return.readonly eq "Y"} disabled="disabled"{/if}>
  {foreach from=$reasons item=v key=k}
  <option value='{$k}'{if $k eq $return.reason} selected="selected"{/if}>{$v}</option>
  {/foreach}
  </select>
  {if $return.readonly eq "Y"}
  <input type="hidden" name="posted_data[reason]" value="{$return.reason}" />
  {/if}
  </td>
</tr>
{/if}
{if $actions ne ''}
<tr> 
  <td>{$lng.lbl_what_you_would_like_us_to_do}</td>
  <td>
    <select name="posted_data[action]"{if $return.readonly eq "Y"} disabled="disabled"{/if}>
{foreach from=$actions item=v key=k} 
      <option value='{$k}'{if $k eq $return.action} selected="selected"{/if}>{$v}</option>
{/foreach}
    </select>
{if $return.readonly eq "Y"}
    <input type="hidden" name="posted_data[action]" value="{$return.action}" />
{/if}
  </td>
</tr>
{/if}
<tr>
  <td>{$lng.lbl_comment}</td>
  <td>
    <textarea{if $return.readonly eq "Y"} disabled="disabled"{/if} rows="3" cols="60" name="posted_data[comment]">{$return.comment|escape}</textarea>
{if $return.readonly eq "Y"}
    <input type="hidden" name="posted_data[comment]" value="{$return.comment}" />
{/if}
  </td>
</tr>
{if $usertype ne 'C'}
<tr>  
  <td>{$lng.lbl_status}</td>
  <td>{include file="modules/RMA/return_status.tpl" status=$return.status name="posted_data[status]" mode="select"}</td> 
</tr>
<tr>
  <td colspan="2" height="30" valign="bottom"><strong>{$lng.lbl_rma_returned_items_number}:</strong></td>
</tr>
<tr>
  <td>{$lng.lbl_rma_requested}</td>
  <td>
    <input type="hidden" name="posted_data[amount_orig]" value="{$return.product.amount}" />
    <select name="posted_data[amount]">
{section name=amnt loop=$return.product.amount+1 start=1}
      <option value="{%amnt.index%}"{if $return.amount eq %amnt.index%} selected="selected"{/if}>{%amnt.index%}</option>
{/section}
    </select>
  </td>
</tr>
<tr>
  <td>{$lng.lbl_rma_confirmed}</td>
  <td>
    <input type="hidden" name="posted_data[returned_amount_orig]" value="{$return.returned_amount}" />
    <select name="posted_data[returned_amount]"{if $return.status ne "A" and $return.status ne "C"} disabled="disabled"{/if}>
{section name=aamnt loop=$return.product.amount+1 start=0}
      <option value="{%aamnt.index%}"{if $return.returned_amount eq %aamnt.index%} selected="selected"{/if}>{%aamnt.index%}</option>
{/section}
    </select>
  </td>
</tr>
{/if}
<tr>
  <td>&nbsp;</td>
  <td><input type="submit"{if $return.readonly eq "Y"} disabled="disabled"{/if} value="{$lng.lbl_modify|strip_tags:false|escape}" />{if $return.status eq 'A' or $return.status eq 'C'}&nbsp;&nbsp;&nbsp;<input type="button" value="{$lng.lbl_print_return_slip|strip_tags:false|escape}" onclick="javascript: window.open('returns.php?mode=print&amp;returnid={$return.returnid}','PRINT_RETURN_SLIP','width=350,height=300,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=yes,location=no,direction=no')" />{/if}</td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_return_n|substitute:"id":$returnid extra='width="100%"'}
