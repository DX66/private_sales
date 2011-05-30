{*
$Id: import_option_password_crypt.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
  <td><b>{$lng.lbl_imported_customer_passwords_encrypted}:</b></td>
</tr>
<tr>
  <td><select name="options[crypt_password]">
  <option value="Y"{if $import_data.options.crypt_password eq 'Y' or $import_data eq ''} selected="selected"{/if}>{$lng.lbl_yes}</option>
  <option value=""{if $import_data.options.crypt_password eq '' and $import_data ne ''} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select ></td>
</tr>
</table>
