{*
$Id: export_option_export_images.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
  <td><b>{$lng.lbl_do_you_wish_to_export_images}</b></td>
</tr>
<tr>
  <td><select name="options[export_images]">
  <option value="Y"{if $export_data.export_images eq 'Y' or $export_data eq ''} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value=""{if $export_data.export_images eq '' and $export_data} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select></td>
</tr>
</table>
