{*
$Id: cc_payflow_pro.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>PayFlow Pro</h1>

{$lng.txt_cc_configure_top_text}

<br /><br />

{capture name=dialog}

<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
  <td>{$lng.lbl_cc_payflow_pro_merchantuser}:</td>
  <td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_payflow_pro_merchantpass}:</td>
  <td><input type="password" name="param04" size="24" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_payflow_pro_vendor}:</td>
  <td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_payflow_pro_partner}:</td>
  <td><input type="text" name="param03" size="24" value="{$module_data.param03|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_payflow_pro_fps}:</td>
  <td>
    <select name="param06">
      <option value="N">{$lng.lbl_no}</option>
      <option value="Y"{if $module_data.param06 eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td><input type="text" name="param05" size="24" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_testlive_mode}:</td>
  <td>
    <select name="testmode">
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
      <option value="Y"{if $module_data.use_preauth eq "Y"} selected="selected"{/if}>{$lng.lbl_auth_method}</option>
    </select>
  </td>
</tr>

<tr>
  <td colspan="2">
    <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

</table>

</form>

<br /><br />

{/capture}

{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
