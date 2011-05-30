{*
$Id: ch_authorizenet.tpl,v 1.1 2010/05/21 08:32:03 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="0" class="data-table">
{if $hide_header ne "Y"}
  <tr>
    <td class="register-section-title" colspan="3">
      <a name="chinfo"></a>
      <label>{$lng.lbl_check_information}</label>
    </td>
  </tr>
{/if}

<tr>
  <td class="data-name">{$lng.lbl_ch_name}</td>
  <td class="data-required">*</td>
  <td><input type="text" name="check_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" /></td>
</tr>

<tr>
  <td class="data-name">{$lng.lbl_ch_bank_name}</td>
  <td class="data-required">*</td>
  <td><input type="text" name="check_bname" size="32" maxlength="50" value="" /></td>
</tr>

<tr>
  <td class="data-name">{$lng.lbl_ch_bank_account}</td>
  <td class="data-required">*</td>
  <td><input type="text" name="check_ban" size="32" maxlength="20" value="" /></td>
</tr>

<tr>
  <td class="data-name">{$lng.lbl_ch_bank_routing}</td>
  <td class="data-required">*</td>
  <td><input type="text" name="check_brn" size="32" maxlength="20" value="" /></td>
</tr>

{if $payment_cc_data.param07 eq 'W'}

  <tr>
    <td class="data-name">{$lng.lbl_ch_org_type}</td>
    <td class="data-required">*</td>
    <td>
      <select name="check_wf_org_type">
        <option value='I'>{$lng.lbl_ch_individual}</option>
        <option value='B'>{$lng.lbl_ch_business}</option>
      </select>
    </td>
  </tr>

  <tr>
    <td class="data-name">{$lng.lbl_ssn}</td>
    <td class="data-required">*</td>
    <td><input type="text" name="check_wf_ssn" size="32" maxlength="9" value="{$userinfo.ssn|escape}" /></td>
  </tr>

  <tr>
    <td align="right" colspan="3">{$lng.txt_if_unknown_ssn}:</td>
  </tr>

  <tr>
    <td class="data-name">{$lng.lbl_ch_driver_license_num}</td>
    <td>&nbsp;</td>
    <td><input type="text" name="check_wf_dln" size="32" maxlength="50" value="" /></td>
  </tr>

  <tr>
    <td class="data-name">{$lng.lbl_ch_driver_license_state}</td>
    <td>&nbsp;</td>
    <td><input type="text" name="check_wf_dls" size="3" maxlength="2" value="" /></td> 
  </tr> 

  <tr>
    <td class="data-name">{$lng.lbl_ch_driver_license_dob}</td>
    <td>&nbsp;</td>
    <td><input type="text" name="check_wf_dldob" size="32" maxlength="15" value="" /></td> 
  </tr>

{/if}
</table>
