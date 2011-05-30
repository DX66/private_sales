{*
$Id: referred_sales.tpl,v 1.1.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_referred_sales}
{$lng.txt_reffered_sales_note}<br />
<br />

<br />

{capture name=dialog}

  <form action="referred_sales.php" method="post" name="referredsalesform">

    <table>

      <tr>
        <td>{$lng.lbl_period_from}:</td>
        <td>{include file="main/datepicker.tpl" name="start_date" date=$search.start_date|default:$month_begin}</td>
      </tr>

      <tr>
        <td>{$lng.lbl_period_to}:</td>
        <td>{include file="main/datepicker.tpl" name="end_date" date=$search.end_date}</td>
      </tr>

      <tr>
        <td>{$lng.lbl_sku}:</td>
        <td><input type="text" name="search[productcode]" size="20" value="{$search.productcode|escape}" /></td>
      </tr>

      <tr>
        <td>{$lng.lbl_show_top_products}</td>
        <td><input type="checkbox" name="search[top]" value="Y"{if $search.top eq 'Y'} checked="checked"{/if} /></td>
      </tr>

      {if $is_admin_user}

        <tr>
          <td>{$lng.lbl_partner}:</td>
          <td>

            <select name="search[partner]">
              <option value=''>{$lng.lbl_all}</option>
              {foreach from=$partners item=v}
                <option value='{$v.id}'{if $search.partner eq $v.id} selected="selected"{/if}>{$v.firstname} {$v.lastname}</option>
              {/foreach}
            </select>

          </td>
        </tr>
      {/if}

      <tr>
        <td>{$lng.lbl_status}</td>
        <td>

          <select name="search[status]">
            <option value=''{if $search.status eq ''} selected="selected"{/if}>{$lng.lbl_all}</option>
            <option value='N'{if $search.status eq 'N'} selected="selected"{/if}>{$lng.lbl_pending}</option>
            <option value='A'{if $search.status eq 'A'} selected="selected"{/if}>{$lng.lbl_approved}</option>
            <option value='Y'{if $search.status eq 'Y'} selected="selected"{/if}>{$lng.lbl_paid}</option>
          </select>

        </td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
      </tr>

    </table>
  </form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_search extra='width="100%"'}

{if $sales ne ''}

  <br />

  {capture name=dialog}

    <table width="100%" cellspacing="1" cellpadding="2">

      <tr class="TableHead">
        {if $is_admin_user and $search.top ne 'Y'}
          <td rowspan="2">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_partner field_name="login"}</td>
          <td rowspan="2">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_partner_parent field_name="parent"}</td>
        {/if}

        <td rowspan="2">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_product field_name="product"}</td>

        {if $search.top ne 'Y'}
          <td colspan="2" align="center">{$lng.lbl_order}</td>
        {/if}

        <td rowspan="2" align="center">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_quantity field_name="amount"}</td>

        {if $config.XAffiliate.partner_allow_see_total eq 'Y' or $usertype ne 'B'}
          <td rowspan="2" align="center">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_total field_name="total"}</td>
        {/if}

        <td rowspan="2" align="center">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_commission field_name="product_commission"}</td>

        {if $search.top ne 'Y'}
          <td rowspan="2" align="center">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_status field_name="paid"}</td>
          {if $usertype eq 'B'}
            <td rowspan="2" align="center">{$lng.lbl_xaff_relation}</td>
          {/if}
        {/if}

      </tr>

      <tr class="TableHead">

        {if $search.top ne 'Y'}
          <td align="center">{include file="main/referred_sales_sort_column.tpl" title="#" field_name="orderid"}</td> 
          <td align="center">{include file="main/referred_sales_sort_column.tpl" title=$lng.lbl_date field_name="add_date"}</td>
        {/if}

      </tr>

      {foreach from=$sales item=v}

        <tr{cycle values=', class="TableSubHead"'}>
          {if $is_admin_user and $search.top ne 'Y'}
            <td><a href="user_modify.php?user={$v.id}&amp;usertype=B">{$v.login}</a></td>
            <td>
              {if $v.parent ne ''}
                <a href="user_modify.php?user={$v.parent}&amp;usertype=B">{$v.parent_login}</a>
              {else}
                &nbsp;
              {/if}
            </td>
          {/if}
          <td>
            {if $is_admin_user}
              <a href="product_modify.php?productid={$v.productid}">{$v.product}</a>
            {else}
              {$v.product}
            {/if}
          </td>
          {if $search.top ne 'Y'}
            <td>
              {if $is_admin_user}
                <a href="order.php?orderid={$v.orderid}">{$v.orderid}</a>
              {else}
                {$v.orderid}
              {/if}
            </td>
            <td nowrap="nowrap">{$v.add_date|date_format:$config.Appearance.date_format}</td>
          {/if}
          <td>{$v.amount}</td>
          {if $config.XAffiliate.partner_allow_see_total eq 'Y' or $usertype ne 'B'}
            <td align="right" nowrap="nowrap">{currency value=$v.total}</td>
          {/if}
          <td align="right" nowrap="nowrap">{currency value=$v.product_commission}</td>
          {if $search.top ne 'Y'}
            <td>
              {if $v.paid eq 'Y'}
                {$lng.lbl_paid}
              {elseif $v.paid eq 'A'}
                {$lng.lbl_approved}
              {else}
                {$lng.lbl_pending}
              {/if}
            </td>
            {if $usertype eq 'B'}
              <td>
                {if $v.level_delta}
                  {$lng.lbl_child_commission|substitute:level:$v.level_delta}
                {else}
                  {$lng.lbl_my_commision}
                {/if}
              </td>
            {/if}
          {/if}
        </tr>

      {/foreach}

      {assign var="colspan_count" value=4}
      {if $is_admin_user}
        {inc assign="colspan_count" value=$colspan_count inc=2}
      {/if}
      {if $search.top ne 'Y'}
        {inc assign="colspan_count" value=$colspan_count inc=5}
      {/if}

      <tr>
        <td colspan="{$colspan_count}" height="1"><hr size="1" /></td>
      </tr>

      {dec assign="colspan_count" value=$colspan_count dec=4}

      {if $usertype eq 'B' and $search.top ne 'Y'}
        <tr>
          <td colspan="{$colspan_count}">{$lng.lbl_pending_aff_commissions}</td>
          <td align="right" nowrap="nowrap">{currency value=$parent_pending}</td>
        </tr>
        <tr> 
          <td colspan="{$colspan_count}">{$lng.lbl_paid_aff_commissions}</td>
          <td align="right" nowrap="nowrap">{currency value=$parent_paid}</td>
        </tr>
      {/if}

      {if $search.top ne 'Y'}
        {inc assign="colspan_count" value=$colspan_count}
      {/if}

      <tr>
        {dec assign="colspan_count" value=$colspan_count dec=3}
        <td colspan="{$colspan_count}"><strong>{$lng.lbl_total}:</strong></td>
        <td>{$total_amount}</td>
        {if $config.XAffiliate.partner_allow_see_total eq 'Y' or $usertype ne 'B'}
          <td align="right" nowrap="nowrap">{currency value=$total_total|default:"0"}</td>
        {/if}
         <td align="right" nowrap="nowrap">{currency value=$total_product_commissions|default:"0"}</td>
      </tr>

    </table>

  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_sales extra='width="100%"'}

{/if}
