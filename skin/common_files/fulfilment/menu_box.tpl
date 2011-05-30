{*
$Id: menu_box.tpl,v 1.1 2010/05/21 08:32:05 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul id="horizontal-menu">

<li>
<a href="{$catalogs.admin}/orders.php">{$lng.lbl_orders}</a>
</li>

<li>
<a href="{$catalogs.admin}/import.php?mode=export">{$lng.lbl_export_data}</a>
</li>

<li>
<a href="{$catalogs.admin}/statistics.php">{$lng.lbl_statistics}</a>
</li>

</ul>
