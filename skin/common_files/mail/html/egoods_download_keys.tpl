{*
$Id: egoods_download_keys.tpl,v 1.1.2.1 2010/12/15 09:44:39 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}
{include file="mail/html/mail_header.tpl"}
<p>{$lng.eml_dear_customer},

<p>{$lng.eml_egoods}

<p>{$lng.eml_egoods_download}:

<hr size="1" noshade="noshade" />

<table cellpadding="0" cellspacing="0" width="100%">
{section name=prod_num loop=$products}
{if $products[prod_num].download_key}
<tr>
<td width="25">&nbsp;&nbsp;&nbsp;</td>
<td width="20%">{$lng.lbl_sku}:</td>
<td width="10">&nbsp;</td>
<td width="80%">{$products[prod_num].productcode}</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><tt>{$lng.lbl_product}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$products[prod_num].product}</tt></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><tt>{$lng.lbl_item_price}:</tt></td>
<td>&nbsp;</td>
<td><tt>{currency value=$products[prod_num].display_price}</tt></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><tt>{$lng.lbl_filename}:</tt></td>
<td>&nbsp;</td>
<td><tt>{$products[prod_num].distribution_filename}</tt></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><tt>{$lng.lbl_download_url}:</tt></td>
<td>&nbsp;</td>
<td><tt><a href="{$catalogs.customer}/download.php?id={$products[prod_num].download_key}" target="_blank">{$catalogs.customer}/download.php?id={$products[prod_num].download_key}</a></tt></td>
</tr>
<tr>
<td><img src="empty.gif" width="1" height="1" alt="" /><br /></td>
<td colspan="3"><hr size="1" noshade="noshade" width="70%" align="left" color="#DDDDDD" /></td>
</tr>
{/if}
{/section}
</table>

<p><b>{$lng.eml_egoods_download_note|substitute:"ttl":$config.Egoods.download_key_ttl}</b></p>

{include file="mail/html/signature.tpl"}
