{*
$Id: promotions.tpl,v 1.2 2010/07/12 08:26:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<br />
{include file="main/promotion_link.tpl" href="banner_info.php" title=$lng.lbl_banners_statistics promo_note=$lng.txt_partner_promotion_section_banners}
{include file="main/promotion_link.tpl" href="stats.php" title=$lng.lbl_summary_statistics promo_note=$lng.txt_partner_promotion_section_summary}
{include file="main/promotion_link.tpl" href="payment_history.php" title=$lng.lbl_payment_history promo_note=$lng.txt_partner_promotion_section_payments}
{include file="main/promotion_link.tpl" href="partner_banners.php" title=$lng.lbl_banners promo_note=$lng.txt_partner_promotion_section_banner_code}
