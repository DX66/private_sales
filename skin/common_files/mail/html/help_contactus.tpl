{*
$Id: help_contactus.tpl,v 1.1 2010/05/21 08:32:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}
<br />{$lng.eml_customers_need_help}

<br /><b>{$lng.lbl_customer_info}:</b>

<hr size="1" noshade="noshade" />

<table cellpadding="2" cellspacing="0">
{if $is_areas.C}

{if $default_fields.company.avail}
<tr>
<td><b>{$lng.lbl_company}:</b></td>
<td>&nbsp;</td>
<td>{$contact.company}</td>
</tr>
{/if}
{if $default_fields.firstname.avail}
<tr>
<td><b>{$lng.lbl_first_name}:</b></td>
<td>&nbsp;</td>
<td>{$contact.firstname}</td>
</tr>
{/if}
{if $default_fields.lastname.avail}
<tr>
<td><b>{$lng.lbl_last_name}:</b></td>
<td>&nbsp;</td>
<td>{$contact.lastname}</td>
</tr>
{/if}

{/if}

{if $is_areas.A}
<tr>
<td colspan="3"><b>{$lng.lbl_address}:</b></td>
</tr>

<tr>
<td colspan="3">
<table cellpadding="1" cellspacing="0">
{if $default_fields.b_address.avail or $default_fields.b_address_2.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_address}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_address}<br />{$contact.b_address_2}</td>
</tr>
{/if}
{if $default_fields.b_city.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_city}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_city}</td>
</tr>
{/if}
{if $default_fields.b_county.avail and $config.General.use_counties eq "Y"}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_county}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_countyname}</td>
</tr>
{/if}
{if $default_fields.b_state.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_state}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_statename}</td>
</tr>
{/if}
{if $default_fields.b_country.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_country}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_countryname}</td>
</tr>
{/if}
{if $default_fields.b_zipcode.avail}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_zip_code}:</b></td>
<td>&nbsp;</td>
<td>{include file="main/zipcode.tpl" val=$contact.b_zipcode zip4=$contact.b_zip4 static=true}</td>
</tr>
{/if}
</table>
</td>
</tr>

{if $default_fields.phone.avail}
<tr>
<td><b>{$lng.lbl_phone}:</b></td>
<td>&nbsp;</td>
<td>{$contact.phone}</td>
</tr>
{/if}
{if $default_fields.fax.avail}
<tr>
<td><b>{$lng.lbl_fax}:</b></td>
<td>&nbsp;</td>
<td>{$contact.fax}</td>
</tr>
{/if}
{if $default_fields.email.avail}
<tr>
<td><b>{$lng.lbl_email}:</b></td>
<td>&nbsp;</td>
<td>{$contact.email}</td>
</tr>
{/if}
{if $default_fields.url.avail}
<tr>
<td><b>{$lng.lbl_web_site}:</b></td>
<td>&nbsp;</td>
<td>{$contact.url}</td>
</tr>
{/if}
{/if}
{if $additional_fields ne ''}

{foreach from=$additional_fields item=v}
<tr>
<td><b>{$v.title}:</b></td>
<td>&nbsp;</td>
<td>{$v.value}</td>
</tr>
{/foreach}
{/if}

{if $default_fields.department.avail}
<tr>
<td><b>{$lng.lbl_department}:</b></td>
<td>&nbsp;</td>
<td>{$contact.department}</td>
</tr>
{/if}
<tr>
<td><b>{$lng.lbl_subject}:</b></td>
<td>&nbsp;</td>
<td>{$contact.subject}</td>
</tr>
<tr>
<td colspan="3"><b>{$lng.lbl_message}:</b><br /><hr size="1" noshade="noshade" color="#DDDDDD" align="left" /></td>
</tr>
<tr>
<td colspan="3">{$contact.body}</td>
</tr>
</table>

{include file="mail/html/signature.tpl"}
