{*
$Id: import_option_images_directory.tpl,v 1.1 2010/05/21 08:32:17 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
  <td><b>{$lng.txt_directory_where_images_are_located}:</b></td>
</tr>
<tr>
  <td><input type="text" size="55" name="options[images_directory]" value="{$import_data.options.images_directory|default:""}" /></td>
</tr>
<tr>
  <td>{$lng.txt_directory_where_images_are_located_expl|substitute:"my_files_location":$my_files_location}</td>
</tr>
</table>
