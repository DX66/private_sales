{*
$Id: register_ddinfo.tpl,v 1.1 2010/05/21 08:32:18 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $hide_header ne "Y"}
<tr valign="middle">
<td height="20" colspan="3"><font class="RegSectionTitle">{$lng.lbl_check_information}</font><hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr valign="middle">
<td align="right"><label for="debit_name">{$lng.lbl_ch_name}</label></td>
<td class="data-required">*</td>
<td nowrap="nowrap">
<input type="text" id="debit_name" name="debit_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" />
</td>
</tr>

<tr valign="middle">
<td align="right"><label for="debit_bank_account">{$lng.lbl_ch_bank_account}</label></td>
<td class="data-required">*</td>
<td nowrap="nowrap">
<input type="text" id="debit_bank_account" name="debit_bank_account" size="32" maxlength="20" value="" />
</td>
</tr>

<tr valign="middle">
<td align="right"><label for="debit_bank_number">{$lng.lbl_ch_bank_routing}</label></td>
<td class="data-required">*</td>
<td nowrap="nowrap">
<input type="text" id="debit_bank_number" name="debit_bank_number" size="32" maxlength="20" value="" />
</td>
</tr>

<tr valign="middle">
<td align="right"><label for="debit_bank_name">{$lng.lbl_ch_bank_name}</label></td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" id="debit_bank_name" name="debit_bank_name" size="32" maxlength="20" value="" />
</td>
</tr>
