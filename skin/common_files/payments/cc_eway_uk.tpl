{*
$Id: cc_eway_uk.tpl,v 1.1 2010/05/21 08:32:52 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>{$module_data.module_name}</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10" width="100%">

<tr>
  <td width="40%">{$lng.lbl_cc_eway_customerid}:</td>
  <td width="60%"><input type="text" name="param01" size="32" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
  <td width="40%">{$lng.lbl_cc_eway_username}:</td>
  <td width="60%"><input type="text" name="param02" size="32" value="{$module_data.param02|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_currency}:</td>
  <td>
    <select name="param03">
      {foreach from=$currencies item=c}
        <option value="{$c.code}"{if $module_data.param03 eq $c.code} selected="selected"{/if}>{$c.name} ({$c.code})</option>
      {/foreach}
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_language}:</td>
  <td>
    <select name="param04">
      <option value="EN"{if $module_data.param04 eq "EN"} selected="selected"{/if}>English</option>
      <option value="ES"{if $module_data.param04 eq "ES"} selected="selected"{/if}>Spanish</option>
      <option value="FR"{if $module_data.param04 eq "FR"} selected="selected"{/if}>French</option>
      <option value="DE"{if $module_data.param04 eq "DE"} selected="selected"{/if}>German</option>
      <option value="NL"{if $module_data.param04 eq "NL"} selected="selected"{/if}>Dutch</option>
    </select>
  </td>
</tr>

<tr>
  <td>{$lng.lbl_cc_order_prefix}:</td>
  <td><input type="text" name="param05" size="32" value="{$module_data.param05|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_eway_page_title}:</td>
  <td><input type="text" name="param06" size="32" value="{$module_data.param06|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_eway_page_descr}:</td>
  <td><input type="text" name="param07" size="32" value="{$module_data.param07|escape}" /></td>
</tr>

<tr>
  <td>{$lng.lbl_cc_eway_page_footer}:</td>
  <td><input type="text" name="param08" size="32" value="{$module_data.param08|escape}" /></td>
</tr>

</table>

<br /><br />

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}
