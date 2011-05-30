{*
$Id: cc_skipjack.tpl,v 1.2 2010/08/04 07:15:55 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>SkipJack</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />

{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
  <td>{$lng.lbl_cc_skipjack_hsnum}:</td>
  <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" maxlength="12" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_skipjack_dev_hsnum}:</td>
  <td><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" maxlength="12" /></td>
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
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td><input type="text" name="param03" size="32" value="{$module_data.param03|escape}" /></td>
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

</table>

<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
