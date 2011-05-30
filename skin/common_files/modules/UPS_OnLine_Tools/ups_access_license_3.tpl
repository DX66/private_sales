{*
$Id: ups_access_license_3.tpl,v 1.1 2010/05/21 08:32:50 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
<!-- ACCESS LICENSE REQUEST SCREEN START -->
<br />

<form action="ups.php" method="post" name="upsstep3form">
<input type="hidden" name="current_step" value="{$ups_reg_step}" />

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
  <td colspan="2">&nbsp;</td>
  <td>
{if $message eq "fillerror"}
<font class="ErrorMessage">{$lng.txt_ups_reg_error}</font>
<br />
{assign var="reg_error" value=1}
{elseif $message ne ""}
<font class="ErrorMessage">{$message}</font>
<br />
{/if}
  </td>
</tr>

<tr>
  <td width="95" align="center" valign="top">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</td>
  <td>&nbsp;</td>
  <td width="100%">

{include file="modules/UPS_OnLine_Tools/ups_regform.tpl"}

<br />

{$lng.txt_fill_regform_from_profile}

<br /><br />

<div align="right">

<table>

<tr>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_fill_from_profile title='' style="button" href="ups.php?mode=fillform"}</td>
  <td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_next title='' style="button" href="javascript: if (checkEmailAddress(document.upsstep3form.email)) document.upsstep3form.submit()"}</td>
  <td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
  <td>{include file="buttons/button.tpl" button_title=$lng.lbl_cancel title='' style="button" href="ups.php?mode=cancel"}</td>
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
<!-- ACCESS LICENSE REQUEST SCREEN END -->
{/capture}
{include file="modules/UPS_OnLine_Tools/dialog.tpl" title=$title content=$smarty.capture.dialog extra='width="100%"'}

