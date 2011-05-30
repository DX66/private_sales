{*
$Id: banner_info.tpl,v 1.1 2010/05/21 08:31:58 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{include file="page_title.tpl" title=$lng.lbl_banners_statistics}
{$lng.txt_banner_stats_note}<br /><br />

<br />
 
{capture name=dialog}
  <form action="banner_info.php" method="post">

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
        <td>{$lng.lbl_partner}:</td>
        <td>

          <select name="search[partner]">
            <option value=''>{$lng.lbl_all}</option>
            {if $partners ne ''}
              {foreach from=$partners item=v}
                <option value='{$v.id}'{if $search.partner eq $v.id} selected="selected"{/if}>{$v.login} ({$v.firstname} {$v.lastname})</option>
              {/foreach}
            {/if}
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

{if $banners ne ''}

  <br />

  {capture name=dialog}

    <table width="100%" cellpadding="2" cellspacing="2">

      <tr class="TableHead">
        <td rowspan="2">{$lng.lbl_banner}</td>
        <td rowspan="2">{$lng.lbl_clicks}</td>
        <td rowspan="2">{$lng.lbl_views}</td>
        <td rowspan="2" nowrap="nowrap">{$lng.lbl_click_rate}</td>
        <td colspan="2">{$lng.lbl_order}</td>
        <td rowspan="2">{$lng.lbl_conversion_rate}</td>
      </tr>
      <tr class="TableHead">
        <td>{$lng.lbl_quantity}</td>
        <td>{$lng.lbl_total}</td>
      </tr>

      {foreach from=$banners item=v}

        <tr{cycle values=', class="TableSubHead"'}>

          <td>
            {if $v.bannerid gt 0}

              {if $v.banner}
                <a href="partner_banners.php?bannerid={$v.bannerid}">{$v.banner|default:$lng.lbl_deleted_banner}</a>
              {else}
                {$v.banner|default:$lng.lbl_deleted_banner}
              {/if}

            {else}
              {$lng.lbl_default_banner}
            {/if}

            {if $v.target and $v.targetid and $v.target_name}

              {if $v.target eq 'P'}
                ({$lng.lbl_product}: <a href="product.php?productid={$v.targetid}">{$v.target_name|truncate:50}</a>)

              {elseif $v.target eq 'C'}
                ({$lng.lbl_category}: <a href="categories.php?cat={$v.targetid}">{$v.target_name|truncate:50}</a>)

              {elseif $v.target eq 'F'}
                ({$lng.lbl_manufacturer}: <a href="manufacturers.php?manufacturerid={$v.targetid}">{$v.target_name|truncate:50}</a>)

              {/if}

            {/if}
          </td>
          <td align="right">{$v.clicks}</td>
          <td align="right">{$v.views}</td>
          <td align="right">{$v.click_rate|formatprice}</td>
          <td align="right">{$v.order_count}</td>
          <td align="right">{$v.total|formatprice}</td>
          <td align="right">{$v.cr|formatprice}</td>
        </tr>
      {/foreach}

      <tr>
        <td colspan="7" height="1"><hr style="margin: 0px;" size="1" /></td>
      </tr>

      <tr>
        <td><strong>{$lng.lbl_total}:</strong></td>
        <td align="right">{$total.clicks}</td>
        <td align="right">{$total.views}</td>
        <td align="right">{$total.click_rate|formatprice}</td>
        <td align="right">{$total.order_count}</td>
        <td align="right">{$total.total|formatprice}</td>
        <td align="right">{$total.cr|formatprice}</td>
      </tr>

    </table>
  {/capture}
  {include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_banners_statistics extra='width="100%"'}

{elseif $smarty.get.mode eq "search"}

  <br />
  {$lng.txt_N_results_found|substitute:"items":0}

{/if}
