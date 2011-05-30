<?php /* Smarty version 2.6.26, created on 2011-05-26 16:19:10
         compiled from single/menu_box.tpl */ ?>
<?php func_load_lang($this, "single/menu_box.tpl","lbl_dashboard,lbl_orders,lbl_this_month_orders,lbl_search_orders_menu,lbl_returns,lbl_gift_certificates,lbl_subscriptions_info,lbl_catalog,lbl_add_new_product,lbl_products,lbl_edit_ratings,lbl_extra_fields,lbl_categories,lbl_manufacturers,lbl_discounts,lbl_coupons,lbl_users,lbl_users,lbl_wish_lists,lbl_membership_levels,lbl_titles,lbl_stop_list,lbl_shipping_and_taxes,lbl_countries,lbl_states,lbl_destination_zones,lbl_taxing_system,lbl_menu_shipping_options,lbl_shipping_methods,lbl_shipping_charges,lbl_shipping_markups,lbl_ups_online_tools,lbl_tools,lbl_import_export,lbl_update_inventory,lbl_summary,lbl_statistics,lbl_db_backup_restore,lbl_webmaster_mode,lbl_patch_upgrade,lbl_change_mpassword,lbl_maintenance,lbl_settings,lbl_general_settings,lbl_payment_methods,lbl_modules,lbl_credit_card_types,lbl_images_location,lbl_content,lbl_languages,lbl_static_pages,lbl_speed_bar,lbl_html_catalog,lbl_news_management,lbl_edit_templates,lbl_files,lbl_survey_surveys"); ?><ul id="horizontal-menu">

<li>
<a href="home.php"><?php echo $this->_tpl_vars['lng']['lbl_dashboard']; ?>
</a>
</li>

<li>

<?php echo $this->_tpl_vars['lng']['lbl_orders']; ?>


<div>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/orders.php?date=M"><?php echo $this->_tpl_vars['lng']['lbl_this_month_orders']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/orders.php"><?php echo $this->_tpl_vars['lng']['lbl_search_orders_menu']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['RMA'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/returns.php"><?php echo $this->_tpl_vars['lng']['lbl_returns']; ?>
</a>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Gift_Certificates'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/giftcerts.php"><?php echo $this->_tpl_vars['lng']['lbl_gift_certificates']; ?>
</a>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Subscriptions'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/orders.php?mode=subscriptions"><?php echo $this->_tpl_vars['lng']['lbl_subscriptions_info']; ?>
</a>
<?php endif; ?>
</div>
</li>

<li>

<?php echo $this->_tpl_vars['lng']['lbl_catalog']; ?>


<div>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/product_modify.php"><?php echo $this->_tpl_vars['lng']['lbl_add_new_product']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/search.php"><?php echo $this->_tpl_vars['lng']['lbl_products']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['Customer_Reviews'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/ratings_edit.php"><?php echo $this->_tpl_vars['lng']['lbl_edit_ratings']; ?>
</a>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Extra_Fields'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/extra_fields.php"><?php echo $this->_tpl_vars['lng']['lbl_extra_fields']; ?>
</a>
<?php endif; ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/categories.php"><?php echo $this->_tpl_vars['lng']['lbl_categories']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['Manufacturers']): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/manufacturers.php"><?php echo $this->_tpl_vars['lng']['lbl_manufacturers']; ?>
</a>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Product_Configurator'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Product_Configurator/pconf_menu_provider.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Feature_Comparison'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Feature_Comparison/admin_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/discounts.php"><?php echo $this->_tpl_vars['lng']['lbl_discounts']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['Discount_Coupons'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/coupons.php"><?php echo $this->_tpl_vars['lng']['lbl_coupons']; ?>
</a>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['Special_Offers'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Special_Offers/menu_provider.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
</div>
</li>

<li>

<?php echo $this->_tpl_vars['lng']['lbl_users']; ?>


<div>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/users.php"><?php echo $this->_tpl_vars['lng']['lbl_users']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['Wishlist']): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/wishlists.php"><?php echo $this->_tpl_vars['lng']['lbl_wish_lists']; ?>
</a>
<?php endif; ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/memberships.php"><?php echo $this->_tpl_vars['lng']['lbl_membership_levels']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/titles.php"><?php echo $this->_tpl_vars['lng']['lbl_titles']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['Stop_List'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/stop_list.php"><?php echo $this->_tpl_vars['lng']['lbl_stop_list']; ?>
</a>
<?php endif; ?>
</div>
</li>

<li>

<?php echo $this->_tpl_vars['lng']['lbl_shipping_and_taxes']; ?>


<div>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/countries.php"><?php echo $this->_tpl_vars['lng']['lbl_countries']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/states.php"><?php echo $this->_tpl_vars['lng']['lbl_states']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/zones.php"><?php echo $this->_tpl_vars['lng']['lbl_destination_zones']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/taxes.php"><?php echo $this->_tpl_vars['lng']['lbl_taxing_system']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/configuration.php?option=Shipping"><?php echo $this->_tpl_vars['lng']['lbl_menu_shipping_options']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/shipping.php"><?php echo $this->_tpl_vars['lng']['lbl_shipping_methods']; ?>
</a>
<?php if ($this->_tpl_vars['config']['Shipping']['enable_shipping'] == 'Y'): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/shipping_rates.php"><?php echo $this->_tpl_vars['lng']['lbl_shipping_charges']; ?>
</a>
<?php if ($this->_tpl_vars['config']['Shipping']['realtime_shipping'] == 'Y'): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/shipping_rates.php?type=R"><?php echo $this->_tpl_vars['lng']['lbl_shipping_markups']; ?>
</a>
<?php endif; ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['UPS_OnLine_Tools'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/ups.php"><?php echo $this->_tpl_vars['lng']['lbl_ups_online_tools']; ?>
</a>
<?php endif; ?>
</div>
</li>

<li>

<?php echo $this->_tpl_vars['lng']['lbl_tools']; ?>


<div>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/import.php"><?php echo $this->_tpl_vars['lng']['lbl_import_export']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['provider']; ?>
/inv_update.php"><?php echo $this->_tpl_vars['lng']['lbl_update_inventory']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/general.php"><?php echo $this->_tpl_vars['lng']['lbl_summary']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/statistics.php"><?php echo $this->_tpl_vars['lng']['lbl_statistics']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/db_backup.php"><?php echo $this->_tpl_vars['lng']['lbl_db_backup_restore']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/editor_mode.php"><?php echo $this->_tpl_vars['lng']['lbl_webmaster_mode']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/patch.php"><?php echo $this->_tpl_vars['lng']['lbl_patch_upgrade']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/change_mpassword.php"><?php echo $this->_tpl_vars['lng']['lbl_change_mpassword']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/tools.php"><?php echo $this->_tpl_vars['lng']['lbl_maintenance']; ?>
</a>
</div>
</li>

<li>

<?php echo $this->_tpl_vars['lng']['lbl_settings']; ?>


<div>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/configuration.php"><?php echo $this->_tpl_vars['lng']['lbl_general_settings']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/payment_methods.php"><?php echo $this->_tpl_vars['lng']['lbl_payment_methods']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/modules.php"><?php echo $this->_tpl_vars['lng']['lbl_modules']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/card_types.php"><?php echo $this->_tpl_vars['lng']['lbl_credit_card_types']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/images_location.php"><?php echo $this->_tpl_vars['lng']['lbl_images_location']; ?>
</a>
</div>
</li>

<li>

<?php echo $this->_tpl_vars['lng']['lbl_content']; ?>


<div>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/languages.php"><?php echo $this->_tpl_vars['lng']['lbl_languages']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/pages.php"><?php echo $this->_tpl_vars['lng']['lbl_static_pages']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/speed_bar.php"><?php echo $this->_tpl_vars['lng']['lbl_speed_bar']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/html_catalog.php"><?php echo $this->_tpl_vars['lng']['lbl_html_catalog']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['News_Management']): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/news.php"><?php echo $this->_tpl_vars['lng']['lbl_news_management']; ?>
</a>
<?php endif; ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/file_edit.php"><?php echo $this->_tpl_vars['lng']['lbl_edit_templates']; ?>
</a>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/file_manage.php"><?php echo $this->_tpl_vars['lng']['lbl_files']; ?>
</a>
<?php if ($this->_tpl_vars['active_modules']['Survey'] != ""): ?>
<a href="<?php echo $this->_tpl_vars['catalogs']['admin']; ?>
/surveys.php"><?php echo $this->_tpl_vars['lng']['lbl_survey_surveys']; ?>
</a>
<?php endif; ?>
</div>
</li>

<?php if ($this->_tpl_vars['active_modules']['XAffiliate'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin/menu_affiliate.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "admin/help.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</ul>