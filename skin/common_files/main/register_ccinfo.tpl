{*
$Id: register_ccinfo.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.General.check_cc_number eq "Y" or $smarty.get.mode eq 'checkout' or $action eq "cart"}
  {assign var=cc_req_flag value=true}
{/if}

<tr style="display: none;">
<td>{include file="check_cc_number_script.tpl"}</td>
</tr>

{if $hide_header ne "Y"}
<tr>
<td valign="middle" height="20" colspan="5"><a name="ccinfo"></a><font class="RegSectionTitle">{$lng.lbl_credit_card_information}</font><hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr>
<td class="data-name" valign="middle" align="right">{if $hide_header eq 'Y'}<a name="ccinfo"></a>{/if}<label for="card_type">{$lng.lbl_cc_type}</label></td>
<td{if $cc_req_flag} class="data-required">*{else}>&nbsp;{/if}</td>
<td nowrap="nowrap" colspan="3">
{if #safeCCNum# eq ""}
<select name="card_type" id="card_type" onchange="javascript: markCVV2(this)">
{section name=card_type loop=$card_types}
<option value="{$card_types[card_type].code}"{if $userinfo.card_type eq $card_types[card_type].code} selected="selected"{/if}>{$card_types[card_type].type}</option>
{/section}
</select>
{else}
{#safeCCType#}
<input type="hidden" name="card_type" id="card_type" value="{#safeCCType#}" />
{/if}
</td>
</tr>

<tr>
<td class="data-name" valign="middle" align="right"><label for="card_name">{$lng.lbl_cc_name_explanation}</td>
<td{if $cc_req_flag} class="data-required">*{else}>&nbsp;{/if}</font></td>
<td nowrap="nowrap" colspan="3">
{if #safeCCNum# eq ""}
{if $userinfo.b_firstname ne ''}{assign var="card_firstname" value=$userinfo.b_firstname}{else}{assign var="card_firstname" value=$userinfo.firstname}{/if}
{if $userinfo.b_lastname ne ''}{assign var="card_lastname" value=$userinfo.b_lastname}{else}{assign var="card_lastname" value=$userinfo.lastname}{/if}
<input type="text" name="card_name" id="card_name" size="32" maxlength="50" value="{if $userinfo.card_name ne ""}{$userinfo.card_name|escape}{else}{$card_firstname|escape}{if $card_firstname ne ''} {/if}{$card_lastname|escape}{/if}" />
{else}
{#safeCCName#}
<input type="hidden" name="card_name" id="card_name" value="{#safeCCName#}" />
{/if}
</td>
</tr>

<tr>
<td class="data-name" valign="middle" align="right"><label for="card_number">{$lng.lbl_cc_number_explanation}</label></td>
<td{if $cc_req_flag} class="data-required">*{else}>&nbsp;{/if}</font></td>
<td nowrap="nowrap" colspan="3">
{if #safeCCNum# eq ""}
<input type="text" name="card_number" id="card_number" size="32" maxlength="20" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_number}{/if}" />
{else}
{#safeCCNum#}
<input type="hidden" name="card_number" id="card_number" value="{#safeCCNum#}" />
{/if}
</td>
</tr>

{if $config.General.uk_oriented_ccinfo eq "Y" or $force_uk_ccinfo}
<tr>
<td class="data-name" valign="middle" align="right"><label for="card_valid_from_">{$lng.lbl_cc_validfrom}</td>
<td{if $cc_req_flag} class="data-required">*{else}>&nbsp;{/if}</font></td>
<td nowrap="nowrap" colspan="3">
{html_select_date prefix="card_valid_from_" display_days=false start_year="-5" month_format="%m" time=$userinfo.card_valid_from_time}
</td>
</tr>
{/if}

<tr>
<td class="data-name" valign="middle" align="right"><label for="card_expire">{$lng.lbl_cc_expiration}</label></td>
<td{if $cc_req_flag} class="data-required">*{else}>&nbsp;{/if}</font></td>
<td nowrap="nowrap" colspan="3">
{if #safeCCNum# eq ""}
{html_select_date prefix="card_expire_" display_days=false end_year="+10" month_format="%m" time=$userinfo.card_expire_time}
{else}
{#safeCCExp#}
<input type="hidden" name="card_expire" id="card_expire" value="{#safeCCExp#}" />
{/if}
</td>
</tr>

{if $payment_cc_data.disable_ccinfo eq "N" or ($payment_cc_data.disable_ccinfo eq "" and $config.General.enable_manual_cc_cvv2 eq 'Y')}
<tr>
<td class="data-name" valign="middle" align="right"><label for="card_cvv2">{$lng.lbl_cc_cvv2}</label></td>
<td{if $cc_req_flag} class="data-required"{/if}><span id="cvv2_star">{if $cc_req_flag}*{else}&nbsp;{/if}</span></td>
<td nowrap="nowrap" colspan="3">
{if #safeCCNum# eq ""}
<table cellspacing="0" cellpadding="0" border="0">
<tr>
  <td valign="middle"><input type="text" name="card_cvv2" size="4" maxlength="4" value="{if $smarty.get.err eq 'fields'}{$userinfo.card_cvv2}{/if}" />&nbsp;</td>
  <td valign="middle">{include file="main/popup_help_link.tpl" section="cvv2"}</td>
  {if $smarty.get.err eq 'fields' and $userinfo.card_cvv2 eq ''}
  <td><font class="Star">&lt;&lt;</font></td>
  {/if}
</tr>
</table>
{else}
{#safeCCcvv2#}
<input type="hidden" name="card_cvv2" value="{#safeCCcvv2#}" />
{/if}
</td>
</tr>
{/if}

{if $config.General.uk_oriented_ccinfo eq "Y" or $force_uk_ccinfo}
<tr>
<td class="data-name" valign="middle" align="right"><label for="card_issue_no">{$lng.lbl_cc_issueno}</label></td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" name="card_issue_no" id="card_issue_no" size="4" maxlength="2" value="" />
</td>
<td colspan="2">{$lng.lbl_cc_leave_empty}</td>
</tr>
{/if}
