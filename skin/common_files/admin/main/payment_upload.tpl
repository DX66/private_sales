{*
$Id: payment_upload.tpl,v 1.1 2010/05/21 08:31:59 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_payment_upload}
{$lng.txt_payment_upload_note}<br />
{$lng.txt_payment_upload_example}<br /><br />

<br />

{capture name=dialog}
<form method="post" action="payment_upload.php" enctype="multipart/form-data">
<input type="hidden" name="mode" value="upload" />

<table cellpadding="0" cellspacing="5" width="100%">
<tr>
  <td width="20%"><b>{$lng.lbl_csv_delimiter}:</b></td>
  <td width="80%">{include file="provider/main/ie_delimiter.tpl"}</td>
</tr>
<tr>
  <td width="20%"><b>{$lng.lbl_csv_file}:</b></td>
  <td width="80%"><input type="file" name="userfile" /></td>
</tr>

<tr>
  <td colspan="2" class="SubmitBox"><input type="submit" value="{$lng.lbl_upload|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_payment_upload extra='width="100%"'}
