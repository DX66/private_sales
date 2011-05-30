{*
$Id: cc_nab_transact.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}

<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_mode}:</b></td>
  <td>
    <select name="testmode">
      <option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
      <option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
    </select>
  </td>
</tr>

<tr>
  <td colspan="2"><font class="AdminSmallMessage">{$lng.txt_cc_nab_transact_hpp_mode_warn}</font><br />{$lng.txt_cc_nab_transact_hpp_mode_explain}<hr /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_clientid}:</b><br />{$lng.txt_cc_nab_transact_hpp_clientid_explain}</td>
  <td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_payment_alert_email}:</b><br />{$lng.txt_cc_nab_transact_payment_alert_explain}</td>
  <td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_display_customer_details}:</b><br />{$lng.txt_cc_nab_transact_display_customer_details_explain}</td>
  <td>
    <select name="param02">
      <option value="Y"{if $module_data.param02 eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
      <option value="N"{if $module_data.param02 eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
    </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_display_shipping_details}:</b><br />{$lng.txt_cc_nab_transact_display_shipping_details_explain}</td>
  <td>
    <select name="param03">
      <option value="Y"{if $module_data.param03 eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
      <option value="N"{if $module_data.param03 eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
    </select>
  </td>
</tr>

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_display_customer_comments}:</b><br />{$lng.txt_cc_nab_transact_display_comments_explain}</td>
  <td>
    <select name="param04">
      <option value="Y"{if $module_data.param04 eq "Y"} selected="selected"{/if}>{$lng.lbl_yes}</option>
      <option value="N"{if $module_data.param04 eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
    </select>
  </td>
</tr>

<tr>
  <td colspan="2" class="SubHeader">{$lng.lbl_cc_nab_transact_hpp_advanced_config}</td>
</tr>

<tr>
  <td colspan="2" class="SubHeaderLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>

<tr>
  <td colspan="2"><font class="AdminSmallMessage">{$lng.txt_cc_nab_transact_hpp_warn_not_to_modify}</font></td>
</tr>

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_test_url}:</b><br />{$lng.txt_cc_nab_transact_test_url_explain}</td>
  <td><input type="text" name="param08" size="32" value="{$module_data.param08|escape}" /></td>
</tr>

<tr>
  <td><b>{$lng.lbl_cc_nab_transact_hpp_live_url}:</b><br />{$lng.txt_cc_nab_transact_live_url_explain}</td>
  <td><input type="text" name="param09" size="32" value="{$module_data.param09|escape}" /></td>
</tr>

</table>

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />

</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
