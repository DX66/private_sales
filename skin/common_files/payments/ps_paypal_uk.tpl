{*
$Id: ps_paypal_uk.tpl,v 1.1 2010/05/21 08:32:53 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="10">

  <tr>
    <td>{$lng.lbl_paypal_api_vendor}:</td>
    <td><input type="text" name="{$conf_prefix}[param01]" size="24" value="{$module_data.param01|escape}" /></td>
  </tr>

  <tr>
    <td>{$lng.lbl_paypal_api_partner}:</td>
    <td><input type="text" name="{$conf_prefix}[param02]" size="24" value="{$module_data.param02|escape}" /></td>
  </tr>

  <tr>
    <td>{$lng.lbl_paypal_api_user}:</td>
    <td><input type="text" name="{$conf_prefix}[param04]" size="24" value="{$module_data.param04|escape}" /></td>
  </tr>

  <tr>
    <td>{$lng.lbl_paypal_api_password}:</td>
    <td><input type="password" name="{$conf_prefix}[param05]" size="24" value="{$module_data.param05|escape}" /></td>
  </tr>

  <tr>
    <td>{$lng.txt_paypal_uk_reqconfirmshipping}:</td>
    <td>
      <select name="{$conf_prefix}[param07]">
        <option value="0">{$lng.lbl_no}</option>
        <option value="1"{if $module_data.param07 eq '1'} selected="selected"{/if}>{$lng.lbl_yes}</option>
      </select>
    </td>
  </tr>

  <tr>
    <td>{$lng.txt_paypal_uk_page_style}:</td>
    <td><input type="text" name="{$conf_prefix}[param08]" size="24" maxlength="30" value="{$module_data.param08|escape}" /></td>
  </tr>

  <tr>
    <td>{$lng.txt_paypal_uk_header_image_url}:</td>
    <td><input type="text" name="{$conf_prefix}[param09]" size="24" maxlength="127" value="{$module_data.param09|escape}" /></td>
  </tr>

  <tr>
    <td>{$lng.lbl_cc_currency}:</td>
    <td>
      <select name="{$conf_prefix}[param03]">
        <option value="USD"{if $module_data.param03 eq "USD"} selected="selected"{/if}>US Dollar (United States)</option>
        <option value="EUR"{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euro (Europe)</option>
        <option value="GBP"{if $module_data.param03 eq "GBP"} selected="selected"{/if}>Pound Sterling (United Kingdom)</option>
        <option value="CAD"{if $module_data.param03 eq "CAD"} selected="selected"{/if}>Canadian Dollar (Canada)</option>
        <option value="JPY"{if $module_data.param03 eq "JPY"} selected="selected"{/if}>Yen (Japan)</option>
        <option value="AUD"{if $module_data.param03 eq "AUD"} selected="selected"{/if}>Australian Dollar (Australia)</option>
      </select>
    </td>
  </tr>

  <tr>
    <td>{$lng.lbl_cc_testlive_mode}:</td>
    <td>
      <select name="{$conf_prefix}[testmode]">
        <option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
        <option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
      </select>
      <br />
      <font class="SmallText">{$lng.lbl_paypal_test_mode_note}</font>
    </td>
  </tr>

  <tr>
    <td>{$lng.lbl_use_preauth_method}:</td>
    <td>
      <select name="{$conf_prefix}[use_preauth]">
        <option value="">{$lng.lbl_auth_and_capture_method}</option>
        <option value="Y"{if $module_data.use_preauth eq 'Y'} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
      </select>
    </td>
  </tr>

  <tr>
    <td>{$lng.lbl_cc_order_prefix}:</td>
    <td><input type="text" name="{$conf_prefix}[param06]" size="36" value="{$module_data.param06|escape}" /></td>
  </tr>

  {if $xpc_dp_processors and $xpc_dp_processors.uk}
    {include file="modules/XPayments_Connector/paypal_section.tpl" xpc_data=$xpc_dp_processors.uk}
  {/if}

</table>
