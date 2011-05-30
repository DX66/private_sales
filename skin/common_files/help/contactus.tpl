{*
$Id: contactus.tpl,v 1.3.2.1 2010/09/14 13:31:59 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="check_required_fields_js.tpl" fillerror=$fillerror}
{include file="check_email_script.tpl"}
{include file="check_zipcode_js.tpl"}
{include file="change_states_js.tpl"}

{if $smarty.get.mode eq "update" or $smarty.get.mode eq ""}
{$lng.txt_contact_us_header}
{/if}
<br /><br />
{capture name=dialog}
{if $smarty.get.mode eq "sent"}
{$lng.txt_contact_us_sent}
{elseif $smarty.get.mode eq "update" or $smarty.get.mode eq ""}
<form action="help.php?section=contactus&amp;mode=update&amp;action=contactus" method="post" name="registerform">
<table width="100%" cellspacing="1" cellpadding="3">

{if $default_fields.username.avail eq 'Y' and $config.email_as_login ne 'Y'}
<tr valign="middle">
<td class="data-name"><label for="username">{$lng.lbl_username}</label></td>
<td{if $default_fields.username.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="username" name="username" size="32" maxlength="32" value="{if $userinfo.username ne ''}{$userinfo.username|escape}{else}{$userinfo.login|escape}{/if}" />
</td>
</tr>
{/if}

{if $default_fields.title.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="title">{$lng.lbl_title}</label></td>
<td{if $default_fields.title.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
{include file="main/title_selector.tpl" val=$userinfo.titleid}
</td>
</tr>
{/if}

{if $default_fields.firstname.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="firstname">{$lng.lbl_first_name}</label></td>
<td{if $default_fields.firstname.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="firstname" name="firstname" size="32" maxlength="32" value="{$userinfo.firstname|escape}" />
</td>
</tr>
{/if}
 
{if $default_fields.lastname.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="lastname">{$lng.lbl_last_name}</label></td>
<td{if $default_fields.lastname.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="lastname" name="lastname" size="32" maxlength="32" value="{$userinfo.lastname|escape}" />
</td>
</tr>
{/if}

{if $default_fields.company.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="company">{$lng.lbl_company}</label></td>
<td{if $default_fields.company.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="company" name="company" size="32" value="{$userinfo.company|escape}" />
</td>
</tr>
{/if}
 
{if $default_fields.b_address.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="b_address">{$lng.lbl_address}</label></td>
<td{if $default_fields.b_address.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="b_address" name="b_address" size="32" maxlength="64" value="{$userinfo.b_address|escape}" />
</td>
</tr>
{/if}
 
{if $default_fields.b_address_2.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="b_address_2">{$lng.lbl_address_2}</label></td>
<td{if $default_fields.b_address_2.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="b_address_2" name="b_address_2" size="32" maxlength="64" value="{$userinfo.b_address_2|escape}" />
</td>
</tr>
{/if}
 
{if $default_fields.b_city.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="b_city">{$lng.lbl_city}</label></td>
<td{if $default_fields.b_city.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="b_city" name="b_city" size="32" maxlength="64" value="{$userinfo.b_city|escape}" />
</td>
</tr>
{/if}
 
{if $default_fields.b_county.avail eq 'Y' and $config.General.use_counties eq "Y"}
<tr valign="middle">
<td class="data-name"><label for="b_county">{$lng.lbl_county}</label></td>
<td{if $default_fields.b_county.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
{include file="main/counties.tpl" counties=$counties name="b_county" default=$userinfo.b_county stateid=$userinfo.b_stateid country_name="b_country"}
</td>
</tr>
{/if}

{if $default_fields.b_state.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="b_state">{$lng.lbl_state}</label></td>
<td{if $default_fields.b_state.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
{include file="main/states.tpl" states=$states name="b_state" default=$userinfo.b_state|default:$config.General.default_state default_country=$userinfo.b_country|default:$config.General.default_country country_name="b_country"}
</td>
</tr>
{/if}
 
{if $default_fields.b_country.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="b_country">{$lng.lbl_country}</label></td>
<td{if $default_fields.b_country.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<select id="b_country" name="b_country" onchange="javascript: check_zip_code_field(this, this.form.b_zipcode);">
{section name=country_idx loop=$countries}
<option value="{$countries[country_idx].country_code}"{if $userinfo.b_country eq $countries[country_idx].country_code} selected="selected"{elseif $countries[country_idx].country_code eq $config.General.default_country and $userinfo.b_country eq ""} selected="selected"{/if}>{$countries[country_idx].country}</option>
{/section}
</select>
</td>
</tr>
{/if}

{if $default_fields.b_state.avail eq 'Y' and $default_fields.b_country.avail eq 'Y'}
<tr style="display: none;">
  <td>
{include file="change_states_js.tpl"}
{include file="main/register_states.tpl" state_name="b_state" country_name="b_country" county_name="b_county" state_value=$userinfo.b_state|default:$config.General.default_state county_value=$userinfo.b_county}
  </td>
</tr>
{/if}

{if $default_fields.b_zipcode.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="b_zipcode">{$lng.lbl_zip_code}</label></td>
<td{if $default_fields.b_zipcode.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
{include file="main/zipcode.tpl" val=$userinfo.b_zipcode zip4=$userinfo.b_zip4 id="b_zipcode" name="b_zipcode"}
</td>
</tr>
{/if}
 
{if $default_fields.phone.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="phone">{$lng.lbl_phone}</label></td>
<td{if $default_fields.phone.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="phone" name="phone" size="32" maxlength="32" value="{$userinfo.phone|escape}" />
</td>
</tr>
{/if}
 
{if $default_fields.email.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="email">{$lng.lbl_email}</label></td>
<td{if $default_fields.email.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="email" name="email" size="32" maxlength="128" value="{$userinfo.email|escape}" onchange="javascript: checkEmailAddress(this);" />
</td>
</tr>
{/if}

{if $default_fields.fax.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="fax">{$lng.lbl_fax}</label></td>
<td{if $default_fields.fax.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="fax" name="fax" size="32" maxlength="128" value="{$userinfo.fax|escape}" /></td>
</tr>
{/if}
 
{if $default_fields.url.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="url">{$lng.lbl_web_site}</label></td>
<td{if $default_fields.url.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="url" name="url" size="32" maxlength="128" value="{if $userinfo.url eq ""}http://{else}{$userinfo.url|escape}{/if}" />
</td>
</tr>
{/if}

{foreach from=$additional_fields item=v key=k}
{if $v.avail eq "Y"}
<tr valign="middle">
<td class="FormButton">{$v.title|default:$v.field}</td>
<td>{if $v.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
{if $v.type eq 'T'}
<input type="text" id="additional_values_{$k}" name="additional_values[{$k}]" size="32" value="{$userinfo.additional_values[$k]|escape}" />
{elseif $v.type eq 'C'}
<input type="checkbox" id="additional_values_{$k}" name="additional_values[{$k}]" value="Y"{if $userinfo.additional_values[$k] eq 'Y'} checked="checked"{/if} />
{elseif $v.type eq 'S'}
<select id="additional_values_{$k}" name="additional_values[{$k}]">
{foreach from=$v.variants item=o}
<option value='{$o|escape}'{if $userinfo.additional_values[$k] eq $o} selected="selected"{/if}>{$o|escape}</option>
{/foreach}
</select>
{/if}
</td>
</tr>
{/if}
{/foreach}

{if $default_fields.department.avail eq 'Y'}
<tr valign="middle">
<td class="data-name"><label for="department">{$lng.lbl_department}</label></td>
<td{if $default_fields.department.required eq 'Y'} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap">
<select id="department" name="department">
<option value="All" {if $userinfo.department eq "All" or $userinfo.department eq ""}selected="selected"{/if}>{$lng.lbl_all}</option>
<option value="Partners" {if $userinfo.department eq "Partners"}selected="selected"{/if}>{$lng.lbl_partners}</option>
<option value="Marketing / publicity" {if $userinfo.department eq "Marketing / publicity"}selected="selected"{/if}>{$lng.lbl_marketing_publicity}</option>
<option value="Webdesign" {if $userinfo.department eq "Webdesign"}selected="selected"{/if}>{$lng.lbl_web_design}</option>
<option value="Sales" {if $userinfo.department eq "Sales"}selected="selected"{/if}>{$lng.lbl_sales_department}</option>
</select>
</td>
</tr>
{/if}

<tr valign="middle">
<td class="FormButton">{$lng.lbl_subject}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" id="subject" name="subject" size="32" maxlength="128" value="{$userinfo.subject|escape}" />
</td>
</tr>

<tr valign="middle">
<td class="FormButton">{$lng.lbl_message}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<textarea cols="48" id="message_body" rows="12" name="body">{$userinfo.body}</textarea>
</td>
</tr>

{if $active_modules.Image_Verification and $show_antibot.on_contact_us eq 'Y'}
{include file="modules/Image_Verification/spambot_arrest.tpl" mode="data-table" id=$antibot_sections.on_contact_us antibot_err=$antibot_contactus_err}
{/if}

<tr valign="middle">
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>
<br />
<input type="button" value="{$lng.lbl_submit|strip_tags:false|escape}" onclick="javascript: this.form.submit();" />
</td>
</tr>
</table>
<input type="hidden" name="usertype" value="{$usertype}" />
</form>
{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_contact_us content=$smarty.capture.dialog extra='width="100%"'}
