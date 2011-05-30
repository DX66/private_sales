{*
$Id: main.tpl,v 1.3.2.1 2010/12/15 09:44:38 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $show_adv and $txt_reg_not_registered eq ""}
{capture name="adv_dialog"}
<table width="100%">
<tr>
<td nowrap="nowrap">
<img src="{$ImagesDir}/spacer.gif" alt="" class="qteam-img" width="173" height="47" /><br />
<a href="http://www.qtmsoft.com" target="_blank">{$lng.lbl_visit_qteam}</a>
</td>
<td>{$show_adv}</td>
</tr>
<tr>
<td colspan="2"></td>
</tr>
{if not $shop_evaluation}
<tr>
<td nowrap="nowrap"><a href="?do_not_show_adv=Y">{$lng.lbl_do_not_show_message}</a></td>
<td>&nbsp;</td>
</tr>
{/if}
</table>
{/capture}
<div class="adv">
{include file="location.tpl" location="" alt_content=$smarty.capture.adv_dialog extra='width="100%"' newid="adv_qteam" alt_type="I" image_none="Y"}
</div>
{/if}

{if $current_passwords_security or $default_passwords_security or $blowfish_key_expired or $db_backup_expired or $new_rma_requests}
{capture name=dialog}
{if $current_passwords_security}
<div class="SecurityWarning">
{$lng.txt_your_password_insecured}
<br /><br />
{if $active_modules.Simple_Mode}
<div align="left"><a class="simple-button" title="{$lng.lbl_chpass}" href="{$catalogs.provider}/change_password.php">{$lng.lbl_chpass}</a></div>
{else}
<div align="left"><a class="simple-button" title="{$lng.lbl_chpass}" href="change_password.php">{$lng.lbl_chpass}</a></div>
{/if}
</div>
{/if}

{if $default_passwords_security}
<div class="SecurityWarning">
{capture name=accounts}
{section name=acc loop=$default_passwords_security}
{if $default_passwords_security[acc] ne $current_passwords_security.0}
{assign var="display_default_passwords_security" value="1"}
&nbsp;&nbsp;&nbsp;{$default_passwords_security[acc]}<br />
{/if}
{/section}
{/capture}
{if $display_default_passwords_security}
{$lng.txt_default_passwords_insecured|substitute:"accounts":$smarty.capture.accounts}
<br /><br />
<div align="left"><a class="simple-button" title="{$lng.lbl_users_management|escape}" href="users.php">{$lng.lbl_users_management}</a></div>
{/if}
</div>
{/if}

{if $blowfish_key_expired}
<div class="SecurityWarning">
{$lng.txt_blowfish_key_expired}
<br /><br />
<div align="left"><a class="simple-button" title="{$lng.lbl_regenerating_blowfish_key|escape}" href="tools.php#regenbk">{$lng.lbl_regenerating_blowfish_key}</a></div>
</div>
{/if}

{if $db_backup_expired}
<div class="SecurityWarning">
{$lng.txt_db_backup_expired}
<br /><br />
<div align="left"><a class="simple-button" title="{$lng.lbl_backup_database|escape}" href="db_backup.php">{$lng.lbl_backup_database}</a></div>
</div>
{/if}

{if $new_rma_requests}
<div class="SecurityWarning">
{$lng.txt_rma_new_requests_avail_note}
<br /><br />
<div align="left"><a class="simple-button" title="{$lng.lbl_rma_check_new|escape}" href="returns.php?new">{$lng.lbl_rma_check_new}</a></div>
</div>
{/if}
{/capture}

{include file="location.tpl" location="" alt_content=$smarty.capture.dialog extra='width="100%"' newid="password_security" alt_type="W"}
{/if}
<br />
<!-- QUICK MENU -->
{include file="main/quick_menu.tpl"}
<!-- QUICK MENU -->

<a name="orders"></a>
{capture name=dialog}
{$lng.txt_top_info_orders}
<br /><br />
<div align="center">
<table cellpadding="0" cellspacing="0" width="90%">
<tr>
<td class="TableHead">

<table cellpadding="3" cellspacing="1" width="100%">
<tr class="TableHead">
<td>{$lng.lbl_status}</td>
<td nowrap="nowrap" align="center">{$lng.lbl_since_last_log_in}</td>
<td align="center">{$lng.lbl_today}</td>
<td nowrap="nowrap" align="center">{$lng.lbl_this_week}</td>
<td nowrap="nowrap" align="center">{$lng.lbl_this_month}</td>
</tr>

{foreach key=key item=item from=$orders}
<tr class="{cycle values='SectionBox,TableSubHead'}">
<td nowrap="nowrap" align="left">{if $key eq "C"}{$lng.lbl_complete}{elseif $key eq "P"}{$lng.lbl_processed}{elseif $key eq "Q"}{$lng.lbl_queued}{elseif $key eq "F" or $key eq "D"}{$lng.lbl_failed}/{$lng.lbl_declined}{elseif $key eq "I"}{$lng.lbl_not_finished}{/if}:</td>
{section name=period loop=$item}
<td align="center">{$item[period]}</td>
{/section}
</tr>
{/foreach}

<tr class="{cycle values='SectionBox,TableSubHead'}">
<td align="right"><b>{$lng.lbl_gross_total}:</b></td>
{section name=period loop=$gross_total}
<td align="center">{currency value=$gross_total[period]}</td>
{/section} 
</tr>

<tr class="{cycle values='SectionBox,TableSubHead'}">
<td align="right"><b>{$lng.lbl_total_paid}:</b></td>
{section name=period loop=$total_paid}
<td align="center">{currency value=$total_paid[period]}</td>
{/section}
</tr>
</table>

</td>
</tr>
</table>
</div>

<br /><br />

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_orders href="orders.php" title=$lng.lbl_search_orders}</div>

{if $last_order}
<br /><br />

{include file="main/subheader.tpl" title=$lng.lbl_last_order}

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
<td>&nbsp;&nbsp;</td>
<td>
<table cellpadding="3" cellspacing="1">

<tr>
<td class="FormButton">{$lng.lbl_order_id}:</td>
<td>#{$last_order.orderid}</td>
</tr>

<tr>
<td class="FormButton">{$lng.lbl_order_date}:</td>
<td>{$last_order.date|date_format:$config.Appearance.datetime_format}</td>
</tr>

<tr>
<td class="FormButton">{$lng.lbl_order_status}:</td>
<td>{include file="main/order_status.tpl" status=$last_order.status mode="static"}</td>
</tr>

<tr>
<td class="FormButton">{$lng.lbl_customer}:</td>
<td>{$last_order.title} {$last_order.firstname} {$last_order.lastname}</td>
</tr>

<tr>
<td class="FormButton" valign="top">{$lng.lbl_ordered}:</td>
<td>
{if $last_order.products}
{section name=product loop=$last_order.products}
<b>{$last_order.products[product].product|truncate:"30":"..."}</b>
[{$lng.lbl_price}: {currency value=$last_order.products[product].price}, {$lng.lbl_quantity}: {$last_order.products[product].amount}]
{if $last_order.products[product].product_options}
<br />
{$lng.lbl_options}: {$last_order.products[product].product_options|replace:"\n":"; "}
{/if}
<br />
{/section}
{/if}
{if $last_order.giftcerts}
{section name=gc loop=$last_order.giftcerts}
<b>{$lng.lbl_gift_certificate} #{$last_order.giftcerts[gc].gcid}</b>
[{$lng.lbl_price}: {currency value=$last_order.giftcerts[gc].amount}]
<br />
{/section}
{/if}
</td>
</tr>

</table>
</td>
</tr>

</table>

<br />

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_order_details_label href="order.php?orderid=`$last_order.orderid`" title=$lng.lbl_order_details_label}</div>

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_orders_info content=$smarty.capture.dialog extra='width="100%"'}

<br /><br />

<a name="topsellers"></a>
{capture name=dialog}

{$lng.txt_top_info_top_sellers}

<br /><br />

<div class="TopLabel" align="center">{$lng.lbl_top_N_products|substitute:"N":$max_top_sellers}</div>

<br />

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="TableHead">
<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_since_last_log_in}</td>
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_today}</td>
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_this_week}</td>
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_this_month}</td>
</tr>

{capture name=top_products}
<tr class="SectionBox">
{foreach key=key item=item from=$top_sellers}
<td align="center"{if $item} valign="top"{/if}>
{if $item}
{assign var="is_top_products" value="1"}
<table cellpadding="2" cellspacing="1" width="100%">
{section name=period loop=$item}
<tr{cycle name=col`%period.index%` values=', class="TableSubHead"'}>
  <td>{inc value=%period.index%}.</td>
  <td align="left"><a href="product_modify.php?productid={$item[period].productid}">{$item[period].product|truncate:"20":"..."}</a></td>
  <td>{$item[period].count}</td>
</tr>
{/section}
</table>
{else}
{$lng.txt_no_top_products_statistics}
{/if}
</td>
{/foreach}
</tr>
{/capture}

{if $is_top_products}

{$smarty.capture.top_products}

</table>
</td>
</tr>
</table>

<br />

<div class="TopLabel" align="center">{$lng.lbl_top_N_categories|substitute:"N":$max_top_sellers}</div>

<br />

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="TableHead">
<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_since_last_log_in}</td>
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_today}</td>
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_this_week}</td>
  <td width="25%" nowrap="nowrap" align="center">{$lng.lbl_this_month}</td>
</tr>

<tr class="SectionBox">
{foreach key=key item=item from=$top_categories}
<td align="center"{if $item} valign="top"{/if}>
{if $item}
<table cellpadding="2" cellspacing="1" width="100%">
{section name=period loop=$item}
<tr{cycle name=col`%period.index%` values=", class='TableSubHead'"}>
  <td>{inc value=%period.index%}.</td>
  <td align="left"><a href="category_modify.php?cat={$item[period].categoryid}">{$item[period].category}</a></td>
  <td>{$item[period].count}</td>
</tr>
{/section}
</table>
{else}
{$lng.txt_no_top_categories_statistics}
{/if}
</td>
{/foreach}
</tr>

{else}

<tr class="SectionBox">
  <td colspan="4" align="center">{$lng.txt_no_statistics}</td>
</tr>

{/if}

</table>
</td>
</tr>
</table>

<br /><br />

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_orders href="orders.php" title=$lng.lbl_search_orders}</div>{$lng.txt_how_setup_store_bottom}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_top_sellers content=$smarty.capture.dialog extra='width="100%"'}
