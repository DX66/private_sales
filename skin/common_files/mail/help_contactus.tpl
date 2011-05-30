{*
$Id: help_contactus.tpl,v 1.1 2010/05/21 08:32:14 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}


{$lng.eml_customers_need_help}

{if $is_areas.C}
{$lng.lbl_customer_info}:
---------------------
{if $default_fields.title.avail}{$lng.lbl_title|mail_truncate}{$contact.title}
{/if}
{if $default_fields.firstname.avail}{$lng.lbl_first_name|mail_truncate}{$contact.firstname}
{/if}
{if $default_fields.lastname.avail}{$lng.lbl_last_name|mail_truncate}{$contact.lastname}
{/if}
{if $default_fields.company.avail}{$lng.lbl_company|mail_truncate}{$contact.company}
{/if}

{/if}
{if $is_areas.A}
{$lng.lbl_address}:
----------------
{if $default_fields.b_address.avail}{$lng.lbl_address|mail_truncate}{$contact.b_address}
{/if}
{if $default_fields.b_address_2.avail}{$lng.lbl_address_2|mail_truncate}{$contact.b_address_2}
{/if}
{if $default_fields.b_city.avail}{$lng.lbl_city|mail_truncate}{$contact.b_city}
{/if}
{if $default_fields.b_county.avail and $config.General.use_counties eq "Y"}
{$lng.lbl_county|mail_truncate}{$contact.b_countyname}
{/if}
{if $default_fields.b_state.avail}{$lng.lbl_state|mail_truncate}{$contact.b_statename}
{/if}
{if $default_fields.b_country.avail}{$lng.lbl_country|mail_truncate}{$contact.b_countryname}
{/if}
{if $default_fields.b_zipcode.avail}{$lng.lbl_zip_code|mail_truncate}{include file="main/zipcode.tpl" val=$contact.b_zipcode zip4=$contact.b_zip4 static=true}
{/if}

{if $default_fields.phone.avail}{$lng.lbl_phone|mail_truncate}{$contact.phone}
{/if}
{if $default_fields.fax.avail}{$lng.lbl_fax|mail_truncate}{$contact.fax}
{/if}
{if $default_fields.email.avail}{$lng.lbl_email|mail_truncate}{$contact.email}
{/if}
{if $default_fields.url.avail}{$lng.lbl_web_site|mail_truncate}{$contact.url}
{/if}
{/if}
{if $additional_fields ne ''}

{foreach from=$additional_fields item=v}
{$v.title|mail_truncate}{$v.value}
{/foreach}
{/if}

{if $default_fields.department.avail}{$lng.lbl_department|mail_truncate}{$contact.department}
{/if}
{$lng.lbl_subject|mail_truncate}{$contact.subject}
{$lng.lbl_message}:
{$contact.body}

{include file="mail/signature.tpl"}
