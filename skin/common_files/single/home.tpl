{*
$Id: home.tpl,v 1.2 2010/07/27 07:35:42 igoryan Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$lng.txt_site_title}</title>
{include file="meta.tpl"}
<link rel="stylesheet" type="text/css" href="{$SkinDir}/css/skin1_admin.css" />
</head>
<body{$reading_direction_tag}{if $login eq ""} class="not-logged-in"{/if}>
{include file="rectangle_top.tpl"}
{include file="head_admin.tpl" need_quick_search="Y"}
{if $login ne ""}
{include file="single/menu_box.tpl"}
{/if}
<!-- main area -->
<table width="100%" cellpadding="0" cellspacing="0" align="center">
<tr>
<td valign="top" class="central-space{if $dialog_tools_data}-dtools{/if}">
<!-- central space -->
{include file="main/evaluation.tpl"}

{include file="location.tpl"}

{if $main eq "authentication"}

{include file="main/authentication.tpl" login_title=$lng.lbl_admin_login_title}

{elseif $smarty.get.mode eq "subscribed"}
{include file="main/subscribe_confirmation.tpl"}

{elseif $main eq "import_export"}
{include file="main/import_export.tpl"}

{elseif $main eq "ups_import"}
{include file="modules/Order_Tracking/ups_import.tpl"}

{elseif $main eq "froogle_export"}
{include file="modules/Froogle/froogle.tpl"}

{elseif $main eq "snapshots"}
{include file="admin/main/snapshots.tpl"}

{elseif $main eq "titles"}
{include file="admin/main/titles.tpl"}

{elseif $main eq "zones"}
{include file="provider/main/zones.tpl"}

{elseif $main eq "zone_edit"}
{include file="provider/main/zone_edit.tpl"}

{elseif $main eq "ups_registration"}
{include file="modules/UPS_OnLine_Tools/ups.tpl"}

{elseif $main eq "order_edit"}
{include file="modules/Advanced_Order_Management/order_edit.tpl"}

{elseif $main eq "manufacturers"}
{include file="modules/Manufacturers/manufacturers.tpl"}

{elseif $main eq "wishlists"}
{include file="modules/Wishlist/wishlists.tpl"}

{elseif $main eq "wishlist"}
{include file="modules/Wishlist/display_wishlist.tpl"}

{elseif $main eq "user_profile"}
{include file="$tpldir/main/register.tpl"}

{elseif $main eq "stop_list"}
{include file="modules/Stop_List/stop_list.tpl"}

{elseif $main eq "returns"}
{include file="modules/RMA/returns.tpl"}

{elseif $main eq "benchmark"}
{include file="modules/Benchmark/bench.tpl"}

{elseif $main eq "slg"}
{include file="modules/Shipping_Label_Generator/generator.tpl"}

{elseif $main eq "register"}
{include file="admin/main/register.tpl"}

{elseif $main eq "product_links"}
{include file="admin/main/product_links.tpl"}

{elseif $main eq "general_info"}
{include file="admin/main/general.tpl"}

{elseif $main eq "tools"}
{include file="admin/main/tools.tpl"}

{elseif $main eq "user_access_control"}
{include file="admin/main/user_access_control.tpl"}

{elseif $main eq "taxes"}
{include file="admin/main/taxes.tpl"}

{elseif $main eq "tax_edit"}
{include file="admin/main/tax_edit.tpl"}

{elseif $main eq "pages"}
{include file="admin/main/pages.tpl"}
 
{elseif $main eq "page_edit"}
{include file="admin/main/page_edit.tpl"}

{elseif $main eq "change_mpassword"}
{include file="admin/main/change_mpassword.tpl"}

{elseif $main eq "countries_edit"}
{include file="admin/main/countries.tpl"}

{elseif $main eq "counties_edit"}
{include file="admin/main/counties.tpl"}

{elseif $main eq "images_location"}
{include file="admin/main/images_location.tpl"}

{elseif $main eq "shipping_options"}
{include file="admin/main/shipping_options.tpl"}

{elseif $main eq "subscriptions"}
{include file="modules/Subscriptions/subscriptions_admin.tpl"}

{elseif $main eq "languages"}
{include file="admin/main/languages.tpl"}

{elseif $main eq "banner_info"}
{include file="admin/main/banner_info.tpl"}

{elseif $main eq "memberships"}
{include file="admin/main/memberships.tpl"}

{elseif $main eq "card_types"}
{include file="admin/main/card_types.tpl"}

{elseif $main eq "referred_sales"}
{include file="main/referred_sales.tpl"}

{elseif $main eq "affiliates"}
{include file="main/affiliates.tpl"}

{elseif $main eq "partner_top_performers"}
{include file="admin/main/partner_top_performers.tpl"}

{elseif $main eq "partner_adv_stats"}
{include file="admin/main/partner_adv_stats.tpl"}

{elseif $main eq "partner_adv_campaigns"}
{include file="admin/main/partner_adv_campaigns.tpl"}

{elseif $main eq "partner_level_commissions"}
{include file="admin/main/partner_level_commissions.tpl"}

{elseif $main eq "partner_orders"}
{include file="admin/main/partner_orders.tpl"}

{elseif $main eq "partner_report"}
{include file="admin/main/partner_report.tpl"}

{elseif $main eq "partner_plans"}
{include file="admin/main/partner_plans.tpl"}

{elseif $main eq "partner_plans_edit"}
{include file="admin/main/partner_plans_edit.tpl"}

{elseif $main eq "partner_banners"}
{include file="main/partner_banners.tpl"}

{elseif $main eq "payment_upload"}
{include file="admin/main/payment_upload.tpl"}

{elseif $smarty.get.mode eq "unsubscribed"}
{include file="main/unsubscribe_confirmation.tpl"}

{elseif $main eq "search"}
{include file="main/search_result.tpl" products=$products}

{elseif $main eq "categories"}
{include file="admin/main/categories.tpl"}

{elseif $main eq "modules"}
{include file="admin/main/modules.tpl"}

{elseif $main eq "payment_methods" and $use_paypal_flow}
{include file="admin/main/paypal_flow.tpl"}

{elseif $main eq "payment_methods"}
{include file="admin/main/payment_methods.tpl"}

{elseif $main eq "cc_processing"}
{include file="admin/main/cc_processing_main.tpl" processing_module=$processing_module}

{elseif $main eq "edit_file"}
{include file="admin/main/edit_file.tpl"}

{elseif $main eq "edit_dir"}
{include file="admin/main/edit_dir.tpl"}

{elseif $main eq "countries"}
{include file="provider/main/countries.tpl"}

{elseif $main eq "statistics"}
{include file="admin/main/statistics.tpl"}

{elseif $main eq "configuration"}
{include file="admin/main/configuration.tpl"}

{elseif $main eq "shipping"}
{include file="admin/main/shipping.tpl"}

{elseif $main eq "states_edit"}
{include file="admin/main/states.tpl"}

{elseif $main eq "users"}
{include file="admin/main/users.tpl"}

{elseif $main eq "featured_products"}
{include file="admin/main/featured_products.tpl"}

{elseif $main eq "category_modify"}
{include file="admin/main/category_modify.tpl"}

{elseif $main eq "category_products"}
{include file="admin/main/category_products.tpl"}

{elseif $main eq "category_delete_confirmation"}
{include file="admin/main/category_del_confirmation.tpl"}

{elseif $main eq "user_delete_confirmation"}
{include file="admin/main/user_delete_confirmation.tpl"}

{elseif $main eq "user_add"}
{include file="$tpldir/main/register.tpl"}

{elseif $main eq "product"}
{include file="main/product.tpl" product=$product}

{elseif $main eq "discounts"}
{include file="provider/main/discounts.tpl"}

{elseif $main eq "coupons"}
{include file="modules/Discount_Coupons/coupons.tpl"}

{elseif $main eq "product_options"}
{if $active_modules.Product_Options ne ""}
{include file="modules/Product_Options/global_prodopts.tpl"}
{/if}

{elseif $main eq "extra_fields"}
{if $active_modules.Extra_Fields ne ""}
{include file="modules/Extra_Fields/extra_fields.tpl"}
{/if}

{elseif $main eq "giftcerts"}
{include file="modules/Gift_Certificates/gc_admin.tpl"}

{elseif $main eq "db_backup"}
{include file="admin/main/db_backup.tpl"}

{elseif $main eq "shipping_rates"}
{include file="provider/main/shipping_rates.tpl"}

{elseif $main eq "shipping_zones"}
{include file="provider/main/shipping_zones.tpl"}

{elseif $main eq "top_info"}
{include file="admin/main/main.tpl"}

{elseif $main eq "promo"}
{include file="admin/main/promotions.tpl"}

{elseif $main eq "ratings_edit"}
{include file="admin/main/ratings_edit.tpl"}

{elseif $main eq "inv_update"}
{include file="provider/main/inv_update.tpl"}

{elseif $main eq "inv_updated"}
{include file="main/inv_updated.tpl"}

{elseif $main eq "error_inv_update"}
{include file="main/error_inv_update.tpl"}

{elseif $main eq "html_catalog"}
{include file="admin/main/html_catalog.tpl"}

{elseif $main eq "speed_bar"}
{include file="admin/main/speed_bar.tpl"}

{elseif $main eq "product_configurator"}
{include file="modules/Product_Configurator/pconf_common.tpl"}

{elseif $main eq "news_management"}
{include file="modules/News_Management/news_common.tpl"}

{elseif $main eq "change_password"}
{include file="main/change_password.tpl"}

{elseif $main eq "test_pgp"}
{include file="admin/main/test_pgp.tpl"}

{elseif $main eq "special_offers"}
{include file="modules/Special_Offers/common.tpl"}

{elseif $main eq "logs"}
{include file="admin/main/logs.tpl"}

{else}
{include file="common_templates.tpl"}
{/if}

<!-- /central space -->
&nbsp;
</td>

<td valign="top">
  {include file="dialog_tools.tpl"}
</td>

</tr>
</table>
{include file="rectangle_bottom.tpl"}
</body>
</html>
