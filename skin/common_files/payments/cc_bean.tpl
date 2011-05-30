{*
$Id: cc_bean.tpl,v 1.1.2.1 2011/01/25 15:24:54 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">
<table cellspacing="10">
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td>{$lng.lbl_cc_bean_merchantid}:</td>
<td><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="32" value="{$module_data.param04|escape}" /></td>
</tr>
<tr>
<td>{$lng.lbl_enable_cvv2}:</td>
<td>
  <select name="disable_ccinfo">
    <option value="N"{if $module_data.disable_ccinfo eq "N"} selected="selected"{/if}>{$lng.lbl_yes}</option>
    <option value="C"{if $module_data.disable_ccinfo eq "C"} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select>
{include file="main/tooltip_js.tpl" text=$lng.txt_what_is_cvv2 type="img" id="what_is_cvv" width='500'}{include file="main/tooltip_js.tpl" text=$lng.lbl_cc_bean_note wrapper_tag='div' width='500' type='img' sticky=true id="CVD_check"}</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" /></td>
</tr>
</table>
</form>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
