{*
$Id: menu_box.tpl,v 1.3.2.1 2011/01/19 14:57:31 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
<ul id="horizontal-menu">

<li class="link">
<a href="home.php">{$lng.lbl_dashboard}</a>
</li>

<li>

{$lng.lbl_orders}

<div>
<a href="{$catalogs.admin}/orders.php?date=M">{$lng.lbl_this_month_orders}</a>
<a href="{$catalogs.admin}/orders.php">{$lng.lbl_search_orders_menu}</a>
<a href="{$catalogs.admin}/commissions.php">{$lng.lbl_provider_commissions}</a>
{if $active_modules.RMA ne ""}
<a href="{$catalogs.admin}/returns.php">{$lng.lbl_returns}</a>
{/if}
{if $active_modules.Subscriptions ne ""}
<a href="{$catalogs.admin}/orders.php?mode=subscriptions">{$lng.lbl_subscriptions_info}</a>
{/if}
{if $active_modules.Gift_Certificates ne ""}
<a href="{$catalogs.admin}/giftcerts.php">{$lng.lbl_gift_certificates}</a>
{/if}
</div>
</li>

<li>

{$lng.lbl_catalog}

<div>
<a href="{$catalogs.admin}/product_modify.php">{$lng.lbl_add_new_product}</a>
<a href="{$catalogs.admin}/search.php">{$lng.lbl_search_for_products}</a>
{if $active_modules.Customer_Reviews ne ""}
<a href="{$catalogs.admin}/ratings_edit.php">{$lng.lbl_ratings}</a>
{/if}
<a href="{$catalogs.admin}/categories.php">{$lng.lbl_categories}</a>
{if $active_modules.Manufacturers ne ""}
<a href="{$catalogs.admin}/manufacturers.php">{$lng.lbl_manufacturers}</a>
{/if}
{if $active_modules.Feature_Comparison ne ""}
<a href="classes.php">{$lng.lbl_product_feature_classes}</a>
{/if}
</div>
</li>

<li>

{$lng.lbl_users}

<div>
<a href="{$catalogs.admin}/users.php">{$lng.lbl_user_search}</a>
{if $active_modules.Wishlist}
<a href="{$catalogs.admin}/wishlists.php">{$lng.lbl_wish_lists}</a>
{/if}
<a href="{$catalogs.admin}/memberships.php">{$lng.lbl_membership_levels}</a>
<a href="{$catalogs.admin}/titles.php">{$lng.lbl_titles}</a>
{if $active_modules.Stop_List ne ""}
<a href="{$catalogs.admin}/stop_list.php">{$lng.lbl_stop_list}</a>
{/if}
</div>
</li>

<li>

{$lng.lbl_shipping_and_taxes}

<div>
<a href="{$catalogs.admin}/countries.php">{$lng.lbl_countries}</a>
<a href="{$catalogs.admin}/states.php">{$lng.lbl_states}</a>
<a href="{$catalogs.admin}/taxes.php">{$lng.lbl_taxes}</a>
<a href="{$catalogs.admin}/configuration.php?option=Shipping">{$lng.lbl_menu_shipping_options}</a>
<a href="{$catalogs.admin}/shipping.php">{$lng.lbl_shipping_methods}</a>
{if $active_modules.UPS_OnLine_Tools ne ""}
<a href="{$catalogs.admin}/ups.php">{$lng.lbl_ups_online_tools}</a>
{/if}
</div>
</li>

<li>

{$lng.lbl_tools}

<div>
<a href="{$catalogs.admin}/import.php">{$lng.lbl_import_export}</a>
<a href="{$catalogs.admin}/general.php">{$lng.lbl_summary}</a>
<a href="{$catalogs.admin}/statistics.php">{$lng.lbl_statistics}</a>
<a href="{$catalogs.admin}/db_backup.php">{$lng.lbl_db_backup_restore}</a>
<a href="{$catalogs.admin}/editor_mode.php">{$lng.lbl_webmaster_mode}</a>
<a href="{$catalogs.admin}/patch.php">{$lng.lbl_patch_upgrade}</a>
<a href="{$catalogs.admin}/change_mpassword.php">{$lng.lbl_change_mpassword}</a>
<a href="{$catalogs.admin}/tools.php">{$lng.lbl_maintenance}</a>
</div>
</li>

<li>

{$lng.lbl_settings}

<div>
<a href="{$catalogs.admin}/configuration.php">{$lng.lbl_general_settings}</a>
<a href="{$catalogs.admin}/payment_methods.php">{$lng.lbl_payment_methods}</a>
<a href="{$catalogs.admin}/card_types.php">{$lng.lbl_credit_card_types}</a>
<a href="{$catalogs.admin}/modules.php">{$lng.lbl_modules}</a>
<a href="{$catalogs.admin}/images_location.php">{$lng.lbl_images_location}</a>
</div>
</li>

<li>

{$lng.lbl_content}

<div>
<a href="{$catalogs.admin}/languages.php">{$lng.lbl_languages}</a>
<a href="{$catalogs.admin}/pages.php">{$lng.lbl_static_pages}</a>
<a href="{$catalogs.admin}/speed_bar.php">{$lng.lbl_speed_bar}</a>
<a href="{$catalogs.admin}/html_catalog.php">{$lng.lbl_html_catalog}</a>
{if $active_modules.News_Management ne ""}
<a href="{$catalogs.admin}/news.php">{$lng.lbl_news_management}</a>
{/if}
<a href="{$catalogs.admin}/file_edit.php">{$lng.lbl_edit_templates}</a>
<a href="{$catalogs.admin}/file_manage.php">{$lng.lbl_files}</a>
{if $active_modules.Survey ne ""}
<a href="{$catalogs.admin}/surveys.php">{$lng.lbl_survey_surveys}</a>
{/if}
</div>
</li>

{if $active_modules.XAffiliate ne ""}
{include file="admin/menu_affiliate.tpl"}
{/if}

{include file="admin/help.tpl"}

</ul>

