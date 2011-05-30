ALTER TABLE xcart_amazon_data ADD sessionid varchar(255) NOT NULL DEFAULT '';
ALTER TABLE xcart_amazon_data ADD INDEX sessionid (sessionid);

ALTER TABLE xcart_customers ADD INDEX email (email);

UPDATE xcart_login_history SET ip=INET_ATON(ip);
ALTER TABLE xcart_login_history MODIFY ip int(11) unsigned NOT NULL DEFAULT '0';


ALTER TABLE xcart_payment_methods ADD INDEX processor_file (processor_file);


DELETE FROM xcart_session_history;
ALTER TABLE xcart_session_history MODIFY ip int(11) unsigned NOT NULL DEFAULT '0';


DROP TABLE IF EXISTS xcart_session_unknown_sid;
CREATE TABLE IF NOT EXISTS xcart_session_unknown_sid (
  sessid varchar(40) NOT NULL default '',
  ip int(11) unsigned NOT NULL default '0',
  cnt int(11) NOT NULL default '0',
  PRIMARY KEY  (ip,sessid)
) ENGINE=MyISAM;


UPDATE xcart_survey_results SET ip=INET_ATON(ip);
ALTER TABLE xcart_survey_results MODIFY ip int(11) unsigned NOT NULL DEFAULT '0';


UPDATE IGNORE xcart_ccprocessors SET `disable_ccinfo` = "N" WHERE `processor` = "cc_bean.php";
UPDATE IGNORE xcart_ccprocessors SET `param06` = "https://www.2checkout.com/checkout/purchase" WHERE `processor` = "cc_2conew.php";

---- ******************************************************************************** ----

INSERT INTO xcart_config (`name`, `comment`, `value`, `category`, `orderby`, `type`, `defvalue`, `variants`, `validation`) VALUES ("adv_generator_url", "Link to adv message generator", "http://www.x-cart.com/ads", "", "0", "text", "http://www.x-cart.com/ads", "", "");
INSERT INTO xcart_config (`name`, `comment`, `value`, `category`, `orderby`, `type`, `defvalue`, `variants`, `validation`) VALUES ("skip_js_validation_admin", "Disable JavaScript validation for User Profiles in the admin area", "N", "User_Profiles", "30", "checkbox", "N", "", "");
INSERT INTO xcart_config (`name`, `comment`, `value`, `category`, `orderby`, `type`, `defvalue`, `variants`, `validation`) VALUES ("force_offline_paymentid", "Use this payment method when on-line payment methods are disabled.", "4", "Egoods", "35", "selector", "4", "func_get_offline_payment_methods", "uint");
INSERT INTO xcart_config (`name`, `comment`, `value`, `category`, `orderby`, `type`, `defvalue`, `variants`, `validation`) VALUES ("use_cached_templates", "Use cached buy_now.tpl template calls", "Y", "General", "855", "checkbox", "Y", "", "");
INSERT INTO xcart_config (`name`, `comment`, `value`, `category`, `orderby`, `type`, `defvalue`, `variants`, `validation`) VALUES ("enable_amazon_top_button", "Display the \"Checkout with Amazon\" button at the top of catalog pages.", "N", "Amazon_Checkout", "200", "checkbox", "N", "", "");

---- ******************************************************************************** ----

UPDATE IGNORE xcart_languages SET `value` = "Make sure you enabled this option if the \"Require CVD number for credit card transactions\" option is checked. To change the \"Require CVD number for credit card transactions\" option:<br /><ol><li>Login to the Merchant Interface at <a href=\"https://www.beanstream.com/admin/sDefault.asp\" target=\"_blank\">https://www.beanstream.com/admin/sDefault.asp</a></li><li>Click on \"Administration :: Account Settings :: Order Settings\" in the left side menu. You will find the option in the \"Transaction Validation Options\" section.</li></ol>" WHERE `code` = "en" AND `name` = "lbl_cc_bean_note";
UPDATE IGNORE xcart_languages SET `topic` = "Labels" WHERE `code` = "en" AND `name` = "lbl_cc_eselect_approve_decline_url";
UPDATE IGNORE xcart_languages SET `topic` = "Labels" WHERE `code` = "en" AND `name` = "lbl_cc_eselect_cvd_avs_efraud";
UPDATE IGNORE xcart_languages SET `topic` = "Labels" WHERE `code` = "en" AND `name` = "lbl_cc_eselect_secret_key";
UPDATE IGNORE xcart_languages SET `topic` = "Labels" WHERE `code` = "en" AND `name` = "lbl_cc_secpay_digest";
UPDATE IGNORE xcart_languages SET `topic` = "Labels" WHERE `code` = "en" AND `name` = "lbl_cc_secpay_remotepass";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "lbl_fedex_address_2";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "lbl_max_review";
UPDATE IGNORE xcart_languages SET `value` = "Your customers will be unable to purchase products because there are no active payment methods in your store." WHERE `code` = "en" AND `name` = "lbl_no_active_payment_methods";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "lbl_no_active_payment_methods_and_gc";
UPDATE IGNORE xcart_languages SET `topic` = "Labels" WHERE `code` = "en" AND `name` = "lbl_save_search_results";
UPDATE IGNORE xcart_languages SET `value` = "Shipping is not applicable or shipping address is not defined yet." WHERE `code` = "en" AND `name` = "lbl_shipping_address_empty_warn";
UPDATE IGNORE xcart_languages SET `value` = "Widget to be used for displaying detailed images in a popup window.<br /><font class=\"ErrorMessage\">Warning! For ColorBox to operate correctly, detailed images must be stored in the file system.</font>" WHERE `code` = "en" AND `name` = "opt_det_image_box_plugin";
UPDATE IGNORE xcart_languages SET `value` = "Enable email notifications for orders department about placed orders (online payment methods)" WHERE `code` = "en" AND `name` = "opt_enable_init_order_notif";
UPDATE IGNORE xcart_languages SET `value` = "Enable email notifications for orders department about placed orders (online payment methods)" WHERE `code` = "en" AND `name` = "opt_enable_init_order_notif_customer";
UPDATE IGNORE xcart_languages SET `value` = "Order is queued/pre-authorized notification to orders department" WHERE `code` = "en" AND `name` = "opt_enable_order_notif";
UPDATE IGNORE xcart_languages SET `value` = "Number of columns to display the offers list in<br />\n\r<font class=\"Star\">Note: The recommended value is 3 or less.</font>" WHERE `code` = "en" AND `name` = "opt_offers_per_row";
UPDATE IGNORE xcart_languages SET `value` = "Number of columns to display the product list in<br />\n\r<font class=\"Star\">Note: The recommended value is 3 or less.</font>" WHERE `code` = "en" AND `name` = "opt_products_per_row";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "txt_about";
UPDATE IGNORE xcart_languages SET `value` = "If you have a valid discount coupon, enter the code below and the store will deduct the discount from your order total." WHERE `code` = "en" AND `name` = "txt_add_coupon_header";
UPDATE IGNORE xcart_languages SET `value` = "Please note: 1) Delivery Method is ignored if you are ordering Gift Certificates or electronically distributed products. 2) Gift Certificates are redeemed during Checkout process." WHERE `code` = "en" AND `name` = "txt_cart_note";
UPDATE IGNORE xcart_languages SET `value` = "<b>Note:</b> In setup NetBanx payment gateway you have to proceed these steps:<ul><li>Set referring URL to :<br />{{http_location}}/payment/cc_netbanx.php<br />or, if you are using Secure Connection,<br />{{https_location}}/payment/cc_netbanx.php</li><li>If you would like to use SHA1 checksum validation enter the Secret key in the form below. Otherwise leave the Secret key field empty</li></ul>" WHERE `code` = "en" AND `name` = "txt_cc_netbanx_note";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "txt_conditions_customer";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "txt_faq";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "txt_privacy_statement";
DELETE FROM xcart_languages WHERE `code` = "en" AND `name` = "txt_publicity_msg";
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_cc_hsbc_use_fraud_service", "Fraud check service", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_enable_cvv2", "Enable CVV2", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_gift_certificate_turn_on", "The <a href=\"modules.php\">\"Gift Certificates\" module</a> should be turned on", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_note_for_zero_cost_orders", "Use this payment method for orders with zero total cost.", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_no_active_payment_methods_and_gc_ac", "Your customers will be unable to purchase products because Google Checkout/Amazon Checkout are not properly configured, and no other payment methods are currently active at your store.", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_signup_for_acheckout", "Sign up for Checkout by Amazon", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_user_profiles_settings", "User Profiles settings", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "lbl_usps_container3", "Container (International Rates)", "Labels");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "opt_descr_force_offline_paymentid", "This payment method will be forced when no other off-line or on-line payment methods are available for any reason. Leave it empty to disable this feature.", "Options");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "opt_descr_skip_js_validation_admin", "Select this if you are experiencing performance problems with your store.", "Options");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "opt_descr_use_cached_templates", "Recommended value is ON<br />You can use smarty\'s {include_cache} function instead of the standart {include} function for the maximum performance. Disable this option for the development mode, to generate HTML code from scratch.", "Options");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "opt_enable_amazon_top_button", "Display the \"Checkout with Amazon\" button at the top of catalog pages.", "Options");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "opt_force_offline_paymentid", "Use this payment method when on-line payment methods are disabled.", "Options");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "opt_skip_js_validation_admin", "Disable JavaScript validation for User Profiles in the admin area", "Options");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "opt_use_cached_templates", "Use cached buy_now.tpl template calls", "Options");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "txt_acheckout_impossible_error", "Error: Cannot start Amazon checkout because a unique key for transaction could not be created.", "Text");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "txt_acheckout_setup_note", "To set up your Checkout by Amazon module, please adjust the fields below. You should obtain your Merchant ID,Access Key ID and Secret Access Key values from your Checkout by Amazon account. Choose \'Test mode\' if you are going to use a Sandbox account. Choose \'Live mode\' if you are going to use your production account.<br /><br />\nThis URL should be used as an \'Merchant URL\' in your Checkout by Amazon account:<br />\n<b>{{callback_url}}</b><br /><br />\n(Log in to Seller Central, click on the \'Settings\' tab, then click on the \'Checkout Pipeline Settings\' link in the menu. Enter this URL into the field \'Merchant URL\')<br /><br />Please note that, in Live mode, Checkout by Amazon only communicates with servers that have SSL certificates installed. Make sure your server has a valid SSL certificate, otherwise the module will not be able to function correctly, as your store will not be able to receive any messages or notifications from Checkout by Amazon.<br /><br />\nIn Test mode, an http connection can be used.<br /><br />\nVisit <a href=\"https://payments.amazon.com/sdui/sdui/business/resources#cba\" target=\"_blank\">this page</a> to learn more about Checkout by Amazon API.", "Text");
INSERT INTO xcart_languages (`code`, `name`, `value`, `topic`) VALUES ("en", "txt_cc_hsbc_fraud_check_note", "To use the Fraud check service, please enable it in your HSBC merchant account settings.", "Text");

---- ******************************************************************************** ----


---- ******************************************************************************** ----

UPDATE xcart_shipping SET `amazon_service` = "TwoDay" WHERE `code` = "UPS" AND `subcode` = "2";
UPDATE xcart_shipping SET `amazon_service` = "TwoDay" WHERE `code` = "UPS" AND `subcode` = "18";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "UPS" AND `subcode` = "20";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "UPS" AND `subcode` = "66";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "UPS" AND `subcode` = "22";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "UPS" AND `subcode` = "105";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "UPS" AND `subcode` = "3";
UPDATE xcart_shipping SET `amazon_service` = "TwoDay" WHERE `code` = "UPS" AND `subcode` = "8";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "FDX" AND `subcode` = "45";
UPDATE xcart_shipping SET `amazon_service` = "TwoDay" WHERE `code` = "FDX" AND `subcode` = "41";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "USPS" AND `subcode` = "50";
UPDATE xcart_shipping SET `amazon_service` = "TwoDay" WHERE `code` = "USPS" AND `subcode` = "51";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "USPS" AND `subcode` = "52";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "USPS" AND `subcode` = "161";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "USPS" AND `subcode` = "150";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "USPS" AND `subcode` = "151";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "USPS" AND `subcode` = "152";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "USPS" AND `subcode` = "170";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "USPS" AND `subcode` = "165";
UPDATE xcart_shipping SET `amazon_service` = "TwoDay" WHERE `code` = "ARB" AND `subcode` = "31";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "ARB" AND `subcode` = "32";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "ARB" AND `subcode` = "33";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "FDX" AND `subcode` = "46";
UPDATE xcart_shipping SET `amazon_service` = "TwoDay" WHERE `code` = "FDX" AND `subcode` = "47";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "FDX" AND `subcode` = "48";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "FDX" AND `subcode` = "49";
UPDATE xcart_shipping SET `amazon_service` = "OneDay" WHERE `code` = "DHL" AND `subcode` = "10000";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "CPC" AND `subcode` = "93";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "ARB" AND `subcode` = "108";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "ARB" AND `subcode` = "109";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "ARB" AND `subcode` = "124";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "ARB" AND `subcode` = "114";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "CPC" AND `subcode` = "116";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "CPC" AND `subcode` = "119";
UPDATE xcart_shipping SET `amazon_service` = "Expedited" WHERE `code` = "APOST" AND `subcode` = "123";

---- ******************************************************************************** ----

UPDATE xcart_config SET `value` = "4.4.3" WHERE `name` = "version";

REPLACE INTO xcart_config (`name`, `comment`, `value`, `category`, `orderby`, `type`, `defvalue`, `variants`, `validation`) VALUES ('bf_generation_date', '', UNIX_TIMESTAMP(now()),'',0,'text','','','');
REPLACE INTO xcart_config (`name`, `comment`, `value`, `category`, `orderby`, `type`, `defvalue`, `variants`, `validation`) VALUES ('db_backup_date','', UNIX_TIMESTAMP(now()),'',0,'text','','','');

DELETE FROM xcart_stats_customers_products WHERE userid=0 ;


-- From 4.4.2
UPDATE xcart_config SET `value` = 'long_direct' WHERE `name` = 'page_title_format' AND value='A';
UPDATE xcart_config SET `value` = 'long_reverse' WHERE `name` = 'page_title_format' AND value='D';
