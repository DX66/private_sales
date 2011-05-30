{*
$Id: modify_return.tpl,v 1.2 2010/06/18 10:05:05 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_modify}</h1>

{capture name=dialog}

  <div class="right-box">
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_returns_list href="returns.php" style="link"}
  </div>

  <form action="returns.php" method="post">
    <input type="hidden" name="returnid" value="{$returnid}" />
    <input type="hidden" name="mode" value="modify" />

    <table cellspacing="0" class="data-table" summary="{$lng.lbl_modify|escape}">
      <tr>
        <td class="data-name">{$lng.lbl_product}</td>
        <td>

          {if $return.product.productid gt 0}
            <a href="product.php?productid={$return.product.productid}">{$return.product.product} ({$return.amount} {$lng.lbl_items})</a>
          {else}
            {$return.product.product} ({$return.amount} {$lng.lbl_items})
          {/if}

          {if $return.product.product_options ne "" and $active_modules.Product_Options ne ''}
            <div class="rma-product-options-box">
              {include file="modules/Product_Options/display_options.tpl" options=$return.product.product_options}
            </div>
          {/if}
        </td>
      </tr>

      {if $reasons ne ''}
        <tr>
          <td class="data-name">{$lng.lbl_reason_for_returning}</td>
          <td>
            <select name="posted_data[reason]"{if $return.readonly eq "Y"} disabled="disabled"{/if}>
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
          <td class="data-name">{$lng.lbl_what_you_would_like_us_to_do}</td>
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
         <td class="data-name">{$lng.lbl_comment}</td>
        <td>
          <textarea{if $return.readonly eq "Y"} disabled="disabled"{/if} rows="3" cols="60" name="posted_data[comment]">{$return.comment|escape}</textarea>
          {if $return.readonly eq "Y"}
            <input type="hidden" name="posted_data[comment]" value="{$return.comment}" />
          {/if}
        </td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td class="buttons-row">
          {if $return.readonly ne 'Y'}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_modify type="input"}
            <div class="button-separator"></div>
          {/if}
          {if $return.status eq 'A' or $return.status eq 'C'}
            {include file="customer/buttons/button.tpl" button_title=$lng.lbl_print_return_slip href="javascript: window.open('returns.php?mode=print&amp;returnid=`$return.returnid`','PRINT_RETURN_SLIP','width=350,height=300,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=yes,location=no,direction=no');"}
          {/if}
        </td>
      </tr>

    </table>
  </form>

{/capture}
{include file="customer/dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_modify noborder=true}
