{*
$Id: ups_access_license_4.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
<!-- REGISTRATION RESPONSE SCREEN START -->
<br />

<form action="ups.php" method="post" name="upsstep4form">
<input type="hidden" name="current_step" value="{$ups_reg_step}" />

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
  <td width="95" align="center" valign="top">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</td>
  <td>&nbsp;</td>
  <td width="100%">
<font class="ProductTitle">{$lng.lbl_ups_reg_success}</font>
<br /><br />

{$lng.txt_ups_reg_success}

<br /><br /><br />

<div align="right">
<table>
<tr>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_finish title='' style="button" href="javascript: document.upsstep4form.submit();"}</td>
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
<!-- REGISTRATION RESPONSE SCREEN END -->
{/capture}
{include file="modules/UPS_OnLine_Tools/dialog.tpl" title=$title content=$smarty.capture.dialog extra='width="100%"'}

