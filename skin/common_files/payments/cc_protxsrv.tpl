{*
$Id: cc_protxsrv.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>

<img src="{$ImagesDir}/sage_pay.jpg" width="150" height="70" alt="Sage Pay logo logo" align="right" style="padding-left: 10px;" />
{$lng.txt_cc_configure_top_text}
<br /><br />
<input type="button" name="protx_signup" value="{$lng.lbl_cc_protx_signup}" onclick="javascript: window.open('https://support.sagepay.com/apply/default.aspx?PartnerID={ldelim}653E8C42-AD93-4654-BB91-C645678FA97B{rdelim}');" />
<br /><br />

{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10" align="center">
  <tr>
    <td>{$lng.lbl_cc_protx_vendorname}:</td>
    <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
  </tr>

  <tr>
    <td>{$lng.lbl_cc_currency}:</td>
    <td>
      <select name="param03">
        <option value="GBP"{if $module_data.param03 eq "GBP"} selected="selected"{/if}>British Pound</option>
        <option value="EUR"{if $module_data.param03 eq "EUR"} selected="selected"{/if}>Euro</option>
        <option value="USD"{if $module_data.param03 eq "USD"} selected="selected"{/if}>US Dollar</option>
      </select>
    </td>
  </tr>

  <tr>
    <td>{$lng.lbl_cc_protx_avscv2}:</td>
    <td>
      <select name="param06">
        <option value="0"{if $module_data.param06 eq "0"} selected="selected"{/if}>If AVS/CV2 enabled then check them. If rules apply, use rules.</option>
        <option value="1"{if $module_data.param06 eq "1"} selected="selected"{/if}>Force AVS/CV2 checks even if not enabled for the account. If rules apply, use rules.</option>
        <option value="2"{if $module_data.param06 eq "2"} selected="selected"{/if}>Force NO AVS/CV2 checks even if enabled on account.</option>
        <option value="3"{if $module_data.param06 eq "3"} selected="selected"{/if}>Force AVS/CV2 checks even if not enabled for the account but DONT apply any rules.</option>
      </select>
    </td>
  </tr>

  <tr>
    <td>{$lng.lbl_cc_protx_3dsecure}:</td>
    <td>
      <select name="param07">
        <option value="0"{if $module_data.param07 eq "0"} selected="selected"{/if}>If 3D-Secure checks are possible and rules allow, perform the checks and apply the authorisation rules.</option>
        <option value="1"{if $module_data.param07 eq "1"} selected="selected"{/if}>Force 3D-Secure checks for this transaction if possible and apply rules for authorisation.</option>
        <option value="2"{if $module_data.param07 eq "2"} selected="selected"{/if}>Do not perform 3D-Secure checks for this transaction and always authorise.</option>
        <option value="3"{if $module_data.param07 eq "3"} selected="selected"{/if}>Force 3D-Secure checks for this transaction if possible but ALWAYS obtain an auth code, irrespective of rule base.</option>
      </select>
    </td>
  </tr>
  <tr>
    <td>{$lng.lbl_cc_testlive_mode}:</td>
    <td>
      <select name="testmode">
        <option value="S"{if $module_data.testmode eq "S"} selected="selected"{/if}>{$lng.lbl_cc_testlive_simulator}</option>
        <option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
        <option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
      </select>
    </td>
  </tr>

  <tr>
    <td>{$lng.lbl_use_preauth_method}:</td>
    <td>
      <select name="use_preauth">
        <option value="">{$lng.lbl_auth_and_capture_method}</option>
        <option value="Y"{if $module_data.use_preauth eq 'Y'} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
      </select>
    </td>
  </tr>

  <tr>
    <td>{$lng.lbl_cc_order_prefix}:</td>
    <td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /><br />
    {$lng.lbl_cc_protx_ordpref_note}
    </td>
  </tr>
</table>
<br />

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
