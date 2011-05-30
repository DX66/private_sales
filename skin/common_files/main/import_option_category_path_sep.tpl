{*
$Id: import_option_category_path_sep.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
  <td><b>{$lng.txt_category_path_sep}:</b></td>
</tr>
<tr>
  <td><input type="text" name="options[category_sep]" value="{$import_data.options.category_sep|default:"/"|escape}" /></td>
</tr>
<tr>
  <td>{$lng.txt_category_path_sep_explain}</td>
</tr>
</table>
