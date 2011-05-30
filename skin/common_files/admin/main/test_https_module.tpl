{*
$Id: test_https_module.tpl,v 1.1 2010/05/21 08:32:00 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{$lng.txt_test_https_module_descr}

<a href="{$catalogs.admin}/configuration.php" title="{$lng.lbl_general_settings|escape}" target="_blank">{$lng.lbl_click_here_to_change} &gt;&gt;</a>
<br /><br />

{capture name="dialog"}

<form action="general.php">
<input type="hidden" name="mode" value="test_https_module" />

<table>

<tr>
  <td>{$lng.lbl_url}</td>
  <td>
  <input type="text" name="url" value="{$url}" size="60" />
  &nbsp;
  <input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" />
  </td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_https_module_test_params extra='width="100%"'}
<br /><br />
{if $headers_data ne "" or $response_data ne ""}
{capture name="dialog"}
<pre>
{$headers_data|escape}
<hr />
{$response_data|escape}
</pre>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_https_module_response extra='width="100%"'}
{/if}
