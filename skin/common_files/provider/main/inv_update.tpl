{*
$Id: inv_update.tpl,v 1.2 2010/07/19 07:09:21 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_update_inventory}

<form method="post" action="inv_update.php" enctype="multipart/form-data">

<table cellpadding="0" cellspacing="4" width="100%">

<tr>
  <td>{$lng.lbl_update}</td>
  <td>
  <select name="what">
     <option value="p" selected="selected">{$lng.lbl_pricing}</option>
     <option value="q" selected="selected">{$lng.lbl_in_stock}</option>
  </select>
  </td>
</tr>
<tr>
  <td>{$lng.lbl_csv_delimiter}</td>
  <td>{include file="provider/main/ie_delimiter.tpl"}</td>
</tr>
<tr>
  <td>{$lng.lbl_csv_file}</td>
  <td><input type="file" name="userfile" />
{if $upload_max_filesize}
<br /><font class="Star">{$lng.lbl_warning}!</font> {$lng.txt_max_file_size_that_can_be_uploaded}: {$upload_max_filesize}b.
{/if} 
  </td>
</tr>

<tr>
  <td colspan="2" class="main-button">
    <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  </td>
</tr>

</table>
</form>
