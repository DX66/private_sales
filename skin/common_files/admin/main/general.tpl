{*
$Id: general.tpl,v 1.2 2010/07/21 11:04:04 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_summary}

<br />

{$lng.txt_summary_admin_top_text}

<br /><br />

{capture name=dialog}
<table cellpadding="2" cellspacing="0" width="100%">

<tr>
  <td width="10"></td>
  <td width="100%"></td>
</tr>

<tr>
<td colspan="2">
{$lng.txt_auth_code_text} <b>{$auth_code}</b>
<br />
<br />
{if $shop_evaluation and $shop_evaluation ne "WRONG_DOMAIN"}
<font class="AdminTitle">{$lng.lbl_not_registered}</font>
{else}
<b>{$lng.lbl_registered_to|substitute:"shop_type":$shop_type:"url":$config.license_url}</b>
{/if}
</td>
</tr>

<tr><td colspan="2"><br /><br /></td></tr>

<tr>
<td colspan="2"><a name="License"></a>{include file="main/subheader.tpl" title=$lng.lbl_license_info}</td>
</tr>

<tr><td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td></tr>

{*** LICENSE INFO ***}

<tr>
<td colspan="2">
{$lng.txt_license_message}
</td>
</tr>

<tr><td colspan="2"><br /><br /></td></tr>

<tr>
<td colspan="2"><a name="General"></a>{include file="main/subheader.tpl" title=$lng.lbl_general_info}</td>
</tr>

<tr>
<td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td>
</tr>

{*** STATUS INFO ***}

{if $active_modules.Simple_Mode eq ""}
<tr>
<td colspan="2">
{if $single_mode}{$lng.txt_single_mode_enabled_text}{else}{$lng.txt_single_mode_disabled_text}{/if}
</td>
</tr>
{/if}

<tr>
<td colspan="2">
{if $config.General.shop_closed eq "Y"}
{$lng.txt_store_disabled_text}
{else}
{$lng.txt_store_enabled_text}
{/if}
</td>
</tr>

<tr>
<td colspan="2">
{if $config.db_backup_date eq ""}
<font class="AdminTitle">{$lng.txt_db_never_backuped}</font>
{else}
{$lng.txt_db_last_backup_date} {$config.db_backup_date|date_format:$config.Appearance.datetime_format}
{/if}
&nbsp;&nbsp;&nbsp;<a href="db_backup.php" title="{$lng.lbl_backup_database|escape}">{$lng.lbl_click_here_to_backup} &gt;&gt;</a>
</td>
</tr>

{if $enable_country eq ""}
<tr>
<td colspan="2">
<font class="AdminTitle">{$lng.txt_countries_deactivated}</font>
&nbsp;&nbsp;&nbsp;<a href="countries.php" title="{$lng.lbl_active|escape}">{$lng.lbl_active} &gt;&gt;</a>
</td>
</tr>
{/if}

{if $empty_prices}
<tr>
<td colspan="2"><font class="AdminTitle">{$lng.txt_N_products_with_empty_price|substitute:"products":$empty_prices}</font>
&nbsp;&nbsp;&nbsp;<a href="search.php?mode=search&amp;price_min=0&amp;price_max=0" title="{$lng.lbl_search_products|escape}">{$lng.lbl_click_here_to_check} &gt;&gt;</a>
</td>
</tr>
{/if}

{if $active_cc.in_testmode}
<tr>
<td colspan="2"><font class="AdminTitle">{$lng.txt_processor_in_test_mode|substitute:"processor_name":$active_cc_params.module_name}</font>
&nbsp;&nbsp;&nbsp;<a href="cc_processing.php?cc_processor={$active_cc_params.module_name|escape:url}&amp;mode=update" title="{$lng.txt_change_settings|escape}">{$lng.lbl_click_here_to_check} &gt;&gt;</a>
</td>
</tr>
{/if}

{if $active_sb.in_testmode}
<tr>
<td colspan="2"><font class="AdminTitle">{$lng.txt_sb_processor_in_test_mode|substitute:"processor_name":$active_sb_params.module_name}</font>
&nbsp;&nbsp;&nbsp;<a href="cc_processing.php?cc_processor={$active_sb_params.module_name|escape:url}&amp;subscribe=yes&amp;mode=update" title="{$lng.txt_change_settings|escape}">{$lng.lbl_click_here_to_check} &gt;&gt;</a>
</td>
</tr>
{/if}

<tr><td colspan="2">&nbsp;</td></tr>

{*** ORDERS INFO ***}

<tr><td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td colspan="2" height="16">&nbsp;<font class="TopLabel">{$lng.lbl_orders_info}</font>&nbsp;&nbsp;&nbsp;<a href="orders.php" title="{$lng.lbl_search_orders|escape}">{$lng.lbl_click_here_for_details} &gt;&gt;</a></td>
</tr>

<tr><td colspan="2" height="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td></tr>

<tr>
<td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
<td width="100%">
<table cellpadding="3" cellspacing="1" width="80%">
<tr>
<td class="TableHead">{$lng.lbl_status}</td>
<td class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_since_last_log_in}</td>
<td class="TableHead" align="center">{$lng.lbl_today}</td>
<td class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_this_week}</td>
<td class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_this_month}</td>
</tr>
{assign var="index" value="0"}
{foreach key=key item=item from=$orders}
<tr{cycle values=' class="TableLine",' name='c1'}>
<td nowrap="nowrap">{if $key eq "P"}{$lng.lbl_processed}{elseif $key eq "Q"}{$lng.lbl_queued}{elseif $key eq "F" or $key eq "D"}{$lng.lbl_failed}/{$lng.lbl_declined}{elseif $key eq "I"}{$lng.lbl_not_finished}{/if}:</td>
{section name=period loop=$item}
<td align="center">{$item[period]}</td>
{/section}
</tr>
{inc value=$index assign="index"}
{/foreach}
</table>

</td>
</tr>
</table>

</td></tr>

<tr><td colspan="2">&nbsp;</td></tr>

{*** SHIPPING METHODS INFO ***}

{if $config.Shipping.enable_shipping ne "Y"}

<tr>
<td colspan="2">&nbsp;<font class="AdminTitle">{$lng.txt_shipping_disabled_text}</font>&nbsp;&nbsp;&nbsp;<a href="{$catalogs.admin}/configuration.php?option=Shipping" title="{$lng.lbl_general_settings|escape}/{$lng.lbl_shipping_options|escape}">{$lng.lbl_click_here_to_change} &gt;&gt;</a></td>
</tr>

{else}

<tr><td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td colspan="2" height="16">&nbsp;<font class="TopLabel">{$lng.lbl_shipping_methods_info}</font>&nbsp;&nbsp;&nbsp;<a href="shipping.php" title="{$lng.lbl_shipping_methods|escape}">{$lng.lbl_click_here_to_define} &gt;&gt;</a></td>
</tr>

<tr><td colspan="2" height="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td></tr>

<tr>
<td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
<td width="100%">
{if $shipping_methods_count gt "0"}
<font class="Text">{$lng.txt_N_shipping_methods_enabled|substitute:"count":$shipping_methods_count}:</font>
<br />
<table cellpadding="1" cellspacing="2" width="80%">

<tr>
<td height="14" class="TableHead">{$lng.lbl_carrier}</td>
<td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_methods_enabled}</td>
</tr>

{section name=idx loop=$shipping_mod_enabled}
<tr {cycle values=' class="TableLine",' name='c2'}>
<td>{if $shipping_mod_enabled[idx].code eq "FDX"}FedEx{elseif $shipping_mod_enabled[idx].code eq "UPS"}UPS{elseif $shipping_mod_enabled[idx].code eq "USPS"}U.S.P.S.{elseif $shipping_mod_enabled[idx].code eq "DHL"}DHL{elseif $shipping_mod_enabled[idx].code eq "ABX"}Airborne{elseif $shipping_mod_enabled[idx].code eq "EWW"}Emery Worldwide{elseif $shipping_mod_enabled[idx].code eq "ANX"}AirNet Express{elseif $shipping_mod_enabled[idx].code}{$shipping_mod_enabled[idx].code}{else}{$lng.lbl_user_defined}{/if}</td>
<td align="center">{$shipping_mod_enabled[idx].count}</td>
</tr>
{/section}

</table>
{else}
<font class="AdminTitle">{$lng.txt_no_shipping_methods_enabled}</font>
{/if}
</td>
</tr>
</table>

</td></tr>

<tr><td colspan="2">&nbsp;</td></tr>

{*** SHIPPING RATES INFO ***}

<tr><td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td colspan="2" height="16">&nbsp;<font class="TopLabel">{$lng.lbl_shipping_rates_info}</font>&nbsp;&nbsp;&nbsp;{if $active_modules.Simple_Mode}<a href="{$catalogs.provider}/shipping_rates.php" title="{$lng.lbl_shipping_rates|escape}">{$lng.lbl_click_here_to_define} &gt;&gt;</a>{else}({$lng.txt_only_providers_able_to_define_this}){/if}</td>
</tr>

<tr><td colspan="2" height="3"><img src="{$ImagesDir}/spacer.gif" width="1" height="3" alt="" /></td></tr>

<tr>
<td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
<td width="100%"><font class="Text">
{if $shipping_rates_count gt "0"}
{$lng.txt_N_shipping_rates_defined|substitute:"count":$shipping_rates_count}:
</font>
<br />
<table cellpadding="1" cellspacing="2" width="80%">
<tr>
<td height="14" class="TableHead">{$lng.lbl_carrier}</td>
<td height="14" class="TableHead" nowrap="nowrap" align="center">{$lng.lbl_rates_enabled}</td>
</tr>

{section name=idx loop=$shipping_rates_enabled}
<tr{cycle values=' class="TableLine",' name='c3'}>
<td>{if $shipping_rates_enabled[idx].code eq "FDX"}FedEx{elseif $shipping_rates_enabled[idx].code eq "UPS"}UPS{elseif $shipping_rates_enabled[idx].code eq "USPS"}U.S.P.S.{elseif $shipping_rates_enabled[idx].code eq "DHL"}DHL{elseif $shipping_rates_enabled[idx].code eq "ABX"}Airborne{elseif $shipping_rates_enabled[idx].code eq "EWW"}Emery Worldwide{elseif $shipping_rates_enabled[idx].code eq "ANX"}AirNet Express{elseif $shipping_rates_enabled[idx].code}{$shipping_rates_enabled[idx].code}{else}{$lng.lbl_user_defined}{/if}</td>
<td align="center">{$shipping_rates_enabled[idx].count}</td>
</tr>
{/section}

</table>
{else}
<font class="AdminTitle">{$lng.txt_no_shipping_rates}</font>
</font>
{/if}
</td>
</tr>
</table>

</td></tr>

<tr>
<td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
<td width="100%"><font class="Text">
{if $config.Shipping.realtime_shipping eq "Y"}
{$lng.txt_realtime_shipping_enabled_text}
{else}
{$lng.txt_realtime_shipping_disabled_text}
{/if}
</font>
</td>
</tr>

{/if}

{if $missing_clean_urls_stats}
  <tr><td colspan="2"><br /><br /></td></tr>

  <tr>
  <td colspan="2"><a name="CleanUrls"></a>{include file="main/subheader.tpl" title=$lng.lbl_clean_urls_info}</td>
  </tr>

  <tr>
    <td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
    <td>
      <table cellpadding="3" cellspacing="1" width="80%">
        <tr>
          <td class="TableHead" width="25%" style="text-align: left">{$lng.lbl_clean_urls_object_type}</td>
          <td class="TableHead" width="40%" style="text-align: right">{$lng.lbl_clean_urls_missing_urls_num}</td>
          <td class="TableHead" width="35%" style="text-align: right">{$lng.lbl_clean_urls_total_num}</td>
        </tr>
        {foreach from=$missing_clean_urls_stats item=resource key=resource_type name=clean_urls}
        <tr{cycle values=' class="TableLine",' name='c4'}>
          <td align="left">
            {$resource.resource_name}
          </td>
          <td align="right">
            {$resource.missing_count}
           </td>
          <td align="right">
            {$resource.total_count}
           </td>
        </tr>
        {/foreach}

        {if $config.SEO.clean_urls_enabled eq 'Y'}
        <tr>
          <td colspan="3" align="right">
            <strong><a href="tools.php#generate_clean_urls">{$lng.lbl_generate_clean_urls}</a></strong>
          </td>
        </tr>
        {/if}

      </table>
    </td>
    </tr>
{/if}

<tr><td colspan="2"><br /><br /></td></tr>

{*** PAYMENT METHODS ***}

<tr>
<td colspan="2"><a name="PaymentMethods"></a>{include file="main/subheader.tpl" title=$lng.lbl_payments_methods_info}</td>
</tr>

<tr><td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td></tr>

<tr><td colspan="2">
{$lng.txt_payment_methods_hiding_text}
</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr><td colspan="2" height="16">&nbsp;<font class="TopLabel">{$lng.lbl_payments_methods_info}:</font></td></tr>

<tr><td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td></tr>
<tr>
<td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
<td width="100%" valign="top">
<table cellpadding="1" cellspacing="2" width="80%">
<tr>
<td height="14" class="TableHead" nowrap="nowrap">{$lng.lbl_payment_method}</td>
<td height="14" class="TableHead" nowrap="nowrap" width="20%">{$lng.lbl_status}</td>
</tr>
{section name=idx loop=$payment_methods}
<tr{cycle values=' class="TableLine",' name='c5'}>
<td>{$payment_methods[idx].payment_method}</td>
<td nowrap="nowrap">{if $payment_methods[idx].is_down}<font class="AdminTitle">{$lng.lbl_disfunctional}</font>{else}{$lng.lbl_ok}{/if}{if $payment_methods[idx].in_testmode} / <font class="AdminTitle">{$lng.lbl_in_test_mode}</font>{/if}</td>
</tr>
{/section}
</table>
</td></tr>
</table>
</td></tr>

<tr><td colspan="2"><br />{$lng.txt_payment_methods_bottom_text}<br /><br /></td></tr>

{*** ENVIRONMENT ***}

<tr>
<td colspan="2"><a name="Environment"></a>{include file="main/subheader.tpl" title=$lng.lbl_environment_info}</td>
</tr>

<tr><td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td></tr>

<tr><td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr><td colspan="2" height="16">&nbsp;<font class="TopLabel">{$lng.lbl_environment_components_info}:</font></td></tr>

<tr><td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td></tr>
<tr>
<td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
<td width="100%" valign="top">
<table cellpadding="1" cellspacing="2" width="80%">
<tr>
<td height="14" class="TableHead" nowrap="nowrap" width="100">{$lng.lbl_component}</td>
<td height="14" class="TableHead" nowrap="nowrap">{$lng.lbl_status}</td>
<td height="14" class="TableHead" nowrap="nowrap" width="70">&nbsp;</td>
</tr>
{section name=idx loop=$environment_info}
{if $environment_info[idx].row_txt ne ""}
<tr class="TableHead"><td colspan="3" height="14"><i>{$environment_info[idx].row_txt}</i></td></tr>
{else}
<tr{cycle values=' class="TableLine",' name='c6'}>
<td nowrap="nowrap">{$environment_info[idx].item}</td>
<td>
{if $environment_info[idx].data ne ""}
{$environment_info[idx].data}
{else}
{if $environment_info[idx].warning ne ""}
<font class="AdminTitle">{$environment_info[idx].default}</font>
{else}{$environment_info[idx].default}{/if}
{/if}
</td>
{if $environment_info[idx].details ne ""}
<td><a href="javascript:void(0);" onclick="{$environment_info[idx].details}">{if $environment_info[idx].details_txt ne ""}{$environment_info[idx].details_txt}{else}{$lng.lbl_details}{/if} &gt;&gt;</a></td>
{elseif $environment_info[idx].details_txt ne ""}
<td>{$environment_info[idx].details_txt}</td>
{else}
<td>&nbsp;</td>
{/if}
</tr>
{/if}
{/section}
</table>
</td></tr>
</table></td></tr>

<tr><td colspan="2">
{$lng.txt_environment_info_text}
</td></tr>

<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td colspan="2" height="16">&nbsp;<font class="TopLabel">{$lng.lbl_directories_must_have_write_permissions}:</font></td>
</tr>
<tr><td colspan="2" height="5"><img src="{$ImagesDir}/spacer.gif" width="1" height="5" alt="" /></td></tr>
<tr>
<td><img src="{$ImagesDir}/spacer.gif" width="30" height="1" alt="" /><br /></td>
<td width="100%">
<table cellpadding="1" cellspacing="2" width="80%">
<tr>
<td height="14" class="TableHead" nowrap="nowrap" width="100%">{$lng.lbl_directory}</td>
<td height="14" class="TableHead" nowrap="nowrap">{$lng.lbl_status}</td>
</tr>
{section name=dir loop=$test_dirs_rights}
<tr{cycle values=' class="TableLine",' name='c7'}>
<td>{$test_dirs_rights[dir].directory}</td>
<td nowrap="nowrap">
{if $test_dirs_rights[dir].exists ne "1"}<font class="AdminTitle">{$lng.lbl_not_exists}</font>
{elseif $test_dirs_rights[dir].writable ne "1"}<font class="AdminTitle">{$lng.lbl_not_writable}</font>
{else}{$lng.lbl_ok}{/if}
</td></tr>
{/section}
</table>
</td></tr></table>
</td>
</tr>

</table>

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_summary content=$smarty.capture.dialog extra='width="100%"'}
