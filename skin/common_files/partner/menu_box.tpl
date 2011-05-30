{*
$Id: menu_box.tpl,v 1.1 2010/05/21 08:32:51 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul id="horizontal-menu">

<li>
<a href="home.php">{$lng.lbl_dashboard}</a>
</li>

<li>
<a href="banner_info.php">{$lng.lbl_banners_statistics}</a>
</li>

<li>
<a href="referred_sales.php">{$lng.lbl_referred_sales}</a>
</li>

<li>
<a href="stats.php">{$lng.lbl_summary_statistics}</a>
</li>

<li>
<a href="payment_history.php">{$lng.lbl_payment_history}</a>
</li>

<li>
<a href="partner_banners.php">{$lng.lbl_banners}</a>
</li>

<li>
<a href="affiliates.php">{$lng.lbl_affiliates_tree}</a>
</li>

{include file="admin/help.tpl"}

</ul>
