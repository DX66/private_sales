{*
$Id: affiliate_search_result.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

{if $mode ne "search" or $products eq ""}

<script type="text/javascript" src="{$SkinDir}/js/reset.js"></script>
<script type="text/javascript">
//<![CDATA[
var searchform_def = [
  ['posted_data[search_in_subcategories]', true],
  ['posted_data[by_title]', true],
  ['posted_data[by_descr]', true],
  ['posted_data[by_keywords]', true],
  ['posted_data[price_min]', '{$zero}'],
  ['posted_data[avail_min]', '0'],
  ['posted_data[weight_min]', '{$zero}']
]
//]]>
</script>

{capture name=dialog}

  <br />

  <form name="searchform" action="partner_banners.php" method="post">
    <input type="hidden" name="bannerid" value="{$banner.bannerid}" />
    <input type="hidden" name="get" value="1" />
    <input type="hidden" name="mode" value="search" />

    <table cellpadding="1" cellspacing="5" width="100%">

      <tr>
        <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_for_pattern}:</td>
        <td width="10" height="10"><font class="CustomerMessage"></font></td>
        <td height="10" width="80%">
          <input type="text" name="posted_data[substring]" size="30" style="width:70%" value="{$search_prefilled.substring|escape}" />
          &nbsp;
          <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
        </td>
      </tr>

      <tr>
        <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_including}:</td>
        <td width="10" height="10"></td>
        <td>

          <table cellpadding="0" cellspacing="0">
            <tr>
              <td width="5"><input type="radio" name="posted_data[including]" value="all"{if $search_prefilled eq "" or $search_prefilled.including eq '' or $search_prefilled.including eq 'all'} checked="checked"{/if} /></td>
              <td nowrap="nowrap">{$lng.lbl_all_word}&nbsp;&nbsp;</td>

              <td width="5"><input type="radio" name="posted_data[including]" value="any"{if $search_prefilled.including eq 'any'} checked="checked"{/if} /></td>
              <td nowrap="nowrap">{$lng.lbl_any_word}&nbsp;&nbsp;</td>

              <td width="5"><input type="radio" name="posted_data[including]" value="phrase"{if $search_prefilled.including eq 'phrase'} checked="checked"{/if} /></td>
              <td nowrap="nowrap">{$lng.lbl_exact_phrase}</td>
            </tr>
          </table>

        </td>
      </tr>

      <tr>
        <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in}:</td>
        <td width="10" height="10"><font class="CustomerMessage"></font></td>
        <td>

          <table cellpadding="0" cellspacing="0">
            <tr>
              <td width="5"><input type="checkbox" id="posted_data_by_title" name="posted_data[by_title]"{if $search_prefilled eq "" or $search_prefilled.by_title} checked="checked"{/if} /></td>
              <td nowrap="nowrap"><label for="posted_data_by_title">{$lng.lbl_product_title}</label>&nbsp;&nbsp;</td>

              <td width="5"><input type="checkbox" id="posted_data_by_descr" name="posted_data[by_descr]"{if $search_prefilled eq "" or $search_prefilled.by_descr} checked="checked"{/if} /></td>
              <td nowrap="nowrap"><label for="posted_data_by_descr">{$lng.lbl_description}</label>&nbsp;&nbsp;</td>
            </tr>
          </table>

        </td>
      </tr>

      {if $active_modules.Extra_Fields and $extra_fields ne ''}
        <tr>
          <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_search_also_in}:</td>
          <td width="10" height="10"><font class="CustomerMessage"></font></td>
          <td>

            <table cellpadding="0" cellspacing="0">

              {foreach from=$extra_fields item=v}
                <tr>
                  <td width="5"><input type="checkbox" name="posted_data[extra_fields][{$v.fieldid}]"{if $v.selected eq "Y"} checked="checked"{/if} /></td>
                  <td>{$v.field}</td>
                </tr>
              {/foreach}

            </table>
          </td>
        </tr>
      {/if}

    </table>

    <br />

    {include file="main/visiblebox_link.tpl" mark="1" title=$lng.lbl_advanced_search_options}

    <br />

    <table cellpadding="0" cellspacing="0" width="100%" style="display: none;" id="box1">
      <tr>
        <td>

          <table cellpadding="1" cellspacing="5" width="100%">
  
            <tr>
              <td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_advanced_search_options}</td>
            </tr>

            <tr>
              <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_search_in_category}:</td>
              <td height="10"></td>
              <td height="10">

                <select name="posted_data[categoryid]" style="width: 70%;">
                  <option value="">&nbsp;</option>
                  {foreach from=$search_categories item=v}
                    <option value="{$v.categoryid}"{if $search_prefilled.categoryid eq $v.categoryid} selected="selected"{/if}>{$v.category_path|amp}</option>
                  {/foreach}
                </select>
              </td>
            </tr>

            <tr>
              <td colspan="2" width="10" height="10">&nbsp;</td>
              <td height="10">

                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="5"><input type="checkbox" id="posted_data_search_in_subcategories" name="posted_data[search_in_subcategories]"{if $search_prefilled eq "" or $search_prefilled.search_in_subcategories} checked="checked"{/if} /></td>
                    <td nowrap="nowrap"><label for="posted_data_search_in_subcategories">{$lng.lbl_search_in_subcategories}</label></td>
                  </tr>
                </table>

              </td>
            </tr>

            {if $active_modules.Manufacturers and $manufacturers ne ''}
              <tr>
                <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_manufacturers}:</td>
                <td height="10"></td>
                <td height="10">
                  <select name="posted_data[manufacturers][]" style="width:70%" multiple="multiple" size="3">
                    {foreach from=$manufacturers item=v}
                      <option value="{$v.manufacturerid}"{if $v.selected eq 'Y'} selected="selected"{/if}>{$v.manufacturer}</option>
                    {/foreach}
                  </select>
                </td>
              </tr>
            {/if}

            <tr>
              <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_sku}:</td>
              <td width="10" height="10"><font class="CustomerMessage"></font></td>
              <td height="10" width="80%">
                <input type="text" maxlength="64" name="posted_data[productcode]" value="{$search_prefilled.productcode|escape}" style="width: 70%;" />
              </td>
            </tr>

            <tr>
               <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_price} ({$config.General.currency_symbol}):</td>
              <td width="10" height="10"><font class="CustomerMessage"></font></td>
              <td height="10" width="80%">

                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <td><input type="text" size="10" maxlength="15" name="posted_data[price_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.price_min|formatprice}{/if}" /></td>
                    <td>&nbsp;-&nbsp;</td>
                    <td><input type="text" size="10" maxlength="15" name="posted_data[price_max]" value="{$search_prefilled.price_max|formatprice}" /></td>
                  </tr>
                </table>

              </td>
            </tr>

            <tr>
              <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_quantity}:</td>
              <td width="10" height="10"><font class="CustomerMessage"></font></td>
              <td height="10" width="80%">

                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <td><input type="text" size="10" maxlength="10" name="posted_data[avail_min]" value="{if $search_prefilled eq ""}0{else}{$search_prefilled.avail_min}{/if}" /></td>
                    <td>&nbsp;-&nbsp;</td>
                    <td><input type="text" size="10" maxlength="10" name="posted_data[avail_max]" value="{$search_prefilled.avail_max}" /></td>
                  </tr>
                </table>

              </td>
            </tr>

            <tr>
              <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_weight} ({$config.General.weight_symbol}):</td>
              <td width="10" height="10"><font class="CustomerMessage"></font></td>
              <td height="10" width="80%">

                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <td><input type="text" size="10" maxlength="10" name="posted_data[weight_min]" value="{if $search_prefilled eq ""}{$zero}{else}{$search_prefilled.weight_min|formatprice}{/if}" /></td>
                    <td>&nbsp;-&nbsp;</td>
                    <td><input type="text" size="10" maxlength="10" name="posted_data[weight_max]" value="{$search_prefilled.weight_max|formatprice}" /></td>
                  </tr>
                </table>

              </td>
            </tr>

            <tr>
              <td class="FormButton normal" width="20%">
                <a href="javascript:void(0);" onclick="javascript: reset_form('searchform', searchform_def);" class="underline">{$lng.lbl_reset_filter}</a>
              </td>
              <td colspan="3" class="SubmitBox"><input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" /></td>
            </tr>

          </table>

        </td>
      </tr>
    </table>

  </form>

{if $search_prefilled.need_advanced_options}
<script type="text/javascript">
//<![CDATA[
visibleBox('1');
//]]>
</script>
{/if}


{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_products content=$smarty.capture.dialog extra='width="100%"'}

<br />

{/if}

<a name="results"></a>

{if $mode eq "search"}
  {if $total_items gt "1"}
    {$lng.txt_N_results_found|substitute:"items":$total_items}<br />
    {$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
  {elseif $total_items eq "0"}
    {$lng.txt_N_results_found|substitute:"items":0}
  {/if}
{/if}

{if $mode eq "search" and $products ne ""}

  <br />
  <br />

  {capture name=dialog}

    <div align="right">
      <a href="partner_banners.php?bannerid={$banner.bannerid}&amp;get=1">{$lng.lbl_search_again}</a>
    </div>

    {if $total_pages gt 2}
      {assign var="navpage" value=$navigation_page}
    {/if}

    {include file="main/navigation.tpl"}

    <br />
    <br />

    {foreach from=$products item=product}

      <table>
        <tr>
          <td align="center" valign="top">
            <div>
              <a href="partner_banners.php?bannerid={$banner.bannerid}&amp;get=1&amp;productid={$product.productid}&amp;page={$navigation_page}">{include file="product_thumbnail.tpl" productid=$product.productid image_x=50 product=$product.product tmbn_url=$product.tmbn_url}</a>
            </div>

          </td>

          <td valign="top">
            <a href="partner_banners.php?bannerid={$banner.bannerid}&amp;get=1&amp;productid={$product.productid}&amp;page={$navigation_page}"><font class="ProductTitle">{$product.product|amp}</font></a>

            {if $config.Appearance.display_productcode_in_list eq "Y" and $product.productcode ne ""}
              <br />
              {$lng.lbl_sku}: {$product.productcode}
            {/if}

            <br />
            <br />
            {$product.descr}
          </td>
        </tr>
      </table>

      <br />
      <br />
      <br />
    {/foreach}

    <br />

    {include file="main/navigation.tpl"}

    <br />

  {/capture}
  {include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}

{/if}
