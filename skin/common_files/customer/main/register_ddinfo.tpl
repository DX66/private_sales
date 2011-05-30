{*
$Id: register_ddinfo.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $hide_header ne "Y"}

  <tr>
    <td class="register-section-title" colspan="3">
      <div>
        <label>{$lng.lbl_check_information}</label>
      </div>
    </td>
  </tr>

{/if}

  <tr style="display: none;">
    <td>
<script type="text/javascript">
//<![CDATA[
requiredFields[requiredFields.length] = ['debit_name','{$lng.lbl_ch_name|wm_remove|escape:javascript}'];
requiredFields[requiredFields.length] = ['debit_bank_account','{$lng.lbl_ch_bank_account|wm_remove|escape:javascript}'];
requiredFields[requiredFields.length] = ['debit_bank_number','{$lng.lbl_ch_bank_routing|wm_remove|escape:javascript}'];
//]]>
</script>
    </td>
  </tr>

  <tr>
    <td class="data-name">{$lng.lbl_ch_name}</td>
    <td class="data-required">*</td>
    <td><input type="text" id="debit_name" name="debit_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" /></td>
  </tr>

  <tr>
    <td class="data-name">{$lng.lbl_ch_bank_account}</td>
    <td class="data-required">*</td>
    <td><input type="text" id="debit_bank_account" name="debit_bank_account" size="32" maxlength="20" value="" /></td>
  </tr>

  <tr>
    <td class="data-name">{$lng.lbl_ch_bank_routing}</td>
    <td class="data-required">*</td>
    <td><input type="text" id="debit_bank_number" name="debit_bank_number"  size="32" maxlength="20" value="" /></td>
  </tr>

  <tr>
    <td class="data-name">{$lng.lbl_ch_bank_name}</td>
    <td class="data-required">&nbsp;</td>
    <td><input type="text" id="debit_bank_name" name="debit_bank_name" size="32" maxlength="20" value="" /></td>
  </tr>
