{*
$Id: ups_main.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
{if $config.UPS_OnLine_Tools.UPS_username ne "" and $config.UPS_OnLine_Tools.UPS_password ne "" and $config.UPS_OnLine_Tools.UPS_accesskey ne ""}
{assign var="have_account" value="1"}
{else}
{assign var="have_account" value=""}
{/if}
<br />

<form action="ups.php" method="post" name="upsstep0form">
<input type="hidden" name="current_step" value="{$ups_reg_step}" />

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
  <td width="95" align="center" valign="top">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</td>
  <td>&nbsp;</td>
  <td width="100%">
<font class="ProductTitle">{$lng.lbl_ups_online_tools}</font>
<br /><br />
{if $have_account ne ""}
{$lng.txt_you_registered_with_ups}
{else}
{$lng.txt_you_unregistered_with_ups}
{/if}
{if $have_account ne ""}
<br /><br />
{$lng.txt_configure_ups_options}
{/if}

<br /><br />

<div align="right">

<table>

<tr>
{if $have_account ne ""}
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_configure title='' style="button" href="ups.php?mode=rss"}</td>
  <td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
{/if}
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_register title='' style="button" href="javascript: document.upsstep0form.submit()"}</td>
  <td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
</tr>

</table>

</div>

  </td>
</tr>

</table>
</form>

<br />
<hr />

<div align="center">
{$lng.txt_ups_trademark_text}
</div>
{/capture}
{include file="modules/UPS_OnLine_Tools/dialog.tpl" title=$title content=$smarty.capture.dialog extra='width="100%"'}

