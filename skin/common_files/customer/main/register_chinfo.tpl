{*
$Id: register_chinfo.tpl,v 1.3 2010/06/08 06:17:39 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $checkout_module eq 'One_Page_Checkout'}
  
  {include file="modules/One_Page_Checkout/payment/ch_info.tpl" hide_header="Y"}

{else}
  
  <tr style="display: none;">
    <td>
<script type="text/javascript">
//<![CDATA[
requiredFields[requiredFields.length] = ['check_name','{$lng.lbl_ch_name|wm_remove|escape:javascript}'];
requiredFields[requiredFields.length] = ['check_ban','{$lng.lbl_ch_bank_account|wm_remove|escape:javascript}'];
requiredFields[requiredFields.length] = ['check_brn','{$lng.lbl_ch_bank_routing|wm_remove|escape:javascript}'];
{if $payment_cc_data.disable_ccinfo eq "N"}
requiredFields[requiredFields.length] = ['check_number','{$lng.lbl_ch_number|wm_remove|escape:javascript}'];
{/if}
//]]>
</script>
    </td>
  </tr>
  
  {if $hide_header ne "Y"}
    <tr>
      <td class="register-section-title" colspan="3">
        <div>
          <a name="chinfo"></a>
          <label>{$lng.lbl_check_information}</label>
        </div>
      </td>
    </tr>
  {/if}
  
  <tr>
    <td class="data-name">{$lng.lbl_ch_name}</td>
    <td class="data-required">*</td>
    <td><input type="text" id="check_name" name="check_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" /></td>
  </tr>
  
  <tr>
    <td class="data-name">{$lng.lbl_ch_bank_account}</td>
    <td class="data-required">*</td>
    <td><input type="text" id="check_ban" name="check_ban" size="32" maxlength="20" value="" /></td>
  </tr>
  
  <tr>
    <td class="data-name">{$lng.lbl_ch_bank_routing}</td>
    <td class="data-required">*</td>
    <td><input type="text" id="check_brn" name="check_brn" size="32" maxlength="20" value="" /></td>
  </tr>
  
  {if $payment_cc_data.disable_ccinfo eq "N"}
  
    <tr>
      <td class="data-name">{$lng.lbl_ch_number}</td>
      <td class="data-required">*</td>
      <td><input type="text" id="check_number" name="check_number" size="32" maxlength="20" value="" /></td>
    </tr>
  
  {/if}

{/if}
