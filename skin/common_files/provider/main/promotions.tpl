{*
$Id: promotions.tpl,v 1.2 2010/07/12 08:26:15 joy Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{if $root_warning}
  {capture name=dialog}
    {$root_warning}
  {/capture}
  {include file="location.tpl" location="" alt_content=$smarty.capture.dialog extra='width="100%"' newid="root_warning" alt_type="W"}
{/if}
<br />
{include file="main/promotion_link.tpl" title=$lng.lbl_add_new_product href="`$catalogs.provider`/product_modify.php" promo_note=$lng.txt_provider_promotion_add_new_product_note}
{include file="main/promotion_link.tpl" title=$lng.lbl_product_modify href="`$catalogs.provider`/search.php" promo_note=$lng.txt_provider_promotion_modify_product_note}
{if $active_modules.Extra_Fields ne ""}
  {include file="main/promotion_link.tpl" title=$lng.lbl_extra_fields href="`$catalogs.provider`/extra_fields.php" promo_note=$lng.lbl_provider_promotion_ef_note}
{/if}
{include file="main/promotion_link.tpl" title=$lng.lbl_shipping_charges href="`$catalogs.provider`/shipping_rates.php" promo_note=$lng.txt_provider_promotion_sc_note}
{include file="main/promotion_link.tpl" title=$lng.lbl_destination_zones href="`$catalogs.provider`/zones.php" promo_note=$lng.txt_provider_promotion_dz_note}
{include file="main/promotion_link.tpl" title=$lng.lbl_discounts href="`$catalogs.provider`/discounts.php" promo_note=$lng.txt_provider_promotion_discounts_note}
{include file="main/promotion_link.tpl" title=$lng.lbl_coupons href="`$catalogs.provider`/coupons.php" promo_note=$lng.txt_provider_promotion_coupons_note}
{include file="main/promotion_link.tpl" title=$lng.lbl_tax_rates href="`$catalogs.provider`/taxes.php" promo_note=$lng.txt_provider_promotion_taxes_note}

{if not $single_mode}
  {include file="main/promotion_link.tpl" title=$lng.lbl_new_orders href="`$catalogs.provider`/orders.php?substring=&amp;status=Q" promo_note=$lng.txt_provider_promotion_no_note}
  {include file="main/promotion_link.tpl" title=$lng.lbl_search_orders_menu href="`$catalogs.provider`/orders.php" promo_note=$lng.txt_provider_promotion_so_note}
{/if}
