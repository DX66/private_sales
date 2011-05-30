{*
$Id: pconf_help.tpl,v 1.1 2010/05/21 08:32:46 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=dialog}
<div class="ProductTitle">{$lng.lbl_pconf_about_module}</div>
<br />
<p align="justify">
{$lng.txt_pconf_about_module}
</p>
<p align="justify">
<b>{$lng.lbl_pconf_about_conf_steps_slots}</b>
</p>
{$lng.txt_pconf_about_conf_steps_slots}
<p align="justify">
<b>{$lng.lbl_pconf_about_product_types}</b>
</p>
{$lng.txt_pconf_about_product_types}
<p align="justify">
<b>{$lng.lbl_pconf_about_product_specifications}</b>
</p>
{$lng.txt_pconf_about_product_specifications}
<p align="justify">
<b>{$lng.lbl_pconf_about_bundled_products}</b>
</p>
{$lng.txt_pconf_about_bundled_products}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_pconf_about_module content=$smarty.capture.dialog extra='width="100%"'}
