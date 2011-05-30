{*
$Id: module_disabled.tpl,v 1.1 2010/05/21 08:32:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<h3><font color="red">{$lng.lbl_partner_area_is_temporary_disabled}</font></h3>
<p align="justify">
{$lng.txt_partner_area_is_temporary_disabled_note|substitute:"email":$config.Company.users_department}
</p>
