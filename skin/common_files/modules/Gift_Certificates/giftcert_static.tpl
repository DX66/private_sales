{*
$Id: giftcert_static.tpl,v 1.1.2.1 2010/12/15 09:44:40 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_view_gift_certificate}

<br />
{capture name=dialog}
<table width="100%" cellpadding="2" cellspacing="1">

<tr valign="middle">
<td height="20" colspan="3"><b>{$lng.lbl_gc_details}</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr>
<td width="20%" align="right">{$lng.lbl_from}:</td>
<td>&nbsp;</td>
<td width="80%" align="left">
<b>{$giftcert.purchaser|escape:"html"}</b>
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_to}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient|escape:"html"}</b>
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_message}:</td>
<td>&nbsp;</td>
<td align="left">
<table cellpadding="0" cellspacing="0"><tr><td bgcolor="#CCCCCC">
<table cellpadding="5" cellspacing="1" width="100%"><tr><td bgcolor="#FFFFFF">
{$giftcert.message|escape:"html"|replace:"\n":"<br />"}
</td></tr></table>
</td></tr></table>
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_amount}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{currency value=$giftcert.amount}</b>
</td>
</tr>

<tr>
<td align="right">{$lng.lbl_gc_template}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.tpl_file}</b>
</td>
</tr>

<tr><td colspan="3">&nbsp;</td></tr>

{if $giftcert.send_via ne "P"}

<tr valign="middle">
<td height="20" colspan="3"><b>E-mail Address</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_email}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_email}</b>
</td>
</tr>

{/if}

{if $config.Gift_Certificates.enablePostMailGC and $giftcert.send_via eq "P"}

<tr valign="middle">
<td height="20" colspan="3"><b>{$lng.lbl_postal_address}</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_first_name}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_firstname|escape:"html"}</b>
</td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_last_name}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_lastname|escape:"html"}</b>
</td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_address}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_address|escape:"html"}</b>
</td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_city}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_city|escape:"html"}</b>
</td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_zip_code}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{include file="main/zipcode.tpl" val=$giftcert.recipient_zipcode zip4=$giftcert.recipient_zip4 static=true}</b>
</td>
</tr>

{if $config.General.use_counties eq "Y"}
<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_county}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_countyname}</b>
</td>
</tr>
{/if}

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_state}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_statename}</b>
</td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_country}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_countryname}</b>
</td>
</tr>

<tr>
<td nowrap="nowrap" align="right">{$lng.lbl_phone}:</td>
<td>&nbsp;</td>
<td align="left">
<b>{$giftcert.recipient_phone}</b>
</td>
</tr>

{/if}

</table>

<br />
<center>
{include file="buttons/go_back.tpl" href="giftcerts.php"}
</center>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_gc_details content=$smarty.capture.dialog extra='width="100%"'}

